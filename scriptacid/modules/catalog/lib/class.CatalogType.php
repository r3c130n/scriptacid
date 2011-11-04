<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class CatalogType {

	private static $table = "b_catalog_type";
	private static $table_lang = "b_catalog_type_lang";
	public static $lastID;

	// Тип каталога по его ID
	public static function GetByID($ID) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` as t1, `".self::$table_lang."` as t2 WHERE t1.ID = '".sSql($ID)."' AND t2.CATALOG_TYPE_ID = '".sSql($ID)."';");
		while($ar_res = $DB->fetchAssoc()) {
			unset($ar_res["CATALOG_TYPE_ID"]);
			$arResult = $ar_res;
		}
		if($DB->Error())
			dbError($DB->Error());
		return empty($arResult) ? false : $arResult;
	}

	// Список элементов
	public static function GetList() {
		global $DB;
		$sql = getListSql(Array("t1" => self::$table, "t2" => self::$table_lang), false, Array("t1.ID" => "t2.CATALOG_TYPE_ID"), "**");
		$DB->Query($sql);
		while($ar_res = $DB->fetchAssoc()) {
			$arResult[] = $ar_res;
		}
		if($DB->Error())
			dbError($DB->Error());
		return empty($arResult) ? false : $arResult;
	}

	// Добавление элемента
	public static function Add($arFields) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".sSql($arFields["ID"])."'");
		if($DB->numRows()) {
			return false;
		} else {
			$arFieldsTable_1 = Array();
			$arFieldsTable_2 = Array();
			$tbl_1 = Array(
				"ID",
				"SORT",
			);
			$tbl_2 = Array(
				"CATALOG_TYPE_ID",
				"SID",
				"NAME",
				"SECTION_NAME",
				"ELEMENT_NAME",
			);
			$arFieldsTable_2["CATALOG_TYPE_ID"] = $arFields["ID"];
			foreach($arFields as $key => $value) {
				if(in_array($key, $tbl_1)) {
					$arFieldsTable_1[$key] = $value;
				}
				if(in_array($key, $tbl_2)) {
					$arFieldsTable_2[$key] = $value;
				}
			}

			$sql1 = addSql(self::$table, $arFieldsTable_1);
			$sql2 = addSql(self::$table_lang, $arFieldsTable_2);
		
			if($DB->Query($sql1) AND $DB->Query($sql2)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	// Обновление элемента
	public static function Update($ID, $arFields) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".sSql($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$arFieldsTable_1 = Array();
			$arFieldsTable_2 = Array();
			$tbl_1 = Array(
				"ID",
				"SORT",
			);
			$tbl_2 = Array(
				"CATALOG_TYPE_ID",
				"SID",
				"NAME",
				"SECTION_NAME",
				"ELEMENT_NAME"
			);
			$arFieldsTable_2["CATALOG_TYPE_ID"] = $arFields["ID"];
			foreach($arFields as $key => $value) {
				if(in_array($key, $tbl_1)) {
					$arFieldsTable_1[$key] = $value;
				}
				if(in_array($key, $tbl_2)) {
					$arFieldsTable_2[$key] = $value;
				}
			}
			$sql1 = updSql(self::$table, $arFieldsTable_1)." WHERE `ID` = '".sSql($ID)."';";
			$sql2 = updSql(self::$table_lang, $arFieldsTable_2)." WHERE `CATALOG_TYPE_ID` = '".sSql($ID)."';";

			if($DB->Query($sql1) AND $DB->Query($sql2)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	// Удаление элемента
	public static function Delete($ID) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".sSql($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$sql1 = "DELETE FROM `".self::$table."` WHERE `ID` = '".sSql($ID)."';";
			$sql2 = "DELETE FROM `".self::$table_lang."` WHERE `CATALOG_TYPE_ID` = '".sSql($ID)."';";
			if($DB->Query($sql1) AND $DB->Query($sql2)) {
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}
}
?>