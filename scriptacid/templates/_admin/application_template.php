<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	$APP->addCSS($APP->getTemplatePath()."/css/screen.css");
	$APP->addCSS($APP->getTemplatePath()."/css/ui-sui.css");
	$APP->addCSS($APP->getTemplatePath()."/css/styles.css");

	$APP->addJS($APP->getTemplatePath()."/js/jquery.js");
	$APP->addJS($APP->getTemplatePath()."/js/jquery-ui.js");
	$APP->addJS("/scriptacid/js/visual.js");
	$APP->addJS("/scriptacid/js/utils.js");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title>АДМИНКА :: <?php echo getSiteName()?> :: <?php showTitle()?></title>
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
			<?php echo App::callComponent("Menu", "top", Array("TYPE" => "top"));?>
    	</div>
    	<div id="header-image"></div>
	</div>
	<div id="content-outer" class="clear">
		<div id="content-wrap">
			<div id="content">
				<div id="left">
					<div class="sidemenu">
						<?php App::callComponent("Menu", "left", Array("TYPE" => "left"));?>
					</div>
				</div>
				<div id="center">
					<h3>АДМИНКА :: <?php $APP->showTitle()?></h3>
					<?php $APP->showPage();?>
				</div>
			</div>
		</div>
	</div>
	<?php if (DEBUG_MODE === false):?>
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