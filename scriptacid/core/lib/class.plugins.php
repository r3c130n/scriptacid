<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class plugins
{
	protected static $pluginDirs = array();
	protected static $plugins;
	protected static $isInit = false;
	const funcNamePrefix = "__plugin__";
	const funcFilePrefix = "plugin.";
	
	protected static function init() {
		//echo "INIT: ";
		if(!self::$isInit) {
			//echo "OK<br />"; 
			self::$plugins = new self();
			self::$isInit = true;
		}
	}
	
	public static function & get() {
		//echo "GET INSTANCE<br />";
		self::init();
		return self::$plugins;
	}
	
	/**
	 * Ф-ия для добавления папки из которой черпать файлы плугинов.
	 * @param String $pluginsPath - полный путь к папке с плугинами.
	 * @param bool $directLoad = false - загрузить все файлы плугинов сразу, а не подгружать по мере вызова.
	 * @return true Если папка есть.
	 * @return false Если папки нет. 
	 */
	static public function addPlugins($pluginsPath, $directLoad = false) {
		removeDirLastSlash($pluginsPath);
		$pluginsPath = DOC_ROOT.$pluginsPath;
		if(is_dir($pluginsPath)) {
			self::$pluginDirs[] = $pluginsPath;
			if($directLoad) {
				$dirPlugins = opendir($pluginsPath);
				while ( $elementOfDir = readdir($dirPlugins) ) {
					if (
						$elementOfDir != ".."
						&& $elementOfDir != "."
						&& substr($elementOfDir, strlen($elementOfDir)-4, strlen($elementOfDir)) == ".php"
						&& substr($elementOfDir, 0, strlen(self::funcFilePrefix)) == self::funcFilePrefix
					) {
						$curLoadFuncName = substr($elementOfDir, strlen(self::funcFilePrefix), -4);
						if ( !function_exists(self::funcNamePrefix.$curLoadFuncName) ) {
							//d("dload fname: ".$curLoadFuncName);
							//d("dload $curLoadFuncName func in ".$pluginsPath."/".$elementOfDir);
							require_once $pluginsPath."/".$elementOfDir;
						}
					}
				}
			}
			return true;
		}
		else {
			return false;
		}
	}
	static public function printPluginDirs() {
		echo "<pre>";
		print_r(self::$pluginDirs);
		echo "</pre>";
	}
	
	function __call($funcName, $arFuncParams) {
		return self::callPlugin($funcName, $arFuncParams);
	}
	public static function __callStatic($funcName, $arFuncParams) {
		return self::callPlugin($funcName, $arFuncParams);
	}
	
	private function callPlugin($funcNameCall, &$arFuncParams) {
		/**
		 * TODO:
		 * В php 5.3 появилась возможность создавать анонимные ф-ии. вот так $func = function() { // CODE OF FUNCTION }
		 * Отсюда вывод, что плугины можно будет писать наполняя какой-нить массив класса ф-иями. 
		 */
		$funcName = false;
		//self::funcFilePrefix;
		foreach(self::$pluginDirs as $pluginDir) {
			if( file_exists($pluginDir."/".self::funcFilePrefix.$funcNameCall.".php") ) {
				//d($pluginDir."/".self::funcFilePrefix.$funcNameCall.".php");
				if ( !function_exists(__NAMESPACE__.'\\'.self::funcNamePrefix.$funcNameCall) ) {
					if(is_file($pluginDir."/".self::funcFilePrefix.$funcNameCall.".php")) {
						//echo $pluginDir."/".self::funcFilePrefix.$funcNameCall.".php".endl;
						require_once $pluginDir."/".self::funcFilePrefix.$funcNameCall.".php";
						if (function_exists(__NAMESPACE__.'\\'.self::funcNamePrefix.$funcNameCall)) {
							$funcName = __NAMESPACE__.'\\'.self::funcNamePrefix.$funcNameCall;
							//echo $funcName." exists.<br />"; 
							break;
						}
					}
					
				}
				else {
					$funcName = __NAMESPACE__.'\\'.self::funcNamePrefix.$funcNameCall;
				}
			}
		}
		if(!$funcName) {
			ShowError("Плагин-Функция $funcNameCall не найдена.");// Префикс функции ".self::funcNamePrefix);
			return false;
		}
		switch ( count($arFuncParams) ) {
			case "1":
				return $funcName($arFuncParams[0]);
				break;
			case "2":
				return $funcName($arFuncParams[0], $arFuncParams[1]);
				break;
			case "3":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2]);
				break;
			case "4":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3]);
				break;
			case "5":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4]);
				break;
			case "6":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4], $arFuncParams[5]);
				break;
			case "7":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4], $arFuncParams[5], $arFuncParams[6]);
				break;
			case "8":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4], $arFuncParams[5], $arFuncParams[6], $arFuncParams[7]);
				break;
			case "9":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4], $arFuncParams[5], $arFuncParams[6], $arFuncParams[7], $arFuncParams[8]);
				break;
			case "10":
				return $funcName($arFuncParams[0], $arFuncParams[1], $arFuncParams[2], $arFuncParams[3], $arFuncParams[4], $arFuncParams[5], $arFuncParams[6], $arFuncParams[7], $arFuncParams[8], $arFuncParams[9]);
				break;

			default:
				echo __CLASS__.": Слишко много параметров";
				//exit();
				break;
		}
	}
}
final class plg extends plugins {}

/*plugins::get()->plugIn("1", "2");
pln::get()->plugIn("1", "2");
plg::get()->plugIn("1", "2");

plugins::addPlugins("plugins dir1");
plugins::addPlugins("plugins dir2");

plg::addPlugins("plg dir1");
plg::addPlugins("plg dir2");

plugins::printPluginDirs();
pln::printPluginDirs();
plg::printPluginDirs();
//*

function __plugin__plugIn($param1, $param2) {
	echo "param1 = $param1<br />";
	echo "param2 = $param2<br />";
}
//*/
?>