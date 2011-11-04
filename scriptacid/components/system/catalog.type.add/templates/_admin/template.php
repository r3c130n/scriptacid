<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php echo ShowError($arResult["ERRORS"])?>
<?php if(!empty($arResult["FIELDS"])):?>
<form method="post" action="" name="add_catalog_type">
	<table class="form-table">
	<?php foreach($arResult["FIELDS"] as $key => $value):?>
		<tr>
			<td class="td-left"><?php echo LANG('CATALOG_TYPE_'.$key)?>:</td>
			<td class="td-right"><input type="text" id="<?php echo $key?>" name="<?php echo $key?>" size="60" value="<?php echo $value?>"/></td>
		</tr>
	<?php endforeach?>
		<tr>
			<td>&nbsp;</td>
			<td class="td-right">
				<?if ($arResult["MODE"] == "ADD"):?>
					<input type="submit" class="button" name="add_btn" value="<?php echo LANG('CATALOG_TYPE_ADD_TEXT')?>"/>&nbsp;
				<?else:?>
					<input type="submit" class="button" name="edit_btn" value="<?php echo LANG('CATALOG_TYPE_EDIT_TEXT')?>"/>&nbsp;
				<?endif?>
				<button class="button" onClick="return RedirectTo(<?php echo SYS_ROOT?>'/admin/catalog/')"><?php echo LANG('CATALOG_TYPE_CANCEL_TEXT')?></button>
			</td>
		</tr>
	</table>
</form>
<?php endif?>