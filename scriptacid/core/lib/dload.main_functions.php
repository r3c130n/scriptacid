<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

// Application object aliases {{{
function showPanel() {
	return App::getInstance()->showPanel();
}
function getTitle() {
	return App::getInstance()->getTitle();
}
function setTitle($title) {
	return App::getInstance()->setTitle($title);
}
function showTitle() {
	App::getInstance()->showTitle();
}
function getDescr() {
	return App::getInstance()->getDescr();
}
function setDescr($descr) {
	return App::getInstance()->setDescr($descr);
}
function showDescr() {
	App::getInstance()->showDescr();
}
function getProperty($property) {
	return App::getInstance()->getProperty($property);
}
function setProperty($key, $value) {
	return App::getInstance()->setProperty($key, $value);
}
function showProperty($property) {
	App::getInstance()->showProperty($property);
}
/**
 * Получить ИМЯ сайта
 * @return array
 */
function getSiteName() {
	return Storage::get()->SITE_NAME;
}

/**
 * Получить ОПИСАНИЕ сайта
 * @return array
 */
function getSiteDescr() {
	return Storage::get()->SITE_DESCRIPTION;
}
function TextModifier($key, $value) {
	return App::getInstance()->TextModifier($key, $value);
}
function getCurDir() {
	return App::getInstance()->getCurDir();
}
function getCurPath() {
	return App::getInstance()->getCurPath();
}
function getCurPage() {
	return App::getInstance()->getCurPage();
}
function addMsg($sMsg = "") {
	return  App::getInstance()->addMsg();
}
function showMsg($sMsg = "", $bEcho = true) {
	return App::getInstance()->showMsg();
}
function showError($arErrors, $bEcho = true) {
	return App::getInstance()->showError($arErrors, $bEcho);
}
function redirectTo($url, $timeOut = 0, $bJS = false) {
	return App::getInstance()->redirectTo($url, $timeOut, $bJS);
}
// }}}

/**
 * Получить путь, подставив в него значения вместо #ключей#
 * @param string $sTpl
 * @param array $arData
 * @return string
 */
function GetStrTpl($sTpl, $arData) {
	foreach ($arData as $field => $text) {
		$arFromStr[] = '#'.$field.'#';
		$arToStr[] = $text;
	}
	return str_replace($arFromStr, $arToStr, $sTpl);
}

/**
 * Дополнить массив безопасными данными, оригинальные значения с "~" ключами
 * @param array $arRes
 * @return array
 */
function TildaArray($arRes) {
	$arTilda = array();
	foreach($arRes as $FName=>$arFValue) {
		if(is_array($arFValue))
			$arTilda[$FName] = htmlspecialcharsEx($arFValue);
		elseif(preg_match("/[;&<>\"]/", $arFValue))
			$arTilda[$FName] = htmlspecialcharsEx($arFValue);
		else
			$arTilda[$FName] = $arFValue;
		$arTilda["~".$FName] = $arFValue;
	}
	return $arTilda;
}

/**
 * Экранирование строки
 * @staticvar array $search
 * @staticvar array $replace
 * @param string $str
 * @return string
 */
function htmlspecialcharsEx($str) {
	static $search = array("&amp;","&lt;","&gt;","&quot;","<",">","\"");
	static $replace = array("&amp;amp;","&amp;lt;","&amp;gt;","&amp;quot;","&lt;","&gt;","&quot;");
	return str_replace($search, $replace, $str);
}

/**
 * Убрать экранирование из строки
 * @param string $str
 * @return string
 */
function htmlspecialcharsback($str) {
	if(strlen($str)>0) {
		$str = str_replace("&lt;", "<", $str);
		$str = str_replace("&gt;", ">", $str);
		$str = str_replace("&quot;", "\"", $str);
		$str = str_replace("&amp;", "&", $str);
	}
	return $str;
}

/**
 * Обрезать текст до нужной длинны
 * @param <type> $strText
 * @param <type> $intLen
 * @return <type>
 */
function TruncateText($strText, $intLen, $sEnd = "") {
	if(strlen($strText) >= $intLen )
		return substr($strText, 0, $intLen).$sEnd;
	else
		return $strText;
}

/**
 * Склеить массив в SQL-выражение
 * @param array $array
 * @param string $MODE
 * @param array $arTables
 * @return string
 */
function glueArraySql($array, $MODE = "F", $arTables = false) {
	switch ($MODE) {
		case "I":
			$cntKey = 0;
			$cntVal = 0;

			foreach($array as $key => $value) {
				if ($value == "") {
					unset($array[$key]);
				}
			}

			$str = "(";
			foreach($array as $key => $value) {
				$cntKey++;
				$str .= sSql($key);
				if($cntKey != count($array)) {
					$str .= ", ";
				}
			}
			$str .= ") VALUES (";
			
			foreach($array as $key => $value) {
				$cntVal++;
				$str .= "'".sSql($value)."'";
				if($cntVal != count($array)) {
					$str .= ", ";
				}
			}
			$str .= ")";
			break;
		case "F":
			$cnt = 0;
			foreach($array as $key => $value) {
				$cnt++;
				$bTbl = false;
				if($arTables AND is_array($arTables)) {
					foreach($arTables as $t => $v) {
						if(strstr($value, $t.'.'))
							$bTbl = true;
					}
				}
				if($arTables AND $bTbl)
					$str .= sSql($key)." = ".sSql($value);
				else
					$str .= sSql($key)." = '".sSql($value)."'";
					
				if($cnt != count($array)) {
					if (is_array($arTables)) {
						$str .= " AND ";
					} else {
						$str .= ", ";
					}
				}
			}
			break;
		case "S":
		default:
			$cnt = 0;
			foreach($array as $value) {
				$cnt++;
				$str .= sSql($value);
				if($cnt != count($array)) {
					$str .= ", ";
				}
			}
			break;
	}
	return $str;
}

/**
 * Очистка массива с фильтром
 * @param array $arFilter
 * @return array
 * @todo Переписать или удалить
 */
function filterClear($arFilter) {
	foreach($arFilter as $key => $value) {
		if (substr($key, 0, 8) == 'CATALOG.') {
			unset($arFilter[$key]);
		}
		if(substr($key, 0, 8) == 'ELEMENT.') {
			unset($arFilter[$key]);
			$arFilter[str_replace('ELEMENT.', '', $key)] = $value;
		}
	}
	return $arFilter;
}

/**
 * Склеить из массива SQL-выражение для фильтра
 * @param array $arFilter
 * @param array $arTables
 * @return string
 */
function filterGlue($arFilter, $arTables = false) {
	//d($arFilter);
	$cnt = 0;
	$str = '';
	$signs = Array('=', '!', '!=', '<', '>', '<=', '>=');

	if(is_array($arFilter['CATALOG_ID']) AND !empty($arFilter['CATALOG_ID'])) {
		$arCatalogIDs = $arFilter['CATALOG_ID'];
		unset($arFilter['CATALOG_ID']);
	}

	foreach($arFilter as $key => $value) {
		if ($value == "" AND !in_array($key, $signs)) {
			unset($arFilter[$key]);
		}
	}

	foreach($arFilter as $key => $value) {
		$cnt++;
		$sign = getSign($key);
		$key = str_replace($signs, '', $key);
		$arConstants = Array("NULL", "null", "FALSE", "false", "TRUE", "true");
		if ($value === "NULL" OR $value === "null") {
			switch ($sign) {
				case '=':
					$sign = 'IS';
					break;
				case '!=':
					$sign = 'IS NOT';
					break;
				default:
					$sign = $sign;
					break;
			}
		}

		$bTbl = false;
		if($arTables AND is_array($arTables)) {
			foreach($arTables as $t => $v) {
				if(strstr($value, $t.'.'))
					$bTbl = true;
			}
		}
		if(($arTables AND $bTbl) OR in_array($value, $arConstants))
			$str .= sSql($key)." ".$sign." ".sSql($value);
		else
			$str .= sSql($key)." ".$sign." '".sSql($value)."'";

		if($cnt != count($arFilter)) {
			$str .= " AND ";
		}
	}

	if (is_array($arCatalogIDs) AND !empty($arCatalogIDs)) {
		if (!empty($str)) {
			$str .= " AND";
		}
		$str .= " (";
		$cntr = 1;
		foreach($arCatalogIDs as $catID) {
			if ($arTables === false) {
				$str .= "CATALOG_ID = '" . intVal($catID) . "'";
			} else {
				$str .= "ELEMENT.CATALOG_ID = '" . intVal($catID) . "'";
			}
			if($cntr != count($arCatalogIDs)) {
				$str .= " OR ";
				$cntr++;
			}
		}
		$str .= ') GROUP BY ';
		if ($arTables === false) {
			$str .= "ID";
		} else {
			$str .= "ELEMENT.ID";
		}
	}
	return $str;
}

/**
 * Преобразование знака ключа массива для фильтра в SQL-условие
 * @param string $key
 * @return string
 */
function getSign($key) {
	switch ($key) {
		case strstr($key, '!'):
			$sign = '!=';
			break;
		case strstr($key, '>'):
			$sign = '>';
			break;
		case strstr($key, '>='):
			$sign = '>=';
			break;
		case strstr($key, '<'):
			$sign = '<';
			break;
		case strstr($key, '<='):
			$sign = '<=';
			break;
		default:
			$sign = '=';
			break;
	}
	return $sign;
}

/**
 * Преобразование массива $aOrder в SQL-выражение для сортировки
 * @param array $arOrder
 * @return string
 */
function getOrderSql($arOrder) {
	$str = "";
	$str .= " ORDER BY ";
	$cnt = 0;
	foreach ($arOrder as $key => $value) {
		$cnt++;
		$str .= $key." ".$value;
		if($cnt != count($arOrder)) {
			$str .= ", ";
		}
	}
	return $str;
}

/**
 * Преобразование массива $arLimit в SQL-выражение для ограничение количества строк результата
 * @param array $arLimit
 * @return string
 */
function getLimitSql($arLimit) {
	$str = " LIMIT ";
	if($arLimit["COUNT"] > 0 AND isset($arLimit["OFFSET"])) {
		$str .= $arLimit["COUNT"] . ' OFFSET ' . $arLimit["OFFSET"];
	} else {
		if(!isset($arLimit["PAGE"]) OR empty($arLimit["PAGE"])) {
			$page = "";
		} else {
			$page = $arLimit["COUNT"]*$arLimit["PAGE"].", ";
		}

		$str .= $page.$arLimit["COUNT"];
	}
	return $str;
}

/**
 * Генерация префикса SQL-выражения для добавления (INSERT) записи в БД
 * @param string $table
 * @param array $arFields
 * @return string
 */
function addSql($table, $arFields) {
	$sql = "INSERT INTO ".$table." ";
	$sql .= glueArraySql($arFields, "I").";";
	return $sql;
}

/**
 * Генерация префикса SQL-выражения для обновления (UPDATE) записи в БД
 * @param string $table
 * @param array $arFields
 * @return string
 */
function updSql($table, $arFields) {
	$sql = "UPDATE ".$table." SET ";
	$sql .= glueArraySql($arFields, "F")."";
	return $sql;
}

/**
 * Генерация SQL-выражения из массивов
 * @param array $arTables
 * @param string $arOrder
 * @param array $arFilter
 * @param string $arSelect
 * @param array $arLimit
 * @return string
 */
function getListSql($arTables, $arOrder = Array(), $arFilter = Array(), $arSelect = Array(),  $arLimit = Array()) {
	// Таблицы
	$table = "";
	if(is_array($arTables)) {
		if(!empty($arTables)) {
			$cntTbl = 0;
			foreach($arTables as $tableAlias => $tableName) {
				$cntTbl++;
				$table .= " `".$tableName."` as ".$tableAlias;
				if($cntTbl < count($arTables)) {
					$table .= ", ";
				}
			}
		} else {
			return false;
		}
	} else {
		if(!empty($arTables)) {
			$table = $arTables;
		} else {
			return false;
		}

	}

	// Порядок
	if(!$arOrder OR empty($arOrder)) {
		$arOrder = Array("SORT" => "ASC");
	}
	$order = getOrderSql($arOrder);

	// Выборка
	if(!$arSelect OR empty($arSelect)) {
		if(count($arTables) > 1) {
			$select = "DISTINCT *";
		} else {
			$select = "*";
		}
	} elseif($arSelect = "**") {
		$select = "DISTINCT *";
	} else {
		$select = glueArraySql($arSelect, "S");
	}

	// Фильтр
	if(!$arFilter OR empty($arFilter)) {
		$where = "";
	} else {
		$where = "WHERE ";
		$where .= filterGlue($arFilter, $arTables);
	}

	// Лимит
	if(!$arLimit) {
		$limit = "";
	} else {
		$limit = getLimitSql($arLimit);
	}

	$sql =  "SELECT " . $select . " FROM " . $table ." ". $where . $order . $limit . ";";
	return $sql;
}

/**
 * Получить путь, подставив в него значения вместо #ключей#
 * @param string $str
 * @param array $arVariables
 * @return string
 * @todo Удалить
 */
function getMsg($arVariables, $str) {
	foreach($arVariables as $var => $val) {
		$arVars[] = "#" . $var . "#";
		$arVals[] = $val;
	}
	return str_replace($arVars, $arVals, $str);
}

/**
 * Вывод сообщения об ошибке БД
 * @param string $msg
 * @todo переделать
 */
function dbError($msg) {
	d("Ошибка SQL: ".$msg);
}

/**
 * Получить случайное значение строки
 * @param int $count
 * @return string
 */
mt_srand(((double)microtime()*1000000) ^ crc32(uniqid('', true)));
function GetRandom($count = 8) {
	$arSymbols = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
	$max = strlen($arSymbols)-1;
	$rand = '';
	for ($i=0; $i < $count; $i++) {
		$rand .= $arSymbols[mt_rand(0, $max)];
	}
	return $rand;
}

/**
 * Экранирование строк перед запросами к БД
 * @param string $strValue
 * @return string
 */
function sSql($strValue) {
	if( PHP_REAL_ESCAPE_STRING_EXISTS ) { // PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if( PHP_MAGIC_QUOTES_ACTIVE ) { $strValue = stripslashes( $strValue ); }
		$strValue = mysql_real_escape_string( $strValue );
	} else { // before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if( !PHP_MAGIC_QUOTES_ACTIVE ) { $strValue = addslashes( $strValue ); }
		// if magic quotes are active, then the slashes already exist
	}
	return $strValue;
}

/**
 * TODO: [pronix] Написать эту ф-ию :)
 * Экраниварование строк от XSS
 * @param string $str
 * @return string
 */
function sXss($str) {
	return $str;
}

/**
 * This function takes a path to a file to output ($file), 
 * the filename that the browser will see ($name) and 
 * the MIME type of the file ($mime_type, optional).
 * 
 * If you want to do something on download abort/finish,
 * register_shutdown_function('function_name');
 */
function output_file($file, $name, $mime_type='', &$arErrors = array())
{
	$arErrors = array();
	
	if(!is_readable($file)) {
		$arErrors[] = 'File not found or inaccessible!';
		return false;
	}

	$size = filesize($file);
	$name = rawurldecode($name);

	/* Figure out the MIME type (if not specified) */
	$known_mime_types=array(
		"pdf" => "application/pdf",
		"txt" => "text/plain",
		"html" => "text/html",
		"htm" => "text/html",
		"exe" => "application/octet-stream",
		"zip" => "application/zip",
		"doc" => "application/msword",
		"xls" => "application/vnd.ms-excel",
		"ppt" => "application/vnd.ms-powerpoint",
		"gif" => "image/gif",
		"png" => "image/png",
		"jpeg"=> "image/jpg",
		"jpg" =>  "image/jpg",
		"php" => "text/plain"
	);

	if($mime_type==''){
		$file_extension = strtolower(substr(strrchr($file,"."),1));
		if(array_key_exists($file_extension, $known_mime_types)){
		$mime_type=$known_mime_types[$file_extension];
		} else {
		$mime_type="application/force-download";
		};
	};

	@ob_end_clean(); //turn off output buffering to decrease cpu usage

	// required for IE, otherwise Content-Disposition may be ignored
	if( ini_get('zlib.output_compression') ) {
		ini_set('zlib.output_compression', 'Off');
	}

	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes');

	/* The three lines below basically make the 
	download non-cacheable */
	header("Cache-control: private");
	header('Pragma: private');
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// multipart-download and download resuming support
	if(isset($_SERVER['HTTP_RANGE']))
	{
		list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
		list($range) = explode(",",$range,2);
		list($range, $range_end) = explode("-", $range);
		$range=intval($range);
		if(!$range_end) {
			$range_end=$size-1;
		} else {
			$range_end=intval($range_end);
		}

		$new_length = $range_end-$range+1;
		header("HTTP/1.1 206 Partial Content");
		header("Content-Length: $new_length");
		header("Content-Range: bytes $range-$range_end/$size");
	} else {
		$new_length=$size;
		header("Content-Length: ".$size);
	}

	/* output the file itself */
	$chunksize = 1*(1024*1024); //you may want to change this
	$bytes_send = 0;
	if ($file = fopen($file, 'r'))
	{
		if(isset($_SERVER['HTTP_RANGE']))
		fseek($file, $range);
		$bConnectionAborted = false;
		while(
			!feof($file) 
			&& !($bConnectionAborted = connection_aborted())
			&& ($bytes_send<$new_length)
		) {
			$buffer = fread($file, $chunksize);
			print($buffer); //echo($buffer); // is also possible
			flush();
			$bytes_send += strlen($buffer);
		}
		fclose($file);
		if($bConnectionAborted) {
			$arErrors[] = 'Error - download aborted.';
			return false;
		}
		return true;
	} else {
		return false;
		$arErrors[] = 'Error - can not open file.';
	}

	/*********************************************
				Example of use
	**********************************************/	
	
	/* 
	 * Make sure script execution doesn't time out.
	 * Set maximum execution time in seconds (0 means no limit).
	 * ----
	 * set_time_limit(0);	
	 * $file_path='that_one_file.txt';
	 * output_file($file_path, 'some file.txt', 'text/plain');
	 */
}

/**
 * Отсортировать массив по полю 'SORT'
 * @param unknown_type $arUnsort
 * @param unknown_type $defaultSortValue
 */
function sortBySORTField($array) {
	uasort($array, function($valA, $valB) {
		if(!$valA['SORT']) $valA['SORT'] = 100;
		if(!$valB['SORT']) $valB['SORT'] = 100;
		return ($valA['SORT'] < $valB['SORT']) ? -1 : 1;
	});
	return $array;
}

?>