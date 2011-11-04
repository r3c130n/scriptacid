<?php
namespace ScriptAcid;
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';

	//App::callComponent(":menu","top",	Array("TYPE" => "top"));
	try {
		if(!App::USER()->IsAdmin()) {
			throw new AppErrorException('Error: operation denied.', 0);
		}
		
		//$_POST['file'];
		//$_POST['replace_component_num'];
		//$_POST['component_name'];
		//$_POST['template_name'];
		//$_POST['replacement_params'];
		unset($_POST['currentCall']['obArParams']['COMPONENT_AJAX_READY']);
		//unset($_POST['replacement_params']['COMPONENT_AJAX_SEND_PAGE_POST']);
		unset($_POST['replacementCall']['obArParams']['COMPONENT_CALL_KEY']);

		//d($_POST);

		if(!@isset($_POST['currentCall']['inFileComponentNum'])) {
			throw new ComponentException('component number in file not set', ComponentException::E_CMP_EDIT_WRONG_CMP_NUM);
		}
		if(!is_file(DOC_ROOT.$_POST['currentCall']['inFile'])) {
			throw new ComponentException('wrong file name for edit', ComponentException::E_CMP_EDIT_WRONG_FILE);
		}

		$arReplacementCall = ComponentTools::makeComponentCallArray(
			$_POST['replacementCall']['componentName'],
			$_POST['replacementCall']['templateName'].':'.$_POST['replacementCall']['templateSkin'],
			$_POST['replacementCall']['obArParams']
		);
		if($arReplacementCall['COMPONENT_EXISTS'] == 'N') {
			throw new ComponentException('wrong component name', ComponentException::E_WRONG_CMP_NAME);
		}
		if($arReplacementCall['TEMPLATE_EXISTS'] == 'N') {
			throw new ComponentException('wrong template name', ComponentException::E_WRONG_TPL_NAME);
		}
		//d($arReplacementCall);
		if(//false&&
			ComponentTools::replaceComponentInFile(
				$_POST['currentCall']['inFileComponentNum'],
				$arReplacementCall,
				$_POST['currentCall']['inFile']
			)
		) {
			echo 'ok.';
		}
	}
	catch(ComponentException $except) {
		//if($except->getCode() == ComponentTools::E_PARSE_FAIL) {
		//}
		//d($except->getComponentCallArray());
		echo $except->getMessage().'.';
	}
	catch (AppErrorException $except) {
		d($arReplacementCall);
	}

?>