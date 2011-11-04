<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
App::get()->makePage(function(&$arPageParams) {?>

<?php if(intval($_GET["ELEMENT_ID"])>0):?>

<?php echo intval($_GET["ELEMENT_ID"]);?>
	
<?php else:?>
<?php ShowError("Елемент не найден.");?>
<?php endif;?>

<?php }); // end of makePage?>