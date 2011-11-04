<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";

App::get()->makePage(function(&$arPageParams) {?>
SetTitle('Проект "ScriptACID CMF": Logger class test.');?>
<?php

class ___LoggerSessionWorkTest
{
	static protected $logger;
	static public function init() {
		self::$logger = new Logger("THIS_LOGGER_OBJ_SESSION_KEY");
	} 
	static public function Simple() {
		$logger = new Logger;
		$logger->ERROR = "ERROR #1";
		$logger->ERROR = "ERROR #2";
		$logger->ERROR = "ERROR #3";
		$logger->ERROR = "ERROR #4";
		d($logger->ERRORS);
		d($logger->LAST_ERROR); 
		d($logger->ERROR);
		$logger->ERRORS = null; // $logger->ERRORS = array(); $logger->ERRORS = 0; $logger->ERRORS = false; $logger->clearErrors();
		d($logger->ERRORS);
		d($logger->LAST_ERROR);  
		
		$logger->MESSAGE = "MESSAGE #1";
		$logger->MESSAGE = "MESSAGE #2";
		$logger->MESSAGE = "MESSAGE #3";
		$logger->MESSAGE = "MESSAGE #4";
		d($logger->MESSAGES);
		d($logger->LAST_MESSAGE); 
		d($logger->MESSAGE);
		$logger->MESSAGES = null; // $logger->MESSAGES = array(); $logger->MESSAGES = 0; $logger->MESSAGES = false; $logger->clearMessages();
		d($logger->MESSAGES);
		d($logger->LAST_MESSAGE); 
	}
	
	static public function Save() {
	
		self::$logger->ERROR = "ERROR #1";
		self::$logger->ERROR = "ERROR #2";
		self::$logger->ERROR = "ERROR #3";
		self::$logger->ERROR = "ERROR #4";
		self::$logger->MESSAGE = "MESSAGE #1";
		self::$logger->MESSAGE = "MESSAGE #2";
		self::$logger->MESSAGE = "MESSAGE #3";
		self::$logger->MESSAGE = "MESSAGE #4";
		
		self::$logger->saveSession();//""TEST");
		
		d(self::$logger->ERRORS);
		d(self::$logger->MESSAGES);
		d($_SESSION["SCD_LOGGER"]);
	}

	static public function Restore() {
		self::$logger->restoreSession();//""TEST");
		d(self::$logger->ERRORS);
		d(self::$logger->MESSAGES);
		d($_SESSION["SCD_LOGGER"]);
	}
	
	static public function Clear() {
		self::$logger->clearSession();//""TEST");
		self::$logger->restoreSession();//"TEST");
		d(self::$logger->ERRORS);
		d(self::$logger->MESSAGES);
		d($_SESSION["SCD_LOGGER"]);
	}
}

___LoggerSessionWorkTest::init();

//___LoggerSessionWorkTest::Simple();
//___LoggerSessionWorkTest::Save();
//___LoggerSessionWorkTest::Restore();
___LoggerSessionWorkTest::Clear();
___LoggerSessionWorkTest::Restore();
	
?>
<?php }); // end of makePage?>