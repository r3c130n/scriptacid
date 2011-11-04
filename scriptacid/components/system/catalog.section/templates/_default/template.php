<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php //d($arResult)?>
<?php if(!empty($arResult["ITEMS"]) AND !empty($arParams["SHOW_FIELDS"])):?>
<?php 
	if($arParams['COMPONENT_AJAX_MODE'] == 'AJAX') {
		$arDecorate = Array(
			//"NAME" => '<a href="/scriptacid/admin/catalog/elements.php?TYPE=#CATALOG_TYPE_ID#&CATALOG_ID=#CATALOG_ID#&ID=#ID#&ACTION=EDIT">#NAME#</a>',
			"NAME" => '<a href="#!ID=#ID#&ACTION=EDIT">#NAME#</a>',
		);
	}
	else {
		$arDecorate = Array(
			//"NAME" => '<a href="/scriptacid/admin/catalog/elements.php?TYPE=#CATALOG_TYPE_ID#&CATALOG_ID=#CATALOG_ID#&ID=#ID#&ACTION=EDIT">#NAME#</a>',
			"NAME" => '<a href="?ID=#ID#&ACTION=EDIT">#NAME#</a>',
		);
	}
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
							<?php echo GetStrTpl($arDecorate[$field], $arItem)?>
						<?php else:?>
							<?php echo $arItem[$field]?>
						<?php endif?>
					</td>
				<?php endforeach;?>
				<?php /*/?><td><a href="?ACTION=EDIT&CATALOG_ID=<?php echo $arItem["CATALOG_ID"]?>&TYPE=<?php echo $arItem["CATALOG_TYPE_ID"]?>&ID=<?php echo $arItem["ID"]?>">Изменить</a> <a href="?ACTION=DELETE&ID=<?php echo $arItem["ID"]?>">Удалить</a></td><?php //*/?>
				<?php //*/?><td><a href="?ACTION=EDIT&ID=<?php echo $arItem["ID"]?>">Изменить</a> <a href="?ACTION=DELETE&ID=<?php echo $arItem["ID"]?>">Удалить</a></td><?php //*/?>
			</tr>
		<?php endforeach?>
	</table>
<?php else:?>
	<p><?php echo LANG('NO_RESULT_TEXT')?></p>
<?php endif?>