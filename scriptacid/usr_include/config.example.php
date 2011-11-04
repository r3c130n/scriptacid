<?php namespace ScriptAcid;
if(!defined('KERNEL_INCLUDED') || KERNEL_INCLUDED!==true)die();
	/**
	 * КОНФИГУРАЦИОННЫЙ ФАЙЛ
	 */

	// Locale
	setlocale(LC_ALL, 'ru_RU.UTF-8');
	// Error report: no Notices, no Deprecated
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	// Set Location
	//ini_set('date.timezone', 'Asia/Krasnoyarsk');
	date_default_timezone_set('Asia/Krasnoyarsk');

	define('CHARSET', 'UTF-8');
	
	//Database connection
	define('DB_TYPE',				'MYSQL');
	define('DBCONN_NAME',			'mysql');
	//define('DB_TYPE',				'POSTGRESQL');
	//define('DBCONN_NAME',			'pgsql');
	define('FS_PERMISSION_FILE',	'666');
	define('FS_PERMISSION_DIR',		'777');

	// Security salt. !Don't change after installation.
	define('SALT',					'some very chaotic string like this: sS(DF8sud9f8s((S(SHBdjfksks3829u8SDF&*');
	// Debug mode
	define('DEBUG_MODE',			false);

	// Загружать ли все классы модулей и классы в папке lib сразу или использовать __autoload.
	define('DIRECT_LOAD_CLASSES',	false);

	define('CACHE_DATA_ENABLED', false);
	define('CACHE_HTML_ENABLED', false);
	
	// Need only for ScriptAcid API developers.
	// {{{
		define('_LIB_LOAD_DEBUG',	false);
		//Default site's template folder name
		define('_DEFAULT_APPLICATION_TEMPLATE',			'_default');
		define('_DEFAULT_APPLICATION_TEMPLATE_SKIN',	'default');
		define('_APP_DEFAULT_COMPONENT_CLASS',			__NAMESPACE__.'\\Component');
		define('_DEFAULT_COMPONENT_TEMPLATE',			'_default');
		define('_DEFAULT_COMPONENT_TEMPLATE_SKIN',		'default');
		define('_DEFAULT_COMPONENT_NAMESPACE',			'system');

	// }}}

	// This is an information about site.
	// TODO: remove from config into the site definition logic
	// {{{
		Storage::add('SITE_NAME',				'scriptacid');
		Storage::add('SITE_DESCRIPTION',		'Супер пупер scriptacid сайтег');
	    // TODO: сделать в админке свой шаблон.
	    // Пока так: если мы в админке - используем шаблон main

		if (substr($_SERVER['REQUEST_URI'], 0, 14) == '/scriptacid/admin/') {
			Storage::add('APP_TEMPLATE', 'main');
		}
		else {
			Storage::add('APP_TEMPLATE', '_default');
		}

	    // Определяем язык и шаблон
		define('LANG_ID', 'ru');
	// }}}
?>