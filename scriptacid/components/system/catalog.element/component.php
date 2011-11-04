<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}


	$arParams["CATALOG_ID"] = intVal($arParams["CATALOG_ID"]);

	$arFilter = Array();
	if (array_key_exists("=SECTION_ID", $arParams)) {
		$arFilter['=CATALOG_SECTION_ID'] = empty($arParams["=SECTION_ID"]) ? 'NULL' : $arParams["=SECTION_ID"];
	} else {
		$arFilter['CATALOG_SECTION_ID'] = !empty($arParams["SECTION_ID"]) ? intVal($arParams["SECTION_ID"]) : '';
	}
	$arFilter["ID"] = intVal($arParams["ELEMENT_ID"]);
	$arFilter["CATALOG_ID"] = $arParams["CATALOG_ID"];
	$rs = CatalogElement::GetList(Array(), $arFilter);
	if ($ob = $rs->GetNextElement()) {
		$arElement = $ob->GetFields();
		$arElement['PROPERTIES'] = $ob->GetProperties();
		$arElement["DETAIL_PAGE_URL"] = str_replace(Array("#SECTION_ID#", "#ID#"), Array($arElement['CATALOG_SECTION_ID'], $arElement['ID']), $arElement["DETAIL_PAGE_URL"]);
		$arElement["SECTION_PAGE_URL"] = str_replace("#SECTION_ID#", $arElement['CATALOG_SECTION_ID'], $arElement["SECTION_PAGE_URL"]);
		$arResult = $arElement;
		SetTitle($arElement["NAME"]);
	}
	
    $this->connectComponentTemplate();
?>