<?php
namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	$APP->addCSS($APP->getTemplatePath()."/css/screen.css");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title><?echo getSiteName()?> :: <?php showTitle()?></title>
	<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
	<?php $APP->showHeader()?>
</head>
<body>
<?php if($APP->USER->IsAdmin()):?>
	<?php echo showPanel()?>
<?php endif?>
	<div id="wrap">
		<div id="header">
			<h1 id="logo-text"><a href="/"><?php echo GetSiteName()?></a></h1>
			<p id="slogan"><?php echo GetSiteDescr()?></p>
			<div  id="nav">
				<?php App::callComponent(
					'system:menu',
					'top:default',
					array(
						'TYPE' => 'top',
						'ARRAY_PARAM' => array(
							'ARRAY_ARRAY' => array(
								'0' => '1',
								'1' => '2',
								'2' => '3',
								'3' => '12',
							),
							'ARRAY_STRING' => 'ARRAY_STRING',
						),
					)
				);?>
	    	</div>
			<?php //<div id="header-image"></div>?>
		</div>
		<div id="content-outer" class="clear">
			<div id="content-wrap">
				<div id="content">
					<div id="left">
						<div class="sidemenu">
							<?php App::callComponent(
								'system:menu',
								'left:default',
								array(
									'COMPONENT_AJAX_MODE' => 'OFF',
									'CACHE_TIME' => '3600',
									'TYPE' => 'left',
									'MENU_STYLE' => 'STYLE1',
									'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
								)
							);?>
						</div>
					</div>
					<div id="center">
						<h3><?php $APP->showTitle()?></h3>
						<?php $APP->showPage();?>
						<?php App::callComponent('test:empty', '', array());?>
						<?php App::callComponent('test:empty', '', array());?>
					</div>
				</div>
			</div>
		</div>
		<?php if (false && DEBUG_MODE):?>
			<div class="msg clear" style="width:100%">
			<?php 
				echo 'Запросов: '.App::DB()->sqlCount;
				echo '<br />';
				echo 'Запросы: <ol>';
				foreach (App::DB()->logSql as $sql) {
					echo '<li>'.$sql.'</li>';
				}
				echo '</ol>';
			?>
			</div>
		<?php endif?>
	</div>
</body>
</html>