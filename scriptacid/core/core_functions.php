<?php namespace ScriptAcid;

/**
 * Debug data print
 * @param mixed $mixed
 * @param mixed $collapse
 */
function d($mixed, $collapse = null, $bPrint = true) {
	if(!$bPrint) {
		return;
	}
	static $arCountFuncCall = 0;
	static $arCountFuncCallWithTitleKey = array();
	$arCountFuncCall++;

	$bCollapse = false;
	if($collapse !== null) {
		$bCollapse = true;
		if( is_string($collapse) && strlen($collapse)>0) {
			if( !@isset($arCountFuncCallWithTitleKey[$collapse]) ) {
				$arCountFuncCallWithTitleKey[$collapse] = 0;
			}
			$arCountFuncCallWithTitleKey[$collapse]++;
			
			$elemTitle = $collapse."#".$arCountFuncCallWithTitleKey[$collapse];
			$elemId = rand(1,500).$collapse."#".$arCountFuncCallWithTitleKey[$collapse];
		}
		else {
			$elemTitle = "dData#".$arCountFuncCall;
			$elemId = rand(1,500).$arCountFuncCall;
		}
	}
	?>
	<?php if($bCollapse):?>
		<a	href="javascript:void(0)"
			style="display: block;background: white; border:1px dotted #5A82CE;padding:3px"
			onclick="document.getElementById('<?php echo $elemId?>').style.display = ( document.getElementById('<?php echo $elemId?>').style.display == 'none')?'block':'none'"
		>
			<?php echo $elemTitle?>
		</a>
		<div id="<?php echo $elemId?>" style="text-align: left; display:none; background-color: #b1cdef; position: absolute; z-index: 10000;">
	<?php endif?>

			<pre style="text-align: left;"><?php print_r($mixed);?></pre>

	<?php if ($bCollapse):?>
		</div>
	<?php endif;
}


function removeDirLastSlash(&$path) {
	if(substr($path, strlen($path)-1) == "/") {
		$path = substr($path, 0, strlen($path)-1);
	}
	return $path;
}

function getFileExt($fileName) {
	return substr($fileName, strrpos($fileName, ".")+1);
}

function isFileInTree($filePath, $dirPath) {
	if( strpos($filePath, $dirPath) !== false ) {
		return true;
	}
	return false;
}

/**
 * @param String $functionName
 */
function fixNamespaceName(&$functionOrClassOfNamespaceName) {
	if(
		is_string($functionOrClassOfNamespaceName)
		&& substr($functionOrClassOfNamespaceName, 0,1)!=='\\'
	) {
		$functionOrClassOfNamespaceName = '\\'.__NAMESPACE__.'\\'.$functionOrClassOfNamespaceName;
		return true;
	}
	return false;
}

/**
 * Распарсить аргументы командной строки
 * 
 * (с) patrick at pwfisher dot com
 * http://php.net/manual/en/features.commandline.php
 * comment: 22-Aug-2009 06:59
 */
/*
	Usage: 
	[pfisher ~]$ php test.php --foo --bar=baz
	["foo"]   => true
	["bar"]   => "baz"

	[pfisher ~]$ php test.php -abc
	["a"]     => true
	["b"]     => true
	["c"]     => true

	[pfisher ~]$ php test.php arg1 arg2 arg3
	[0]       => "arg1"
	[1]       => "arg2"
	[2]       => "arg3"
*/
function parseCliArgs($argv){
	array_shift($argv);
	$out = array();
	foreach ($argv as $arg){
		if (substr($arg,0,2) == '--'){
			$eqPos = strpos($arg,'=');
			if ($eqPos === false){
				$key = substr($arg,2);
				$out[$key] = isset($out[$key]) ? $out[$key] : true;
			} else {
				$key = substr($arg,2,$eqPos-2);
				$out[$key] = substr($arg,$eqPos+1);
			}
		} else if (substr($arg,0,1) == '-'){
			if (substr($arg,2,1) == '='){
				$key = substr($arg,1,1);
				$out[$key] = substr($arg,3);
			} else {
				$chars = str_split(substr($arg,1));
				foreach ($chars as $char){
					$key = $char;
					$out[$key] = isset($out[$key]) ? $out[$key] : true;
				}
			}
		} else {
			$out[] = $arg;
		}
	}
	return $out;
}
?>