<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}


	$arParams["SECTION_ID"] = intVal($arParams["SECTION_ID"]);
	$arParams["CATALOG_ID"] = intVal($arParams["CATALOG_ID"]);
	$arParams["CATALOG_TYPE"] = sXss($arParams["CATALOG_TYPE"]);

	if (empty($arParams["SHOW_FIELDS"] )) {
		$arParams["SHOW_FIELDS"] = Array(
				"ID",
				"NAME",
				"SORT",
				"ACTIVE",
				"SID",
				"CODE",
			);
	}

	$arFilter = Array();

	if (!empty($arParams["SECTION_ID"])) {
		$arFilter['CATALOG_SECTION_ID'] = $arParams["SECTION_ID"];
	}
	$arFilter["CATALOG_ID"] = $arParams["CATALOG_ID"];
	$rs = CatalogSection::GetList(Array(), $arFilter);
	while($ob = $rs->GetNextElement()) {
		$arResult["ITEMS"][] = $ob->GetFields();
	}

    $this->connectComponentTemplate();
?>