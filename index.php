<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF"');
App::get()->makePage(function(&$arPageParams) {?>

<p class="some-class">Тестовая версия ScriptACID CMF</p>
<?php
	//soem test
	//Storage::define("USER", new User);
	//d(Storage::get()->USER);
	App::USER()->Authorize("1");
?>
UserID = <?php echo App::USER()->GetID();?>

<?php }); // end of makePage?>