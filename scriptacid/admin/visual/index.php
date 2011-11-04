<?php
namespace ScriptAcid; 
/**
 * @name Показывает форму редактирования параметров компонент
 * @example Запрашивается с помощью AJAX запроса
 * @author r3c130n
 * @deprecated не трогать
 */
if(!empty($_GET['show'])) {
	require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";

	switch ($_GET['show']) {
		case 'params':
			if (!empty($_GET['arParams']['componentUrl']) && is_dir($_SERVER["DOCUMENT_ROOT"] . $_GET['arParams']['componentUrl']) &&
					!empty($_GET['arParams']['templateUrl']) && is_dir($_SERVER["DOCUMENT_ROOT"] . $_GET['arParams']['templateUrl'])) {
				echo Component::GetParametersForm($_GET['arParams']['componentUrl'], $_GET['arParams']['templateUrl'], $_GET['arParams']);
			}
			break;
		case 'page-params':
				echo Face::GetPageParametersForm($_GET['arParams']['pageUrl'], $_GET['arParams']);
			break;
		default:
    		break;
	}
}