<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	function __plugin__test2($param1, $param2, $param3) {
		echo "function: ".__FUNCTION__."<br />";
		$arArgList = func_get_args();
		foreach($arArgList as $argName => $argValue) {
			echo "param".$argName.": ".$argValue."<br />";			
		}
	}
?>