<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";

//sleep(2);

if(!App::USER()->IsAdmin()) {
	die('error: access denied.');
}

$componentName = $_GET['component_name'];
$templateName = $_GET['template_name'];

$arComponentSettings = ComponentTools::getSettingsByName($componentName, $templateName);
//$arTemplateSettings = Component::getTemplateSettingsByName($componentName, $templateName);

if(!$arComponentSettings) {
	die('error: illegal component name "'.$componentName.'"');
}

ComponentTools::setParametersListForEachGroup($arComponentSettings);
if($arComponentSettings === false) {
	exit('error: incorrect component name.');	
}

//d($_GET, '$_GET');
//d($_POST, '$_POST');

$arCurrentParams = array();
if(is_array($_POST['current_params'])) {
	$arCurrentParams = $_POST['current_params'];
}

$arHiddenParams = array();

// Задаем текущие значения
foreach($arComponentSettings['PARAMETERS'] as $keyParameter => &$arParameter) {
	$arParameter['CURRENT_VALUE'] = null;
	if(@isset($arCurrentParams[$keyParameter])) {
		$arParameter['CURRENT_VALUE'] = $arCurrentParams[$keyParameter];
	}
	if($arParameter['TYPE'] == 'HIDDEN') {
		$arHiddenParams[$keyParameter] = $arParameter;
	}
}

// Получаем список шаблонов компонента
$arComponentSettings['COMPONENT']['TEMPLATES_LIST'] = ComponentTools::_getTemplatesList($arComponentSettings['COMPONENT']);

//if(false) {
//d($arComponentSettings, '$arComponentSettings');
//d($arComponentSettings['PARAMETERS'], '$arComponentSettings[PARAMETERS]');
//d($arComponentSettings['PARAMETERS_GROUPS'], '$arComponentSettings[PARAMETERS_GROUPS]');
//}


//$arComponentSettings['PARAMETERS']['COMPONENT_AJAX_MODE']['LIST_SETTINGS']['MULTIPLE'] = 'Y';
//$arComponentSettings['PARAMETERS']['COMPONENT_AJAX_MODE']['LIST_SETTINGS']['RADIO'] = 'Y';
//d($arComponentSettings['PARAMETERS'], 'PARAMETERS');
?>

<form action="" method="post">
<table width="100%">
	<tr class="sacid-cmp-param-group-title">
		<td colspan="2">
			Компонент: <b><?php echo $arComponentSettings['COMPONENT']['NAMESPACE'].':'.$arComponentSettings['COMPONENT']['NAME'];?></b>
			<?php //d($arComponentSettings, '$arComponentSettings');?>
		</td>
	</tr>
	<tr>
		<td width="40%">Шаблон компонента:</td>
		<td>
			<select class="template_name" name="template[name]">
				<?php foreach($arComponentSettings['COMPONENT']['TEMPLATES_LIST'] as $arOneOfTemplates):
					$oneOfTemplateName = '';
					$oneOfTemplatePlacement = '';
					$oneOfTemplateSelected = '';
					if( @isset($arOneOfTemplates['DESCRIPTION']['NAME']) ) {
						$oneOfTemplateName = '['.$arOneOfTemplates['NAME'].'] '.$arOneOfTemplates['DESCRIPTION']['NAME'];
					}
					else {
						$oneOfTemplateName = '['.$arOneOfTemplates['NAME'].']';
					}
					if($arOneOfTemplates['PLACEMENT'] == 'app_template') {
						$oneOfTemplatePlacement = 'шаблон';
					}
					else {
						$oneOfTemplatePlacement = 'системный';
					}
					if($arOneOfTemplates['NAME'] == $arComponentSettings['TEMPLATE']['NAME']) {
						$oneOfTemplateSelected = 'selected ';
					}
				?>
				<option value="<?php echo $arOneOfTemplates['NAME']?>" <?php echo $oneOfTemplateSelected;?>><?php echo $oneOfTemplateName;?> (<?php echo $oneOfTemplatePlacement;?>)</option>
				<?php endforeach;?>
			</select>

			<input type="hidden" name="template[skin]" value="default" />
		</td>
	</tr>

	<?php if(true):?>
	<tr>
		<td width="40%">Скин шаблона компонента:</td>
		<td>
			<select class="template_skin" name="template[skin]">
				<?php
				if(is_array($arComponentSettings['TEMPLATE']['SKINS_LIST'])):
					foreach($arComponentSettings['TEMPLATE']['SKINS_LIST'] as $skinCode => &$skinName):
						$activeSkin = '';
						if($skinCode == $arComponentSettings['TEMPLATE']['SKIN']) {
							$activeSkin = 'selected';
						}
				?>
				<option value="<?php echo $skinCode;?>" <?php echo $activeSkin;?>><?php echo $skinName;?>(<?php echo $skinCode;?>)</option>
				<?php
					endforeach;
				endif;?>
			</select>
		</td>
	</tr>
	<?php endif;?>
<?php
foreach($arComponentSettings['PARAMETERS_GROUPS'] as $paramGroupCodeName => &$arParamGroup):
	if(
		$arParamGroup['PARAMETERS_COUNT']<1 || $paramGroupCodeName == 'HIDDEN' || $paramGroupCodeName == 'EXPERIMENTAL') {
		continue;	
	}
	?>
	<tr class="sacid-cmp-param-group-title">
		<td colspan="2"><?php echo $arParamGroup['NAME'];?></td>
	</tr>
	<?php
	foreach($arParamGroup['PARAMETERS_LIST'] as &$keyParameter):
		$arParameter = &$arComponentSettings['PARAMETERS'][$keyParameter];
		$arParameter['TYPE'] = strtoupper($arParameter['TYPE']);
		$bPermanent = false;
		$strPermanent = '';
		if($arParameter['PERMANENT'] == 'Y') {
			$bPermanent = true;
			$strPermanent = 'disabled';
		}
	?>
	<?php if(false):?>
	<tr>
		<td colspan="2">
		<?php
			d($keyParameter);
			d($arParameter, '$arParameter');
		?>
		</td>
	</tr>
	<?php endif?>
	<?php ///////////////////// HIDDEN //////////////////////?>
	<?php if($arParameter['TYPE'] == 'HIDDEN'):
		continue;
	?>
	<?php ///////////////////// STRING //////////////////////?>
	<?php elseif( $arParameter['TYPE'] == 'STRING'):
		if($bPermanent) {
			if( strlen(trim($arParameter['VALUE']))>0 ) {
				$strCurrentValue = $arParameter['VALUE'];
			}
			elseif(strlen(trim($arParameter['DEFAULT'])) ) {
				$strCurrentValue = $arParameter['DEFAULT'];
			}
			else {
				$strCurrentValue = null;
			}
		}
		else {
			if( strlen(trim($arParameter['CURRENT_VALUE']))>0 ) {
				$strCurrentValue = $arParameter['CURRENT_VALUE'];
			}
			elseif( strlen(trim($arParameter['DEFAULT']))>0 ) {
				$strCurrentValue = $arParameter['DEFAULT'];
			}
			elseif(strlen(trim($arParameter['VALUE'])) ) {
				$strCurrentValue = $arParameter['VALUE'];
			}
			else {
				$strCurrentValue = null;
			}
		}
	?>
	<tr>
		<td width="40%"><?php echo $arParameter['NAME'];?>:</td>
		<td>
			<input type="text" <?php echo $strPermanent;?> name="parameters[<?php echo $keyParameter;?>]" value="<?php echo $strCurrentValue?>" />
		</td>
	</tr>
	<?php unset($strCurrentValue);?>
	<?php ///////////////////// LIST //////////////////////?>
	<?php elseif( $arParameter['TYPE'] == 'LIST'):?>
	<tr>
		<td width="40%"><?php echo $arParameter['NAME'];?>:</td>
		<?php ///////// LIST RADIO ////////?>
		<?php if( $arParameter['LIST_SETTINGS']['RADIO'] == 'Y' ):?>
		<td>
			<?php ///////// LIST RADIO MULTIPLE ////////?>
			<?php if($arParameter['LIST_SETTINGS']['MULTIPLE'] == 'Y'):?>
				<?php foreach($arParameter['LIST_ITEMS'] as $itemName => &$itemValue):
					$strItemSelected = '';
					if($bPermanent) {
						if( is_array($arParameter['VALUE']) ) {
							if(in_array($itemName, $arParameter['VALUE'])) {
								$strItemSelected = 'checked';
							}
						} 
						elseif( is_array($arParameter['DEFAULT']) ) {
							if(in_array($itemName, $arParameter['DEFAULT'])) {
								$strItemSelected = 'checked';
							}	
						}
					}
					else {
						if(isset($arParameter['CURRENT_VALUE']) && in_array($itemName, $arParameter['CURRENT_VALUE'])) {
							$strItemSelected = 'checked';
						}
					}
					
				?>
				<input type="checkbox" name="parameters[<?php echo $keyParameter?>][]" <?php echo $strPermanent;?> <?php echo $strItemSelected;?> value="<?php echo $itemName?>" id="list-chkbx-<?php echo $keyParameter;?>-<?php echo $itemName?>" />
				<label for="list-chkbx-<?php echo $keyParameter;?>-<?php echo $itemName?>"><?php echo $itemValue?></label>
				<br />
				<?php endforeach; unset($strItemSelected);?>
			<?php ///////// LIST RADIO ////////?>
			<?php else:?>
				<?php foreach($arParameter['LIST_ITEMS'] as $itemName => &$itemValue):
					$strItemSelected = '';
					if($bPermanent) {
						// single element check
						if(is_array($arParameter['VALUE'])) {
							$arParameter['VALUE'] = $arParameter['VALUE'][0];
						}
						if(is_array($arParameter['DEFAULT'])) {
							$arParameter['DEFAULT'] = $arParameter['DEFAULT'][0];
						}
						// set selected
						if($itemName == $arParameter['VALUE']) {
							$strItemSelected = 'checked';
						}
						elseif($itemName == $arParameter['DEFAULT']) {
							$strItemSelected = 'checked';
						}
					}
					else {
						if(is_array($arParameter['CURRENT_VALUE'])) {
							$arParameter['CURRENT_VALUE'] = $arParameter['CURRENT_VALUE'][0];
						}
						if($itemName == $arParameter['CURRENT_VALUE']) {
							$strItemSelected = 'checked';
						}
					}
				?>
				<input type="radio" name="parameters[<?php echo $keyParameter?>]" <?php echo $strPermanent;?> <?php echo $strItemSelected;?> value="<?php echo $itemName?>" id="list-radio-<?php echo $keyParameter;?>-<?php echo $itemName?>" />
				<label for="list-radio-<?php echo $keyParameter;?>-<?php echo $itemName?>"><?php echo $itemValue?></label>
				<br />
				<?php endforeach; unset($strItemSelected);?>
			<?php endif;?>
		</td>
		<?php ///////// JUST A LIST ////////?>
		<?php else:?>
		<td>
			<?php ///////// LIST MULTIPLE ////////?>
			<?php if($arParameter['LIST_SETTINGS']['MULTIPLE'] == 'Y'):
				if(@isset($arParameter['LIST_SETTINGS']['LINES'])) {
					$arParameter['LIST_SETTINGS']['LINES'] = intval($arParameter['LIST_SETTINGS']['LINES']);
				}
				else {
					$arParameter['LIST_SETTINGS']['LINES'] = 0;
				}
			?>
				<select <?php echo $strPermanent;?> multiple<?php if($arParameter['LIST_SETTINGS']['LINES']>0):?> size="<?php echo $arParameter['LIST_SETTINGS']['LINES']?>"<?php endif;?> name="parameters[<?php echo $keyParameter;?>][]">
				<?php foreach($arParameter['LIST_ITEMS'] as $itemName => &$itemValue):
					$strItemSelected = '';
					if($bPermanent) {
						if( is_array($arParameter['VALUE']) ) {
							if(in_array($itemName, $arParameter['VALUE'])) {
								$strItemSelected = 'selected';
							}
						} 
						elseif( is_array($arParameter['DEFAULT']) ) {
							if(in_array($itemName, $arParameter['DEFAULT'])) {
								$strItemSelected = 'selected';
							}	
						}
					}
					else {
						if(in_array($itemName, $arParameter['CURRENT_VALUE'])) {
							$strItemSelected = 'selected';
						}
					}
				?>
					<option <?php echo $strItemSelected;?> value="<?php echo $itemName?>"><?php echo $itemValue?></option>
				<?php endforeach; unset($strItemSelected);?>
				</select>
			<?php ///////// SIMPLE LIST ////////?>
			<?php else:?>
				<select <?php echo $strPermanent;?> name="parameters[<?php echo $keyParameter;?>]">
				<?php foreach($arParameter['LIST_ITEMS'] as $itemName => &$itemValue):
					$strItemSelected = '';
					if($bPermanent) {
						// single element check
						if(is_array($arParameter['VALUE'])) {
							$arParameter['VALUE'] = $arParameter['VALUE'][0];
						}
						if(is_array($arParameter['DEFAULT'])) {
							$arParameter['DEFAULT'] = $arParameter['DEFAULT'][0];
						}
						// set selected
						if($itemName == $arParameter['VALUE']) {
							$strItemSelected = 'selected';
						}
						elseif($itemName == $arParameter['DEFAULT']) {
							$strItemSelected = 'selected';
						}
					}
					else {
						if(is_array($arParameter['CURRENT_VALUE'])) {
							$arParameter['CURRENT_VALUE'] = $arParameter['CURRENT_VALUE'][0];
						}
						if($itemName == $arParameter['CURRENT_VALUE']) {
							$strItemSelected = 'selected';
						}
					}
				?>
					<option <?php echo $strItemSelected;?> value="<?php echo $itemName?>"><?php echo $itemValue?></option>
				<?php endforeach; unset($strItemSelected);?>
				</select>
			<?php endif;?>
		</td>
		<?php endif;?>
	</tr>
	<?php else:?>
	
	<?php endif;?>
	<?php endforeach;?>
<?php endforeach;?>
</table>
<?php foreach($arHiddenParams as $hiddenParamName => &$arHiddenParam):?>
	<input type="hidden" name="parameters[<?php echo $hiddenParamName?>]" value="<?php echo $arHiddenParam['VALUE']?>" />
<?php endforeach;?>
</form>

<?php // d($_POST);?>
