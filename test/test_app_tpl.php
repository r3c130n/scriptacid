<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
App::page(function(&$arPageParams) {?>

<b><?php echo App::get()->getTemplateName()?></b><br />
<b><?php echo App::get()->getTemplateSkin()?></b><br />

<?php },false); // end of makePage?>
<?php App::setTitle("Тестируем работу шаблонов и скинов");?>

<?php  App::connectTemplate("_default:skin1");?>
<?php // App::connectTemplate(":skin2");?>
<?php // App::connectTemplate("app_template:skin1");?>
<?php // App::get()->connectTemplate("../../../../../index.php\0");?>
<?php // App::setTemplateName("_admin"); // Сначала задаем шаблон?>
<?php // App::setTemplateName(":skin1"); // Потом отдельно скин?>
<?php // App::setTemplateName("_admin:skin1"); // Или все сразу?>
<?php // App::setTemplateName("some_wrong_name:skin1"); // скин _не_ будет задан?>
<?php // App::connectTemplate();?>
