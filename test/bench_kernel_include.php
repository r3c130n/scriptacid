<?php namespace ScriptAcid;
$startKernelIncludeTime = microtime();
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/kernel.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';
$stopKernelIncludeTime = microtime();
$diffKernelIncludeTime = ($stopKernelIncludeTime - $startKernelIncludeTime);

ob_start();
App::setTitle('bench_kernel_include');
$startMakePage = microtime();
App::page(function(){ echo '{__bench_placeholder}'; });
$stopMakePage = microtime();
$fullBufferContents = ob_get_clean();


$diffMakePage = ($stopMakePage - $startMakePage);

echo str_replace(
	'{__bench_placeholder}',
	''
		.'kernel include time: '.$diffKernelIncludeTime.endl
		.'Application::makePage time: '.$diffMakePage.endl
	,
	$fullBufferContents
);


?>