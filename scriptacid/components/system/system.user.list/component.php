<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	global $USER;
	$arPagin = Array(
		'PAGE' => !empty($_GET['PAGE']) ? (int)$_GET['PAGE'] : 1,
		'PAGE_COUNT' => $arParams['PAGE_COUNT']
	);

	Cache::DeleteOldCache(60);

	$cache = new Cache(Array($arParams, $arPagin), 3600);
	if ($cache->StartCache()) {
		$arResult = $USER->GetList(Array(), $arPagin);
		$cache->SaveCache($arResult);
	} else {
		$arResult = $cache->GetCache();
	}

	$this->connectComponentTemplate();
?>