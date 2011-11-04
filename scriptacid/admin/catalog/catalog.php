<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Панель управления :: Каталоги");
App::get()->makePage(function(&$arPageParams) {?>


<?php if(!Modules::includeModule('catalog')):
	ShowError("Модуль каталогов не утановен.");
else:?>
<?php switch ($_GET["ACTION"]):
	case 'ADD':?>
		<h3><?php echo LANG("MODULE_CATALOG_ADD_TITLE")?></h3>
		<?php ShowMsg()?>
		<?php Component::callComponent(":catalog.add",
			"_admin",
			Array(
				"FIELDS" => Array(
					"ID",
					"ACTIVE",
					"NAME",
					"CATALOG_TYPE_ID",
					"SORT",
					"SID",
					"CODE",
					"LIST_PAGE_URL",
					"DETAIL_PAGE_URL",
					"SECTION_PAGE_URL",
					"PICTURE",
					"DESCRIPTION",
					"DESCRIPTION_TYPE",
					"SECTIONS_NAME",
					"ELEMENTS_NAME",
					"SECTION_NAME",
					"ELEMENT_NAME",
					"SEO_DESCRIPTION",
					"SEO_KEYWORDS",
				),
				"TYPE" => $_GET["TYPE"]
			)
		);?>
	<?php break?>
	<?php case 'EDIT':?>
		<h3><?php echo LANG("MODULE_CATALOG_EDIT_TITLE")?></h3>
		<?php ShowMsg()?>
		<?php Component::callComponent(":catalog.add",
			"_admin",
			Array(
				"FIELDS" => Array(
					"ID",
					"ACTIVE",
					"NAME",
					"CATALOG_TYPE_ID",
					"SORT",
					"SID",
					"CODE",					
					"LIST_PAGE_URL",
					"DETAIL_PAGE_URL",
					"SECTION_PAGE_URL",
					"PICTURE",
					"DESCRIPTION",
					"DESCRIPTION_TYPE",
					"SECTIONS_NAME",
					"ELEMENTS_NAME",
					"SECTION_NAME",
					"ELEMENT_NAME",
					"SEO_DESCRIPTION",
					"SEO_KEYWORDS",
				),
				"ID" => $_GET['CATALOG_ID'],
				"TYPE" => $_GET["TYPE"]
			)
		);?>
	<?php break?>
	<?php case 'DELETE':?>
		<?php ShowMsg()?>
		<?
			if(Catalog::Delete($_GET["CATALOG_ID"])) {
				AddMsg("Каталог успешно удалён");
				RedirectTo('/scriptacid/admin/catalog/');
			} else {
				echo ShowError("Ошибка при удалении типа каталога");
			}
		?>
		<p><a href="/scriptacid/admin/catalog/">Список типов</a></p>
	<?php break?>
	<?php default:?>
	<?php $arCaralogType = CatalogType::GetByID($_GET["TYPE"]);?>
	<h3><?php echo str_replace('#TYPE#', $arCaralogType["NAME"], LANG("MODULE_CATALOG_TITLE"))?></h3>
	<?php ShowMsg()?>
	<p>
		<a href="?ACTION=ADD&TYPE=<?php echo sXss($_GET['TYPE'])?>"><?php echo LANG("MODULE_CATALOG_ADD_TEXT")?></a>
	</p>
	<?php Component::callComponent(":catalog.list",
		"_admin",
		Array(
			"SHOW_FIELDS" => Array(
				"ID",
				"NAME",
				"CATALOG_TYPE_ID",
				"SORT",
				"ACTIVE",
				"SID",
				"CODE",
			),
			"CATALOG_TYPE" => $_GET["TYPE"]
		)
	);?>
	<?php break;?>
<?php endswitch?>

<p>
	<a href="/scriptacid/admin/catalog/">Список типов</a>&nbsp;&nbsp;
</p>

<?php endif?>


<?php }); // end of makePage?>