<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}


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

	$arParams['CURRENT_PAGE'] = intVal($arParams['CURRENT_PAGE']) > 0 ? intVal($arParams['CURRENT_PAGE']) : 1;
	$arParams['PAGE_COUNT'] = empty($arParams['PAGE_COUNT']) ? 0 : intVal($arParams['PAGE_COUNT']);

	$arFilter = Array();
	if (array_key_exists("=SECTION_ID", $arParams)) {
		$arFilter['=CATALOG_SECTION_ID'] = empty($arParams["=SECTION_ID"]) ? 'NULL' : $arParams["=SECTION_ID"];
	} else {
		$arFilter['CATALOG_SECTION_ID'] = !empty($arParams["SECTION_ID"]) ? intVal($arParams["SECTION_ID"]) : '';
	}

	if (array_key_exists("SECTION_CODE", $arParams) AND !empty($arParams['SECTION_CODE'])) {
		$arFilter['SECTION_CODE'] = $arParams['SECTION_CODE'];
	}

	/*
	if (array_key_exists("ELEMENT_ID", $arParams) AND intVal($arParams['ELEMENT_ID']) > 0) {
		$arFilter['ID'] = $arParams['ELEMENT_ID'];
	}
	*/
	$arFilter["CATALOG_ID"] = $arParams["CATALOG_ID"];

	$arLimit = Array();
	if ($arParams['PAGE_COUNT'] !== 0) {
		$arLimit = Array(
			'PAGE' => $arParams['CURRENT_PAGE'],
			'COUNT' => $arParams['PAGE_COUNT']
		);
	}
	
	if(!empty($arParams['SORT_FIELD']) AND !empty($arParams['SORT_ORDER'])) {
		$arOrder[$arParams['SORT_FIELD']] =$arParams['SORT_ORDER'];
	} else {
		$arOrder = Array("ID" => "DESC");
	}
	$arParams["CACHE_OFF"] = ($arParams["CACHE_OFF"] == "Y")?true:false;

	$cache = new Cache(Array($arParams), 3600, ! $arParams["CACHE_OFF"]);
	if ($cache->StartCache()) {
		$rs = CatalogElement::GetList($arOrder, $arFilter, Array(), $arLimit);
		$arResult['PAGINATION'] = $rs->GetPagination();
		while($ob = $rs->GetNextElement()) {
			$arElement = $ob->GetFields();
			$arElement['PROPERTIES'] = $ob->GetProperties();

			if (intVal($arElement['CATALOG_SECTION_ID']) > 0) {
				$rsec = CatalogSection::GetByID($arElement['CATALOG_SECTION_ID']);
				$arSection = $rsec->GetNext();
			} else {
				$arSection['CODE'] = '';
			}

			$arPrepare = Array(
				'ID' => $arElement['ID'],
				'SECTION_ID' => $arElement['CATALOG_SECTION_ID'],
				'SECTION_CODE' => $arSection['CODE'],
			);

			$arElement["DETAIL_PAGE_URL"] = CatalogElement::PrepareURL($arPrepare, $arElement["DETAIL_PAGE_URL"]);
			$arElement["SECTION_PAGE_URL"] = CatalogElement::PrepareURL($arPrepare, $arElement["SECTION_PAGE_URL"]);
			$arResult["ITEMS"][] = $arElement;
		}
		$cache->SaveCache($arResult);
	} else {
		$arResult = $cache->GetCache();
	}
	
    $this->connectComponentTemplate();
?>