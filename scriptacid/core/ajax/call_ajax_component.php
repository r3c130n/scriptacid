<?php namespace ScriptAcid;
define('COMPONENT_AJAX_CALL', true);
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";

if( !strlen($_REQUEST["component_call_key"]) ) {
	echo "error: component not set.";
	unset($_SESSION[SESS_COMPONENTS_CALL_KEYS]);
	exit;
} 

$componentCallKey = $_REQUEST["component_call_key"];

if( !@isset($_SESSION[SESS_COMPONENTS_CALL_KEYS][$componentCallKey]) ) {
	echo "error: no component params.";
	//d(array($componentAjaxCallKey));
	//d($_SESSION[SESS_COMPONENTS_CALL_KEYS]);
	exit;
}
$arComponentRequest = $_SESSION[SESS_COMPONENTS_CALL_KEYS][$componentCallKey];
if(!is_array($arComponentRequest)) {
	echo "error: no component params.";
	exit;
}
//sunset($_SESSION[SESS_COMPONENTS_CALL_KEYS][$componentAjaxCallKey]);

$arComponentRequest["PARAMS"]["COMPONENT_AJAX_READY"] = 'Y';
$arComponentRequest["PARAMS"]["COMPONENT_CALL_KEY"] = $componentCallKey;

//d($arComponentRequest);
//exit;

//ob_start();
//d($arComponentRequest["PARAMS"], 'arPARAMS');
App::callComponent($arComponentRequest["NAME"], $arComponentRequest["TEMPLATE"], $arComponentRequest["PARAMS"]);

//$contents = ob_get_clean();

//$contents = str_replace('<script type="text/javascript">', '<sacid:javascript>', $contents);
//$contents = str_replace('</script>', '</sacid:javascript>', $contents);
//echo $contents;

?>