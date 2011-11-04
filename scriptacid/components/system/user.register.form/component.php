<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	global $USER;

	if (
			isset($_POST) &&
			!empty($_POST) &&
			isset($_POST['register_btn']) &&
			!empty($_POST['LOGIN']) &&
			!empty($_POST['PASSWORD']) &&
			!empty($_POST['PASSWORD_RT']) &&
			!empty($_POST['EMAIL'])
		) {

		if ($_POST['PASSWORD'] == $_POST['PASSWORD_RT']) {

			$userID = $USER->GetByLogin($_POST['LOGIN'], true);
			if ($userID !== false AND intVal($userID) > 0) {
				$arResult["ERRORS"] = "Пользователь с такими данными уже существует!";
			} else {
				$arFields = Array(
					'GROUPS' => 2,
					'NAME' => $_POST['NAME'],
					'LAST_NAME' => $_POST['LAST_NAME']
				);
				
				$USER_ID = $USER->Register($_POST['LOGIN'], $_POST['PASSWORD'], $_POST['EMAIL'], $arFields);
				$USER->Authorize($USER_ID);
				$this->redirectTo(GetCurDir());
			}
		} else {
			$arResult["ERRORS"] = "Пароли не совпадают";
		}
	}

	if ($_GET['logout'] == 'Y') {
		$USER->UnAuthorize();
	}

	$this->connectComponentTemplate();
?>