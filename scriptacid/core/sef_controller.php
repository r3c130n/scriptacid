<?php namespace ScriptAcid;
/**
 * Контроллер ЧПУ
 */
$arSEFUrls = Array();
if(file_exists($_SERVER['DOCUMENT_ROOT']."/sef.php"))
	include($_SERVER['DOCUMENT_ROOT']."/sef.php");

if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == '404' || isset($_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"])) {
	if(isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == '404')
		$url = $_SERVER["REQUEST_URI"];
	else
		$url = $_SERVER["REQUEST_URI"] = $REQUEST_URI = (is_array($_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"])? '':$_REQUEST["SEF_APPLICATION_CUR_PAGE_URL"]);

	if(($pos=strpos($url, "?"))!==false) {
		$params = substr($url, $pos+1);
		parse_str($params, $vars);

		$_GET += $vars;
		$_SERVER["QUERY_STRING"] = $QUERY_STRING = $params;
	}

	$HTTP_GET_VARS=$_GET;
}

foreach($arSEFUrls as $val)
{
	if(preg_match($val["CONDITION"], $_SERVER["REQUEST_URI"]))
	{
		if (strlen($val["RULE"]) > 0)
			$url = preg_replace($val["CONDITION"], (StrLen($val["PATH"]) > 0 ? $val["PATH"]."?" : "").$val["RULE"], $_SERVER["REQUEST_URI"]);
		else
			$url = $val["PATH"];

		if(($pos=strpos($url, "?"))!==false)
		{
			$params = substr($url, $pos+1);
			parse_str($params, $vars);

			$_GET += $vars;
			$_REQUEST += $vars;
			$GLOBALS += $vars;
			$_SERVER["QUERY_STRING"] = $QUERY_STRING = $params;
			$url = substr($url, 0, $pos);
		}
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$url) || !is_file($_SERVER['DOCUMENT_ROOT'].$url))
			continue;

		header("200 OK");

		$_SERVER["REAL_FILE_PATH"] = $url;

		include_once($_SERVER['DOCUMENT_ROOT'].$url);

		die();
	}
}
?>