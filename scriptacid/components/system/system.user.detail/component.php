<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	global $USER;
	
	if ($arParams["SET_TITLE"]) {
		SetTitle($arParams["TITLE_TEXT"]);
	}
	
	$arResult["USER"] = $USER->GetByID(intVal($arParams["USER_ID"]));
	if(!$arResult["GROUPS"] = UserGroups::GetGroupList())
		$arResult["GROUPS"] = Array();

	$this->connectComponentTemplate();
?>