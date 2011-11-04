<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

/**
 * Управление элементами каталога
 *
 * @author r3c130n
 * @version 20091017
 * @package ScriptACID CMF
 *
 * @example Методы:
 * @method GetByID - Получить элемент каталога по его ID
 * @method GetList - Получить элементы каталога
 * @method Add - Добавить элемент каталога
 * @method Update - Обновить элемент каталога
 * @method Delete - Удалить элемент каталога
 * @method setFieldValue - Обновление осн. св-ва элемента каталога
 * @method getFieldValue - Получение осн. св-ва элемента каталога
 * @method setPropertyValue - Обновление доп. св-ва элемента элемента
 * @method getPropertyValue - Получение доп. св-ва элемента элемента
 * @method GetPropIDByCODE - Получить ID св-ва по его CODE
 */

 //TODO: Работа с множественными свойствами, расширеная обработка фильтра
class CatalogElement extends CatalogElementResult {
	private static $table = "b_catalog_element";
	private static $table_props = "b_catalog_property";
	private static $table_props_enum = "b_catalog_property_enum";
	private static $table_props_values = "b_catalog_element_property";
	private static $table_catalog = "b_catalog";
	public static $elementID;
	public static $lastID;


	/**
	 * Получить элемент каталога по его ID
	 * @global resource $DB
	 * @param integer $ID
	 * @return object
	 */
	public static function GetByID($ID) {
		$res = self::GetList(Array(), Array("ID" => intVal($ID)));
		self::$elementID = intVal($ID);
		parent::$result = $res;
		return $res;
	}

	public static function GetCount($arFilter = Array()) {
		$DB = App::DB();
		$sql = 'SELECT count(*) FROM ' . self::$table;
		// Фильтр
		if(empty($arFilter)) {
			$where = "";
		} else {
			$where = " WHERE ";
			$where .= filterGlue(filterClear($arFilter));
		}
		$where = str_replace(' GROUP BY ID', '', $where);
		$res = $DB->Query($sql . $where);
		if($result = $DB->Fetch()) {
			return array_shift($result);
		}
		
		if($DB->Error()) {
			dbError($DB->Error());
			return false;
		}
	}

	// Список элементов
	public static function GetList($arOrder = Array(), $arFilter = Array(), $arSelect = Array(),  $arLimit = Array()) {
		$DB = App::DB();

		$arFilter = self::GetFilter($arFilter);

		if (empty($arOrder)) {
			$arOrder = Array("SORT" => "ASC");
		}
		$arOrder = self::GetSort($arOrder);

		$tables = Array("CATALOG" => self::$table_catalog, "ELEMENT" => self::$table);

		if (is_array($arLimit) AND !empty($arLimit)) {
			$page = !empty($arLimit['PAGE']) ? (int) $arLimit['PAGE'] : 1;
			$total_count = self::GetCount($arFilter);
			$arLimit['COUNT'] = $arLimit['COUNT'] > 0 ? $arLimit['COUNT'] : 3;

			$pagination = new Paginator($arLimit['PAGE'], $arLimit['COUNT'] , $total_count);
			self::$pagination = $pagination->GetHtml();
			$arLimit['OFFSET'] = $pagination->Offset();
		} else {
			$arLimit = Array();
			self::$pagination = '';
		}
		
		$sql = getListSql($tables, $arOrder, $arFilter, $arSelect, $arLimit);
		//d($sql);
		$res = $DB->Query($sql, "ScriptAcid\CatalogElementResult");
		if($DB->Error()) {
			dbError($DB->Error());
			return false;
		}
		parent::$result = $res;
		return $res;
	}

	// Добавление элемента
	public static function Add($arFields) {
		//d($arFields);
		global $DB, $USER;

		unset($arFields["ID"]);

		Event::Run('catalog', 'OnBeforeCatalogElementAdd', $arFields);

		if( !isset($arFields["CATALOG_ID"]) || intval($arFields["CATALOG_ID"]) == 0 ) {
			dbError("Не указан идентификатор каталога!");
			return false;
		}
		
		$arProps = $arFields["PROPERTIES"];
		unset($arFields["PROPERTIES"]);

		$arFields['DATE_CREATE'] = date("Y-m-d H:i:s");
		$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
		$arFields['CREATED_BY'] = empty($arFields['CREATED_BY']) ? $USER->GetID() : $arFields['CREATED_BY'];

		if (is_array($arFields['PREVIEW_PICTURE']) AND !empty($arFields['PREVIEW_PICTURE'])) {
			if ($arFields['PREVIEW_PICTURE']['tmp_name'] != '') {
				$fileID = File::SaveFile($arFields['PREVIEW_PICTURE'], Array('MODULE_ID' => 'catalog'));
				unset($arFields['PREVIEW_PICTURE']);
				$arFields['PREVIEW_PICTURE'] = $fileID;
			}
		}
		if (is_array($arFields['DETAIL_PICTURE']) AND !empty($arFields['DETAIL_PICTURE'])) {
			if ($arFields['DETAIL_PICTURE']['tmp_name'] != '') {
				$fileID = File::SaveFile($arFields['DETAIL_PICTURE'], Array('MODULE_ID' => 'catalog'));
				unset($arFields['DETAIL_PICTURE']);
				$arFields['DETAIL_PICTURE'] = $fileID;
			}
		}

		$sql = addSql(self::$table, $arFields);
		
		if($DB->Query($sql)) {
			self::$lastID = $DB->LastID();
			if(!empty($arProps)) {
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
					if (is_array($propValue) AND !empty($propValue)) {
						if ($propValue['tmp_name'] != '') {
							$fileID = File::SaveFile($propValue, Array('MODULE_ID' => 'catalog'));
							if (intVal($fileID) > 0) {
								$arPropFields['VALUE'] = $fileID;
								$arPropFields['VALUE_TYPE'] = 'file';
								$sqlProps = addSql(self::$table_props_values, $arPropFields);
								$DB->Query($sqlProps);
							}
						} else {
							foreach ($propValue as $propVal) {
								if (is_array($propVal)) {
									if ($propVal['tmp_name'] != '') {
										$fileID = File::SaveFile($propVal, Array('MODULE_ID' => 'catalog'));
										if (intVal($fileID) > 0) {
											$arPropFields['VALUE'] = $fileID;
											$arPropFields['VALUE_TYPE'] = 'file';
											$sqlProps = addSql(self::$table_props_values, $arPropFields);
											$DB->Query($sqlProps);
										}
									}
								} else {
									if ($propVal != '') {
										$arPropFields['VALUE'] = $propVal;
										$sqlProps = addSql(self::$table_props_values, $arPropFields);
										$DB->Query($sqlProps);
									}
								}
							}
						}
					} else {
						if (!empty($propValue)) {
							$sqlProps = addSql(self::$table_props_values, $arPropFields);
							$DB->Query($sqlProps);
						}
					}
				}
			}
			Event::Run('catalog', 'OnAfterCatalogElementAdd', $arFields);
			return self::$lastID;
		} else {
			dbError($DB->Error());
			return false;
		}
	}

	// Обновление элемента
	public static function Update($ID, $arFields) {
		global $DB, $USER;

		Event::Run('catalog', 'OnBeforeCatalogElementUpdate', $arFields);

		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			$arProperties = $arFields["PROPERTIES"];
			unset($arFields["PROPERTIES"]);

			$arFields['TIMESTAMP_X'] = date("Y-m-d H:i:s");
			$arFields['MODIFIED_BY'] = empty($arFields['MODIFIED_BY']) ? $USER->GetID() : $arFields['MODIFIED_BY'];

			$sql = updSql(self::$table, $arFields)." WHERE `ID` = '".intVal($ID)."';";
			if(!empty($arProperties)) {
				foreach ($arProperties as $propCODE => $propVal) {
					$arUpd = Array("VALUE" => $propVal);
					if(is_numeric($propCODE)) {
						$propID = intVal($propCODE);
					} else {
						$propID = self::GetPropIDByCODE($propCODE);
					}
					$sql_props = updSql(self::$table_props_values, $arUpd)." WHERE `CATALOG_ELEMENT_ID` = '".intVal($ID)."' AND `ID` = ".intVal($propID).";";
					$DB->Query($sql_props);
				}
			}
			
			if($DB->Query($sql)) {
				Event::Run('catalog', 'OnAfterCatalogElementUpdate', $arFields);
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

		Event::Run('catalog', 'OnBeforeCatalogElementDelete', $ID);

		$DB->Query("SELECT * FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if(!$DB->numRows()) {
			return false;
		} else {
			
			$sql = "DELETE FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."';";
			$sql_props = "DELETE FROM `".self::$table_props_values."` WHERE `CATALOG_ELEMENT_ID` = '".intVal($ID)."';";

			if($DB->Query($sql) AND $DB->Query($sql_props)) {
				Event::Run('catalog', 'OnAfterCatalogElementDelete', $ID);
				return true;
			} else {
				dbError($DB->Error());
				return false;
			}
		}
	}

	public static function GetFilter($arFilter) {

		if (!empty($arFilter['SECTION_CODE'])) {
			$arFilter['SECTION_ID'] = CatalogSection::GetByCODE($arFilter['SECTION_CODE']);
			unset($arFilter['SECTION_CODE']);
		}

		if(is_array($arFilter['CATALOG_ID']) AND !empty($arFilter['CATALOG_ID'])) {
			$arCatalogIDs = $arFilter['CATALOG_ID'];
			unset($arFilter['CATALOG_ID']);
		}

		$arElemFields = Array(
			"ID" => "ELEMENT.ID",
			"SORT" => "ELEMENT.SORT",
			"CATALOG_ID" => "ELEMENT.CATALOG_ID",
			"SECTION_ID" => "ELEMENT.CATALOG_SECTION_ID",
			"CODE" => "ELEMENT.CODE",
		);
		
		foreach ($arFilter as $key => $value) {
			if (array_key_exists($key, $arElemFields)) {
				$arFilter[$arElemFields[$key]] = $value;
				unset($arFilter[$key]);
			}
		}

		if (isset($arFilter["ELEMENT.CATALOG_ID"])) {
			$arFilter["CATALOG.ID"] = "ELEMENT.CATALOG_ID";
		}

		$arFilter['CATALOG_ID'] = $arCatalogIDs;

		return $arFilter;
	}

	public static function GetSort($arSort) {
		$arElemFields = Array(
			"ID" => "ELEMENT.ID",
			"SORT" => "ELEMENT.SORT",
			"CATALOG_ID" => "ELEMENT.CATALOG_ID"
		);

		foreach ($arSort as $key => $value) {
			if (array_key_exists($key, $arElemFields)) {
				unset($arSort[$key]);
				$arSort[$arElemFields[$key]] = $value;
			}
		}
		return $arSort;
	}

	// Обновление осн. св-ва элемента каталога
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

	// Получение осн. св-ва элемента каталога
	public function getFieldValue($ID, $propertyCODE) {
		global $DB;
		$DB->Query("SELECT `".$propertyCODE."` FROM `".self::$table."` WHERE `ID` = '".intVal($ID)."'");
		if($ar_res = $DB->fetchAssoc()) {
			return $ar_res[$propertyCODE];
		} else {
			return false;
		}
	}

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

	// Получить ID св-ва по его CODE
	public static function GetPropIDByCODE($CODE) {
		global $DB;
		$DB->Query("SELECT `ID` FROM `".self::$table_props."` WHERE `CODE` = '".sSql($CODE)."'");
		if($arResult = $DB->Fetch()) {
			return $arResult["ID"];
		}
	}

	public static function PrepareURL($arPrepare, $url) {
		foreach ($arPrepare as $key => $value) {
			switch ($key) {
				case 'SECTION_ID':
				case 'CATALOG_SECTION_ID':
					$url = str_replace("#SECTION_ID#", $value, $url);
					break;
				case 'SECTION_CODE':
				case 'CATALOG_SECTION_CODE':
					$url = str_replace("#SECTION_CODE#", $value, $url);
					break;
				case 'ID':
				case 'ELEMENT_ID':
					$url = str_replace("#ID#", $value, $url);
					$url = str_replace("#ELEMENT_ID#", $value, $url);
					break;
				case 'CODE':
				case 'ELEMENT_CODE':
					$url = str_replace("#CODE#", $value, $url);
					$url = str_replace("#ELEMENT_CODE", $value, $url);
					break;
			}
		}
		return $url;
	}
}

class CatalogElementResult extends DBResult {
	public static $result;
	public static $arResult;
	public static $elementID;
	public static $pagination;
	
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

		$res = new _CatalogElement($fields);
		return $res;
	}
	
	public static function GetPagination() {
		return self::$pagination;
	}
}

class _CatalogElement {
	public $fields;
	private $table_props = "b_catalog_property";
	private $table_props_enum = "b_catalog_property_enum";
	private $table_props_values = "b_catalog_element_property";
	private $table_catalog_section = "b_catalog_section";
	public  $elementID;

	public function  __construct($fields) {
		$this->fields = $fields;
		$this->elementID = $fields["ID"];
		//$this->GetSectionInfo();
	}

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
		$arPropExists = Array();
		$arPreResult = Array();
		while($ar_res = $DB->fetchAssoc()) {
			if (in_array($ar_res['CODE'], $arPropExists)) {
				if (is_array($arPreResult[$ar_res["CODE"]]['VALUE'])) {
					$arPreResult[$ar_res["CODE"]]['VALUE'][] = $ar_res['VALUE'];
				} else {
					$arval = $arPreResult[$ar_res["CODE"]]['VALUE'];
					unset($arPreResult[$ar_res["CODE"]]['VALUE']);
					$arPreResult[$ar_res["CODE"]]['VALUE'][] = $arval;
					$arPreResult[$ar_res["CODE"]]['VALUE'][] = $ar_res['VALUE'];
				}
			} else {
				$arPreResult[$ar_res["CODE"]] = TildaArray($ar_res);
				$arPropExists[] = $ar_res['CODE'];
			}
		}

		//if(count($arPreResult) == 1) {
		//	$arResult = current($arPreResult);
		//} else {
			$arResult = $arPreResult;
		//}
		return $arResult;
	}

	function GetSectionInfo() {
		global $DB;
		$sql = "SELECT * FROM `". $this->table_catalog_section ."` WHERE `ID` = ". intVal($this->fields['CATALOG_SECTION_ID']) .";";
		$DB->Query($sql);
		if ($ar_res = $DB->fetchAssoc()) {
			$this->fields["SECTION"] = TildaArray($ar_res);
		}
		//d($this->fields);
	}

	function GetProperty($ID) {
		$res = $this->GetProperties(Array(), Array("CODE"=>$ID));
		return $res;
	}
}
?>