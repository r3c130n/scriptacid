<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
function __plugin__test3() {
	$args = func_get_args();
	d($args);
}
?>