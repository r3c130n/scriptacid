<?php namespace ScriptAcid;
define("DIRECT_LOAD_CLASSES", true);
$startSysinit = microtime(true);
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF": DIRECT_LOAD_CLASSES test.');

App::get()->arPageParams = array(
	"startSysinit" => $startSysinit
);
App::get()->makePage(function(&$arPageParams) {?>

	<?php
	$stopSysinit = microtime(true);
	echo $deltaSysinit = ($stopSysinit - $arPageParams["startSysinit"]);
	?>
	
	<p class="some-class">Тестовая версия ScriptACID CMF: DIRECT_LOAD_CLASSES test.</p>
	DIRECT_LOAD_CLASSES == <?php echo(DIRECT_LOAD_CLASSES?"true":"false")?>
	<br />
<?php }); // end of makePage?>
