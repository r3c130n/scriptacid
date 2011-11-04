<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<div class="post no-bg">
<?php if(!empty($arResult)):?>
	<div>
		<?php Component::callComponent("system:player.video",
			"",
			Array(
				"FILE_ID" => $arResult['PROPERTIES']['FILE']['VALUE'],
				//"URL" => 'http://vs4.krasview.ru/3404888ffbb7d8b.flv',
				"WIDTH" => 480,
				"HEIGHT" => 320
			)
		)
		?>
	</div>
	<p><?php echo $arResult["DETAIL_TEXT"]?></p>
	<p class="tags">
		<strong>Автор: </strong> <a href="/admin/users/user.php?ID=<?php echo $arResult["CREATED_BY"]?>"><?php echo User::GetSign($arResult["CREATED_BY"])?></a>
		| <strong>Дата: </strong> <?php echo $arResult["DATE_CREATE"]?>
	</p>
</div>
<?php else:?>
	<?ShowError("Элемент не найден");?>
<?php endif?>
<p><a href="/video/">Список видео</a></p>