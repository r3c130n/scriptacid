<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Админка::Элементы каталога");
Modules::includeModule("catalog");
App::get()->makePage(function(&$arPageParams) {?>


<?php switch ($_GET["ACTION"]):
	case 'ADD_SECTION':?>
		<h3><?php echo LANG("MODULE_CATALOG_SECTION_ADD_TITLE")?></h3>
		<?php ShowMsg()?>
		<?php App::callComponent(":catalog.section.add",
			"_admin",
			Array(
				"FIELDS" => Array(
					"ID",
					"ACTIVE",
					"NAME",
					"CATALOG_SECTION_ID",
					"CATALOG_ID",
					"SORT",
					"CODE",
					"PREVIEW_PICTURE",
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"DETAIL_PICTURE",
					"DETAIL_TEXT",
					"DETAIL_TEXT_TYPE",
				),
				"TYPE" => $_GET["TYPE"],
				"CATALOG_ID" => $_GET['CATALOG_ID'],
			)
		);?>
		<?php break;
	case 'ADD':?>
		<h3><?php echo LANG("MODULE_CATALOG_ELEMENTS_ADD_TITLE")?></h3>
		<?php ShowMsg()?>
		<?php App::callComponent(":catalog.element.add",
			"_admin",
			Array(
				"FIELDS" => Array(
					"ID",
					"ACTIVE",
					"NAME",
					"CATALOG_SECTION_ID",
					"CATALOG_ID",
					"SORT",
					"CODE",
					"PREVIEW_PICTURE",
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"DETAIL_PICTURE",
					"DETAIL_TEXT",
					"DETAIL_TEXT_TYPE",
					"TAGS"
				),
				"TYPE" => $_GET["TYPE"],
				"CATALOG_ID" => $_GET['CATALOG_ID'],
			)
		);?>
	<?php break?>
	<?php case 'EDIT':?>
		<h3><?php echo LANG("MODULE_CATALOG_ELEMENTS_EDIT_TITLE")?></h3>
		<?php ShowMsg()?>
		<?php App::callComponent(":catalog.element.add",
			"_admin",
			Array(
				"FIELDS" => Array(
					"ID",
					"ACTIVE",
					"NAME",
					"CATALOG_SECTION_ID",
					"CATALOG_ID",
					"SORT",
					"CODE",
					"PREVIEW_PICTURE",
					"PREVIEW_TEXT",
					"PREVIEW_TEXT_TYPE",
					"DETAIL_PICTURE",
					"DETAIL_TEXT",
					"DETAIL_TEXT_TYPE",
					"TAGS"
				),
				"ID" => $_GET['ID'],
				"CATALOG_ID" => $_GET['CATALOG_ID'],
				"TYPE" => $_GET["TYPE"]
			)
		);?>
	<?php break?>
	<?php case 'DELETE':?>
		<?php ShowMsg()?>
		<?php 
			if(CatalogElement::Delete($_GET["ID"])) {
				AddMsg("Элемент успешно удалён");
				RedirectTo(
					'/scriptacid/admin/catalog/element.php'
					.'?CATALOG_ID='.$_GET["CATALOG_ID"].'&TYPE='.$_GET["TYPE"].'&ID='.$_GET["ID"]
				);
			} else {
				echo ShowError("Ошибка при удалении элемента каталога");
			}
		?>
		<p><a href="/scriptacid/admin/catalog/">Список типов</a></p>
	<?php break?>
		
	<?php default:?>
		
	<?php $arCaralogType = CatalogType::GetByID($_GET["TYPE"]);?>
	<h3><?php echo str_replace('#TYPE#', $arCaralogType["NAME"], LANG("MODULE_CATALOG_TITLE"))?></h3>
	<?php ShowMsg()?>
	<p>
		<a href="?ACTION=ADD&TYPE=<?php echo sXss($_GET['TYPE'])?>&CATALOG_ID=<?php echo sXss($_GET['CATALOG_ID'])?>"><?php echo LANG("MODULE_CATALOG_ELEMENTS_ADD_TEXT")?></a>&nbsp;&nbsp;&nbsp;
		<a href="?ACTION=ADD_SECTION&TYPE=<?php echo sXss($_GET['TYPE'])?>&CATALOG_ID=<?php echo sXss($_GET['CATALOG_ID'])?>"><?php echo LANG("MODULE_CATALOG_SECTION_ADD_TEXT")?></a>
	</p>
	<?php Component::callComponent(":catalog.section.list",
		"_admin",
		Array(
			"SECTION_ID" => $_GET["SECTION_ID"],
			"CATALOG_ID" => $_GET["CATALOG_ID"],
			"=CATALOG_TYPE" => $_GET["TYPE"],
			"FIELDS" => Array(
				"ID",
				"ACTIVE",
				"NAME",
				"CATALOG_SECTION_ID",
				"CATALOG_ID",
				"SORT",
				"CODE",
				"PREVIEW_PICTURE",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"DETAIL_PICTURE",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"TAGS"
			),
		)
	);?>
	<?php Component::callComponent(":catalog.section",
		"_admin",
		Array(
			"CATALOG_TYPE" => $_GET["TYPE"],
			"CATALOG_ID" => intVal($_GET["CATALOG_ID"]),
			"=SECTION_ID" => $_GET["SECTION_ID"]
		)
	);?>
	<?php break;?>
<?php endswitch?>

<p>
	<a href="/scriptacid/admin/catalog/">Список типов</a>&nbsp;&nbsp;
	<a href="/scriptacid/admin/catalog/catalog.php?TYPE=<?php echo $_GET["TYPE"]?>">Список каталогов</a>
</p>

<?php }); // end of makePage?>