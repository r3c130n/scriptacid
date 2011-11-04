<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Панель управления - Настройки сайта");
App::get()->makePage(function(&$arPageParams) {?>


<p>
Здесь скоро будет страница настроек системы
</p>
<p>Будет возможность менять следующие параметры системы:</p>
<ul>
	<li>Заголовок сайта</li>
	<li>Описание сайта</li>
	<li>Название сайта</li>
	<li>Логотип сайта</li>
</ul>

<?php }); // end of makePage?>