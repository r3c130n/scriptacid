<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if(!empty($arResult["ITEMS"]) AND !empty($arParams["SHOW_FIELDS"])):?>
<?php foreach($arResult["ITEMS"] as $arItem) {
	if(!empty($arItem["CATALOG_SECTION_ID"])) {
		$parentSecID = $arItem["CATALOG_SECTION_ID"];
	}
}
$rs = CatalogSection::GetByID($parentSecID);
if ($arParent = $rs->GetNext()) {
	$parentID = $arParent["CATALOG_SECTION_ID"] === NULL ? 0 : $parentSecID;
}
$bShowUpBtn = true;

	$arDecorate = Array(
		"NAME" => '<a href="'.SYS_ROOT.'/admin/catalog/elements.php?TYPE=#CATALOG_TYPE#&CATALOG_ID=#CATALOG_ID#&SECTION_ID=#ID#">#NAME#</a>',
	);
?>
<?php elseif(!empty($arParams["SECTION_ID"]) AND $arParams["SECTION_ID"] != '0'):?>
<?php 
	$bShowUpBtn = true;
	$parentID = 0;
?>
<?php endif?>
<?php if($bShowUpBtn):?>
	<span style="margin-left: 50px; border: 1px solid; border-color:#D8EBF5 #D8EBF5 #B9DBEE; padding: 3px 25px 3px 25px">
		<a href="/scriptacid/admin/catalog/elements.php?TYPE=<?php echo $arParams["CATALOG_TYPE"]?>&CATALOG_ID=<?php echo $arParams["CATALOG_ID"]?>&SECTION_ID=<?php echo $parentID?>">Вверх</a>
	</span>
<?php endif;?>
	<table>
		<thead>
			<tr>
			<?php foreach ($arParams["FIELDS"] as $field):?>
				<th>
					<?php echo LANG('MODULE_CATALOG_ELEMENTS_'.$field)?>
				</th>
			<?php endforeach?>
				<th>&nbsp;</th>
			</tr>
		</thead>
<?php if(!empty($arResult["ITEMS"]) AND !empty($arParams["SHOW_FIELDS"])):?>
		<?php foreach ($arResult["ITEMS"] as $n => $arItem):?>
			<tr <?php echo $n%2 ? 'class="altrow"' : ''?>>
				<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
					<td>
						<?php if (array_key_exists($field, $arDecorate)):?>
							<img src="<?php echo App::getTEmplatePath()?>/images/folder.jpg" class="table-img" title="Раздел" />
							<?php echo GetStrTpl($arDecorate[$field], array_merge($arParams, $arItem))?>
						<?php else:?>
							<?php echo $arItem[$field]?>
						<?php endif?>
					</td>
				<?php endforeach;?>
				<td><a href="?ACTION=EDIT&CATALOG_ID=<?php echo $arItem["CATALOG_ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>&ID=<?php echo $arItem["ID"]?>">Изменить</a> <a href="?ACTION=DELETE&ID=<?php echo $arItem["ID"]?>">Удалить</a></td>
			</tr>
		<?php endforeach?>
<?php endif?>