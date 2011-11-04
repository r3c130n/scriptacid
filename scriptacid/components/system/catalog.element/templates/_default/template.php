<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<div class="post no-bg">
<?php if(!empty($arResult)):?>
	<h2><?php echo $arResult["NAME"]?></h2>
	<p class="post-info"><?php echo $arResult["DATE_CREATE"]?></p>
	<p><?php echo $arResult["DETAIL_TEXT"]?></p>
	<p class="tags"><strong>Автор: </strong> <a href="/admin/users/user.php?ID=<?php echo $arResult["CREATED_BY"]?>"><?php echo User::GetSign($arResult["CREATED_BY"])?></a>,  <strong>Теги : </strong> <a href="#">orci</a>, <a href="#">lectus</a></p>
</div>
<?php else:?>
	<?ShowError("Элемент не найден");?>
<?php endif?>
<p><a href="/articles/<?php echo $arParams["SECTION_ID"]?>/">Список статей</a></p>