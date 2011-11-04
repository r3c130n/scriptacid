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
	$arHidden = Array('ID', 'CATALOG_TYPE_ID', 'TIMESTAMP_X');
	$arSelect = Array('SID', 'DESCRIPTION_TYPE');
	$arTextarea = Array('DESCRIPTION');
	$arFile = Array('PICTURE');
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

			if ($k == 'CATALOG_TYPE_ID') {
				$arField['VALUE'] = sXss($arParams['TYPE']);
			} elseif ($k == 'SID') {
				$arField['VALUE'] = Array('ru');
			} elseif ($k == 'DESCRIPTION_TYPE') {
				$arField['VALUE'] = Array('text', 'html');
			}else {
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

			if ($k == 'CATALOG_TYPE_ID') {
				$arField['VALUE'] = sXss($v);
			} elseif ($k == 'SID') {
				$arField['VALUE'] = Array($v);
			} elseif ($k == 'DESCRIPTION_TYPE') {
				$arField['VALUE'] = Array('text', 'html');
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
			"CATALOG_TYPE_ID" => sXss($arParams["TYPE"]),
			"SORT" => intVal($_POST["SORT"]),
			"SID" => sXss($_POST["SID"]),
			"CODE" => sXss($_POST["CODE"]),
			"NAME" => sXss($_POST["NAME"]),
			"ACTIVE" => sXss($_POST["ACTIVE"]),
			"LIST_PAGE_URL" => sXss($_POST["LIST_PAGE_URL"]),
			"DETAIL_PAGE_URL" => sXss($_POST["DETAIL_PAGE_URL"]),
			"SECTION_PAGE_URL" => sXss($_POST["SECTION_PAGE_URL"]),
			"PICTURE" => sXss($_POST["PICTURE"]),
			"DESCRIPTION" => sXss($_POST["DESCRIPTION"]),
			"DESCRIPTION_TYPE" => sXss($_POST["DESCRIPTION_TYPE"]),
			"SECTIONS_NAME" => sXss($_POST["SECTIONS_NAME"]),
			"ELEMENTS_NAME" => sXss($_POST["ELEMENTS_NAME"]),
			"SECTION_NAME" => sXss($_POST["SECTION_NAME"]),
			"ELEMENT_NAME" => sXss($_POST["ELEMENT_NAME"]),
			"SEO_DESCRIPTION" => sXss($_POST["SEO_DESCRIPTION"]),
			"SEO_KEYWORDS" => sXss($_POST["SEO_KEYWORDS"]),
			"PROPERTIES" => $_POST["PROPERTY"],
		);
		if ($arParams["ID"] != '') {
			if (Catalog::Update($arParams["ID"], $arFields)) {
				AddMsg("Каталог успешно обновлен!");
				if (!isset($_POST["apply_btn"])) {
					$this->redirectTo('/scriptacid/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
				}
			} else {
				$arResult["ERRORS"][] = "Не могу обновить тип каталога";
			}
		} else {
			if (Catalog::Add($arFields)) {
				AddMsg("Каталог успешно добавлен!");
				$this->redirectTo('/scriptacid/admin/catalog/catalog.php?TYPE='.$arParams["TYPE"]);
			} else {
				$arResult["ERRORS"][] = "Такой тип уже есть";
			}
		}
	}
}
if ($arParams["ID"] != '') {
	$arCatalogFields = Catalog::GetByID($arParams["ID"]);
	$arProps = $arCatalogFields["PROPERTIES"];
	unset($arCatalogFields["PROPERTIES"]);
	$arResult["FIELDS"] = getCatalogFields($arCatalogFields);
	$arResult["PROPERTIES"] = Catalog::GeneratePropsArray($arProps);
	$arResult["MODE"] = "EDIT";
} else {
	$arResult["FIELDS"] = getCatalogFields($arParams["FIELDS"], true);
	$arProps = $arCatalogFields["PROPERTIES"];
	unset($arCatalogFields["PROPERTIES"]);
	$arResult["PROPERTIES"] = Catalog::GeneratePropsArray();
	$arResult["MODE"] = "ADD";
}
	$this->connectComponentTemplate();
?>