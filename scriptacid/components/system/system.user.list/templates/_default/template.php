<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if(!empty($arResult) AND !empty($arParams["SHOW_FIELDS"])):?>
<?php 
	$arDecorate = Array(
		"LOGIN" => '<a href="/scriptacid/admin/users/user.php?ID=#ID#">#LOGIN#</a>',
	);
?>
	<table>
		<thead>
			<tr>
			<?php foreach ($arParams["SHOW_FIELDS"] as $field):?>
				<th>
					<?php echo LANG('SYTEM_USER_'.$field)?>
				</th>
			<?php endforeach?>
			</tr>
		</thead>
		<?foreach ($arResult['USERS'] as $n => $arItem):?>
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
			</tr>
		<?php endforeach?>
	</table>
<div class="paginator">
	<?php echo str_replace('#URL#', GetCurPage().'?PAGE=', $arResult['PAGINATOR'])?>
</div>
<?php endif?>