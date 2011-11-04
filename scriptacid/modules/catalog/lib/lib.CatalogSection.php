<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class CatalogSection extends CatalogSectionResult {
	private static $table = "b_catalog_section";
	private static $table_section_element = "b_catalog_section_element";
	public static $sectionID;
	public static $lastID;

	public static function GetByID($ID) {
		if (intVal($ID) <= 0)
			return false;

		self::$sectionID = intVal($ID);
		parent::$result = self::GetList(Array(), Array("ID" => intVal($ID)));
		return parent::$result;
	}

	public static function GetByCODE($CODE) {
		global $DB;
		$sql = "SELECT * FROM `". self::$table ."` WHERE `CODE` = '". sSql($CODE) ."';";
		$DB->Query($sql);
		if ($ar_res = $DB->fetchAssoc()) {
			return $ar_res['ID'];
		}
	}

	/**
	 * Список элементов
	 * @param array $arOrder
	 * @param array $arFilter
	 * @param array $arSelect
	 * @param array $arLimit
	 * @return DBResult
	 */
	public static function GetList($arOrder = Array(), $arFilter = Array(), $arSelect = Array(),  $arLimit = Array()) {
		global $DB;

		if($arFilter["CATALOG_SECTION_ID"] === 0) {
			$arFilter["CATALOG_SECTION_ID"] = 'NULL';
		}

		$sql = getListSql(self::$table, $arOrder, $arFilter, $arSelect, $arLimit);
		d($sql);
		$res = $DB->Query($sql, "\ScriptAcid\CatalogSectionResult");
		if($DB->Error()) {
			dbError($DB->Error());
			return false;
		}
		parent::$result = $res;
		return $res;
	}

	/**
	 * Добавление элемента
	 * @param array $arFields
	 * @return int $lastID
	 */
	public static function Add($arFields) {
		global $DB, $USER;

		unset($arFields["ID"]);
		
		Event::Run('catalog', 'OnBeforeCatalogSectionAdd', $arFields);

		$arProps = $arFields["PROPERTIES"];
		unset($arFields["PROPERTIES"]);

		$arFields['DATE_CREATE'] = date("Y-m-d H:i:s");
		$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
		$arFields['CREATED_BY'] = empty($arFields['CREATED_BY']) ? $USER->GetID() : $arFields['CREATED_BY'];

		$sql = addSql(self::$table, $arFields);

		if($DB->Query($sql)) {
			self::$lastID = $DB->LastID();
			/*if(!empty($arProps)) {
				$arPropFields = Array();
				foreach($arProps as $propCODE => $propValue) {
					if(is_numeric($propCODE)) {
						$propID = intVal($propCODE);
					} else {
						$propID = self::GetPropIDByCODE($propCODE);
					}
					$arPropFields = Array(
						"CATALOG_PROPERTY_ID" => intVal($propID),
						"CATALOG_ELEMENT_ID" => self::$lastID,
						"VALUE" => $propValue,
						//"VALUE_TYPE",
						//"VALUE_ENUM",
						//"DESCRIPTION" => "",
					);
					$sqlProps = addSql(self::$table_props_values, $arPropFields);
					$DB->Query($sqlProps);
				}
			}*/
			Event::Run('catalog', 'OnAfterCatalogSectionAdd', $arFields);
			return self::$lastID;
		} else {
			dbError($DB->Error());
			return false;
		}
	}

	// Обновление элемента
	public static function Update($ID, $arFields) {
		global $DB;

		Event::Run('catalog', 'OnBeforeCatalogSectionUpdate', $arFields);

		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$arProperties = $arFields["PROPERTIES"];
			unset($arFields["PROPERTIES"]);

			$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
			$arFields['MODIFIED_BY'] = empty($arFields['MODIFIED_BY']) ? $USER->GetID() : $arFields['MODIFIED_BY'];

			$sql = updSql(self::$table, $arFields)." WHERE `ID` = '".intVal($ID)."';";
			/*if(!empty($arProperties)) {
				foreach ($arProperties as $propCODE => $propVal) {
					$arUpd = Array("VALUE" => $propVal);
					if(is_numeric($propCODE)) {
						$propID = intVal($propCODE);
					} else {
						$propID = self::GetPropIDByCODE($propCODE);
					}
					$sql_props = updSql(self::$table_props_values, $arUpd)." WHERE `CATALOG_ELEMENT_ID` = '".intVal($ID)."' AND `CATALOG_PROPERTY_ID` = ".intVal($propID).";";
					$DB->Query($sql_props);
				}
			}*/

			if($DB->Query($sql)) {
				Event::Run('catalog', 'OnAfterCatalogSectionUpdate', $arFields);
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
		Event::Run('catalog', 'OnBeforeCatalogSectionDelete', $ID);
		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {

			$sql = "DELETE FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."';";
			//$sql_props = "DELETE FROM `".self::$table_props_values."` WHERE `CATALOG_ELEMENT_ID` = '".intVal($ID)."';";
			// AND $DB->Query($sql_props)
			if($DB->Query($sql)) {
				Event::Run('catalog', 'OnAfterCatalogSectionDelete', $ID);
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	/**
	 * Обновление осн. св-ва элемента каталога
	 * @param int $ID
	 * @param string $propertyCODE
	 * @param string $propertyValue
	 */
	public function setFieldValue($ID, $propertyCODE, $propertyValue) {
		global $DB;
		$sql = updSql(self::$table, Array($propertyCODE => $propertyValue))." WHERE `ID` = '".intVal($ID)."';";
		if($DB->Query($sql)) {
			return true;
		} else {
			dbError($DB->Error());
			return false;
		}
	}

	/**
	 * Получение осн. св-ва элемента каталога
	 * @param int $ID
	 * @param string $propertyCODE
	 */
	public function getFieldValue($ID, $propertyCODE) {
		global $DB;
		$DB->Query("SELECT `".$propertyCODE."` FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if($ar_res = $DB->fetchAssoc()) {
			return $ar_res[$propertyCODE];
		} else {
			return false;
		}
	}
/*
	// Получение доп. св-ва элемента элемента
	public function getPropertyValue($elementID, $propertyCODE) {
		global $DB;
		if(is_numeric($propertyCODE)) {
			$propID = intVal($propertyCODE);
		} else {
			$propID = self::GetPropIDByCODE($propertyCODE);
		}
		$sql = "SELECT * FROM `".self::$table_props_values."` WHERE `CATALOG_ELEMENT_ID` = '".intVal($elementID)."' AND `CATALOG_PROPERTY_ID` = '".intVal($propID)."';";
		$DB->Query($sql);
		if($ar_res = $DB->Fetch()) {
			return $ar_res;
		} else {
			return false;
		}
	}

	// Обновление доп. св-ва элемента элемента
	public function setPropertyValue($elementID, $propertyCODE, $propertyValue) {
		global $DB;
		$arUpd = Array("VALUE" => $propertyValue);
		if(is_numeric($propertyCODE)) {
			$propID = intVal($propertyCODE);
		} else {
			$propID = self::GetPropIDByCODE($propertyCODE);
		}
		$sql_props = updSql(self::$table_props_values, $arUpd)." WHERE `CATALOG_ELEMENT_ID` = '".intVal($elementID)."' AND `CATALOG_PROPERTY_ID` = ".intVal($propID).";";
		if($DB->Query($sql_props)) {
			return true;
		} else {
			dbError($DB->Error());
			return false;
		}
	}
*/
	/**
	 * Получить ID св-ва по его CODE
	 * @param string $CODE
	 */
	public static function GetPropIDByCODE($CODE) {
		global $DB;
		$DB->Query("SELECT `ID` FROM `".self::$table."` WHERE `CODE` = '".sSql($CODE)."'");
		if($arResult = $DB->Fetch()) {
			return $arResult["ID"];
		}
	}
}

class CatalogSectionResult extends DBResult {
	public static $result;
	public static $arResult;
	public static $sectionID;

	function __construct($res=NULL) {
		if(is_array($res))
			$this->arResult = $res;
		else
			$this->result = $res;
	}

	function Fetch() {
		return (@mysql_fetch_assoc($this->result));
	}

	function GetNext() {
		if($arRes = $this->Fetch())	{
			return TildaArray($arRes);
		}
		return $arRes;
	}

	function GetNextElement() {
		if(!($fields = $this->GetNext()))
			return $fields;

		$res = new _CatalogSection;
		$res->fields = $fields;
		$res->sectionID = $fields["ID"];
		return $res;
	}
}

class _CatalogSection {
	public $fields;
	private static $table = "b_catalog_section";
	private static $table_section_element = "b_catalog_section_element";
	public  $sectionID;

	public function GetFields() {
		return $this->fields;
	}

	public function GetProperties($arOrder = false, $arFilter=Array()) {
		global $DB;

		if (!$arOrder) {
			$order = "ORDER BY PROPS.ID ASC";
		} else {
			$cnt = 0;
			foreach ($arOrder as $f => $t) {
				$cnt++;
				$order = " ORDER BY PROPS.".$f." ".$t;
				if ($cnt != count($arOrder)) {
					$order .= ",";
				}
			}
		}

		if(!empty($arFilter)) {
			if(!empty($arFilter["CODE"])) {
				$where = "AND PROPS.CODE = '".$arFilter["CODE"]."' ";
			}
		} else {
			$where = "";
		}

		$sql = "SELECT * FROM ".
			"`".$this->table_props."` AS PROPS, ".
			"`".$this->table_props_values."` AS PROP_VALUES ".
			"WHERE ".
			"PROP_VALUES.CATALOG_PROPERTY_ID = PROPS.ID AND ".
			"PROP_VALUES.CATALOG_ELEMENT_ID = '".intVal($this->elementID)."' ".
			$where.
			$order.";";

		$DB->Query($sql);
		while($ar_res = $DB->fetchAssoc()) {
			$arPreResult[$ar_res["CODE"]] = TildaArray($ar_res);
		}

		if(count($arPreResult) == 1) {
			$arResult = current($arPreResult);
		} else {
			$arResult = $arPreResult;
		}
		return $arResult;
	}

	function GetProperty($ID) {
		$res = $this->GetProperties(Array(), Array("CODE"=>$ID));
		return $res;
	}
}
?>