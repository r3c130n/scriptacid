<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class SQueryPostgreSQL extends AbstractSQuery
{
	const DB_TYPE = "POSTGRESQL";
	public function  __construct($strSql) {
		exit("Database type ".self::DB_TYPE." does not support yet.");
		$this->AbstractConstructor(__CLASS__);
		parent::__construct($strSql);
	}
}

class DatabasePostgreSQL extends AbstractDatabase
{
	const DB_TYPE = "POSTGRESQL";
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
	
	public function  __construct($DBCONN_NAME = DBCONN_NAME) {
		exit("Database type ".self::DB_TYPE." does not support yet.");
		require_once $this->AbstractConstructor(__CLASS__, $DBCONN_NAME);
	}
	
	// Открыть соединение с БД
	protected function open() {
		
	}
	// Закрыть соединение с БД
	public function close() {
		
	}
	// выполнить запрос(инструкции) SQL
	public function query() {
		
	}
	// начать транзакцию
	public function begin() {
		
	}
	// Завершить странзакцию
	public function commit() {
		
	}
	// Откатить транзацкию
	public function rollback() {
		
	}
}

class DBResultPostgreSQL extends AbstractDBResult
{
	const DB_TYPE = "POSTGRESQL";
	public static $result; //результат (первоначальный дескриптор)
	public static $arResult; //результат в виде массива после NavStart
	public static $elementID;
	
	public function  __construct() {
		exit("Database type ".self::DB_TYPE." does not support yet.");
		$this->AbstractConstructor(__CLASS__);
	}

	public function Fetch() {
		
	}

	public function GetNext() {
		if($arRes = $this->Fetch())	{
			return TildaArray($arRes);
		}
		return $arRes;
	}

	public function GetNextElement() {
		
	}

	public function Dump() {
		
	}
}
?>