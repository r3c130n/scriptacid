<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

Modules::setAutoloadClasses($moduleID, array(
		"Catalog"				=> "lib/class.Catalog.php",
		"CatalogType"			=> "lib/class.CatalogType.php",
		"CatalogUtils"			=> "lib/class.CatalogUtils.php",
	
		"CatalogSection"		=> "lib/lib.CatalogSection.php",
		"CatalogSectionResult"	=> "lib/lib.CatalogSection.php",
		"_CatalogSection"		=> "lib/lib.CatalogSection.php",
	
		"CatalogElement"		=> "lib/lib.CatalogElement.php",
		"CatalogElementResult"	=> "lib/lib.CatalogElement.php",
		"_CatalogElement"		=> "lib/lib.CatalogElement.php",
	)
	//, Modules::global_set
	//, Modules::global_set
);

//echo "<b>MODULE CATALOG: INCLUDE LIB:</b><br />";
//Modules::includeLibFiles($CurentModulePath."/lib");	

?>