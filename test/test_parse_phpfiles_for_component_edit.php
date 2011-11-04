<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
?>
<?php

	$fileApplicationTemplate = DOC_ROOT.'/'.App::getTemplatePath().'/'.App::get()->TEMPLATE_FILENAME.'.php';
	if( file_exists($fileApplicationTemplate) ) {
		$strAppclicationTemplate = file_get_contents($fileApplicationTemplate);
	}
	else {
		echo 'Шаблон не найден'.endl;
		die();
	}
	
	$strAppclicationTemplate = trim($strAppclicationTemplate);
	
	$arPhpEntries = ComponentTools::parseString($strAppclicationTemplate, 'all');
	
	$strResult = '';
	
	foreach($arPhpEntries['SPLIT_STRING'] as $key => &$arStrPart) {
		if($arPhpEntries['MATCHES'][$key][1] < $arStrPart[1]) {
			$strResult .= $arPhpEntries['MATCHES'][$key][0].$arStrPart[0];
		}
		else {
			$strResult .= $arStrPart[0].$arPhpEntries['MATCHES'][$key][0];
		}
	}

	$strResult = trim($strResult);

	

	if(false) echo '<!--'."\n"
		.'ORIG_FILE_CONTENT:'."\n"
		.$strAppclicationTemplate
		."\n\n"
		.'GENERATED_AFTER_PART_CONTENT:'."\n"
		.$strResult
	.'-->';

	if($strResult == $strAppclicationTemplate) {
		echo "ok.\n";
	}
	else {
		echo "error.\n";
	}
?>


