<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Панель управления :: Пользователи");
App::get()->makePage(function(&$arPageParams) {?>

<?php if (intVal($_GET["ID"]) > 0):?>
<?php Component::callComponent("system.user.detail", 
	"", 
	Array(
		"SET_TITLE" => "Y",
		"TITLE_TEXT" => "Панель управления :: Профиль пользователя",
		"USER_ID" => intVal($_GET["ID"])
	)
);?>
<?php endif?>

<?php }); // end of makePage?>