<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	//echo "MYSQL";
	try {
		if(!defined("MAIN_DB_INCLUDED") || !MAIN_DB_INCLUDED) {
			class Database extends DatabaseMySQL {}
			class DBResult extends DBResultMySQL {}
			class SQuery extends SQueryMySQL {}
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