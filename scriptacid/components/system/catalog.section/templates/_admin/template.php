<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php //d($arResult)?>
<?php if(!empty($arResult["ITEMS"]) AND !empty($arParams["SHOW_FIELDS"])):?>
<?php 
	$arDecorate = Array(
		"NAME" => '<a href="'.SYS_ROOT.'/admin/catalog/elements.php?TYPE=#CATALOG_TYPE_ID#&CATALOG_ID=#CATALOG_ID#&ID=#ID#&ACTION=EDIT">#NAME#</a>',
	);
?>
		<?php foreach ($arResult["ITEMS"] as $n => $arItem):?>
			<tr <?php echo $n%2 ? 'class="altrow"' : ''?>>
				<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
					<td>
						<?php if (array_key_exists($field, $arDecorate)):?>
							<img src="<?php echo App::get()->getTemplatePath()?>/images/elem.jpg" class="table-img" title="Элемент" />
							<?php echo GetStrTpl($arDecorate[$field], $arItem)?>
						<?php else:?>
							<?php echo $arItem[$field]?>
						<?php endif?>
					</td>
				<?php endforeach;?>
				<td>
					<a href="?ACTION=EDIT&CATALOG_ID=<?php echo $arItem["CATALOG_ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>&ID=<?php echo $arItem["ID"]?>">Изменить</a>
					<a href="?ACTION=DELETE&CATALOG_ID=<?php echo $arItem["CATALOG_ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>&ID=<?php echo $arItem["ID"]?>">Удалить</a>
				</td>
			</tr>
		<?php endforeach?>
<?php endif?>
</table>