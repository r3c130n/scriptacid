<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class Catalog {
	//TODO: Сделать работу со св-ми ИБ (ДОБАВЛЕНИЕ и тд)
	private static $table = "b_catalog";
	private static $table_props = "b_catalog_property";
	private static $table_props_enum = "b_catalog_property_enum";
	public static $lastID;

	
	// Элемент по его ID
	public static function GetByID($ID) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."';");
		while($ar_res = $DB->Fetch()) {
			$arResult = $ar_res;
		}
		if($DB->Error())
			dbError($DB->Error());

		if(!empty($arResult)) {
			$DB->Query("SELECT * FROM `".self::$table_props."` WHERE `CATALOG_ID` = '".intVal($ID)."';");
			while($ar_res = $DB->Fetch()) {
				$arResult["PROPERTIES"][$ar_res["ID"]] = $ar_res;
			}
			if($DB->Error())
				dbError($DB->Error());
		}
		return empty($arResult) ? false : $arResult;
	}

	// Список элементов
	public static function GetList($arOrder = Array(), $arFilter = Array(), $arSelect = Array(),  $arLimit = Array()) {
		global $DB;
		$sql = getListSql(self::$table, $arOrder, $arFilter, $arSelect, $arLimit);
		$DB->Query($sql);
		while($ar_res = $DB->fetchAssoc()) {
			$arResult[] = $ar_res;
			if($DB->Error()) {
				dbError($DB->Error());
			}
		}
		if (!empty($arResult)) {
			foreach ($arResult as $ID => $arItem) {
				$DB->Query("SELECT * FROM `".self::$table_props."` WHERE `CATALOG_ID` = '".intval($arItem["ID"])."';");
				while($ar_prop = $DB->Fetch()) {
					$arResult[$ID]["PROPERTIES"][$ar_prop["ID"]] = $ar_prop;
				}
			}
		}
		
		if($DB->Error())
			dbError($DB->Error());
		
		return empty($arResult) ? false : $arResult;
	}
	
	// Добавление элемента
	public static function Add($arFields) {
		global $DB;
		unset($arFields["ID"]);

		Event::Run('catalog', 'OnBeforeCatalogAdd', $arFields);

		$arProps = $arFields["PROPERTIES"];
		unset($arFields["PROPERTIES"]);
		$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
		$sql = addSql(self::$table, $arFields);
		if($DB->Query($sql)) {
			self::$lastID = $DB->LastID();
			// Свойства каталога
			if (!empty($arProps)) {
				foreach ($arProps as $arProp) {
					if (!empty($arProp["NAME"])) {
						$arValues = Array();
						$arValues = $arProp["VALUES"];
						unset($arProp["ID"]);
						unset($arProp["VALUES"]);
						$arProp["CATALOG_ID"] = self::$lastID;
						$sql = addSql(self::$table_props, $arProp);
						$DB->Query($sql);
						$propId = $DB->LastID();
						if (!empty($arValues)) {
							foreach ($arValues as $arValue) {
								self::createPropertyEnum($propId, $arValue);
							}
						}
					}
				}
			}
			Event::Run('catalog', 'OnAfterCatalogAdd', $arFields);
			return self::$lastID;
		} else {
			dbError($DB->Error());
			return false;
		}
	}

	// Обновление элемента
	public static function Update($ID, $arFields) {
		global $DB;
		Event::Run('catalog', 'OnBeforeCatalogUpdate', $arFields);
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$arProps = $arFields["PROPERTIES"];
			unset($arFields["PROPERTIES"]);
			$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
			$sql = updSql(self::$table, $arFields)." WHERE `ID` = '".intVal($ID)."';";
			if($DB->Query($sql)) {
				// Свойства каталога
				if (!empty($arProps)) {
					foreach ($arProps as $arProp) {
						if (!empty($arProp["NAME"])) {
							if(!empty($arProp["ID"])) {
								if (!empty($arProp["VALUES"]) AND !empty($arProp["ID"])) {
									foreach ($arPropValues as $arValue) {
										self::updatePropertyEnum($arProp["ID"], $arValue);
									}
								}
								unset($arProp["VALUES"]);
								$propID = $arProp["ID"];
								unset($arProp["ID"]);
								$sql = updSql(self::$table_props, $arProp)." WHERE `ID` = '".intVal($propID)."';";
								$DB->Query($sql);
							} else {
								$arValues = Array();
								$arValues = $arProp["VALUES"];
								unset($arProp["ID"]);
								unset($arProp["VALUES"]);
								$arProp["CATALOG_ID"] = intVal($ID);
								$sql = addSql(self::$table_props, $arProp);
								$DB->Query($sql);
								$propId = $DB->LastID();
								if (!empty($arValues)) {
									foreach ($arValues as $arValue) {
										self::createPropertyEnum($propId, $arValue);
									}
								}
							}
						}
					}
				}
				Event::Run('catalog', 'OnAfterCatalogUpdate', $arFields);
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
		Event::Run('catalog', 'OnBeforeCatalogDelete', $ID);
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$sql = "DELETE FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."';";
			if($DB->Query($sql)) {
				Event::Run('catalog', 'OnAfterCatalogDelete', $ID);
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}
	
	// Обновление осн. св-ва каталога
	public static function setFieldValue($ID, $propertyCODE, $propertyValue) {
		global $DB;
		$sql = updSql(self::$table, Array($propertyCODE => $propertyValue))." WHERE `ID` = '".intVal($ID)."';";
		if($DB->Query($sql)) {
			return true;
		} else {
			dbError($DB->Error());
			return false;
		}
	}

	// Получение осн. св-ва каталога
	public static function getFieldValue($ID, $propertyCODE) {
		global $DB;
		$DB->Query("SELECT `".$propertyCODE."` FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if($ar_res = $DB->fetchAssoc()) {
			return $ar_res[$propertyCODE];
		} else {
			return false;
		}
	}

	private static function createPropertyEnum($propertyID, $arValue) {
		$sql =	"INSERT INTO " . self::$table_props_enum.
				" (`TIMESTAMP_X`, `PROPERTY_ID`, `VALUE`, `SORT`, `XML_ID`, `DEF`) ".
				"VALUES ('".
				date("Y-m-d H:i:s").", ".
				$propertyID.", ".
				sXss(sSql($arValue["VALUE"]))."', ".
				(!empty($arValue["SORT"])?intVal($arValue["SORT"]):'500')."', ".
				(!empty($arValue["XML_ID"])?sXss(sSql($arValue["XML_ID"])):'')."', ".
				($arValue["DEF"]=="N"?'N':'Y')."';".
		$DB->Query($sql);
		return $DB->LastID();
	}

	private static function updatePropertyEnum($ID, $arValue) {
		global $DB;
		$sql =	"UPDATE " . self::$table_props_enum." SET ".
				"`TIMESTAMP_X` = '".date("Y-m-d H:i:s")."', ".
				"`VALUE` = '".sXss(sSql($arValue["VALUE"]))."', ".
				"`SORT` = '".(!empty($arValue["SORT"])?intVal($arValue["SORT"]):'500')."', ".
				"`XML_ID` = '".(!empty($arValue["XML_ID"])?sXss(sSql($arValue["XML_ID"])):'')."', ".
				"`DEF` = '".($arValue["DEF"]=="N"?'N':'Y')."' ".
				"WHERE `PROPERTY_ID` = '".intVal($ID)."';";
		$DB->Query($sql);
		return true;
	}

	public static function getPropertyByID($ID) {
		global $DB;
		$DB->Query("SELECT * FROM `".self::$table_props."` WHERE `ID` = '".intVal($ID)."'");
		if($ar_res = $DB->fetchAssoc()) {
			return $ar_res;
		} else {
			return false;
		}
	}

	public static function GeneratePropsArray($arProps = Array(), $count = 10) {
		$arResult = Array();
		if (empty($arProps)) {
			for ($n=0; $n <= $count; $n++) {
				$arResult[] = Array(
					"ID" => "",
					"NAME" => "",
					"PROPERTY_TYPE" => "TEXT",
					"SORT" => "500",
					"CODE" => "",
					"IS_REQUIRED" => "",
					"MULTIPLE" => "",
					"DEFAULT_VALUE" => "",
				);
			}
		} else {
			foreach ($arProps as $k => $prop) {
				$arResult[] = $prop;
			}
			if (count($arResult) < $count) {
				$arEmpty = self::GeneratePropsArray(Array(), $count - count($arResult));
				$arResult = array_merge($arResult, $arEmpty);
			}
		}
		return $arResult;
	}

	public static function GetCatalogProperties($ID) {
		global $DB;
		$arProps = Array();
		$DB->Query("SELECT * FROM `".self::$table_props."` WHERE `CATALOG_ID` = '".intVal($ID)."' ORDER BY `SORT`");
		while ($ar_res = $DB->fetchAssoc()) {
			$arProps[] = $ar_res;
		}
		return $arProps;
	}

	public static function GetPropTypes() {
		$arTypes = Array(
			"S" => "Текст",
			"L" => "Список",
			"H" => "Текст/HTML",
			"N" => "Число",
			"EL" => "CATALOG_ELEMENT",
			"SL" => "CATALOG_SECTION",
			"F" => "FILE",
			"C" => "Чекбокс"
		);
		return $arTypes;
	}
}

?>