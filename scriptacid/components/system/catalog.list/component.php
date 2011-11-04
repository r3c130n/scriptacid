<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}

	$arFilter = Array();
	if (!empty($arParams["CATALOG_TYPE"])) {
		$arFilter['CATALOG_TYPE_ID'] = $arParams["CATALOG_TYPE"];
	}
	$arResult = Catalog::GetList(Array(), $arFilter);
	//d($arFilter);
	//d($arResult);
    $this->connectComponentTemplate();
?>