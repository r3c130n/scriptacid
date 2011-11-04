<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	global $USER;

	if (
			isset($_POST) &&
			!empty($_POST) &&
			isset($_POST['login_btn']) &&
			!empty($_POST['LOGIN']) &&
			!empty($_POST['PASSWORD'])
		) {

		$userID = $USER->CheckAuth($_POST['LOGIN'], $_POST['PASSWORD']);
		if ($userID !== false AND intVal($userID) > 0) {
			$USER->Authorize($userID);
			$this->redirectTo(GetCurDir());
		} else {
			$arResult["ERRORS"] = "Введённые Вами данные не верны!";
		}
	}


	$this->connectComponentTemplate();
?>