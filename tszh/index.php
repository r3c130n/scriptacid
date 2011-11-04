<?php
namespace ScriptAcid;
$startPageTime = microtime(true);
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";
Storage::add("startPageTime", $startPageTime);

App::get()->makePage(function(&$arPageParams) {?>


<script type="text/javascript">
$(document).ready(function() {
	//$('#tszh-edit-form').hide();
	$('a#tszh-add-button').click(function() {
		$('#tszh-edit-form').toggle("slow");
		return false;
	});
	<?php if($_GET["ACTION"] != "EDIT" || intval($_GET["ID"])==0):?>
	$('#tszh-edit-form').toggle("fast");
	<?php endif;?>
});
</script>
<a href="#" id="tszh-add-button">Добавить новое ТСЖ</a>


<p><?php ShowMsg()?></p>
<div id="tszh-edit-form">
	<?php Component::callComponent("catalog.element.add",
		"",
		Array(
			"FIELDS" => Array(
				"ID",
				"ACTIVE",
				"NAME",
				//"CATALOG_SECTION_ID",
				"CATALOG_ID",
				//"SORT",
				//"CODE",
				//"PREVIEW_PICTURE",
				"PREVIEW_TEXT",
				//"PREVIEW_TEXT_TYPE",
				//"DETAIL_PICTURE",
				//"DETAIL_TEXT",
				//"DETAIL_TEXT_TYPE",
				//"TAGS"
			),
			"TYPE" => "orion_locations",
			"CATALOG_ID" => "3",
			"ID" => $_GET["ID"],
			
			"LIST_URL" => "/tszh/",
			"ELEMENT_URL" => "/tszh/index.php?ID=#ID#",
		)
	);?>

</div>

<h1>catalog.section</h1>
<?php Component::callComponent("catalog.section",
	"",
	Array(
		"TYPE" => "orion_locations",
		"CATALOG_ID" => "3",
		"CACHE_OFF" => "Y",
		//"=SECTION_ID" => $_GET["SECTION_ID"]
	)
);?>


<?php 
$startPageTime = Storage::get("startPageTime");
$stopPageTime = microtime(true);
$delta = $stopPageTime - $startPageTime;
echo ("<br /><br />Общее время выполнения: ".$delta."<br /><br />");
?>

<?php }); // end of makePage?>