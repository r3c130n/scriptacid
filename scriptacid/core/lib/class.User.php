<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
/**
 * Пользователи системы
 * @author r3c130n
 */
class User {
	private $arUser;
	private $USER_ID = false;
	private $bAuthorized = false;
	private $bAdmin = false;
	private $arGroups;
	public  $cUsersCount = 0;

	public function  __construct() {
		if(!empty($_SESSION['USER']) AND $this->CheckUser()) {
			if (intVal($_SESSION['USER']["USER_ID"]) > 0) {
				$this->USER_ID = intVal($_SESSION['USER']["USER_ID"]);
				$this->FillUser();
				$this->FillGroups();
				$this->UpdateSession();
				$this->bAuthorized = true;
			}
		}
	}

	/**
	 * Получить подпись вида "ИМЯ [НИК] ФАМИЛИЯ"
	 * @param int $ID
	 * @return string
	 */
	public static function GetSign($ID) {
		$arUser = self::GetByID($ID);

		$sign = '';

		$sign .= empty($arUser["NAME"]) ? '' : $arUser["NAME"].' [';
		$sign .= $arUser["LOGIN"];
		$sign .= empty($arUser["LAST_NAME"]) ? '' : '] '.$arUser["LAST_NAME"];


		return $sign;
	}

	/**
	 * Получить ID текущего пользователя
	 * @return int
	 */
	public function GetID() {
		if ($this->USER_ID > 0) {
			return $this->USER_ID;
		}
		return 0;
	}

	/**
	 * Авторизоваться под пользователем с ID = $USER_ID
	 * @param int $USER_ID
	 * @return bool
	 */
	public function Authorize($USER_ID = false) {
		if ($USER_ID) {
			$this->USER_ID = intVal($USER_ID);
		} else {
			return false;
		}
		if($this->Check($this->USER_ID)) {
			$this->FillUser();
			$this->FillGroups();
			$this->FillSession();
			$this->bAuthorized = true;
			/**
			 * TODO: Предусмотреть на событиях перегрузку страницы после
			 * полного цикла выполнения. поскольку если авторизация происходит
			 * в теле страницы, то верх шаблона до выполнения тела
			 * выполняется без авторизации. Напрпимер не отображается панель.
			 */
			return true;
		} else {
			return false;
		}
		
	}

	/**
	 * Убрать авторизацию пользователя
	 */
	public function UnAuthorize() {
		$this->arUser = Array();
		$this->arGroups = Array();
		$this->USER_ID = 0;
		$this->bAuthorized = false;
		$this->bAdmin = false;

		unset($_SESSION["USER"]);
	}

	/**
	 * Проверить, авторизован ли текущий пользователь
	 * @return bool
	 */
	public function IsAuthorized() {
		return $this->bAuthorized;
	}

	/**
	 * Заполнить массив полей текущего пользователя
	 * @global object $DB
	 */
	private function FillUser() {
		$DB = Application::getInstance()->DB; // Любой из вариантов: App::DB();App::get()->DB;Application::get()->DB;
		$sql = "SELECT * FROM `b_user` WHERE `ID` = '".$this->USER_ID."';";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			$this->arUser = $arUser;
		}
	}

	/**
	 * Заполнить группы, которым принадлежит текущий пользователь
	 * @global object $DB
	 * @param int $USER_ID
	 * @return array
	 */
	private function FillGroups($USER_ID = false) {
		$DB = App::DB();
		$arGroups = Array();
		if (!$USER_ID) {
			$USER_ID = $this->USER_ID;
			$bSetGroup = true;
		} else {
			$USER_ID = IntVal($USER_ID);
			$bSetGroup = false;
		}

		$sql = "SELECT * FROM `b_user_group` WHERE `USER_ID` = '".$USER_ID."';";
		$DB->Query($sql);
		while ($arGroup = $DB->Fetch()) {
			$arGroups[] = $arGroup["GROUP_ID"];
		}
		if($bSetGroup) {
			$this->arGroups = $arGroups;
			if (in_array('1', $arGroups)) {
				$this->bAdmin = true;
			}
		}
		return $arGroups;
	}

	/**
	 * Проверить, существует ли пользователь с заданным ID
	 * @global object $DB
	 * @param int $USER_ID
	 * @return bool
	 */
	public function Check($USER_ID) {
		$DB = App::DB();
		$sql = "SELECT `LOGIN` FROM `b_user` WHERE `ID` = '".IntVal($USER_ID)."';";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			if(!empty($arUser)) {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * Проверить соответствие данных в сессии и соответствующего SESSION_ID
	 * @return bool
	 */
	public function CheckUser() {
		if ($_SERVER["REMOTE_ADDR"] === $_SESSION['USER']['IP'] AND $_SERVER["HTTP_USER_AGENT"] === $_SESSION['USER']['BROWSER']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Записать в сессию данные текущего пользователя
	 * @return bool
	 */
	private function FillSession() {
		$_SESSION['USER'] = Array(
			"USER_ID" => $this->USER_ID,
			"NAME" => $this->arUser["NAME"],
			"LAST_NAME" => $this->arUser["LAST_NAME"],
			"LOGIN" => $this->arUser["LOGIN"],
			"EMAIL" => $this->arUser["EMAIL"],
			"IS_ADMIN" => $this->bAdmin ? "Y" : "N",
			"IP" => $_SERVER["REMOTE_ADDR"],
			"BROWSER" => $_SERVER["HTTP_USER_AGENT"],
			"LAST_ACTIVITY" => time(),
		);
		return true;
	}

	/**
	 * Обновить данные текущего пользователя в сессии (при каждом хите)
	 * @return bool
	 */
	private function UpdateSession() {
		$_SESSION['USER']["LAST_ACTIVITY"] = time();
		return true;
	}

	/**
	 * Проверить принадлежность пользователя к группе $GROUP_ID
	 * @param int $GROUP_ID
	 * @return bool
	 */
	public function CheckGroup($GROUP_ID) {
		if (in_array($GROUP_ID, $this->arGroups)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Проверить, есть ли пользователь с такими логином и паролем
	 * @global object $DB
	 * @param string $login
	 * @param string $password
	 * @return bool
	 */
	public function CheckAuth($login, $password) {
		$DB = App::DB();
		$passwd = $this->GetHash($password);
		$sql = "SELECT ID FROM `b_user` WHERE `LOGIN` = '".sSql($login)."' AND `PASSWORD` = '".$passwd."';";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			return $arUser["ID"];
		} else {
			return false;
		}
	}

	/**
	 * Является ли текущий пользователь админом
	 * @return bool
	 */
	public function IsAdmin() {
		//TODO: добавить проверку любого пользователя по его ID
		return $this->bAdmin;
	}

	/**
	 * Сгенерировать хеш пароля
	 * @param string $passwd
	 * @return hash
	 */
	public function GetHash($passwd) {
		$passwd64 = base64_encode($passwd);
		return strtoupper(md5($passwd.':'.$passwd64.':'.SALT));
	}

	/**
	 * Зарегистрировать пользователя
	 * @global object $DB
	 * @param string $login
	 * @param string $password
	 * @param string $email
	 * @param array $arFields
	 * @return int ID нового пользователя
	 */
	public function Register($login, $password, $email, $arFields = Array()) {
		$DB = App::DB();
		
		if ($this->CheckAuth($login, $password))
			return false;
		$usr = $this->GetByLogin($login, true);
		if (!empty($usr)) {
			return false;
		}

		$passwd = $this->GetHash($password);
		$arUserFields = Array(
			'LOGIN' => $login,
			'PASSWORD' => $passwd,
			'EMAIL'	=>	$email,
		);

		if (!empty($arFields)) {
			if (!empty($arFields["GROUPS"])) {
				foreach ($arFields["GROUPS"] as $arGroup) {
					if (is_array($arGroup) AND !empty($arGroup["GROUP_ID"])) {
						$arGr[] = Array(
							"USER_ID" => '',
							"GROUP_ID" => intVal($arGroup["GROUP_ID"]),
							"DATE_ACTIVE_FROM" => $arGroup["DATE_ACTIVE_FROM"],
							"DATE_ACTIVE_TO" => $arGroup["DATE_ACTIVE_TO"]
						);
					} else {
						$arGr[] = Array(
							"USER_ID" => '',
							"GROUP_ID" => intVal($arGroup),
							"DATE_ACTIVE_FROM" => null,
							"DATE_ACTIVE_TO" => null
						);
					}
					
				}
				unset($arFields["GROUPS"]);
			}
		}
		
		$sql = addSql('b_user', array_merge($arFields, $arUserFields));

		if ($DB->Query($sql)) {
			$newID = $DB->LastID();
			if ($newID > 0) {
				if (!empty($arGr)) {
					foreach ($arGr as $group) {
						$group["USER_ID"] = $newID;
						$sql = addSql('b_user_group', $group);
						if(!$DB->Query($sql))
							return false;
					}
				}
				return $newID;
			}
		}
	}

	/**
	 * Получить массив с данными пользователя по его ID
	 * @global object $DB
	 * @param int $USER_ID
	 * @param bool $bWOGroups без получения групп
	 * @return array
	 */
	public static function GetByID($USER_ID, $bWOGroups = false) {
		$DB = App::DB();
		$sql = "SELECT * FROM `b_user` WHERE `ID` = '".intVal($USER_ID)."';";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			if(!$bWOGroups) {
				$sql = "SELECT * FROM `b_user_group` WHERE `USER_ID` = '".$arUser["ID"]."';";
				$DB->Query($sql);
				while ($arGroup = $DB->Fetch()) {
					$arUser["GROUPS"][] = $arGroup["GROUP_ID"];
				}
			}
			return $arUser;
		} else {
			return $arUser;
		}
		return false;
	}

	/**
	 * Получить массив с данными пользователя по его логину
	 * @global object $DB
	 * @param string $login
	 * @param bool $bWOGroups без получения групп
	 * @return array
	 */
	public static function GetByLogin($login, $bWOGroups = false) {
		$DB = App::DB();
		$sql = "SELECT * FROM `b_user` WHERE `LOGIN` = '".sSql($login)."';";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			if(!$bWOGroups) {
				$sql = "SELECT * FROM `b_user_group` WHERE `USER_ID` = '".$arUser["ID"]."';";
				$DB->Query($sql);
				while ($arGroup = $DB->Fetch()) {
					$arUser["GROUPS"][] = $arGroup["GROUP_ID"];
				}
				return $arUser;
			} else {
				return $arUser;
			}
		}
		return false;
	}

	/**
	 * Общее количество пользователей в системе
	 * @global object $DB
	 * @return int
	 */
	public function GetCount() {
		$DB = App::DB();
		$sql = "SELECT COUNT(*) FROM `b_user`";
		$DB->Query($sql);
		if ($arUser = $DB->Fetch()) {
			$this->cUsersCount;
			return array_shift($arUser);
		} else {
			return false;
		}
	}

	/**
	 * Получить список пользователей по фильтру
	 * @global object $DB
	 * @param array $arFilter
	 * @param array $arPaginator
	 * @return array
	 */
	public function GetList($arFilter = Array(), $arPaginator = Array()) {
		$DB = App::DB();

		// Постраничка
		if (!empty($arPaginator)) {
			$page = !empty($arPaginator['PAGE']) ? (int) $arPaginator['PAGE'] : 1;
			$per_page = $arPaginator['PAGE_COUNT'];
			$total_count = $this->GetCount();
			$pagination = new Paginator($page, $per_page, $total_count);
			$arUsers['PAGINATOR'] = $pagination->GetHtml();
		}

		// TODO: Добавить фильтр
		$sql = "SELECT * FROM `b_user`";
		
		if (!empty($arPaginator)) {
			$sql .= "LIMIT {$per_page} ";
			$sql .= "OFFSET {$pagination->Offset()}";
		}
		$DB->Query($sql);
		while ($arUser = $DB->Fetch()) {
			$arUsers['USERS'][] = $arUser;
		}

		if (empty($arUsers))
			return false;

		foreach ($arUsers as $k => $user) {
			$sqlg = "SELECT * FROM `b_user_group` WHERE `USER_ID` = '".$user["ID"]."';";
			$DB->Query($sqlg);
			while ($arGroup = $DB->Fetch()) {
				$arUsers['USERS'][$k]["GROUPS"][] = $arGroup["GROUP_ID"];
			}
		}
		return $arUsers;
	}

	/**
	 * Получить массив данных текущего пользователя
	 * @return array
	 */
	public function GetUser() {
		$arUser = $this->arUser;
		$arUser["GROUPS"] = $this->arGroups;
		return $arUser;
	}

	/**
	 * Получить массив групп текущего пользователя
	 * @param int $USER_ID
	 * @return array
	 */
	public function GetUserGroups($USER_ID = false) {
		if (!$USER_ID) {
			$USER_ID = $this->USER_ID;
			return $this->arGroups;
		} else {
			return $this->FillGroups($USER_ID);
		}
	}

	/**
	 * Принадлежит ли пользователь $USER_ID группе $GROUP_ID
	 * @global object $DB
	 * @param int $USER_ID
	 * @param int $GROUP_ID
	 * @return bool
	 */
	public function InGroup($USER_ID, $GROUP_ID) {
		$DB = App::DB();
		$sql = "SELECT * FROM `b_user_group` WHERE `USER_ID` = '".intVal($USER_ID)."' AND `GROUP_ID` = '".intVal($GROUP_ID)."';";
		$DB->Query($sql);
		if ($arGroup = $DB->Fetch()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Добавить пользователя в группу
	 * @global object $DB
	 * @param int $USER_ID
	 * @param int $GROUP_ID
	 * @param [date $FROM]
	 * @param [date $TO]
	 * @return bool
	 */
	public function AddGroup($USER_ID, $GROUP_ID, $FROM = false, $TO = false) {
		$DB = App::DB();

		if($this->InGroup($USER_ID, $GROUP_ID))
			return false;

		$arGroup = Array(
			"USER_ID" => intVal($USER_ID),
			"GROUP_ID" => intVal($GROUP_ID),
		);

		if ($FROM)
			$arGroup["DATE_ACTIVE_FROM"] = $FROM;

		if ($TO)
			$arGroup["DATE_ACTIVE_TO"] = $TO;

		$sql = addSql('b_user_group', $arGroup);
		if ($DB->Query($sql)) {
			return true;
		} else {
			return false;
		}
	}

}

/**
 * Группы пользователей
 */
class UserGroups {
	/**
	 * Получить полный список всех групп пользователей системы
	 * @global object $DB
	 * @return array
	 */
	public static function GetGroupList() {
		$DB = App::DB();
		$sql = "SELECT * FROM `b_group`";
		$DB->Query($sql);
		while ($arGroup = $DB->Fetch()) {
			$arGroups[] = $arGroup;
		}
		if (!empty($arGroups))
			return $arGroups;
		else {
			return false;
		}
	}

	public static function Add($groupName, $groupDescription = "") {
		//TODO: реализовать UserGroups::Add()
	}

	public static function Edit($groupID, $groupName, $groupDescription = "") {
		//TODO: реализовать UserGroups::Edit()
	}

	public static function Delete($groupID) {
		//TODO: реализовать UserGroups::Delete()
	}
}

?>