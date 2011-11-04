<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class SQueryMySQL extends AbstractSQuery
{
	const DB_TYPE = "MYSQL";
	public function  __construct($strSql = "") {
		$this->AbstractConstructor(__CLASS__);
		parent::__construct($strSql);
	}
	
	// Прверял что для проверки вызывается именно переопределенная ф-ия
	/*static protected function checkArgValue_int4(&$arPACEHOLDER, &$value) {
		d("Проверка на int4 класса ".get_class(&$this));
		exit;
	}*/
}

class DatabaseMySQL extends AbstractDatabase
{
	const DB_TYPE = "MYSQL";
	protected $host;
	protected $user;
	protected $password;
	protected $database;
	protected $persistent = false;	// Параметры соединения
	protected $conn = NULL;			// Дескриптор соединения с базой данных
	protected $result = false;		// Результат запроса
	public 	$lastQuery;
	public  $lastID;
	public  $sqlCount = 0;
	public	$sqlLog = Array();
    // TODO: сделать трекинг времени запросов

	public function  __construct($DBCONN_NAME = DBCONN_NAME) {
		require_once $this->AbstractConstructor(__CLASS__, $DBCONN_NAME);
		$this->host = $DB_HOST;
		$this->user = $DB_USER;
		$this->password = $DB_PASS;
		$this->database = $DB_NAME;
		$this->persistent = ($DB_PERSISTENT)?true:false;
		$this->open();
	}
	
	protected function _get_LAST_ID() {
		return $this->getLastID();
	}

	protected function open()	{
		// Выбрать соответствующую функцию соединения
		if ($this->persistent) {
			$func = 'mysql_pconnect';
		} else {
			$func = 'mysql_connect';
		}

		// Соединиться с сервером MySQL
		$this->conn = $func($this->host, $this->user, $this->password);
		if (!$this->conn) {
			die("Ошибка при соединенияя с БД: " . mysql_error());
		}

		//echo $this->database;
		// Выбрать запрошенную базу данных
		if (@!mysql_select_db($this->database, $this->conn)) {
			die("Не могу найти нужную БД: " . mysql_error());
		}
		@mysql_query("SET NAMES 'utf8'");
		return true;
	}


	public function close() {
		if(isset($this->conn)) {
			mysql_close($this->conn);
			unset($this->conn);
		}
	}

	// TODO: переписать с учетом использования класса Logger.
	public function error() {
		return (mysql_error());
	}

	public function query($sql, $obj = NULL) {
		$this->lastQuery = $sql;
		//d($sql);
		$this->sqlCount++;
		$this->sqlLog[] = $sql;
		$this->result = @mysql_query($sql, $this->conn);
		if($obj) {
			//throw new AppException("!");
			return new $obj($this->result);
		}
		$this->confirmQuery($this->result);
		return($this->result != false);
	}

	public function affectedRows() {
		return(@mysql_affected_rows($this->conn));
	}

	public function numRows() {
		return(@mysql_num_rows($this->result));
	}

	public function fetchObject() {
		return(@mysql_fetch_object($this->result, MYSQL_ASSOC));
	}

	public function fetchArray() {
		return(@mysql_fetch_array($this->result, MYSQL_NUM));
	}

	public function fetchAssoc() {
		return (@mysql_fetch_assoc($this->result));
	}

	public function Fetch() {
		return(@mysql_fetch_array($this->result, MYSQL_ASSOC));
	}

	public function freeResult() {
		return(@mysql_free_result($this->result));
	}
	
	public function getLastID() {
      	return mysql_insert_id($this->conn);
  	}
	
	public function confirmQuery($result) {
		if (!$result) {
	   		$output = "Ошибка в запросе: " . mysql_error() . "<br /><br />";
	    	$output .= "Последний SQL запрос: " . $this->lastQuery;
	    	die( $output );
		}
	}
	
	public function commit() {
		
	}
}

class DBResultMySQL extends AbstractDBResult
{
	const DB_TYPE = "MYSQL";
	public static $result; //результат (первоначальный дескриптор)
	public static $arResult; //результат в виде массива после NavStart
	public static $elementID;
	
	public function  __construct($res = NULL) {
		$this->AbstractConstructor(__CLASS__);
		if(is_array($res))
			$this->arResult = $res;
		else
			$this->result = $res;
	}

	public function Fetch() {
		return (@mysql_fetch_assoc($this->result));
	}

	public function GetNext() {
		if($arRes = $this->Fetch())	{
			return TildaArray($arRes);
		}
		return $arRes;
	}

	public function GetNextElement() {
		return mysql_fetch_object($this->result);
	}

	public function Dump() {
		var_dump(mysql_fetch_object($this->result));
	}
}
?>