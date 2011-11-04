<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php //d($arResult)?>
<?php if(!empty($arResult["ITEMS"]) AND !empty($arParams["SHOW_FIELDS"])):
	$arDecorate = Array(
		"NAME" => '<a href="/admin/catalog/elements.php?TYPE=#CATALOG_TYPE#&CATALOG_ID=#CATALOG_ID#&SECTION_ID=#ID#">#NAME#</a>',
	);
?>
	<table>
		<thead>
			<tr>
			<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
				<th>
					<?php echo LANG('MODULE_CATALOG_ELEMENTS_'.$field)?>
				</th>
			<?php endforeach?>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<?php foreach ($arResult["ITEMS"] as $n => $arItem):?>
			<tr <?php echo $n%2 ? 'class="altrow"' : ''?>>
				<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
					<td>
						<?php if (array_key_exists($field, $arDecorate)):?>
							<?php echo GetStrTpl($arDecorate[$field], array_merge($arParams, $arItem))?>
						<?php else:?>
							<?php echo $arItem[$field]?>
						<?php endif?>
					</td>
				<?php endforeach;?>
				<td><a href="?ACTION=EDIT&CATALOG_ID=<?php echo $arItem["CATALOG_ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>&ID=<?php echo $arItem["ID"]?>">Изменить</a> <a href="?ACTION=DELETE&ID=<?php echo $arItem["ID"]?>">Удалить</a></td>
			</tr>
		<?php endforeach?>
	</table>
<?php else:?>
	<p><?php echo LANG('NO_RESULT_TEXT')?></p>
<?php endif?>