<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

if(!defined("CACHE_DATA_ENABLED")) define("_CACHE_DATA_ENABLED", true);
if(!defined("CACHE_HTML_ENABLED")) define("_CACHE_HTML_ENABLED", true);

/**
 * Кеширование
 * @author r3c130n
 * @version 1
 */
class Cache {
	public $timeOut = 3600;			// Время на которое кешировать данные
	public $arCondition = Array();	// Условие получения некешированных данных
	public $enabled = true;			// ВКЛ/ВЫКЛ кеширование
	
	const CACHE_DATA = CACHE_DATA_ENABLED; 
	
	public function __construct($arCondition = Array(), $timeOut = 3600, $enabled = true){
		$this->timeOut = (int)$timeOut;
		$this->arCondition = $arCondition;
		$this->enabled = (bool) ($enabled)?true:false;
	}

	/**
	 * Определяем: нужно ли кешировать данные
	 * @return bool
	 */
	public function StartCache() {
		if (!$this->enabled && self::CACHE_DATA) {
			return true;
		}
		$cacheFile = CACHE_PATH_FULL . "/" . $this->GetCacheName() . '.php';
		if (file_exists($cacheFile)) {
			$timeOutTime = filemtime($cacheFile) + $this->timeOut;
			if (time() >= $timeOutTime) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Получаем имя файла с кешем
	 * @return string
	 */
	public function GetCacheName() {
		return md5(serialize($this->arCondition).$_SERVER['PHP_SELF']);
	}

	/**
	 * Сохраняем данные в кеш
	 * @param mixed $arResult
	 * @return bool
	 */
	public function SaveCache($arResult) {
		if (!$this->enabled && self::CACHE_DATA) {
			return false;
		}
		$cacheFile = CACHE_PATH_FULL . "/" . $this->GetCacheName() . '.php';
		if (file_exists($cacheFile)) {
			unlink($cacheFile);
		}

		$cacheData = $this->SetCacheData($arResult);

		$fp = fopen($cacheFile, 'w');
		if (!fwrite($fp, $cacheData)) {
			fclose($fp);
			return false;
		}
		fclose($fp);
		return true;
	}

	/**
	 * Сериализуем данные для кеша
	 * @param mixed $arResult
	 * @return serialized
	 */
	public function SetCacheData($arResult) {
		return serialize($arResult);
	}

	/**
	 * Получаем данные из кеша
	 * @return mixed
	 */
	public function GetCache() {
		$cacheFile = CACHE_PATH_FULL . "/" . $this->GetCacheName() . '.php';
		if (!file_exists($cacheFile)) {
			return false;
		}
		$fp = fopen($cacheFile, 'r');
		$cachedData = fread($fp, filesize($cacheFile));
		fclose($fp);
		return unserialize($cachedData);
	}

	/**
	 * Удаляем старый кеш
	 * @param int $timeOut
	 * @return bool
	 */
	public static function DeleteOldCache($timeOut) {
		if(is_dir(CACHE_PATH_FULL)) {
			if($dir = opendir(CACHE_PATH_FULL)) {
				while(false !==($file = readdir($dir))) {
					if(is_file(CACHE_PATH_FULL."/".$file) AND substr($file, strlen($file) - 4, 4) == ".php") {
						$timeOutTime = filemtime(CACHE_PATH_FULL."/".$file) + $timeOut;
						if (time() >= $timeOutTime) {
							unlink(CACHE_PATH_FULL."/".$file);
						}
					}
				}
			}
		} else {
			return false;
		}
	}
}

/******************************
 *			HOW TO:
 ******************************
 *
 * 1. Создаём экземляр объекта класса Cache:
 *  $cache = new Cache(Array($condition), 3600);
 *	
 * 2. Проверяем, нужно ли кешировать данные, если да - то стартуем кеш:
 *  if ($cache->StartCache()) { // foo bar
 *
 * 3. Кешируем данные:
 *  $cache->SaveCache($arResult);
 *
 * 4. Если кешировать не нужно - получаем данные из кеша
 *  } else { $arResult = $cache->GetCache(); }
 */

?>