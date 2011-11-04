<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<div class="post no-bg">
<?php if(!empty($arResult)):?>
<?php $title = $arResult["PROPERTIES"]["GROUP"]["VALUE"] . ' - (' . $arResult["PROPERTIES"]["YEAR"]["VALUE"] . ') ' . $arResult["PROPERTIES"]["ALBUM"]["VALUE"]?>
<?php SetTitle($title);?>
	<table width="100%">
		<tr>
			<td width="20%">
				<img src="<?php echo File::GetPath($arResult['DETAIL_PICTURE'])?>" title="<?php echo $title?>" alt="<?php echo $title?>"/>
			</td>
			<td style="vertical-align: top">
				<b>Автор: </b> <?php echo $arResult["PROPERTIES"]["GROUP"]["VALUE"]?><br />
				<b>Альбом: </b> <?php echo $arResult["PROPERTIES"]["ALBUM"]["VALUE"]?><br />
				<b>Год: </b> <?php echo $arResult["PROPERTIES"]["YEAR"]["VALUE"]?><br />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<strong>Список треков: </strong> <br /><?php echo nl2br($arResult["DETAIL_TEXT"])?><br />
			</td>
		</tr>
	</table>
	
	<?php if(is_array($arResult["PROPERTIES"]["LINKS"]["VALUE"])):?>
		<p>
			<b>Ссылки:</b>
			<?php $cLink = 0;?>
			<?php foreach($arResult["PROPERTIES"]["LINKS"]["VALUE"] as $link):?>
				<?php if (!empty($link)):?>
					<a href="<?php echo $link?>">ссылка #<?php echo $cLink++?></a>,
				<?php endif?>
			<?php endforeach?>
		</p>
	<?php elseif(!empty($arResult["PROPERTIES"]["LINKS"]["VALUE"])):?>
		<p>
			<b>Ссылки:</b>
			<a href="<?php echo $arResult["PROPERTIES"]["LINKS"]["VALUE"]?>">ссылка #1</a>
		</p>
	<?php endif?>
</div>
<?php if($USER->IsAdmin()):?>
	<p>
		<a href="/manager/music/?ID=<?php echo $arResult['ID']?>">Редактировать элемент</a>
	</p>
<?php endif?>
<?php else:?>
	<?ShowError("Элемент не найден");?>
<?php endif?>
<p><a href="/music/">Список релизов</a></p>