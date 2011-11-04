<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();?>
<?php $arSEF = Array(
	'1' => '/news/#ELEMENT_ID#.html',
	'2' => '/music/#ELEMENT_ID#.html',
	'3' => '/video/#ELEMENT_ID#.html',
	'4' => '/lib/#ELEMENT_ID#.html',
	'5' => '/art/#ELEMENT_ID#.html',
);?>
<div class="post no-bg">
<?php if(!empty($arResult["ITEMS"])):?>
	<table>
	<?php foreach ($arResult["ITEMS"] as $n => $arItem):?>
		<?php 
			$URL = str_replace('#ELEMENT_ID#', $arItem['ID'], $arSEF[$arItem['CATALOG_ID']]);
			$NAME = $arItem['CATALOG_ID'] == 2 ?
				$arItem["PROPERTIES"]["GROUP"]["VALUE"] . ' - (' . $arItem["PROPERTIES"]["YEAR"]["VALUE"] . ') ' . $arItem["PROPERTIES"]["ALBUM"]["VALUE"]
				: $arItem['NAME'];
		?>
		<tr>
			<td width="110px" style="vertical-align: top">
				<?php if(!empty($arItem['PREVIEW_PICTURE'])):?>
					<a href="<?php echo $URL?>"><img src="<?php echo File::GetPath($arItem['PREVIEW_PICTURE'])?>" title="<?php echo $NAME?>" alt="<?php echo $NAME?>" width="100px"/></a>
				<?php else:?>
					<a href="<?php echo $URL?>"><div class="no-image-pic"><span><?php echo LANG('NO_IMAGE')?></span></div></a>
				<?php endif?>
			</td>
			<td>
				<h2><a href="<?php echo $URL?>"><?php echo $NAME?></a></h2>
				<p class="post-info"><?php echo TruncateText($arItem["DETAIL_TEXT"], 80)?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p class="tags">
					<strong><?php echo LANG('AUTHOR')?> </strong> <?php echo User::GetSign($arItem["CREATED_BY"])?>,&nbsp;
					<strong><?php echo LANG('ADD_DATE')?> </strong> <?php echo $arItem["DATE_CREATE"]?>
				</p>
			</td>
		</tr>
	<?php endforeach?>
	</table>
<p>
	<?php echo str_replace('#URL#', GetCurPage().'?PAGE=', $arResult['PAGINATION'])?>
</p>
</div>
<?php else:?>
	<p><?php echo LANG('NO_RESULT_TEXT')?></p>
<?php endif?>