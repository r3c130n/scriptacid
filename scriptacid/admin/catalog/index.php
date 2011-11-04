<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle("Панель управления :: Типы каталогов");
App::get()->makePage(function(&$arPageParams) {?>


<?php if(!Modules::includeModule('catalog')):
	ShowError("Модуль каталогов не утановен.");
else:?>


	<?php switch ($_GET["ACTION"]):
		case 'ADD':?>
			<h3><?php echo LANG("MODULE_CATALOG_TYPES_ADD_TITLE")?></h3>
			<?php ShowMsg()?>
			<?php App::callComponent(":catalog.type.add",
				"_admin", 
				Array(
					"FIELDS" => Array(
						"ID",
						"NAME",
						"SORT",
						"SID",
						"SECTION_NAME",
						"ELEMENT_NAME"
					),
				)
			);?>
		<?php break?>
		<?php case 'EDIT':?>
			<h3><?php echo LANG("MODULE_CATALOG_TYPES_EDIT_TITLE")?></h3>
			<?php ShowMsg()?>
			<?php App::callComponent(":catalog.type.add",
				"_admin", 
				Array(
					"FIELDS" => Array(
						"ID",
						"NAME",
						"SORT",
						"SID",
						"SECTION_NAME",
						"ELEMENT_NAME"
					),
					"ID" => $_GET['ID'],
				)
			);?>
		<?php break?>
		<?php case 'DELETE':?>
			<?php ShowMsg()?>
			<?php
				if(CatalogType::Delete($_GET["ID"])) {
					AddMsg("Тип успешно удалён");
					RedirectTo('/scriptacid/admin/catalog/');
				} else {
					ShowError("Ошибка при удалении типа каталога");
				}
			?>
			<p><a href="/scriptacid/admin/catalog/">Список типов</a></p>
		<?php break?>
		<?php default:?>
		<h3><?php echo LANG("MODULE_CATALOG_TYPES_TITLE")?></h3>
		<?php ShowMsg()?>
		<p>
			<a href="?ACTION=ADD"><?=LANG("MODULE_CATALOG_TYPES_ADD_TEXT")?></a>
		</p>
		<?php App::callComponent("system:catalog.type.list",
			"_admin", 
			Array(
				"SHOW_FIELDS" => Array(
					"ID",
					"NAME",
					"SORT",
					"SID",
					"SECTION_NAME",
					"ELEMENT_NAME"
				),
			)
		);?>
		<?php break;?>
	<?php endswitch?>
<?php endif?>

<?php }); // end of makePage?>