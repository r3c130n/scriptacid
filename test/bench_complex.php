<?php namespace ScriptAcid;
define('PAGE_FINISH_STREAM', true);
$startKernelIncludeTime = microtime();
//require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/kernel.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';
$stopKernelIncludeTime = microtime();
$diffKernelIncludeTime = ($stopKernelIncludeTime - $startKernelIncludeTime);

ob_start();
App::setTitle('bench_complex');
$startMakePage = microtime();
App::page(function(){ ?>
<?php //*/?>
<?php Bench::startTime(":catalog.section");?>
<?php App::callComponent(":catalog.section","",	array(
	"TYPE" => "orion_locations",
	"CATALOG_ID" => "3",
	"CACHE_OFF" => "Y",
	"COMPONENT_AJAX_MODE" => "N",
	//"=SECTION_ID" => $_GET["SECTION_ID"]
));?>
<p>Время: <?php echo Bench::stopTime(":catalog.section");?></p>


<?php Bench::startTime(":catalog.element.add@_default");?>
<?php App::callComponent(":catalog.element.add", "_default", array(
	"COMPONENT_AJAX_MODE" => "N",
	"FIELDS" => Array(
		"ID",
		"ACTIVE",
		"NAME",
		"CATALOG_SECTION_ID",
		"CATALOG_ID",
		"SORT",
		"CODE",
		"PREVIEW_PICTURE",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_PICTURE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"TAGS"
	),
	"TYPE" => "orion_locations",
	"CATALOG_ID" => "3",
	"ID" => '{%_GET[ID]}',
	"ACTION" => '{%_GET[ACTION]}',
	//"hashget_ID1" => '{%_GET[get_ID1]}',
	//"hashget_ID2" => '{%_GET[get_ID2]}',
	//"hashget_ID3" => '{%_GET[get_ID3]}',
	//"hashget_ID4" => '{%_GET[get_ID4]}',
	"LIST_URL" => "/test/test_ajax_component_call.php",
	"ELEMENT_URL" => "/test/test_ajax_component_call.php?ID=#ID#",
));?>
<p>Время: <?php echo Bench::stopTime(":catalog.element.add@_default");?></p>
<?php //*/?>
{__bench_placeholder}
<?php });
$stopMakePage = microtime();
$fullBufferContents = ob_get_clean();


$diffMakePage = ($stopMakePage - $startMakePage);
echo str_replace(
	'{__bench_placeholder}',
	''
		.'application.php include time: '.$diffKernelIncludeTime.endl
		.'Application::makePage time: '.$diffMakePage.endl
		.'Total time: '.($diffKernelIncludeTime + $diffMakePage)
	,
	$fullBufferContents
);


?>