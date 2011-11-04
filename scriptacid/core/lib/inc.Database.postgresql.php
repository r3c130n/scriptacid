<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	//echo "POSTGRESQL";
	try {
		if(!defined("MAIN_DB_INCLUDED") || !MAIN_DB_INCLUDED) {
			class Database extends DatabasePostgreSQL {} 
			class DBResult extends DBResultPostgreSQL {}
			class SQuery extends SQueryPostgreSQL {}
			define("MAIN_DB_INCLUDED", true);
		}
		else {
			throw new Exception("Подержка основного типа базы даных уже включена. Основной тип БД: ".Database::DB_TYPE);
		}
	}
	catch(Exception $except) {
		showExcaptionText($except);
	}
?>