<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}

$arParams['BTN_NAME'] = strlen($arParams['BTN_NAME']) > 0 ? $arParams['BTN_NAME'] : 'add_btn';
$arParams['ADD_BTN_NAME'] = strlen($arParams['ADD_BTN_NAME']) > 0 ? $arParams['ADD_BTN_NAME'] : LANG('ADD_BTN');
$arParams['EDIT_BTN_NAME'] = strlen($arParams['EDIT_BTN_NAME']) > 0 ? $arParams['EDIT_BTN_NAME'] : LANG('EDIT_BTN');
$arParams['CATALOG_ID'] = intVal($arParams['CATALOG_ID']) > 0 ? intVal($arParams['CATALOG_ID']) : false;
$arParams['USE_CAPTCHA'] = $arParams['USE_CAPTCHA'] == "Y" ? "Y" : "N";
$arParams['MSG_ADD'] =  strLen($arParams['MSG_ADD']) > 0 ? $arParams['MSG_ADD'] : LANG('MSG_ADD');
$arParams['MSG_EDIT'] = strLen($arParams['MSG_EDIT']) > 0 ? $arParams['MSG_EDIT'] : LANG('MSG_EDIT');
$arParams['PREVIEW_TEXT_LENGTH'] = intVal($arParams['PREVIEW_TEXT_LENGTH']) > 0 ? intVal($arParams['PREVIEW_TEXT_LENGTH']) : 80;
$arParams["ID"] = intVal($arParams["ID"]) > 0 ? intVal($arParams["ID"]) : 0;

$arCatalog['PROPERTIES'] = Catalog::GetCatalogProperties($arParams['CATALOG_ID']);

$arFields = CatalogUtils::GetMainFieldsArray();

if(!empty($arCatalog['PROPERTIES'])) {
	foreach ($arCatalog['PROPERTIES'] as $arProp) {
		$arFields[$arProp['ID']] = Array(
			"NAME" => $arProp['NAME'],
			"CODE" => $arProp['CODE'],
			"TYPE" => $arProp['PROPERTY_TYPE'],
			"MULTIPLE" => $arProp['MULTIPLE'],
			"DEFAULT" => $arProp['DEFAULT_VALUE'],
		);
	}
}

$arResult['FIELDS'] = $arFields;

// SECTION TREE
$arSectionTree = Array('' => Array('NAME' => 'Корневой каталог'));
$rsec = CatalogSection::GetList(Array("NAME" => "ASC"), Array("CATALOG_ID" => $arParams['CATALOG_ID']));
while ($obSec = $rsec->GetNextElement()) {
	$arSec = $obSec->GetFields();
	if ($arSec['CATALOG_SECTION_ID'] > 0) {
		$arSectionTree[$arSec['CATALOG_SECTION_ID']]['SUB'][$arSec['ID']] = $arSec['NAME'];
	} else {
		$arSectionTree[$arSec['ID']]['NAME'] = $arSec['NAME'];
	}
	//d($arSec);
}
$arResult['SECTIONS_TREE'] = Array();
foreach ($arSectionTree as $seid => $arSect) {
	$arResult['SECTIONS_TREE'][$seid] = $arSect['NAME'];
	if (is_array($arSect['SUB']) AND !empty($arSect['SUB'])) {
		foreach ($arSect['SUB'] as $subID => $subName) {
			$arResult['SECTIONS_TREE'][$subID] = '&nbsp;|&nbsp;' . $subName;
		}
	}
}

if (!empty($arResult['FIELDS'])) {
	$bCatalog = true;
}

if ($arParams["ID"] > 0) {
	$rsEl = CatalogElement::GetByID($arParams["ID"]);
	if ($obEl = $rsEl->GetNextElement()) {
		$arEl = $obEl->GetFields();
		$arEl['PROPERTIES'] = $obEl->GetProperties();

		$arProps = Array();
		foreach ($arEl['PROPERTIES'] as $prop) {
			$arProps[$prop['CATALOG_PROPERTY_ID']] = $prop['VALUE'];
			$arPropNames[$prop['CATALOG_PROPERTY_ID']] = $prop['CODE'];
		}

		foreach ($arParams['FIELDS'] as $field) {
			if (is_numeric($field)) {
				if(is_array($arProps[$field]) AND !isset($_POST['PROPERTY'][$arPropNames[$field]])) {
					foreach ($arProps[$field] as $prp) {
						$_POST['PROPERTY'][$arPropNames[$field]][] = $prp;
					}
				} else {
					$_POST['PROPERTY'][$arPropNames[$field]] = isset($_POST['PROPERTY'][$arPropNames[$field]]) ? $_POST['PROPERTY'][$arPropNames[$field]] : $arProps[$field];
				}
			} else {
				$_POST[$field] = isset($_POST[$field]) ? $_POST[$field] : $arEl[$field];
			}
		}
	}
}
/**
 *  Добавление/Изменение элемента
 */
if (!empty($_POST) AND (isset($_POST['add_'.$arParams['BTN_NAME']]) OR isset($_POST['edit_'.$arParams['BTN_NAME']])) AND $bCatalog) {
	foreach ($arParams['REQUIRED'] as $required) {
		if (is_numeric($required)) {
			if ($arFields[$required]['TYPE'] == "F") {
				if (empty($_FILES['PROPERTY']['name'][$required])) {
					$arResult["ERRORS"][] = "Не заполнено поле ".$required;
				}
			} else {
				if (empty($_POST['PROPERTY'][$required])) {
					$arResult["ERRORS"][] = "Не заполнено поле ".$required;
				}
			}
		} else {
			if ($required == 'PREVIEW_PICTURE' OR $required == 'DETAIL_PICTURE') {
				if (empty($_FILES[$required]['tmp_name'])) {
					$arResult["ERRORS"][] = "Не заполнено поле ".$required;
				}
			} else {
				if (empty($_POST[$required])) {
					$arResult["ERRORS"][] = "Не заполнено поле ".$required;
				}
			}
		}
	}

	if (!empty($_POST["NAME"]) AND empty($arResult["ERRORS"])) {
		$arProperties = Array();

		if (!empty($arParams["FIELDS"])) {
			foreach ($arParams["FIELDS"] as $propID) {
				if (is_numeric($propID)) {
					if ($arFields[$propID]['TYPE'] == "F") {
						if ($arFields[$propID]['MULTIPLE'] == "Y" AND !empty($_FILES['PROPERTY'])) {
							foreach ($_FILES['PROPERTY']['tmp_name'] as $arPropVal) {
								foreach ($arPropVal as $prid => $file) {
									if (!empty($file)) {
										$arFile = Array();
										$arFile['name'] = $_FILES['PROPERTY']['name'][$propID][$prid];
										$arFile['tmp_name'] = $_FILES['PROPERTY']['tmp_name'][$propID][$prid];
										$arFile['type'] = $_FILES['PROPERTY']['type'][$propID][$prid];
										$arFile['error'] = $_FILES['PROPERTY']['error'][$propID][$prid];
										$arProperties[$propID][] = $arFile;
									}
								}
							}
						} else {
							$arFile = Array();
							$arFile['name'] = $_FILES['PROPERTY']['name'][$propID];
							$arFile['tmp_name'] = $_FILES['PROPERTY']['tmp_name'][$propID];
							$arFile['type'] = $_FILES['PROPERTY']['type'][$propID];
							$arFile['error'] = $_FILES['PROPERTY']['error'][$propID];
							$arProperties[$propID] = $arFile;
						}
					} else {
						$arProperties[$propID] = $_POST["PROPERTY"][$propID];
					}
				}
			}
		}

		$arElementFields = Array(
			"CATALOG_SECTION_ID" => intVal($_POST["CATALOG_SECTION_ID"]),
			"SORT" => intVal($_POST["SORT"]),
			"CODE" => sXss($_POST["CODE"]),
			"NAME" => sXss($_POST["NAME"]),
			"ACTIVE" => $_POST["ACTIVE"] == "Y"?"Y":'',
			"CATALOG_ID" => intVal($arParams["CATALOG_ID"]),
			"PREVIEW_TEXT" => sXss($_POST["PREVIEW_TEXT"]),
			"PREVIEW_PICTURE" => $_FILES["PREVIEW_PICTURE"],
			"PREVIEW_TEXT_TYPE" => sXss($_POST["PREVIEW_TEXT_TYPE"]),
			"DETAIL_TEXT" => sXss($_POST["DETAIL_TEXT"]),
			"DETAIL_TEXT_TYPE" => sXss($_POST["DETAIL_TEXT_TYPE"]),
			"DETAIL_PICTURE" => $_FILES["DETAIL_PICTURE"],
			"PROPERTIES" => $arProperties,
		);

		if ($arParams['PREVIEW_FROM_DETAIL'] == "Y") {
			$arElementFields['PREVIEW_TEXT'] = TruncateText(sXss($_POST["DETAIL_TEXT"]), $arParams['PREVIEW_TEXT_LENGTH']);
		}

		if ($arParams["ID"] > 0) {
			if (CatalogElement::Update($arParams["ID"], $arElementFields)) {
				AddMsg($arParams['MSG_EDIT']);
				if (!isset($_POST["apply_btn"])) {
					$this->redirectTo($arParams['LIST_URL']);
				}
			} else {
				$arResult["ERRORS"][] = "Не могу найти элемент каталога";
			}
		} else {
			if (CatalogElement::Add($arElementFields)) {
				AddMsg($arParams['MSG_ADD']);
				$this->redirectTo($arParams['LIST_URL']);
			} else {
				$arResult["ERRORS"][] = "Такой элемент каталога уже есть";
			}
		}
	}
}
	$this->connectComponentTemplate();
?>