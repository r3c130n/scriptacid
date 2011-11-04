<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
App::page(function(&$arPageParams) {?>

<?php App::USER()->Authorize('1');?>
<?php SetTitle('Проект "ScriptACID CMF"');?>

<?php App::callComponent(
	'system:catalog.section',
	'_default:default',
	array(
		'TYPE' => 'orion_locations',
		'CATALOG_ID' => '3',
		'CACHE_TIME' => '3600',
		'COMPONENT_AJAX_MODE' => 'OFF',
		'LIST_URL' => '',
		'ELEMENT_URL' => '',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>


<?php App::callComponent(
	'system:catalog.element.add',
	'_default:default',
	array(
		'TYPE' => 'orion_locations',
		'FIELDS' => array(
			'1' => 'ID',
			'2' => 'NAME',
			'3' => 'CATALOG_SECTION_ID',
			'4' => 'CODE',
			'5' => 'PREVIEW_PICTURE',
			'6' => 'PREVIEW_TEXT',
			'7' => 'PREVIEW_TEXT_TYPE',
			'8' => 'DETAIL_PICTURE',
			'9' => 'DETAIL_TEXT',
		),
		'CATALOG_ID' => '3',
		'ID' => '{%_GET[ID]}',
		'ACTION_PARAMETER' => '{%_GET[ACTION]}',
		'COMPONENT_AJAX_MODE' => 'OFF',
		'CACHE_TIME' => '3600',
		'LIST_URL' => '/test/test_edit_component_call.php',
		'ELEMENT_URL' => '/test/test_edit_component_call.php?ID=#ID#',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>

<?php App::callComponent(
	'test:empty',
	'_default:default',
	array(
		'CACHE_TIME' => '36001',
		'COMPONENT_AJAX_MODE' => 'OFF',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>
<?php App::callComponent(
	'test:empty',
	'_default:default',
	array(
		'CACHE_TIME' => '36002',
		'COMPONENT_AJAX_MODE' => 'OFF',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>

<?php // d(ComponentTools::getComponentListInFile('public_page'))?>

<?php }); // end makePage?>
