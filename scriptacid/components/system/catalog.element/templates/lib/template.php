<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<div class="post no-bg">
<?php if(!empty($arResult)):?>
	<p><?php echo $arResult["DETAIL_TEXT"]?></p>
	<p>
		<a href="<?php echo File::GetPath($arResult['PROPERTIES']['TEXT']['VALUE'])?>" target="_blank">Читать весь</a>
	</p>

	<p class="tags">
		<strong>Автор: </strong> <?php echo User::GetSign($arResult["CREATED_BY"])?>
		| <strong>Дата: </strong> <?php echo $arResult["DATE_CREATE"]?>
	</p>
</div>
<?php else:?>
	<?php ShowError("Элемент не найден");?>
<?php endif?>
<p><a href="/lib/">Список произведений</a></p>