<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}
//d($_GET, '$_GET');
//d($_POST, '$_POST');
//d($_REQUEST, '$_REQUEST');
//d($arParams, '$arParams');
//sleep(3);

function getCatalogFields(&$arParams, $array, $clear = false) {
	$arCheckbox = Array("ACTIVE");
	$arText = Array('NAME', 'CODE');
	$arHidden = Array('ID', 'CATALOG_ID', 'TIMESTAMP_X', 'SID');
	$arSelect = Array('CATALOG_SECTION_ID', 'PREVIEW_TEXT_TYPE', 'DETAIL_TEXT_TYPE');
	$arTextarea = Array('PREVIEW_TEXT', 'DETAIL_TEXT');
	$arFile = Array('PREVIEW_PICTURE', 'DETAIL_PICTURE');
	$arExclude = Array("ID");
	if($clear) {
		foreach ($array as $k) {
			$arField = Array();
			if (in_array($k, $arHidden)) $arField['TYPE'] = 'hidden';
			elseif (in_array($k, $arSelect)) $arField['TYPE'] = 'select';
			elseif (in_array($k, $arCheckbox)) $arField['TYPE'] = 'checkbox';
			elseif (in_array($k, $arTextarea)) $arField['TYPE'] = 'textarea';
			elseif (in_array($k, $arFile)) $arField['TYPE'] = 'file';
			else $arField['TYPE'] = 'text';

			if ($k == 'CATALOG_ID') {
				$arField['VALUE'] = intVal($arParams['CATALOG_ID']);
			} elseif ($k == 'ACTIVE') {
				$arField['VALUE'] = empty($arField['VALUE']) ? 'Y' : sXss($arField['VALUE']);
			} elseif ($k == 'PREVIEW_TEXT_TYPE') {
				$arField['VALUES'] = Array('text' => 'text','html' => 'html');
			} elseif ($k == 'DETAIL_TEXT_TYPE') {
				$arField['VALUES'] = Array('text' => 'text','html' => 'html');
			} elseif ($k == "CATALOG_SECTION_ID") {
				if( $rs = CatalogSection::GetList(Array(), Array("CATALOG_ID" => intVal($arParams["CATALOG_ID"]))) ) {
					$arSections = Array();
					$arSections[] = '';
					while ($abSect = $rs->GetNext()) {
						$arSections[$abSect["ID"]] = $abSect["NAME"];
					}
					$arField['VALUES'] = $arSections;
				}
			} else {
				$arField['VALUE'] = '';
			}
			if (!in_array($k, $arExclude)) {
				$result[$k] = $arField;
			}
		}
	} else {
		foreach ($array as $k => $v) {
			$arField = Array();
			if (in_array($k, $arHidden)) $arField['TYPE'] = 'hidden';
			elseif (in_array($k, $arSelect)) $arField['TYPE'] = 'select';
			elseif (in_array($k, $arCheckbox)) $arField['TYPE'] = 'checkbox';
			elseif (in_array($k, $arTextarea)) $arField['TYPE'] = 'textarea';
			elseif (in_array($k, $arFile)) $arField['TYPE'] = 'file';
			else $arField['TYPE'] = 'text';

			switch($k) {
				case 'CATALOG_ID':
					$arField['VALUE'] = intVal($v);
					break;
				case 'ACTIVE':
					$arField['VALUE'] = empty($v) ? 'Y' : sXss($v);
					break;
				case 'PREVIEW_TEXT_TYPE':
					$arField['VALUES'] = Array('text', 'html');
					break;
				case 'DETAIL_TEXT_TYPE':
					$arField['VALUES'] = Array('text', 'html');
					break;
				case 'CATALOG_SECTION_ID':
					$rs = CatalogSection::GetList();
					$arSections = Array();
					$arSections[] = '';
					while ($arSect = $rs->GetNext()) {
						$arSections[$arSect["ID"]] = $arSect["NAME"];
					}
					$arField['VALUES'] = $arSections;
					break;
				default:
					$arField['VALUE'] = $v;
			}

			if (!in_array($k, $arExclude)) {
				$result[$k] = $arField;
			}
		}
	}
	return $result;
}

$arParams["ID"] = isset($arParams["ID"]) ? intVal($arParams["ID"]) : '';
$arParams["TYPE"] = isset($arParams["TYPE"]) ? sXss($arParams["TYPE"]) : '';

$arParams["ELEMENT_URL"] = str_replace("#ID#", $arParams["ID"], $arParams["ELEMENT_URL"]); 


if( intval($arParams["CATALOG_ID"])>0 ) {
	$CATALOG_ID = intval($arParams["CATALOG_ID"]);
}
elseif( intVal($_GET["CATALOG_ID"])>0 ) {
	$CATALOG_ID = intVal($_GET["CATALOG_ID"]);
}
else {
	AddMsg("Не указан идентификатор каталога!");
	return false;
}

if (!empty($_POST)) {
	if (!empty($_POST["NAME"])) {
		$arFields = Array(
			"CATALOG_SECTION_ID" => intVal($_POST["CATALOG_SECTION_ID"]),
			"SORT" => intVal($_POST["SORT"]),
			"CODE" => sXss($_POST["CODE"]),
			"NAME" => sXss($_POST["NAME"]),
			"ACTIVE" => $_POST["ACTIVE"] == "Y"?"Y":'',
			"CATALOG_ID" => $CATALOG_ID,
			"PREVIEW_TEXT" => sXss($_POST["PREVIEW_TEXT"]),
			"PREVIEW_TEXT_TYPE" => sXss($_POST["PREVIEW_TEXT_TYPE"]),
			"DETAIL_TEXT" => sXss($_POST["DETAIL_TEXT"]),
			"DETAIL_TEXT_TYPE" => sXss($_POST["DETAIL_TEXT_TYPE"]),
			"PROPERTIES" => $_POST["PROPERTY"],
		);
		if ($arParams["ID"] != '') {
			d($arParams);
			d($CATALOG_ID);
			exit;
			if (CatalogElement::Update($arParams["ID"], $arFields)) {
				AddMsg("Элемент успешно обновлен!");
				if (!isset($_POST["apply_btn"])) {
					//$this->redirectTo('/scriptacid/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
					$this->redirectTo($arParams["ELEMENT_URL"]);
				}
			} else {
				$arResult["ERRORS"][] = "Не могу найти элемент каталога";
			}
		} else {
			if (CatalogElement::Add($arFields)) {
				AddMsg("Элемент успешно добавлен!");
				//$this->redirectTo('/scriptacid/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
				$this->redirectTo($arParams["LIST_URL"]);
			} else {
				$arResult["ERRORS"][] = "Такой элемент каталога уже есть";
			}
		}
	}
}
if ($arParams["ID"] != '') {
	$rs = CatalogElement::GetByID($arParams["ID"]);
	if($ob = $rs->GetNextElement()) {
		$arCatalogFields = $ob->GetFields();
		$arResult["FIELDS"] = getCatalogFields($arParams, $arCatalogFields);
		$arResult["PROPERTIES"] = $ob->GetProperties();
		$arResult["MODE"] = "EDIT";
	} else {
		AddMsg("Ошибка: Элемент с данным ID не найден!");
		$this->redirectTo('/admin/catalog/catalog.php');
	}
} else {
	$arResult["FIELDS"] = getCatalogFields($arParams, $arParams["FIELDS"], true);
	$arProps = Catalog::GetCatalogProperties($arParams["CATALOG_ID"]);
	$arResult["PROPERTIES"] = Array();
	foreach ($arProps as $k => $arProp) {
		$arProperties[] = Array(
							"ID" => $arProp["ID"],
							"NAME" => $arProp["NAME"],
							"VALUE" => $arProp["DEFAULT_VALUE"]
							);
	}
	$arResult["PROPERTIES"] = $arProperties;
	$arResult["MODE"] = "ADD";
}
	//global $DB;
	//d($DB->sqlLog);
	$this->connectComponentTemplate();
?>