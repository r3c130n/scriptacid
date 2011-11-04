<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php if($arResult['B_VIDEO']):?>
<object width="<?php  echo $arParams['WIDTH']?>"
		height="<?php  echo $arParams['HEIGHT']?>"
		codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"
		classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">
	<param value="/scriptacid/components/system/player.video/templates/main/player.swf" name="movie">
	<param value="high" name="quality">
	<param value="transparent" name="wmode">
	<param value="file=<?php  echo $arResult['FILE_PATH']?>&amp;fullscreen=true&amp;bufferlength=10" name="flashvars">
	<param value="always" name="allowscriptaccess">
	<param value="true" name="allowfullscreen">
	<embed width="<?php  echo $arParams['WIDTH']?>"
		   height="<?php  echo $arParams['HEIGHT']?>"
		   flashvars="file=<?php  echo $arResult['FILE_PATH']?>&amp;fullscreen=true&amp;bufferlength=10&amp;"
		   wmode="transparent" menu="true" allowfullscreen="true" allowscriptaccess="always"
		   type="application/x-shockwave-flash"
		   src="/scriptacid/components/system/player.video/templates/main/player.swf"
		   name="flv_player_embed">
</object>
<?php else:?>
<p>Видео-файл не найден</p>
<?php endif?>