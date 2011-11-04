<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

	$arParams['WIDTH'] = intVal($arParams['WIDTH']) > 0 ? intVal($arParams['WIDTH']) : 320;
	$arParams['HEIGHT'] = intVal($arParams['HEIGHT']) > 0 ? intVal($arParams['HEIGHT']) : 240;

	$arResult['B_VIDEO'] = true;

	if (intVal($arParams['FILE_ID']) > 0) {
		$arResult["FILE_PATH"] = File::GetPath($arParams['FILE_ID']);
		
	} elseif (strLen($arParams['URL']) > 0) {
		$arResult["FILE_PATH"] = $arParams['URL'];
	} else {
		$arResult['B_VIDEO'] = false;
	}
    $this->connectComponentTemplate();
?>