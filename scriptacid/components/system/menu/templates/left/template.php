<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if(!empty($arResult["ITEMS"])):?>
	<ul id="left-menu">
	<?php foreach($arResult["ITEMS"] as $k => $arItem):?>
		<li <?php echo $k == 0 ? 'class="first"' : ''?> <?php echo $arItem["CURRENT"] == "Y" ? 'id="current"' : ''?>>
			<a href="<?php echo $arItem["URL"]?>" title="<?php echo $arItem["NAME"]?>"><?php echo $arItem["NAME"]?></a>
		</li>
	<?php endforeach?>
	</ul>
<?php endif?>