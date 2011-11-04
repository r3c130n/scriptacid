<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
define("APPLICATION_INCLUDED", true);

require_once CORE_PATH_FULL.'/session.php';

// Определение языка сайта
Lang::getLangFiles(SYSTEM_LANG_PATH_FULL."/".LANG_ID);

if( file_exists(USR_PATH_FULL."/application_init.php") ) {
	require_once USR_PATH_FULL."/application_init.php";
}


/**
 *  Инициализация приложения происходит автоматически
 *  при первом обращении к классу Application
 *  (например след строке - App final класс который оборачивается над экземпляром Application).
 *  В будущем вообще необходимо исключить какую либо инициализацию в ядре.
 *  При использовании Скриптоцыда как библиотеки все должно происходить 
 *  через инициализацию в классе приложения(Application) Который можно унаследовать
 *  И там уже пределить и параметры БД и другие параметры.
 */
$APPLICATION = $APP = App::getInstance();
$APPLICATION->setTitle(Storage::get()->SITE_NAME);
$APPLICATION->setDescr($descr);
$DB = App::DB(); // $APP->DB; //App::get()->DB;
Storage::add("DB", $DB);

if (!isset($_SESSION['WEB_SERFER'])) {
	$_SESSION['WEB_SERFER'] = Array(
		"IP" => $_SERVER["REMOTE_ADDR"],
		"BROWSER" => $_SERVER["HTTP_USER_AGENT"],
		"WEB_ID" => md5(GetRandom(8).$_SERVER["REMOTE_ADDR"]),
	);
}
$_SESSION['WEB_SERFER']["LAST_ACTIVITY"] = time();

$USER = App::USER();
$bIsAdmin = $USER->IsAdmin();
Storage::add("USER", $USER);


// [Рудименты]
if( Storage::exists("APP_TEMPLATE") ) {
	App::get()->templateName = Storage::get("APP_TEMPLATE");
}
if( !Storage::exists("SITE_NAME") ) Storage::add("SITE_NAME", "");
if( !Storage::exists("SITE_DESCRIPTION") ) Storage::add("SITE_DESCRIPTION", "");
App::get()->setTitle(Storage::get()->SITE_NAME);
App::get()->setDescr(Storage::get()->SITE_DESCRIPTION);
// [/Рудименты]

// Задаем тип отображения для сессии
if( 
	@isset($_REQUEST["sacid_display_mode"])
	&& $bIsAdmin
) {
	switch( strtoupper($_REQUEST["sacid_display_mode"]) ) {
		case 'EDIT':
			$_SESSION['SACID_DISPLAY_MODE'] = 'EDIT';
			break;
		case 'NORMAL':
		default:
			$_SESSION['SACID_DISPLAY_MODE'] = 'NORMAL';
			break;
	}
}

// Определение режима отображения сайта (пользовательский/админский)
if (!defined('APP_DISPLAY_MODE')) {
	if (
		$bIsAdmin
		&& isset($_SESSION['SACID_DISPLAY_MODE'])
		&& $_SESSION['SACID_DISPLAY_MODE'] == 'EDIT'
	) {
		define("APP_DISPLAY_MODE", 'EDIT');
	}
	else {
		define("APP_DISPLAY_MODE", 'NORMAL');
	}
}

/**
 * Файл с пунктами меню администратора
 */
if ($bIsAdmin && APP_DISPLAY_MODE == 'NORMAL') {
	Panel::setItem('?sacid_display_mode=edit', 'Режим редактирования');
	Panel::setItem('/scriptacid/logout.php?logout=Y', 'Выход');
}
elseif ($USER->IsAdmin() && APP_DISPLAY_MODE == 'EDIT') {
	Panel::setItem('<js>ChgPageTitle(\''.$_SERVER['PHP_SELF'].'\')', 'Изменить заголовок');
	Panel::setItem('<br>');
	Panel::setItem('<js>CreatePage()', 'Создать страницу');
	Panel::setItem('<js>EditPage()', 'Изменить страницу');
	Panel::setItem('<js>DeletePage()', 'Удалить страницу');
	Panel::setItem('<br>');
	Panel::setItem('<js>CreateDir()', 'Создать раздел');
	Panel::setItem('<br>');
	Panel::setItem('?sacid_display_mode=normal', 'Режим просмотра');
}

// Проверяем права пользователя на доступ к данной папке
$bAccessPath = Permitions::CheckPathPerms(getCurDir());
define("ACCESS_PATH", $bAccessPath);

?>