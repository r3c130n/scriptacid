<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

abstract class AbstractDatabase extends AbstractLogger
{
	
	protected $connectedDatabases = array();
	
	/**
	 * $ChildClassName - class which call AbstractConstructor (usualy in self constructor)
	 * $DBCONN_NAME - name of file with connection parameters
	 * returns full path to connection file. 
	 */
	protected function AbstractConstructor($ChildClassName, $DBCONN_NAME = DBCONN_NAME) {
		//$ChildClassName = get_class($this);
		
		require dirname(__FILE__).'/codetpl.DBAbstractConstrTypeCheck.php';
		
		$DBCONN_FULLNAME = USR_PATH_FULL.'/conn.'.$DBCONN_NAME.'.php';
		if(!file_exists($DBCONN_FULLNAME)) {
			$ERROR_STRING = 'Database connection file does not exist.';
		}
		// Проверяем не соединялись ли мы уже с этими параметрами
		if( !is_array($this->connectedDatabases[constant($ChildClassName.'::DB_TYPE')]) ) {
			$this->connectedDatabases[constant($ChildClassName.'::DB_TYPE')] = array();
		}
		elseif( in_array($DBCONN_NAME, $this->connectedDatabases[constant($ChildClassName.'::DB_TYPE')]) ) {
			$ERROR_STRING = 'This database already connected. Please use another connection name.';
		}
		if(false) {
			// other check for $DBCONN_NAME
		}
		
		require dirname(__FILE__).'/codetpl.DBAbstractConstrShowError.php';
		// Все проверили.
		// Добавляем метку что это соединение уже присутствует
		$this->connectedDatabases[constant($ChildClassName.'::DB_TYPE')][] = $DBCONN_NAME;
		//Возвращаем путь до connection-файла.
		return $DBCONN_FULLNAME;
	}
	
	function __call_undefinedMethod($funcName) {
		throw new AppErrorException('Вызван не существующий метод: '.$funcName);
		return false;
	}
	
	// INTERFACE:
	/*
	// Открыть соединение с БД
	abstract protected function open();
	
	// Закрыть соединение с БД
	abstract public function close();
	
	// выполнить запрос(инструкции) SQL
	abstract public function query($strSql);
	
	// начать транзакцию
	abstract public function begin();
	
	// Завершить странзакцию
	abstract public function commit();
	
	// Откатить транзацкию
	abstract public function rollback();
	*/

	protected function open() {}
	public function close() {}
	public function query() {}
	public function begin() {}
	public function commit() {}
	public function rollback() {}
}


abstract class AbstractDBResult
{
	public static $result; //результат (первоначальный дескриптор)
	public static $arResult; //результат в виде массива после NavStart
	public static $elementID;
	
	protected function AbstractConstructor($ChildClassName) {
		//$ChildClassName = get_class($this);
		require dirname(__FILE__).'/codetpl.DBAbstractConstrTypeCheck.php';
		require dirname(__FILE__).'/codetpl.DBAbstractConstrShowError.php';
	}
	
	public function  __construct($res = NULL) {
		if(is_array($res))
			$this->arResult = $res;
		else
			$this->result = $res;
	}

	public function Fetch() {
		
	}

	public function GetNext() {
		if($arRes = $this->Fetch())	{
			return TildaArray($arRes);
		}
		return $arRes;
	}

	public function GetNextElement() {
		
	}

	public function Dump() {
		
	}
}

interface IAbstractQuery
{
	// Получить строку с плейсхолдерами
	function _get_template();
	
	// Задать строку с плейсхолдерами
	function _set_template($value);
	
	// Задать задать значение плейсхолдера
	function arg($placeholderValue, $position);
	
	// Проверить значение плейсхолдера
	function checkArgValue($position, &$value);
	
	// Получить список плейсхолдеров
	function _get_placeholderList();
	
	// Получить конкретный плейсхолдер строки в виде масива
	function getPlaceholderArray($position);
	
	// Получить список не заданных плейсхолдеров
	function _get_emptyPlaceholders();
}


/**
 * TODO: AbstractSQuery: Доделать проверки для текстовых типов.
 */
abstract class AbstractSQuery extends AbstractLogger implements IAbstractQuery
{
	const __undefined__ = '-- UNDEFINED --';
	const __query_not_ready__ = '-- This query does not ready for use --';
	
	protected function AbstractConstructor($ChildClassName) {
		//$ChildClassName = get_class($this);
		require dirname(__FILE__).'/codetpl.DBAbstractConstrTypeCheck.php';
		require dirname(__FILE__).'/codetpl.DBAbstractConstrShowError.php';
	}
	
	protected $arTypesList = array(
		// int(1)	1 байт		MySQL: TINYINT [-127, 127]
		// uint(1)	1 байт		MySQL: UNSIGNED TINYINT [0, 255]
		// int(2)	2 байта		MySQL: SMALLINT [-32'768, 32'767]
		// uint(2)	2 байта		MySQL: UNSIGNED SMALLINT [0, 65'535]
		// int(4)	4 байта		MySQL: STANDART 32bit INT [-2'147'483'648, 2'147'483'647]
		// uint(4)	4 байтв		MySQL: STANDART 32bit UNSIGNED INT [0, 4'294'967'295]
		// int(8)	8 байтов	MySQL: BIGINT 64bit INT [-9'223'372'036'854'775'808, 9'223'372'036'854'775'807]
		// uint(8)	8 байтов	MySQL: BIGINT 64bit INT [0, 18'446'744'073'709'551'615]
		'bool'				=> 'self::checkArgValue_bool',
		'boolean'			=> 'self::checkArgValue_bool',
	
		// integer
		'int'				=> 'checkArgValue_int4',
		'int1'				=> 'checkArgValue_int1',
		'int2'				=> 'checkArgValue_int2',
		'int4'				=> 'checkArgValue_int4',
		'int8'				=> 'checkArgValue_int8',
		'integer'			=> 'checkArgValue_int4',
	
		'uint'				=> 'checkArgValue_uint4',
		'uint1'				=> 'checkArgValue_uint1',
		'uint2'				=> 'checkArgValue_uint2',
		'uint4'				=> 'checkArgValue_uint4',
		'uint8'				=> 'checkArgValue_uint8',
		'unsigned_int'		=> 'checkArgValue_uint4',
		'uinteger'			=> 'checkArgValue_uint4',
		'unsigned_integer'	=> 'checkArgValue_uint4',
	
		'float'				=> 'checkArgValue_float',
		'ufloat'			=> 'checkArgValue_ufloat',
		'unsigned_float' 	=> 'checkArgValue_ufloat',

		'char'				=> 'checkArgValue_char',
		'varchar'			=> 'checkArgValue_char',

		'text'				=> 'checkArgValue_text',

		'time'				=> 'checkArgValue_datetime',
		'date'				=> 'checkArgValue_datetime',
		'datetime'			=> 'checkArgValue_datetime',

		'whatever'			=> 'checkArgValue_whatever',
	);
	
	protected $regPlaceholderList = '#\?([0-9a-zA-Z\_]{2,20})#';
	
	// Переменная содержит все вхождения плейсхолдеров
	protected $arPlaceholders = array();
	// Значения плейсхолдеров
	protected $arPlaceholdersValues = array();
	// Номера заданных значений
	protected $arPlaceholdersDefined = array();
	// Кол-во плейсхолдеров
	protected $countPlaceholders = 0;
	// Строка разделенная. Содержить промежутки между плейсхолдерами
	protected $arSplitString = array();
	
	protected $_strQuery = '';
	protected $arArguments = array();
	
	protected $throwIfTypeValueCheckFunctionDoesNotExists = false;
	
	public function __construct($template = null) {
		foreach($this->arTypesList as $type => &$arTypeCheckFunction) {
			//d($type.':'.$arTypeCheckFunction);
			if( !is_callable($arTypeCheckFunction) ) {
				if( !method_exists(&$this, $arTypeCheckFunction) ) {
					if($this->throwIfTypeValueCheckFunctionDoesNotExists) {
						$arTypeCheckFunction = 'self::checkArgValue_please_make_typecheck_function';
					}
					else {
						$arTypeCheckFunction = 'self::checkArgValue_whatever';
					}
				}
			}
		}
		if($template != null) {
			$this->_set_template($template);
		}
	}
	
	// Получить строку с плейсхолдерами
	function _get_template() {
		return $this->_strQuery;
	}
	
	// Задать строку с плейсхолдерами
	function _set_template($value) {
		//echo $this->regPlaceholderList;
		$this->_strQuery = $value;
		if( preg_match_all($this->regPlaceholderList, $this->_strQuery, $arPlHMatches) ) {
			//d($arPlHMatches);
			//exit;
			$this->arPlaceholdersValues = array();
			$this->arSplitString = array();
			//d($arPlHMatches);
			foreach($arPlHMatches[0] as $plHIndex => &$placeholder) {
				if(!@isset($this->arTypesList[$arPlHMatches[1][$plHIndex]])) {
					throw new AppErrorException('Type '.$arPlHMatches[1][$plHIndex].' does not exists');
					continue;
				}
				//d($this->arTypesList[$arPlHMatches[1][$plHIndex]]);
				$this->arPlaceholders[$plHIndex] = array(
					'PLACEHOLDER' => $arPlHMatches[0][$plHIndex],
					'POSITION_INDEX' => $plHIndex,
					'POSITION_NUMBER' => ($plHIndex+1),
					'TYPE_NAME' => $arPlHMatches[1][$plHIndex],
					'CHECK_VALUE_FUNCTION' => &$this->arTypesList[$arPlHMatches[1][$plHIndex]]
				);
			}
			unset($arPlHMatches);
			//d($this->arPlaceholders);
			$arSplitStrQueryTemplate = preg_split($this->regPlaceholderList, $this->_strQuery, -1, PREG_SPLIT_OFFSET_CAPTURE);
			//d($arSplitStrQueryTemplate);
			foreach($arSplitStrQueryTemplate as $plHIndex => &$arQueryTplPart) {
				$queryTplPart = $arQueryTplPart[0];
				$this->arSplitString[$plHIndex] = $queryTplPart;
			}
			$this->countPlaceholders = count($this->arPlaceholders);
			for($i=0; $i<$this->countPlaceholders; $i++) {
				$this->arPlaceholdersValues[$i] = self::__undefined__;
			}
			//d($this->arPlaceholders);
			//d($this->arPlaceholdersValues);
		}
	}
	
	public function arg($placeholderValue, $position = 0, $checkValue = true) {
		// $position - считается с 1  параметр передаваемый в ф-ию
		// $realPosition - начинается с 0. По сути индекс в массиве $this->arPlaceholdersValues

		if( $this->isReady() ) {
			return $this;
		}
		$positionIndex = $this->getArgPositionIndex($position);
		// Такое может произойти если все плейсхолдеры уже заменены. см. self::isReady();
		// Условие исключающее ибо выше есть проверка :)
		if($positionIndex<0) {
			return $this;
		}
		$arPLACEHOLDER = &$this->arPlaceholders[$positionIndex];
		if( $checkValue && !$this->checkArgValue($positionIndex, $placeholderValue) ) {
			$this->ERROR = ''
				."Неверное значение плейсхолдера:\n"
				."\tплейсхолдер: {$arPLACEHOLDER["PLACEHOLDER"]}\n"
				."\tпозиция: {$arPLACEHOLDER["POSITION_NUMBER"]}\n"
				."\tиндекс позиции: {$arPLACEHOLDER["POSITION_INDEX"]}\n"
				."\tтип: {$arPLACEHOLDER["TYPE_NAME"]}\n"
				."\tзначение: {$placeholderValue}\n"
				."\tчасть строки относящийся к ошибке: "
				.$this->arSplitString[$arPLACEHOLDER["POSITION_INDEX"]]
			;
			return $this;
		}
		$arPLACEHOLDER['VALUE'] = $placeholderValue;
		$this->arPlaceholdersDefined[$positionIndex] = true;
		$this->arPlaceholdersValues[$positionIndex] = $placeholderValue;

		return $this;
	}
	public function checkArgValue($position, &$value) {
		//d($this->arPlaceholders[$position]);
		$arPLACEHOLDER = &$this->arPlaceholders[$position];
		$checkFunction = &$arPLACEHOLDER['CHECK_VALUE_FUNCTION'];
		if(method_exists(&$this, $checkFunction)) {
			return $this->{$checkFunction}($arPLACEHOLDER, $value);
		}
		else {
			return call_user_func($checkFunction, $arPLACEHOLDER, $value);
		}
	}
	
	public function setType($typeName, $checkFunction, $override = false) {
		if( !$override && @isset($this->arTypesList[$typeName]) ) {
			return false;
		}
		if(!preg_match($this->regPlaceholderList, '\\?'.$typeName)) {
			return false;
		}
		if(
			( is_string($checkFunction) && method_exists($checkFunction) )
			||
			( is_object($checkFunction) && is_callable($checkFunction) )
		) {
			$this->arTypesList[$typeName] = $checkFunction;
			return true;
		}
		return false;
	}
	
	public function _get_placeholderList() {
		return $this->arPlaceholders;
	}
	public function _get_emptyPlaceholders() {
		$arEmptyPlaceholders = array();
		if(!in_array(self::__undefined__, $this->arPlaceholdersValues)) {
			return $arEmptyPlaceholders;
		}
		foreach($this->arPlaceholdersValues as $plIndex => &$plValue) {
			if( $plValue == self::__undefined__ ) {
				$arEmptyPlaceholders[$plIndex] = $this->arPlaceholders[$plIndex];
			}
		}
		return $arEmptyPlaceholders;
	}
	public function getPlaceholderArray($position) {
		$positionIndex = $this->getArgPositionIndex($position);
		$arPLACEHOLDER = $this->arPlaceholders[$positionIndex];
		return $arPLACEHOLDER;
	}
	public function & getPlaceholderArrayLink($position) {
		$positionIndex = $this->getArgPositionIndex($position);
		$arPLACEHOLDER = &$this->arPlaceholders[$positionIndex];
		return $arPLACEHOLDER;
	}
	
	/**
	 * Возвращяет статус готовновти запроса
	 * т.е. заполнены ли все значения (заменены ли все плейсхолдеры в запросе)
	 * @return bool
	 */
	public function isReady() {
		/*if(in_array(self::__undefined__, $this->arPlaceholdersValues)) {
			return false;
		}*/
		if( count($this->arPlaceholdersDefined) < $this->countPlaceholders) {
			return false;
		}
		return true;
	}

	public function _get_strQuery() {
		$strQuery = '';
		//d($this->arSplitString);
		//d($this->arPlaceholdersValues);
		foreach($this->arPlaceholders as $plHIndex => $plH) {
			//d($plHIndex);
			$plHValue = ($this->arPlaceholdersValues[$plHIndex] == self::__undefined__)?$plH:$this->arPlaceholdersValues[$plHIndex];
			$strQuery .= ''
				.$this->arSplitString[$plHIndex]
				.$plHValue
			;
		}
		return $strQuery;
	}

	/**
	 * Просто отдаем запрос в виде строки
	 */
	public function __toString() {
		if(!$this->isReady()) {
			return self::__query_not_ready__.endl;
		}
		return $this->_get_strQuery();
	}
	
	/**
	 * Получить индекс в массиве плейсхолдеров по позиции
	 * Все просто. $placeholderIndex = $placeholderPosition - 1;
	 * $placeholderPosition - начинает счет с '1'
	 * $placeholderIndex - с '0'
	 * @param unknown_type $position
	 */
	protected function getArgPositionIndex($position = 0) {
		//temp см. TODO
		if( $position <= 0 || $position > $this->countPlaceholders ) {
			/*foreach($this->arPlaceholdersValues as $plHIndex => &$plHVal) {
				if($plHVal == self::__undefined__) {
					return $plHIndex;
				}
			}*/
			if( count($this->arPlaceholdersDefined) < $this->countPlaceholders) {
				for($plHIndex=0; $plHIndex < $this->countPlaceholders; $plHIndex++) {
					if(!@isset($this->arPlaceholdersDefined[$plHIndex])) {
						return $plHIndex;
					}
				}
			}
			return -1;
		}
		return $position-1;
	}
	
	/**
	 * Что угодно. Проверка не производится
	 */
	static protected function checkArgValue_whatever(&$arPACEHOLDER, &$value) {
		return true;
	}
	/**
	 * Если произошел вызов этой ф-ии, значит
	 * либо
	 * 	нет значения ['CHECK_VALUE_FUNCTION']
	 * 	в массиве описания типа,
	 * либо
	 * 	в значении ['CHECK_VALUE_FUNCTION']
	 * 	указана несуществующая ф-ия
	 */
	static protected function checkArgValue_please_make_typecheck_function(&$arPACEHOLDER, &$value) {
		throw new AppErrorException(get_class($this).': У типа ('.$arPACEHOLDER['TYPE_NAME'].') нет метода для проверки значения');
	}
	
	static protected function checkArgValue_bool(&$arPACEHOLDER, &$value) {
		switch(strtoupper((string) $value)) {
			case 'TRUE':
			case 'FALSE':
			case '1':
			case '0':
				$value = ($value)?'true':'false';
				return true;
			default:
				return false;
		}
		return true;
	}
	
	
	// int(1)	1 байт		MySQL: TINYINT [-127, 127]
	// uint(1)	1 байт		MySQL: UNSIGNED TINYINT [0, 255]
	// int(2)	2 байта		MySQL: SMALLINT [-32'768, 32'767]
	// uint(2)	2 байта		MySQL: UNSIGNED SMALLINT [0, 65'535]
	// int(4)	4 байта		MySQL: STANDART 32bit INT [-2'147'483'648, 2'147'483'647]
	// uint(4)	4 байтв		MySQL: STANDART 32bit UNSIGNED INT [0, 4'294'967'295]
	// int(8)	8 байтов	MySQL: BIGINT 64bit INT [-9'223'372'036'854'775'808, 9'223'372'036'854'775'807]
	// uint(8)	8 байтов	MySQL: BIGINT 64bit INT [0, 18'446'744'073'709'551'615]
	static protected function checkArgValue_int1(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^(\-)?[0-9]{1,3}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value >127 || $value < -127	) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_int2(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^(\-)?[0-9]{1,5}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value >32768 || $value < -32768	) {
			return false;
		}
		return true;
	}
	
	static protected function checkArgValue_int4(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^(\-)?[0-9]{1,10}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value > 2147483648 || $value < -2147483648	) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_int8(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^(\-)?[0-9]{1,19}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value > 9223372036854775808 || $value < -9223372036854775808	) {
			return false;
		}
		return true;
	}

	static protected function checkArgValue_uint1(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^[0-9]{1,3}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value < 0 || $value > 255 ) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_uint2(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^[0-9]{1,5}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value < 0 || $value > 65535 ) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_uint4(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^[0-9]{1,10}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value < 0 || $value > 4294967295 ) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_uint8(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^[0-9]{1,20}$#', $value) ) {
			return false;
		}*/
		if( !is_numeric($value) ) {
			return false;
		}
		$value = intval($value);
		if( $value < 0 || $value > 18446744073709551615 ) {
			return false;
		}
		return true;
	}
	
	static protected function checkArgValue_float(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^(\-)?[0-9]{1,20}(\.[0-9]{1,20})?$#', $value) ) {
			return false;
		}*/
		if( !is_float($value) ) {
			return false;
		}
		$value = floatval($value);
		return true;
	}
	static protected function checkArgValue_ufloat(&$arPACEHOLDER, &$value) {
		/*if( !preg_match('#^[0-9]{1,20}(\.[0-9]{1,20})?$#', $value) ) {
			return false;
		}*/
		if( !is_float($value) ) {
			return false;
		}
		$value = floatval($value);
		if($value < 0) {
			return false;
		}
		return true;
	}
	static protected function checkArgValue_char(&$arPACEHOLDER, &$value) {
		return true;
	}
	
	static protected function checkArgValue_text(&$arPACEHOLDER, &$value) {
		return true;
	}
	
	/*protected function checkArgValue_datetime(&$arPACEHOLDER, &$value) {
		return true;
	}*/
}

?>