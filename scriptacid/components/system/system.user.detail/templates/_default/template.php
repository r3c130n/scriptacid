<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if(!empty($arResult["USER"])):?>
<?php 
	$arTabs["main"] = Array(
		"ID" => "text", 
		"LOGIN" => "input", 
		"NAME" => "input", 
		"LAST_NAME" => "input",  
		"PASSWORD" => "password", 
		"PASSWORD_RETYPE" => "password", 
	);
	$arUser = $arResult["USER"];
	unset($arResult["USER"]);
?>
<script type="text/javascript">
	if(window.ixedit){ixedit.deployed = true};
	if(window.jQuery){jQuery(function(){
		(function(){ var target = jQuery('div#user-profile'); target.tabs({event:'click'}); })();
	})};
</script>
<h3>Пользователь: <?php echo $arUser["NAME"]?> <?php echo $arUser["LOGIN"]?> <?php echo $arUser["LAST_NAME"]?></h3>
	<form method="post" action="">
	<div id="user-profile">
		<ul>
			<li><a href="#main">Основное</a></li>
			<li><a href="#personal">Личные данные</a></li>
			<li><a href="#groups">Группы</a></li>
		</ul>
		<div id="main">
			<table class="form-table">
			<?php foreach($arTabs["main"] as $field => $type):?>
				<tr>
					<td class="td-left">
						<b><?php echo LANG('SYTEM_USER_'.$field)?>:</b>
					</td>
					<td class="td-right">
						<?php switch($type) {
							case 'text':
								echo $arUser[$field];
								break;
							case 'input':
								?><input type="text" name="<?php echo $field?>" value="<?php echo $arUser[$field]?>"  size="70"/><?
								break;
							case 'password':
								?><input type="password" name="<?php echo $field?>" value="" size="70"/><?
								break;
							// checkbox radio select file
						}?>
					</td>
				</tr>
			<?php endforeach?>
			</table>
		</div>
		<div id="personal">
			<table width="100%">
				<tr>
					<td>
						<b><?php echo LANG('SYTEM_USER_LOGIN')?>:</b>
					</td>
					<td>
						<input type="text" name="LOGIN" value="<?php echo $arUser["LOGIN"]?>" />
					</td>
				</tr>
			</table>
		</div>
		<div id="groups">
			<table class="form-table">
				<?php foreach ($arResult["GROUPS"] as $arGroup):?>
				<tr>
					<td class="td-left">
						<b><?php echo $arGroup["NAME"]?>:</b>
					</td>
					<td class="td-right">
						<?if(!is_array($arUser["GROUPS"])) $arUser["GROUPS"] = Array();?>
						<input type="checkbox" name="GROUPS[]" value="<?php echo $arGroup["ID"]?>" <?php echo in_array($arGroup["ID"], $arUser["GROUPS"]) ? 'checked="checked"' : ''?> />
					</td>
				</tr>
				<?php endforeach?>
			</table>
		</div>
		<div class="form-table-buttons">
			<input type="submit" value="Сохранить" />
		</div>
	</div>
	
	</form>
<?php endif?>