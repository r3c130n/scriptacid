<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
if ( !Modules::includeModule('catalog') ) {
	ShowError("Модуль каталогов не установлен.");
	return;
}


$arParams["ID"] = isset($arParams["ID"]) ? sXss($arParams["ID"]) : '';
if (!empty($_POST)) {
	if (!empty($_POST["ID"])) {
		$arFields = Array(
			"ID" => sXss($_POST["ID"]),
			"SORT" => intVal($_POST["SORT"]),
			"SID" => sXss($_POST["SID"]),
			"NAME" => sXss($_POST["NAME"]),
			"SECTION_NAME" => sXss($_POST["SECTION_NAME"]),
			"ELEMENT_NAME" => sXss($_POST["ELEMENT_NAME"]),
		);
		if ($arParams["ID"] != '') {
			if (CatalogType::Update($arParams["ID"], $arFields)) {
				AddMsg("Тип успешно обновлен!");
				$this->redirectTo(SYS_ROOT.'/admin/catalog');
			} else {
				$arResult["ERRORS"][] = "Не могу обновить тип каталога";
			}
		} else {
			if (CatalogType::Add($arFields)) {
				AddMsg("Тип успешно добавлен!");
				$this->redirectTo(SYS_ROOT.'/admin/catalog/');
			} else {
				$arResult["ERRORS"][] = "Такой тип уже есть";
			}
		}
	}
}
if ($arParams["ID"] != '') {
	$arResult["FIELDS"] = CatalogType::GetByID($arParams["ID"]);
	$arResult["MODE"] = "EDIT";
} else {
	foreach ($arParams["FIELDS"] as $v) {
		$arResult["FIELDS"][$v] = '';
	}
	$arResult["MODE"] = "ADD";
}
	$this->connectComponentTemplate();
?>