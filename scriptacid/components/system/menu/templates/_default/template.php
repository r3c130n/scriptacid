<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<b><?php echo LANG("NAVIGATION")?></b>
<ul id="left-menu">
<?php foreach($arResult["ITEMS"] as $arItem):?>
    <li><a href="<?php echo $arItem["URL"]?>" title="<?php echo $arItem["NAME"]?>"><?php echo $arItem["NAME"]?></a></li>
<?php endforeach?>
</ul>