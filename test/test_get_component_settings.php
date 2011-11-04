<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF". Тест получения параметров компонента');
App::page(function(&$arPageParams) {?>


<?php
	echo "<b>:catalog.element.add@_default</b>".endl;
	$arCatalogElementAddComponentSettings = ComponentTools::getSettingsByName(':catalog.element.add');
	d($arCatalogElementAddComponentSettings, ':catalog.element.add');
	$arTplList = ComponentTools::getTemplatesList(':catalog.element.add');
	d($arTplList, ':catalog.element.add - templates list');
	
	echo "<b>:menu@top</b>".endl;
	$arMenuTopComponentSettings = ComponentTools::getSettingsByName(":menu", 'top');
	d($arMenuTopComponentSettings, ':menu@top');
	
	echo "<b>:menu@left</b>".endl;
	$arMenuLeftComponentSettings = ComponentTools::getSettingsByName(':menu', 'left');
	d($arMenuLeftComponentSettings, ':menu@left');
	$arTplList = ComponentTools::getTemplatesList(':menu');
	d($arTplList, ':menu - templates list');
?>


<?php }); // end of makePage?>