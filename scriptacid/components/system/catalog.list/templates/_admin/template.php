<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if(!empty($arResult) AND !empty($arParams["SHOW_FIELDS"])):?>
<?php 
	$arDecorate = Array(
		"NAME" => '<a href="/scriptacid/admin/catalog/elements.php?TYPE=#CATALOG_TYPE_ID#&CATALOG_ID=#ID#">#NAME#</a>',
	);
?>
	<table>
		<thead>
			<tr>
			<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
				<th>
					<?php echo LANG('MODULE_CATALOG_'.$field)?>
				</th>
			<?php endforeach?>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<?php foreach ($arResult as $n => $arItem):?>
			<tr <?php echo $n%2 ? 'class="altrow"' : ''?>>
				<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
					<td>
						<?php if (array_key_exists($field, $arDecorate)):?>
							<?php echo GetStrTpl($arDecorate[$field], $arItem)?>
						<?php else:?>
							<?php echo $arItem[$field]?>
						<?php endif?>
					</td>
				<?php endforeach;?>
				<td><a href="?ACTION=EDIT&CATALOG_ID=<?php echo $arItem["ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>">Изменить</a> <a href="?ACTION=DELETE&CATALOG_ID=<?php echo $arItem["ID"]?>">Удалить</a></td>
			</tr>
		<?php endforeach?>
	</table>
<?php else:?>
	<p><?php echo LANG('NO_RESULT_TEXT')?></p>
<?php endif?>