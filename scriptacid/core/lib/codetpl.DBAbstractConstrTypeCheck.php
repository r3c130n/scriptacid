<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	//echo "!".$ChildClassName."!".DB_TYPE."!".constant($ChildClassName."::INCLUDED_DB_TYPE");
	$ERROR_STRING = "";
	if(strlen(trim(DB_TYPE))==0) {
		$ERROR_STRING = DB_TYPE." database type does not set.";
	}
	if( !defined($ChildClassName."::DB_TYPE") ) {
		$ERROR_STRING = "Class constant ".$ChildClassName."::DB_TYPE doesn't defined.";
	}
	if(DB_TYPE != constant($ChildClassName."::DB_TYPE")) {
		$ERROR_STRING = DB_TYPE." database type differs from included class for database type ".constant($ChildClassName."::DB_TYPE").".";
	}
?>