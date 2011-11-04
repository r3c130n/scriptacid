<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php ShowError($arResult["ERRORS"])?>
<form method="post" action="" enctype="multipart/form-data">
<table>
<?php foreach($arResult['FIELDS'] as $FID => $arField):?>
	<?php if(in_array($FID, $arParams['FIELDS'])):?>
		<tr>
			<td valign="top">
				<?php $LABEL = isset($arParams['CUSTOM_LABEL_' . $FID]) ? $arParams['CUSTOM_LABEL_' . $FID] : ''?>
				<?php if(!empty($LABEL)):?><?php echo $LABEL?><?php echo in_array($FID, $arParams['REQUIRED']) ? '<span class="required">*</span>' : ''?>:<?else:?>
					<?php echo is_numeric($FID) ? $arField['NAME'] : LANG('CATALOG_ELEMENT_ADD_LABEL_' . $FID)?><?php echo in_array($FID, $arParams['REQUIRED']) ? '<span class="required">*</span>' : ''?>:
				<?php endif?>
			</td>
		<?php if(!is_numeric($FID)):?>
			<td><?php 
			switch ($FID) {
				case 'CATALOG_SECTION_ID':
					$value = $_POST[$FID];
					?>
					<select name="CATALOG_SECTION_ID" >
						<?php foreach($arResult['SECTIONS_TREE'] as $secID => $secName):?>
							<option value="<?php echo $secID?>" <?php echo $value == $secID ? 'selected="selected"' : ''?>><?php echo $secName?></option>
						<?php endforeach?>
					</select>
					<?php 
					break;
				case 'PREVIEW_TEXT':
				case 'DETAIL_TEXT':
					$value = $_POST[$FID];
					?><textarea name="<?php echo $FID?>" cols="70" rows="10"><?php echo $value?></textarea><?php 
					break;
				case 'PREVIEW_PICTURE':
				case 'DETAIL_PICTURE':
					$value = $_POST['PROPERTY'][$arField['CODE']];
					?><input type="file" name="<?php echo $FID?>" size="70"/><?php 
					break;
				case 'NAME':
				default:
					$value = $_POST[$FID];
					?><input type="text" name="<?php echo $FID?>" size="70" value="<?php echo $value?>"/><?php 
					break;
			}
			?></td>
		<?php else:?>
			<td><?php 
			switch ($arField['TYPE']) {
				case 'L':
					$values = $_POST[$FID];
					/*?>
					<select name="PROPERTY[<?php echo $FID?>]" >
						<?foreach($arResult['SECTIONS_TREE'] as $secID => $secName):?>
							<option value="<?php echo $secID?>" <?php echo $value == $secID ? 'selected="selected"' : ''?>><?php echo $secName?></option>
						<?endforeach?>
					</select><?*/
					break;
				case 'F':
					if ($arField['MULTIPLE'] == "Y") {
						$values = $_POST['PROPERTY'][$arField['CODE']];
						$values = empty($values) ? Array('','','','','') : $values;
						foreach ($values as $value) {
							?><input type="file" name="PROPERTY[<?php echo $FID?>][]" size="70"/><br /><?
						}
					} else {
						$value = $_POST['PROPERTY'][$arField['CODE']];
						?><input type="file" name="PROPERTY[<?php echo $FID?>]" size="70"/><?
					}
					break;
				case 'S':
				case 'N':
				default:
					if ($arField['MULTIPLE'] == "Y") {
						$values = $_POST['PROPERTY'][$arField['CODE']];
						$values = empty($values) ? Array('','','','','') : $values;
						if (!is_array($values)) {
							$tmp = $values;
							$values = Array($values,'','','','');
						}
						foreach ($values as $value) {
							?><input type="text" name="PROPERTY[<?php echo $FID?>][]" size="70" value="<?php echo $value?>"/><br /><?php 
						}
					} else {
						$value = $_POST['PROPERTY'][$arField['CODE']];
						?><input type="text" name="PROPERTY[<?php echo $FID?>]" size="70" value="<?php echo $value?>"/><?php 
					}
					break;
			}
			?></td>
		<?php endif?>
		</tr>
	<?php endif?>
<?php endforeach?>
		<tr>
			<td></td>
			<td><input type="submit" name="add_<?php echo $arParams['BTN_NAME']?>" value="<?php echo $arParams['ADD_BTN_LABEL']?>"/></td>
		</tr>
</table>
</form>