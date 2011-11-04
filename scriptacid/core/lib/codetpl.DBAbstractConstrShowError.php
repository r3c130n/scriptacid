<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	if(strlen(trim($ERROR_STRING))>0) {
		if( file_exists(USR_PATH_FULL."/dbconn_error.php") ) {
			require_once USR_PATH_FULL."/dbconn_error.php";
		}
		else {
			echo $ERROR_STRING."<br />";
		}
		exit();
	}
?>