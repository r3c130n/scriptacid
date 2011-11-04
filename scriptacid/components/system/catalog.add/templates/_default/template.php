<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

$arTabs["main"] = Array(
		"ACTIVE",
		"NAME",
		"CATALOG_TYPE_ID",
		"CODE",
		"SID",
		"SORT",
		"LIST_PAGE_URL",
		"DETAIL_PAGE_URL",
		"SECTION_PAGE_URL",
		"PICTURE",
		"DESCRIPTION_TYPE",
		"DESCRIPTION",

	);
$arTabs["other"] = Array(
	"SECTIONS_NAME",
	"SECTION_NAME",
	"ELEMENTS_NAME",
	"ELEMENT_NAME",
	"SEO_DESCRIPTION",
	"SEO_KEYWORDS"
);
?>
<?php echo ShowError($arResult["ERRORS"])?>
<?/*
<style type="text/css">
	body { font-size: 62.5%; }
	label, input { display:block; }
	input.text { margin-bottom:12px; width:95%; padding: .4em; }
	fieldset { padding:0; border:0; margin-top:25px; }
	h1 { font-size: 1.2em; margin: .6em 0; }
	div#users-contain {  width: 350px; margin: 20px 0; }
	div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
	div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	.ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
	.ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }
</style>
*/?>
<script type="text/javascript">

	if(window.ixedit){ixedit.deployed = true};
	if(window.jQuery){jQuery(function(){
		(function(){ var target = jQuery('div#catalog'); target.tabs({event:'click'}); })();
		$('div.settings-img').click(function(evt) {
			var divID = this.id;
			var prID = divID.replace(/settings_prop_img_/, '');
			var child = 'div#'+ divID.replace(/img_/, '');
			$(child).dialog({
				bgiframe: true,
				autoOpen: false,
				height: 200,
				modal: true,
				buttons: {
					'Сохранить': function() {
						var DEF_VAL = $('#DEFAULT_VALUE_' + prID);
						$('#settings_prop_' + prID + '_DEFAULT').val(DEF_VAL.val());
						$(this).dialog('close');
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				},
				close: function() {
				}
			});
			$(child).dialog('open');
		});
	})
};
		

</script>
<form method="post" action="" name="add_catalog_type">
	<div id="catalog" style="width:100%">
		<ul>
			<li><a href="#main">Основное</a></li>
			<li><a href="#properties">Свойства</a></li>
			<li><a href="#other">Разное</a></li>
		</ul>
<?php foreach($arTabs as $tab => $arTab):?>
	<div id="<?php echo $tab?>">
	<table class="styled" width="100%" style="margin:0;padding:0"><?// class="form-table">?>
	<?php foreach($arTab as $key):?>
		
			<?$value = $arResult["FIELDS"][$key]?>
			<tr>
				<td class="td-left"><?php echo LANG('MODULE_CATALOG_'.$key)?>:</td>
				<td class="td-right">
					<?switch ($value["TYPE"]):
						case 'text':?>
							<input type="text" name="<?php echo $key?>" size="60" value="<?php echo $value["VALUE"]?>"/>
						<?break?>
						<?case 'hidden':?>
							<?php echo $value["VALUE"]?>
							<input type="hidden" name="<?php echo $key?>" size="60" value="<?php echo $value["VALUE"]?>"/>
						<?break?>
						<?case 'checkbox':?>
							<input type="checkbox" name="<?php echo $key?>" value="Y" <?php echo $value["VALUE"] == "Y" ? 'checked=checked' : ''?>/>
						<?break?>
						<?case 'radio':?>
							<input type="radio" name="<?php echo $key?>" value="<?php echo $value["VALUE"]?>"/>
						<?break?>
						<?case 'select':?>
							<select name="<?php echo $key?>" style="width: 390px">
								<?php foreach($value["VALUE"] as $sid):?>
									<option value="<?php echo $sid?>"><?php echo $sid?></option>
								<?endforeach?>
							</select>
						<?break?>
						<?case 'file':?>
							<input type="file" name="<?php echo $key?>" size="60" value="<?php echo $value["VALUE"]?>"/>
						<?break?>
						<?case 'textarea':?>
							<textarea name="<?php echo $key?>" cols="57" rows="5"><?php echo $value["VALUE"]?></textarea>
						<?break?>
					<?endswitch?>
				</td>
			</tr>
		
	<?endforeach?>
	</table>
</div>
<?endforeach?>
	<div id="properties">
		<table class="styled" width="90%" style="margin:0;padding:0">
			<thead>
				<tr>
					<th>ID</th>
					<th>NAME</th>
					<th>SORT</th>
					<th>TYPE</th>
					<th>*</th>
					<th>M</th>
					<th>CODE</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($arResult["PROPERTIES"] as $k => $arProp):?>
					<tr>
						<td><input type="hidden" name="PROPERTY[<?php echo $k?>][ID]" value="<?php echo $arProp["ID"]?>" /><?php echo $arProp["ID"]?></td>
						<td><input type="text" name="PROPERTY[<?php echo $k?>][NAME]" value="<?php echo $arProp["NAME"]?>" size="25" /></td>
						<td><input type="text" name="PROPERTY[<?php echo $k?>][SORT]" value="<?php echo $arProp["SORT"]?>" size="3"/></td>
						<td>
							<select name="PROPERTY[<?php echo $k?>][PROPERTY_TYPE]" style="width: 70px">
								<?$arTypes = Catalog::GetPropTypes()?>
								<?php foreach($arTypes as $type => $typeName):?>
									<option value="<?php echo $type?>" <?php echo $arProp["PROPERTY_TYPE"] == $type ? 'selected=selected':''?>><?php echo $typeName?></option>
								<?endforeach?>
							</select>
						</td>
						<td>
							<input type="checkbox" name="PROPERTY[<?php echo $k?>][IS_REQUIRED]" value="Y" <?php echo $arProp["IS_REQUIRED"]=='Y'?'checked=checkes':''?>/>
						</td>
						<td>
							<input type="checkbox" name="PROPERTY[<?php echo $k?>][MULTIPLE]" value="Y" <?php echo $arProp["MULTIPLE"]=='Y'?'checked=checkes':''?>/>
						</td>

						<td><input type="text" name="PROPERTY[<?php echo $k?>][CODE]" value="<?php echo $arProp["CODE"]?>" size="25" /></td>
						<td>
							<div title="Настройки" id="settings_prop_img_<?php echo $k?>"  class="settings-img" >&nbsp;</div>
							<div id="settings_prop_<?php echo $k?>" style="display:none; text-align: left">
								DEFAULT:<br />
								<input type="text" name="DEFAULT_VALUE_<?php echo $k?>" id="DEFAULT_VALUE_<?php echo $k?>" value="<?php echo $arProp["DEFAULT_VALUE"]?>" size="35" /><br />
							</div>
							<input type="hidden" id="settings_prop_<?php echo $k?>_DEFAULT" name="PROPERTY[<?php echo $k?>][DEFAULT_VALUE]" value="" />
						</td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	</div>
	<div class="form-table-buttons">
		<?php if($arResult["MODE"] == "ADD"):?>
			<input type="submit" class="button" name="add_btn" value="<?php echo LANG('MODULE_CATALOG_ADD_TEXT')?>"/>&nbsp;
		<?php else:?>
			<input type="submit" class="button" name="edit_btn" value="<?php echo LANG('MODULE_CATALOG_EDIT_TEXT')?>"/>&nbsp;
			<input type="submit" class="button" name="apply_btn" value="<?php echo LANG('MODULE_CATALOG_APPLY_TEXT')?>"/>&nbsp;
		<?php endif?>
		<button class="button" onClick="return RedirectTo('/scriptacid/admin/catalog/')"><?php echo LANG('MODULE_CATALOG_CANCEL_TEXT')?></button>
	</div>
</div>
</form>