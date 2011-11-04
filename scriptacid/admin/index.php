<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
App::get()->makePage(function(&$arPageParams) {?>

<?php SetTitle("Панель управления");?>
<p style="width:100%"><img src="/scriptacid/admin/images/r3c130n.jpg" width="120" height="120" alt="" class="float-left border" />
Вы находитесь в <b>Панели управления</b> сайтом. Здесь Вы можете менять настройки сайта, производить свои хитрые манипуляции 
с элементами сайта и многое другое..
</p>

<?php }); // end of makePage?>