<?php
namespace ScriptAcid;
/**
 * ЯДРО СИСТЕМЫ
 */
/* Определение констант */

if(
	is_dir($_SERVER["DOCUMENT_ROOT"])
	&&
	is_link($_SERVER["DOCUMENT_ROOT"])
) {
	define("DOC_ROOT",		realpath($_SERVER["DOCUMENT_ROOT"])); // Путь к корню сайта
}
else {
	define("DOC_ROOT",		$_SERVER["DOCUMENT_ROOT"]); // Путь к корню сайта
}

// Cистема
define("SYS_ROOT",				"/scriptacid");
define("SYS_ROOT_FULL",			DOC_ROOT.SYS_ROOT);

// Папка для загрузки файлов
define("UPLOAD_PATH",			"/upload");
define("UPLOAD_PATH_FULL",		DOC_ROOT.UPLOAD_PATH);

// Папка с ядром, прологом и эпилогом
define("CORE_PATH",				SYS_ROOT."/core");
define("CORE_PATH_FULL",		DOC_ROOT.CORE_PATH);

// Конфигурационные файлы и кастомные скрипты
define("USR_PATH",				SYS_ROOT."/usr_include");
define("USR_PATH_FULL",			DOC_ROOT.USR_PATH);

// Компоненты
define("COMPONENTS_PATH",		SYS_ROOT."/components");
define("COMPONENTS_PATH_FULL",	DOC_ROOT.COMPONENTS_PATH);

// Модули
define("MODULES_PATH",			SYS_ROOT."/modules");
define("MODULES_PATH_FULL",		DOC_ROOT.MODULES_PATH);

// Кэш
define("CACHE_PATH",			SYS_ROOT."/cache");
define("CACHE_PATH_FULL",		DOC_ROOT.CACHE_PATH);

// Различные библиотеки
define("LIB_PATH",				SYS_ROOT."/lib");
define("LIB_PATH_FULL",			DOC_ROOT.LIB_PATH);

// Шаблоны приложения
define("TEMPLATES_PATH",		SYS_ROOT."/templates");
define("TEMPLATES_PATH_FULL",	DOC_ROOT.TEMPLATES_PATH);

// Языковые файлы
define("SYSTEM_LANG_PATH",		SYS_ROOT."/lang");
define("SYSTEM_LANG_PATH_FULL",	DOC_ROOT.SYSTEM_LANG_PATH);



define("ENDL", "<br />\n");
define("endl", ENDL);

define("KERNEL_INCLUDED", true);

// Регистриуем autoload классов
spl_autoload_register(function($className) {
	return \ScriptAcid\Modules::autoloadClasses($className);
});

require_once CORE_PATH_FULL."/corelib.php";

require_once USR_PATH_FULL."/config.php";

// define not defined
if( !defined("DEBUG_MODE") ) 				define("DEBUG_MODE", 			false);
if( !defined("DIRECT_LOAD_CLASSES") )		define("DIRECT_LOAD_CLASSES",	false);
if( !defined("DB_TYPE") ) 					define("DB_TYPE", 				"MYSQL");
if( !defined("DBCONN_NAME") ) 				define("DBCONN_NAME", 			strtolower(DB_TYPE));
if( !defined("FS_PERMISSION_FILE") )		define("FS_PERMISSION_FILE", 	"644");
if( !defined("FS_PERMISSION_DIR") )			define("FS_PERMISSION_DIR", 	"755");
if( !defined("SESS_COMPONENTS_CALL_KEYS"))	define("SESS_COMPONENTS_CALL_KEYS", "SESS_COMPONENTS_CALL_KEYS");
if( !defined('CHARSET') )					define('CHARSET', 'UTF-8');

header('Content-Type: text/html; charset='.CHARSET);

// Задаем свой обработич всех ошибок РНР.
ErrorHandlers::setErrorHandler();

//Debug
if( DEBUG_MODE === true ) {
	require_once CORE_PATH_FULL."/debug.php";	
}

//require_once 'StaticStorageTest.php';

require_once CORE_PATH_FULL."/session.php";

/**
 * Классы будут подключены или сразу или будут подключаться автоматически (__autoload)
 * в зависимости от константы DIRECT_LOAD_CLASSES
 * Если не определена, то false;
 */ 
Modules::init(DIRECT_LOAD_CLASSES);
/**
 * В режиме прямой загрузки класов
 * файлы будут подключаться сразу в вызове Modules::setAutoloadClasses()
 * Если мы имеем зависимости (extends) классов,
 * то все зависимости необходимо указать тут,
 * поскольку билиотка, в которой могут лежить зависимости,
 * инициализируется позже.
 * 
 *  В последних двух параметрах `Modules::setAutoloadClasses` 
 *  `Modules::global_set` можно не указывать. Они по дефолту им и равны. 
 */
//echo "<b>MAIN MODULE: SET AUTOLOAD CLASSES PATH LIST:</b><br />";
Modules::setAutoloadClasses(false,array(
		"Lang"					=> CORE_PATH."/lib/lib.lang.php",
	)
	, true // true - Будет загружено директлоадом при любых условиях.
	, Modules::global_set // true - Отладить.
);
$DB_TYPE = strtolower(DB_TYPE);
Modules::setAutoloadClasses(false, array(
		"Application"			=> CORE_PATH."/lib/class.Application.php",
		"AbstractDatabase"		=> CORE_PATH."/lib/lib.AbstractDatabase.php",
		"AbstractDBResult"		=> CORE_PATH."/lib/lib.AbstractDatabase.php",
		"AbstractSQuery"		=> CORE_PATH."/lib/lib.AbstractDatabase.php",
		"DatabasePostgreSQL"	=> CORE_PATH."/lib/lib.postgresql.php",
		"DBResultPostgreSQL"	=> CORE_PATH."/lib/lib.postgresql.php",
		"SQueryPostgreSQL"		=> CORE_PATH."/lib/lib.postgresql.php",
		"DatabaseMySQL"			=> CORE_PATH."/lib/lib.mysql.php",
		"DBResultMySQL"			=> CORE_PATH."/lib/lib.mysql.php",
		"SQueryMySQL"			=> CORE_PATH."/lib/lib.mysql.php",
		"Database"				=> CORE_PATH."/lib/inc.Database.".$DB_TYPE.".php",
		"DBResult"				=> CORE_PATH."/lib/inc.Database.".$DB_TYPE.".php",
		"SQuery"				=> CORE_PATH."/lib/inc.Database.".$DB_TYPE.".php",
		"Table"					=> CORE_PATH."/lib/lib.visual.php",
	)
	, Modules::global_set	// true - Будет загружено директлоадом при любых условиях.
	, Modules::global_set	// true - Отладить.
	// Modules::global_set - взять из конфига
);



/**
 * автоматическая инициализация библиотеки.
 */
if( !Modules::includeLibFiles(CORE_PATH."/lib") ) {
	die('Не удалось подключить основную библиотеку'.endl);
}
if( !Modules::includeLibFiles(CORE_PATH."/lib.external") ) {
	die('Не удалось основную библиотеку со сторонним кодом'.endl);
}
if( !Modules::includeLibFiles(LIB_PATH) ) {
	die('Не удалось подключить пользовательскую библиотеку'.endl);
}
plugins::addPlugins(LIB_PATH);

//d(Modules::getClassesArray());

define("PHP_MAGIC_QUOTES_ACTIVE", get_magic_quotes_gpc());
define("PHP_REAL_ESCAPE_STRING_EXISTS", function_exists( "mysql_real_escape_string"));
?>