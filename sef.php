<?php
namespace ScriptAcid;
$arSEFUrls = array(
	array(
		"CONDITION"	=>	"#^/news/([a-zA-Z0-9]+)/([0-9]+).html#",
		"RULE"	=>	"SECTION_CODE=$1&ELEMENT_ID=$2",
		"ID"	=>	"system:catalog.section",
		"PATH"	=>	"/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^/news/([0-9]+).html#",
		"RULE"	=>	"ELEMENT_ID=$1",
		"ID"	=>	"system:catalog.section",
		"PATH"	=>	"/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^/news/([a-zA-Z0-9]+)/#",
		"RULE"	=>	"SECTION_CODE=$1",
		"ID"	=>	"system:catalog.section",
		"PATH"	=>	"/news/index.php",
	),
	array(
		"CONDITION"	=>	"#^/photos/([a-zA_Z0-9\.\-\_]+)/#",
		"RULE"	=>	"CODE=$1",
		"ID"	=>	"zonevisage:photo.album",
		"PATH"	=>	"/photos/index.php",
	),
	array(
		"CONDITION"	=>	"#^/video/([0-9]+).html#",
		"RULE"	=>	"ELEMENT_ID=$1",
		"ID"	=>	"system:catalog.section",
		"PATH"	=>	"/video/index.php",
	),
	array(
		"CONDITION"	=>	"#^/lib/([0-9]+).html#",
		"RULE"	=>	"ELEMENT_ID=$1",
		"ID"	=>	"system:catalog.section",
		"PATH"	=>	"/lib/index.php",
	),
);
?>