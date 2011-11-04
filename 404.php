<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
App::get()->makePage(function() {
@define("ERROR_404","Y");
$_SERVER["REDIRECT_STATUS"] = 404;
header("HTTP/1.0 404 Not Found");
SetTitle("404 - HTTP not found");
?>
<?php ShowError("Страница не найдена")?>

<?php }); // end of makePage?>