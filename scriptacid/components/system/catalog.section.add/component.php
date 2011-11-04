<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}

function getCatalogFields($array, $clear = false) {
	global $arParams;
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
			} elseif ($k == 'SORT') {
				$arField['VALUE'] = empty($arField['VALUE']) ? '500' : intVal($arField['VALUE']);
			} elseif ($k == 'ACTIVE') {
				$arField['VALUE'] = empty($arField['VALUE']) ? 'Y' : sXss($arField['VALUE']);
			} elseif ($k == 'PREVIEW_TEXT_TYPE') {
				$arField['VALUES'] = Array('text', 'html');
			} elseif ($k == 'DETAIL_TEXT_TYPE') {
				$arField['VALUES'] = Array('text', 'html');
			} elseif ($k == "CATALOG_SECTION_ID") {
				$rs = CatalogSection::GetList(Array(), Array("CATALOG_ID" => intVal($arParams["CATALOG_ID"])));
				$arSections = Array();
				$arSections[] = '';
				while ($abSect = $rs->GetNext()) {
					$arSections[$abSect["ID"]] = $abSect["NAME"];
				}
				$arField['VALUES'] = $arSections;
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

			if ($k == 'CATALOG_ID') {
				$arField['VALUE'] = intVal($v);
			} elseif ($k == 'ACTIVE') {
				$arField['VALUE'] = empty($v) ? 'Y' : sXss($v);
			} elseif ($k == 'SORT') {
				$arField['VALUE'] = empty($v) ? '500' : intVal($v);
			} elseif ($k == 'PREVIEW_TEXT_TYPE') {
				$arField['VALUES'] = Array('text' => 'text','html' => 'html');
			}  elseif ($k == 'DETAIL_TEXT_TYPE') {
				$arField['VALUES'] = Array('text' => 'text','html' => 'html');
			} elseif ($k == "CATALOG_SECTION_ID") {
				$rs = CatalogSection::GetList(Array(), Array("CATALOG_ID" => intVal($arParams["CATALOG_ID"])));
				$arSections = Array();
				$arSections[] = '';
				while ($arSect = $rs->GetNext()) {
					$arSections[$arSect["ID"]] = $arSect["NAME"];
				}
				$arField['VALUES'] = $arSections;
			} else {
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

if (!empty($_POST)) {
	if (!empty($_POST["NAME"])) {
		$arFields = Array(
			"CATALOG_SECTION_ID" => intVal($_POST["CATALOG_SECTION_ID"]),
			"SORT" => intVal($_POST["SORT"]),
			"CODE" => sXss($_POST["CODE"]),
			"NAME" => sXss($_POST["NAME"]),
			"ACTIVE" => $_POST["ACTIVE"] == "Y"?"Y":'',
			"CATALOG_ID" => intVal($_GET["CATALOG_ID"]),
			"PREVIEW_TEXT" => sXss($_POST["PREVIEW_TEXT"]),
			"PREVIEW_TEXT_TYPE" => sXss($_POST["PREVIEW_TEXT_TYPE"]),
			"DETAIL_TEXT" => sXss($_POST["DETAIL_TEXT"]),
			"DETAIL_TEXT_TYPE" => sXss($_POST["DETAIL_TEXT_TYPE"]),
			"PROPERTIES" => $_POST["PROPERTY"],
		);
		if ($arParams["ID"] != '') {
			if (CatalogSection::Update($arParams["ID"], $arFields)) {
				AddMsg("Секция успешно обновлена!");
				if (!isset($_POST["apply_btn"])) {
					$this->redirectTo('/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
				}
			} else {
				$arResult["ERRORS"][] = "Не могу обновить секцию";
			}
		} else {
			if (CatalogSection::Add($arFields)) {
				AddMsg("Секция успешно добавлена!");
				$this->redirectTo('/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
			} else {
				$arResult["ERRORS"][] = "Такая секция уже есть";
			}
		}
	}
}
if ($arParams["ID"] != '') {
	$rs = CatalogElement::GetByID($arParams["ID"]);
	if($ob = $rs->GetNextElement()) {
		$arCatalogFields = $ob->GetFields();
		$arResult["FIELDS"] = getCatalogFields($arCatalogFields);
		$arResult["PROPERTIES"] = $ob->GetProperties();
		$arResult["MODE"] = "EDIT";
	} else {
		AddMsg("Ошибка: Элемент с данным ID не найден!");
		$this->redirectTo('/admin/catalog/catalog.php');
	}
} else {
	$arResult["FIELDS"] = getCatalogFields($arParams["FIELDS"], true);
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
$this->connectComponentTemplate();
?>