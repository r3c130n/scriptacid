<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
 
$arTabs["main"] = Array(
	"ACTIVE",
	"NAME",
	"CATALOG_ID",
	"CATALOG_SECTION_ID",
	"CODE",
	"SORT",
);

$arTabs["preview"] = Array(
	"PREVIEW_PICTURE",
	"PREVIEW_TEXT_TYPE",
	"PREVIEW_TEXT",
);
$arTabs["detail"] = Array(
	"DETAIL_PICTURE",
	"DETAIL_TEXT_TYPE",
	"DETAIL_TEXT",
);

?>
<?php ShowError($arResult["ERRORS"])?>
<script type="text/javascript">

	if(window.ixedit){ixedit.deployed = true};
	if(window.jQuery){jQuery(function(){
		(function(){ var target = jQuery('div#catalog'); target.tabs({event:'click'}); })();
	})
	function showAddPropDialog() {
			$('div#addPropDiv').dialog({
				bgiframe: true,
				autoOpen: false,
				height: 250,
				modal: true,
				buttons: {
					'Сохранить': function() {
						$(this).dialog('close');
						return false;
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				},
				close: function() {
				}
			});
			$('div#addPropDiv').dialog('open');
	}
};
</script>
<form method="post" action="" name="add_catalog_type">
	<div id="catalog" style="width:100%">
		<ul>
			<li><a href="#main">Основное</a></li>
			<li><a href="#preview">Анонс</a></li>
			<li><a href="#detail">Детально</a></li>
			<li><a href="#props">Свойства</a></li>
		</ul>
<?php foreach($arTabs as $tab => $arTab):?>
	<div id="<?php echo $tab?>">
	<table class="styled" width="100%" style="margin:0;padding:0"><?// class="form-table">?>
	<?php foreach($arTab as $key):?>
			<?php $value = $arResult["FIELDS"][$key]?>
			<tr>
				<?php if ($key != "DETAIL_TEXT" AND $key != "PREVIEW_TEXT"):?>
					<td class="td-left" style="vertical-align: top"><?php echo LANG('MODULE_CATALOG_ELEMENTS_'.$key)?>:</td>
					<td class="td-right">
				<?php else:?>
					<th colspan="2"><?php echo LANG('MODULE_CATALOG_ELEMENTS_'.$key)?>:</th>
					</tr>
					<tr>
						<td colspan="2">
				<?php endif?>
					<?php switch ($value["TYPE"]):
						case 'text':?>
							<input type="text" name="<?php echo $key?>" size="90" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'hidden':?>
							<?php echo $value["VALUE"]?>
							<input type="hidden" name="<?php echo $key?>" size="60" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'checkbox':?>
							<input type="checkbox" name="<?php echo $key?>" value="Y" <?php echo $value["VALUE"] == "Y" ? 'checked=checked' : ''?>/>
						<?php break?>
						<?php case 'radio':?>
							<input type="radio" name="<?php echo $key?>" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'select':?>
							<select name="<?php echo $key?>" style="width: 390px">
								<?php foreach($value["VALUES"] as $id => $name):?>
									<option value="<?php echo $id?>" ><?php echo $name?><?php echo $value["VALUE"]?></option>
								<?php endforeach?>
							</select>
						<?php break?>
						<?php case 'file':?>
							<input type="file" name="<?php echo $key?>" size="60" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'textarea':?>
							<?php if($key == "DETAIL_TEXT" OR $key == "PREVIEW_TEXT"):?>
								<textarea name="<?php echo $key?>" cols="125" rows="25"><?php echo $value["VALUE"]?></textarea>
							<?php else:?>
								<textarea name="<?php echo $key?>" cols="90" rows="5"><?php echo $value["VALUE"]?></textarea>
							<?php endif?>
						<?php break?>
					<?php endswitch?>
				</td>
			</tr>
	<?php endforeach?>
	</table>
</div>
<?php endforeach?>
<div id="props">
	<a href="javascript:void(0)" onclick="showAddPropDialog()" style="color: #0788C3;">Добавить свойство</a><br />
	<div id="addPropDiv" style="display:none;">
		<form method="post" action="" name="add_prop">
			<table class="styled" width="100%" style="margin:0;padding:0">
				<tr>
					<td>Название:</td>
					<td><input type="text" name="NEW_PROP_NAME" value=""/><br /></td>
				</tr>
				<tr>
					<td>Тип:</td>
					<td><select name="NEW_PROP_TYPE">
							<option value="S">Строка</option>
							<option value="L">Список</option>
							<option value="N">Число</option>
							<option value="C">Чекбокс</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Значение по-умолчанию:</td>
					<td><input type="text" name="NEW_PROP_DEFAULT" value=""/></td>
				</tr>
			</table>
		</form>
	</div>
	<?if(!empty($arResult["PROPERTIES"])):?>
		<table class="styled" width="100%" style="margin:0;padding:0"><?// class="form-table">?>
			<tr>
				<th colspan="2"><b>Свойства элемента:</b></th>
			</tr>
			<?foreach ($arResult["PROPERTIES"] as $k => $arProp):?>
				<tr>
					<td><a href="#"><?php echo $arProp["NAME"]?></a></td>
					<td>
						<input type="text" name="PROPERTY[<?php echo $arProp["ID"]?>]" value="<?php echo $arProp["VALUE"]?>" size="90" />
					</td>
				</tr>
			<?endforeach?>
		</table>
	<?endif?>
</div>
	<div class="form-table-buttons">
		<?if ($arResult["MODE"] == "ADD"):?>
			<input type="submit" class="button" name="add_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_ADD_TEXT')?>"/>&nbsp;
		<?else:?>
			<input type="submit" class="button" name="edit_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_EDIT_TEXT')?>"/>&nbsp;
			<input type="submit" class="button" name="apply_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_APPLY_TEXT')?>"/>&nbsp;
		<?endif?>
		<button class="button" onClick="return RedirectTo('/admin/catalog/')"><?php echo LANG('MODULE_CATALOG_ELEMENTS_CANCEL_TEXT')?></button>
	</div>
</div>
</form>