<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Новости');
App::get()->makePage(function(&$arPageParams) {?>

	<p>Новостей нет. Здесь закомментирован код вызова компонент (как пример)</p>
	<?php if(intVal($_GET["ELEMENT_ID"])):?>
	<?php App::callComponent("system:catalog.element", "", Array(
			"CATALOG_TYPE" => "news",
			"CATALOG_ID" => 1,
			"SECTION_CODE" => $_GET["SECTION_CODE"],
			"ELEMENT_ID" => $_GET["ELEMENT_ID"],
		)
	);?>
	<?php else:?>
	<?php App::callComponent("system:catalog.section", "news", Array(
			"CATALOG_TYPE" => "news",
			"CATALOG_ID" => 1,
			"SORT_ORDER" => 'DESC',
			"SORT_FIELD" => 'ID',
			"SECTION_CODE" => $_GET["SECTION_CODE"],
			"CURRENT_PAGE" => $_GET['PAGE'],
			"PAGE_COUNT" => 5,
		)
	);?>
	<?php endif//*/?>

<?php }); // end of makePage?>