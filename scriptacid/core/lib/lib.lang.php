<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

$LANG = Array();
/**
 * Работа с языками сайта и их настройками
 */
class Lang {
	private static $table = "b_language";
	
	public static function GetByID($lang) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `SID` = '".sSql($lang)."';");
		while($ar_res = $DB->fetchAssoc()) {
			$arResult = $ar_res;
		}
		if($DB->Error())
			dbError($DB->Error());
		return empty($arResult) ? false : $arResult;
	}

	public static function GetList($arOrder = false, $arFilter = false, $arSelect = false,  $arLimit = false) {
		global $DB;
		$sql = getListSql(self::$table, $arOrder, $arFilter, $arSelect, $arLimit);
		$DB->Query($sql);
		while($ar_res = $DB->fetchAssoc()) {
			$arResult[] = $ar_res;
		}
		if($DB->Error())
			dbError($DB->Error());
		return empty($arResult) ? false : $arResult;
	}

	public static function Add($arFields) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `SID` = '".sSql($arFields["SID"])."'");
		if($DB->numRows()) {
			return false;
		} else {
			$sql = addSql(self::$table, $arFields);
			if($DB->Query($sql)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	public static function Update($lang, $arFields) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `SID` = '".sSql($lang)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$sql = updSql(self::$table, $arFields)." WHERE `SID` = '".sSql($lang)."';";
			if($DB->Query($sql)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	public static function Delete($lang) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `SID` = '".sSql($lang)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$sql = "DELETE FROM `".self::$table."` WHERE `SID` = '".sSql($lang)."';";
			if($DB->Query($sql)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}
	
	
	/************************************************************************
	 * ЯЗЫКОВЫЙ ФУНКЦИИ:
	 */
	
	/**
	 * Подключение языкового файла
	 * @global <type> $LANG
	 * @param <type> $file
	 */
	public static function getLangFile($file) {
		global $LANG;
		if(file_exists($file)) {
			require_once $file;
		}
	}
	
	/**
	 * Подключение языковых файлов
	 * @global <type> $LANG
	 * @param <type> $file
	 */
	public static function getLangFiles($path) {
		global $LANG;
		if(is_dir($path)) {
			if($dir = opendir($path)) {
				while(false !==($file = readdir($dir))) {
					if(is_file($path."/".$file) AND substr($file, strlen($file) - 4, 4) == ".php") {
						require_once $path."/".$file;
					}
				}
				closedir($dir);
			}
		}
	}
	
	public static function getLangFilesLocale($langFilesPath) {
		global $CTLANG;
		if(is_dir($langFilesPath)) {
			if($dir = opendir($langFilesPath)) {
				while(false !==($file = readdir($dir))) {
					if(is_file($langFilesPath."/".$file) AND substr($file, strlen($file) - 4, 4) == ".php") {
						require $langFilesPath."/".$file;
						$CTLANG = $LANG;
					}
				}
				closedir($dir);
			}
		}
	}
	
	/**
	 * Перевод текста на заданный язык
	 * @global string $LANG Массив с переводом
	 * @param string $msgCode Ключ массива с переводом
	 * @return string Фраза перевода
	 */
	static public function tr($msgCode) {
		global $LANG, $CTLANG;
		if(isset($CTLANG[$msgCode])) {
				return $CTLANG[$msgCode];
		} else {
			if(isset($LANG[$msgCode])) 
				return $LANG[$msgCode];
			else
				return '<b style="color: red">НЕТ ПЕРЕВОДА (NO TRANSLATE) - "'.$msgCode.'"!</b>';
		}
	}
	public static function getCodeTranslate($msgCode) {
		return self::tr($msgCode);
	}
}

function LANG($msgCode) {
	return Lang::tr($msgCode);
}

?>