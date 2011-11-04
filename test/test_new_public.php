<?php
namespace asdf {
	class FirstPageClass {
	function firstPage() { ?>
	
	<p class="some-class">Тестовая версия ScriptACID CMF</p>
	<?php
		//soem test
		//Storage::define("USER", new User);
		//d(Storage::get()->USER);
		\ScriptAcid\App::USER()->Authorize("1");
	?>
	UserID = <?php echo \ScriptAcid\App::USER()->GetID();?>
	
	<?php }}
}
namespace ScriptAcid {
	
$closure = function() { ?>
	
	<p class="some-class">Тестовая версия ScriptACID CMF</p>
	<?php
		//soem test
		//Storage::define("USER", new User);
		//d(Storage::get()->USER);
		\ScriptAcid\App::USER()->Authorize("1");
	?>
	UserID = <?php echo \ScriptAcid\App::USER()->GetID();?>
	
<?php };
	
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF"');
App::get()->makePage('asdf\FirstPageClass::firstPage');
//App::get()->makePage($closure);

}
?>