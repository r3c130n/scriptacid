<?php namespace ScriptAcid;

$arComponentSettings = array(
	'DESCRIPTION' => array(
		'NAME' => 'Компонента меню.',
		'DESCRIPTION' => 'Не работает в режиме AJAX.',
		'COMPLEX' => 'N',
		'SORT' => '100',
		'GROUP' => array(
			'NAME' => 'Каталог',
			'CODE' => 'CATALOG'
			// "PARENT_GROUP"
		)
	),
	'PARAMETERS' => array(
		"TYPE" => array(
			'NAME' => 'Тип меню',
			'GROUP' => 'MAIN_PARAMETERS',
			'TYPE' => 'STRING',
		),
		'COMPONENT_AJAX_MODE' => array(
			'NAME' => 'Режим AJAX',
			'GROUP' => 'MAIN_PARAMS',
			'TYPE' => 'HIDDEN',
			'VALUE' => 'OFF',
		),
	),
);

?>