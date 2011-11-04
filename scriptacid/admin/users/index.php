<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Панель управления :: Пользователи");
App::get()->makePage(function(&$arPageParams) {?>


<h3>Пользователи сайта</h3>

<?php echo Component::callComponent("system.user.list", 
	"", 
	Array(
		"SHOW_FIELDS" => Array(
			"ID",
			"LOGIN",
			"NAME",
			"LAST_NAME",
			"EMAIL",
		),
		"PAGE_COUNT" => 20
	)
);?>

<?php }); // end of makePage?>