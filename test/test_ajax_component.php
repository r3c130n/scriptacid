<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF"');
App::page(function(&$arPageParams) {?>

<?php App::USER()->Authorize('1');?>
<?php SetTitle('Проект "ScriptACID CMF"');?>

<p class="some-class">TEST AJAX Component Call</p>


<p><b>Тестирование редиректа в аджакс вызовах.</b></p>
<?php App::callComponent('test:test.redirect_to', '', array(
	'REDIRECT_TO' => '{%_GET[REDIRECT_TO]}',
	'COMPONENT_AJAX_MODE' => 'N',
));?>


<p><b>Тест передачи пост запроса к асинхронному запросу компонента</b></p>
<span>Нажмите "отправить", что бы увидеть вывод в компоненте</span>
<br />
<?php App::callComponent('test:test.print_post', '', array(
	'COMPONENT_AJAX_MODE' => 'Y',
	'COMPONENT_AJAX_SEND_PAGE_POST' => 'Y',
	//'POST_PARAM1' => '{%_POST[post_param1]}',
));?>
<form action="" method="post">
	<input type="hidden" name="post_param1" value="value1" />
	<input type="hidden" name="post_param2" value="value2" />
	<input type="hidden" name="post_param3" value="value3" />
	<input type="submit" name="post_send" value="Отправить" />
</form>


<br />
<br />
<p><b>Тест работы аджакс двух компонентов.</b></p>
<?php App::callComponent(
	'system:catalog.section',
	'_default:default',
	array(
		'TYPE' => 'orion_locations',
		'CATALOG_ID' => '3',
		'COMPONENT_AJAX_MODE' => 'AJAX',
		'CACHE_TIME' => '3600',
		'ELEMENT_URL' => '',
		'LIST_URL' => '',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>
<?php App::callComponent(
	'system:catalog.element.add',
	'_default:inherit',
	array(
		'TYPE' => 'orion_locations',
		'FIELDS' => array(
			'1' => 'ID',
			'2' => 'ACTIVE',
			'3' => 'NAME',
			'4' => 'CATALOG_SECTION_ID',
			'5' => 'CATALOG_ID',
			'6' => 'SORT',
			'7' => 'CODE',
			'8' => 'PREVIEW_PICTURE',
			'9' => 'PREVIEW_TEXT',
			'10' => 'PREVIEW_TEXT_TYPE',
			'11' => 'DETAIL_PICTURE',
			'12' => 'DETAIL_TEXT',
			'13' => 'DETAIL_TEXT_TYPE',
			'14' => 'TAGS',
		),
		'CATALOG_ID' => '3',
		'ID' => '{%_REQUEST[ID]}',
		'ACTION_PARAMETER' => '{%_GET[ACTION]}',
		'COMPONENT_AJAX_MODE' => 'AJAX',
		'CACHE_TIME' => '3600',
		'LIST_URL' => '/test/test_ajax_component_call.php',
		'ELEMENT_URL' => '/test/test_ajax_component_call.php?ID=#ID#',
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	)
);?>
<p>
	Так бдет делаться связывание компонентов<br />
	$cckSystemCatalogSection_1 = App::callComponent(":catalog.section", ...);<br />
	$cckSystemCatalogElementAdd_1 = App::callComponent(":catalog.element.add", ...);<br />
	App::linkComponentKeys(array($cckSystemCatalogSection_1, $cckSystemCatalogElementAdd_1));<br />
</p>


<b>Ссылки для того, что бы убедиться в том, что компоненты перегружаются только в 
случае изменения только тез #?-параметров от которых зависит компонент. см. параметры вызова.</b><br />
<a href="#!get_ID1=11&get_ID2=12&get_ID3=13&get_ID4=14">#?get_ID1=11&amp;get_ID2=12&amp;get_ID3=13&amp;get_ID4=14</a>,<br />
<a href="#!get_ID1=21&get_ID2=22&get_ID3=23&get_ID4=24">#?get_ID1=21&amp;get_ID2=22&amp;get_ID3=23&amp;get_ID4=24</a>,<br />
<a href="#!get_ID1=31&get_ID2=32&get_ID3=33&get_ID4=34">#?get_ID1=31&amp;get_ID2=32&amp;get_ID3=33&amp;get_ID4=34</a>,<br />
<a href="#!get_ID1=41&get_ID2=42&get_ID3=43&get_ID4=44">#?get_ID1=41&amp;get_ID2=42&amp;get_ID3=43&amp;get_ID4=44</a>,<br />

<?php }); // end of makePage?>