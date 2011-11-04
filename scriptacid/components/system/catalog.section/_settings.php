<?php
$arComponentSettings = array(
	'DESCRIPTION' => array(
		'NAME' => 'Список элементов секции',
		'DESCRIPTION' => '',
		'COMPLEX' => 'N',
		'SORT' => '100',
		'GROUP' => array(
			'NAME' => 'Каталог',
			'CODE' => 'CATALOG'
			// "PARENT_GROUP"
		)
	),
	'PARAMETERS_GROUPS' => array(
		'MAIN_CATALOG_PARAMS' => array('NAME' => 'Основные параметры каталога', 'SORT' => 200),
	),
	'PARAMETERS' => array(
		'TYPE' => array(
			'NAME' => 'Тип каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'SORT' => 100,
			//'TYPE' => 'LIST',
			'TYPE' => 'STRING',
			'LIST_ITEMS' => array(
				
			),
			//'PERMANENT' => 'Y'
		),
		'CATALOG_ID' => array(
			'NAME' => 'Идентификатор каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'SORT' => 200,
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			//'PERMANENT' => 'Y'
		),
		'LIST_URL' => array(
			'NAME' => 'Ссылка на список',
			'GROUP' => 'LINK_TEMPLATES',
			'TYPE' => 'STRING',
		),
		'ELEMENT_URL' => array(
			'NAME' => 'Ссылка на детальное описание элемента',
			'GROUP' => 'LINK_TEMPLATES',
			'TYPE' => 'STRING',
			'SORT' => '1',
		),
	)
);
?>