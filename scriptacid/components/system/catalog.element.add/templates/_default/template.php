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
$arTabs["other"] = Array(
	"TAGS",
);
$arTabsHide;

foreach($arTabs as $tabName => &$arTab) {
	$hideThisTab = true;
	foreach($arTab as $key => &$tabField) {
		if(in_array($tabField, $arParams["FIELDS"])) {
			$hideThisTab = false;
		}
	}
	if($hideThisTab) {
		$arTabsHide[] = $tabName;
	}
}
?>
<?php //ShowError($arResult["ERRORS"])?>
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
			<?php if(!@in_array("main", $arTabsHide)):?><li><a href="#main">Основное</a></li><?php endif?>
			<?php if(!@in_array("preview", $arTabsHide)):?><li><a href="#preview">Описание</a></li><?php endif?>
			<?php if(!@in_array("detail", $arTabsHide)):?><li><a href="#detail">Детально</a></li><?php endif?>
			<?php if(!@in_array("other", $arTabsHide)):?><li><a href="#other">Разное</a></li><?php endif?>
		</ul>
<?php foreach($arTabs as $tab => $arTab):
	if(@in_array($tab, $arTabsHide)) continue;
?>
	<div id="<?php echo $tab?>">
	<table class="styled" width="100%" style="margin:0;padding:0"><?php // class="form-table">?>
	<?php foreach($arTab as $key):
		if(!@in_array($key, $arParams["FIELDS"])) continue;
		$value = $arResult["FIELDS"][$key];
	?>
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
							<input type="text" name="<?php echo $key?>" style="width: 100%;" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'hidden':?>
							<?php echo $value["VALUE"]?>
							<input type="hidden" name="<?php echo $key?>" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'checkbox':?>
							<input type="checkbox" name="<?php echo $key?>" value="Y" <?php echo $value["VALUE"] == "Y" ? 'checked=checked' : ''?>/>
						<?php break?>
						<?php case 'radio':?>
							<input type="radio" name="<?php echo $key?>" value="<?php echo $value["VALUE"]?>"/>
						<?php break?>
						<?php case 'select':?>
							<select name="<?php echo $key?>" style="width: 90%">
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
								<textarea name="<?php echo $key?>" rows="15" style="width: 100%;"><?php echo $value["VALUE"]?></textarea>
							<?php else:?>
								<textarea name="<?php echo $key?>" rows="15" style="width: 100%;"><?php echo $value["VALUE"]?></textarea>
							<?php endif?>
						<?php break?>
					<?php endswitch?>
				</td>
			</tr>
	<?php endforeach?>
	<?php if($tab == "main"):?>
		<?php if(!empty($arResult["PROPERTIES"])):?>
			<tr>
				<th colspan="2"><b>Свойства элемента:</b></th>
			</tr>
			<?php foreach ($arResult["PROPERTIES"] as $k => $arProp):?>
				<tr>
					<td><?php echo $arProp["NAME"]?></td>
					<td><input type="text" style="width: 100%;" name="PROPERTY[<?php echo $arProp["ID"]?>]" value="<?php echo $arProp["VALUE"]?>" /></td>
				</tr>
			<?php endforeach?>
		<?php endif?>
	<?php endif?>
	</table>
</div>
<?php endforeach?>
	
	<div class="form-table-buttons">
		<?php if ($arResult["MODE"] == "ADD"):?>
			<input type="submit" class="button" name="add_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_ADD_TEXT')?>"/>&nbsp;
		<?php else:?>
			<input type="submit" class="button" name="edit_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_EDIT_TEXT')?>"/>&nbsp;
			<input type="submit" class="button" name="apply_btn" value="<?php echo LANG('MODULE_CATALOG_ELEMENTS_APPLY_TEXT')?>"/>&nbsp;
		<?php endif?>
		<?php /*/?><button class="button" onClick="return RedirectTo('/scriptacid/admin/catalog/')"><?php echo LANG('MODULE_CATALOG_ELEMENTS_CANCEL_TEXT')?></button><?php  //*/?>
	</div>
</div>
</form>