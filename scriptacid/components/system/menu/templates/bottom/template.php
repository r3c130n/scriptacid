<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<br />
<?//d($arResult)?>
<b><?php echo LANG("NAVIGATION")?></b>: 
<?php foreach($arResult["ITEMS"] as $arItem):?>
   <a href="<?php echo $arItem["URL"]?>" title="<?php echo $arItem["NAME"]?>" style="color:red"><?php echo $arItem["NAME"]?></a>&nbsp;&nbsp;
<?php endforeach?>