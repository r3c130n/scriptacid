<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
SetTitle("Завершение сеанса пользователя");
//App::get()->makePage(function(&$arPageParams) {?>

<?php
if (App::USER()->IsAuthorized() AND $_GET['logout'] == 'Y') {
	App::USER()->UnAuthorize();
	ShowMsg('Заходите к нам еще!');
	RedirectTo('/', 3000);
} else {
	ShowError('Вы не вошли');
}
?>

<?php // }); // end of makePage?>