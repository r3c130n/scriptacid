<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
SetTitle('Проект "ScriptACID CMF"');
App::get()->makePage(function(&$arPageParams) {?>
<p class="some-class">Тест работы Плугинов ScriptACID CMF</p>
<?php 
	App::USER()->Authorize("1");
	
	plg::addPlugins("/test/plugin_test/plugin_dir1/", true);
	plg::addPlugins("/test/plugin_test/plugin_dir2/");
	plg::addPlugins("/test/plugin_test/plugin_dir3/");
	plg::printPluginDirs();
	plg::test1('param1', 'param2');
	plg::test2('param1', 'param2', 'param3');
	plg::test3('param1', 'param2', 'param3');
?>
<?php }); // end of makePage?>