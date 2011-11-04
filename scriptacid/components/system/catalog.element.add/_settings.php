<?php namespace ScriptAcid;

$arComponentSettings = array(
	'DESCRIPTION' => array(
		'NAME' => 'Форма добавления/редактирования элемента',
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
		'MAIN_CATALOG_PARAMS' => array('NAME' => 'Основные параметры каталога', 'SORT' => 100),
	),
	'PARAMETERS' => array(
		'TYPE' => array(
			'NAME' => 'Тип каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			//'TYPE' => 'LIST',
			'TYPE' => 'STRING',
			'LIST_ITEMS' => array(
				
			),
			//'PERMANENT' => 'Y'
		),
		'CATALOG_ID' => array(
			'NAME' => 'Идентификатор каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'TYPE' => 'STRING',
			'DEFAULT' => '',
			//'PERMANENT' => 'Y'
		),
		'ID' => array(
			'NAME' => 'Идентификатор элемента каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'TYPE' => 'STRING',
		),
		'ACTION_PARAMETER' => array(
			'NAME' => 'Действие',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'TYPE' => 'STRING',
			'DEFAULT' => '{%_GET[ACTION]}'
		),
		'FIELDS' => array(
			'NAME' => 'Поля каталога',
			'GROUP' => 'MAIN_CATALOG_PARAMS',
			'TYPE' => 'LIST',
			'LIST_ITEMS' => array(
				'ID' => 'Идентификатор элемента [ID]',
				'ACTIVE' => 'ACTIVE',
				'NAME' => 'Имя [NAME]',
				'CATALOG_SECTION_ID' => 'Идентификатор секции [CATALOG_SECTION_ID]',
				'CATALOG_ID' => 'Идентификатор каталога [CATALOG_ID]',
				'SORT' => 'Сортировка [SORT]',
				'CODE' => 'Мнемонический код [CODE]',
				'PREVIEW_PICTURE' => 'Изображение для анонса [PREVIEW_PICTURE]',
				'PREVIEW_TEXT' => 'Текст анонса [PREVIEW_TEXT]',
				'PREVIEW_TEXT_TYPE' => 'Тип текста анонса [PREVIEW_TEXT_TYPE]',
				'DETAIL_PICTURE' => 'Детальное изображение [DETAIL_PICTURE]',
				'DETAIL_TEXT' => 'Детальный текст [DETAIL_TEXT]',
				'DETAIL_TEXT_TYPE' => 'Тип детальгного текста [DETAIL_TEXT_TYPE]',
				'TAGS' => 'Теги [TAGS]',
			),
			'LIST_SETTINGS' => array(
				'RADIO' => 'Y',
				'MULTIPLE' => 'Y',
				'LINES' => 4
			),
			//test//
			//'PERMANENT' => 'Y',
			//'VALUE' => Array(
			//	//"ID",
			//	"ACTIVE",
			//	"NAME",
			//	//"CATALOG_SECTION_ID",
			//	"CATALOG_ID",
			//	//"SORT",
			//	"CODE",
			//	"PREVIEW_PICTURE",
			//	"PREVIEW_TEXT",
			//	//"PREVIEW_TEXT_TYPE",
			//	"DETAIL_PICTURE",
			//	"DETAIL_TEXT",
			//	//"DETAIL_TEXT_TYPE",
			//	//"TAGS"
			//),
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
		),
	),
);

?>