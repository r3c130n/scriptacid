<?php
namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

require_once "core_functions.php";

/**
 * Класс есть статический бинд для сингтона Application (или MyFooBarApplication).
 * Нужен для сокращения кода, а так же для подмены базового класса приложения Application.
 * @author pr0n1x
 */
define('_APP_DEFAULT_APPLICATION_CLASS', __NAMESPACE__.'\\Application');
final class App {
	
	/**
	 * По умолчанию тип переменной:
	 * @var Application
	 */
	static private $_ApplicationInstance = null;

	const DEFAULT_APPLICATITION_CLASS = _APP_DEFAULT_APPLICATION_CLASS;
	static private $_APPLICATITION_CLASS = _APP_DEFAULT_APPLICATION_CLASS;
	
	static public function setApplicationClass($ApplicationClass) {
		if(
			class_exists($ApplicationClass, true)
			&&
			defined($ApplicationClass.'::MAIN_APPLICATION_CLASS')
			&&
			get_parent_class($ApplicationClass) == __NAMESPACE__."\\Application"
		) {
			self::$_ApplicationInstance = null;
			self::$_APPLICATITION_CLASS = $ApplicationClass;
			self::init();
		}
	}
	static public function getApplicationClass() {
		return self::$_APPLICATITION_CLASS;
	}
	
	static private function init() {
		//echo self::$_APPLICATITION_CLASS.' : ';
		if(
			self::$_ApplicationInstance == null
			||
			!(self::$_ApplicationInstance instanceof self::$_APPLICATITION_CLASS)
		) {
			self::$_ApplicationInstance = call_user_func(self::$_APPLICATITION_CLASS.'::getInstance');
			//echo get_class(self::$_ApplicationInstance).' : ';
		}
	}
	
	/**
	 * Вернуть объект. Singleton
	 * @return Application
	 */
	static public function & get() {
		self::init();
		return self::$_ApplicationInstance;
	}
	/**
	 * Вернуть объект. Singleton
	 * @return Application
	 */
	static public function & getInstance() {
		self::init();
		return self::$_ApplicationInstance;
	}
	/**
	 * Вернуть юзера.
	 * @return User
	 */
	static function & USER() {
		self::init();
		return self::$_ApplicationInstance->_get_USER();
	}
	/**
	 * Вернуть объект БД
	 * @return Database
	 */
	static function & DB() {
		self::init();
		return self::$_ApplicationInstance->_get_DB();
	}
	
	
	static function page($makePageFunction, $autoConnectTemplate = true) {
		self::init();
		self::$_ApplicationInstance->makePage($makePageFunction, $autoConnectTemplate);
	}

	/**
	 * Позволяет вызывать методы синглтона Application
	 * через статические методы класса App.
	 * Если конечно бинд не сделан явно.
	 * @param unknown_type $funcName
	 */
	static function __callStatic($funcName, $arArguments) {
		self::init();
		switch(count($arArguments)) {
			case 1:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0]);
				break;
			case 2:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1]);
				break;
			case 3:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2]);
				break;
			case 4:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3]);
				break;
			case 5:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4]);
				break;
			case 6:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5]);
				break;
			case 7:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6]);
				break;
			case 8:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7]);
				break;
			case 9:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8]);
				break;
			case 10:
			default:
				return self::$_ApplicationInstance->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9]);
				break;
		}
	}
}

/**
 * TODO:
 * Эти классы нужны для обработки ошибок на самом низком уровне
 * Как применять AppException классы надо подумать, но это надо
 * @author pr0n1x
 * 
 * Суть:
 * Данные классы необходимы как обработчики типов ошибок
 * E_USER_ERROR
 * E_USER_WARNING 
 * E_USER_NOTICE
 * E_USER_DEPRECATED
 * 
 * Пример использования /test/test_exception.php
 * Обработчик set_error_handler прописан в /scriptacid/core/application.php
 * Сделано это для возможности его включать или отключать через конфиг.
 */
abstract class AppException extends \Exception {

	protected function getCatchedText() {
		if(DEBUG_MODE) {
			$except = &$this;
			$message = "<b>Exception code: ".$except->getCode().": </b>\n";
			$message .= "<b>".$except->getMessage()."</b> <br />\n";
			$message .= " in line ".$except->getLine()." in file ".$except->getFile()."<br />\n";
			$message .= "Trace: <br />\n";
			$message .= str_replace("\n", "<br />\n", $except->getTraceAsString());
			$message .= "<br />\n"."<br />\n";
			return $message;
		}
		
		//$arBacktrace = debug_backtrace();
		//$arShortTrace = array();
		//foreach($arBacktrace as $key => &$arCall) {
		//	$arShortTrace[$key]['file'] = $arCall['file'];
		//	$arShortTrace[$key]['line'] = $arCall['line'];
		//	$arShortTrace[$key]['function'] = $arCall['function'];
		//	$arShortTrace[$key]['class'] = $arCall['class'];
		//}
		//return '<pre>'.print_r($arShortTrace, true).'</pre>';*/
	}
	public function catchException() {
		echo $this->getCatchedText();
	}
	
	final static public function throwException($AppExceptionClass, $message, $code = 0, $line = 0) {
		$AppExceptionClass = __NAMESPACE__.'\\'.$AppExceptionClass;
		
		$AppExceptionChildInstance = new $AppExceptionClass($message, $code, $previous);
		if(get_parent_class($AppExceptionChildInstance) == __CLASS__) {
			$AppExceptionChildInstance->__throwException();
		}
		else {
				$arBacktrace = debug_backtrace();
				$needKey = 0;
				/*foreach($arBacktrace as $key => &$arFCall) {
					//echo 'key: '.$key.endl;
					if($arFCall['file'] == __FILE__) {
						$needKey++;
						//echo 'need key: '.$key.endl;
						break;
					}
				}*/
				if($arBacktrace[$needKey]['file'] == __FILE__) {
					$needKey++;
				}
				$arErrorCall = $arBacktrace[$needKey];
				echo ''
					.'<b>Exception class must be a child of AppException.</b>'.endl
					.$arErrorCall['file'].' (function: '.$arErrorCall['function'].'; line: '.$arErrorCall['line'].')'
					.endl.endl
				;
		}
	}
	public function __throwException() {
		try {
			throw $this;
		}
		catch(self $except) {
			$except->catchException($except);
		}
	}
}
/**
 * TODO: Переопределять catchException()
 * 			в зависимости от типа исключения
 * @author pr0n1x
 *
 */
final class AppErrorException extends AppException {
	public function catchException() {
		//parent::catchException();
		echo $this->getCatchedText();
		// AppErrorException - Это фатальная ошибка - останавливаем выполнение.
		die();
	}
}
final class AppWarningException extends AppException {
	// public function catchException() {}
}
final class AppNoticeException extends AppException {
	// public function catchException() {}
}
final class AppDeprecatedException extends AppException {
	// public function catchException() {}
}
function throwException($AppExceptionClass, $message, $code = 0, $line = 0) {
	AppException::throwException($AppExceptionClass, $message, $code, $line);
}

class SomeExcept extends \Exception {
	
}


final class ErrorHandlers {
	static public $callCountSetErrorHandler = 0;
	static public function setErrorHandler($error_handler = null) {
		if($error_handler === null) {
			$error_handler = __CLASS__.'::triggerPhpError';
		}
		self::$callCountSetErrorHandler++;
		set_error_handler(
			$error_handler
			//,E_USER_ERROR ^ E_USER_WARNING ^ E_USER_NOTICE ^ E_USER_DEPRECATED
		);
	}
	
	static public function restoreErrorHandler() {
		for($i=0; $i<self::$callCountSetErrorHandler; $i++) {
			restore_error_handler();
		}
		self::$callCountSetErrorHandler = 0;
	}
	
	static public function triggerPhpError($errno, $errstr, $errfile = "", $errline = 0, $errcontext = array()) {
		
		/*
		 * If return true or void then standart message will be printed.
		 */
		if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting
	        return;
	    };
		try {
			switch ($errno) {
				case E_ERROR:
				case E_USER_ERROR:
				 	//echo "E_USER_ERROR".endl;
					throw new AppErrorException($errstr);
					break;
				case E_WARNING:
				case E_USER_WARNING:
					//echo "E_USER_WARNING".endl;
					throw new AppWarningException($errstr);
					break;
				case E_NOTICE:
				case E_USER_NOTICE:
					//echo "E_USER_NOTICE".endl;
					throw new AppNoticeException($errstr);
					break;
				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					//echo "E_USER_DEPRECATED".endl;
					throw new AppDeprecatedException($errstr);
					break;
				default:
					return false;				
			}
			return;
		}
		catch(AppException $except) {
			$except->catchException($except);
		}
	}
}


/**
 * Класс для автоматического подключения файлов библиотек(модулей).
 * @author pr0n1x
 */
final class Modules
{
	static private $modules = array();
	static private $classesPaths = array();
	static private $bInit = false;
	static private $directLoadClasses = false;
	static private $debugMe = false;
	
	const global_set = "__GLOBAL_SET__";

	private function  __construct() {}
	private function __clone() {}
	
	static public function init($directLoadClasses = false) {
		if(self::$bInit) {
			return;
		}
		self::$debugMe = (defined("_LIB_LOAD_DEBUG") && _LIB_LOAD_DEBUG === true)?true:false;
		if($directLoadClasses) {
			self::$directLoadClasses = true;
		}
		self::$bInit = true;
	}
	// temporary method
	static public function getClassesArray() {
		return self::$classesPaths;
	}
	
	static public function includeLibFiles($libDirPath,  $argDirectLoadClasses = self::global_set, $debug = self::global_set) {
		if(!self::$bInit) {
			return false;
		}
		if($debug === self::global_set) $debug = self::$debugMe;
		$dctLoadSetSrc = "";
		if($argDirectLoadClasses === self::global_set ) {
			$argDirectLoadClasses = (bool) self::$directLoadClasses;
			$dctLoadSetSrc = "global set";
		}
		else {
			$argDirectLoadClasses = (bool) $argDirectLoadClasses;
			$dctLoadSetSrc = "argument set";
		}
		if($debug) echo "<b>CALL Modules::includeLibFiles($libDirPath)</b><br />";
		removeDirLastSlash($libDirPath);
		$libDirPath = DOC_ROOT.'/'.$libDirPath;
		if(!is_dir($libDirPath)) {
			if($debug) echo "<b>Library directory is incorrect: ".$libDirPath.")</b><br />";
			return false;
		}
		$dirLib = opendir($libDirPath);
		$arFilesList = array();
		$arDLoad = array();
		$arBLoad = array();
		while ( $elementOfDir = readdir($dirLib) ) {
			if (
				$elementOfDir != ".."
				&& $elementOfDir != "."
				&& substr($elementOfDir, strlen($elementOfDir)-4, strlen($elementOfDir)) == ".php"
			) {
				$arFilesList[] = $elementOfDir;
			}
		}
		// Необходимо для регулирования порядка загрузки.
		sort($arFilesList);
		
		$newClassesPaths = array();
		foreach( $arFilesList as $elementOfDir ) {
			// Заполняем массив классов и файлов в которых они лежат
			if(substr($elementOfDir, 0, 6) == "class.") {
				$className = substr($elementOfDir, 6, -4);
				//echo "CLASS NAME: ".$className."<br />";
				$regClassNumPrefix = "#^(([0-9]{1,2}\.){1,2}){1}([a-zA-Z0-9]{2,}){1}$#";
				if (preg_match($regClassNumPrefix, $className, $arClassFileNameMatches)) {
					$className = $arClassFileNameMatches[3];
				}
				
				$arClassName = explode("-",$className);
				if(count($arClassName) < 2) {
					$className = '\\'.__NAMESPACE__.'\\'.$arClassName[0];
				}
				else {
					$className = '\\';
					foreach($arClassName as $keyNameSpaceOrClass => &$nameSpaceOrClass) {
						$className .= (($keyNameSpaceOrClass==0)?"":"\\").$nameSpaceOrClass;
					}
				}
				
				if(!array_key_exists($className, self::$classesPaths)) {
					$newClassesPaths[$className] = $libDirPath."/".$elementOfDir;
				}
			}
			// Заполняем массив bload и dload для послед загрузки.
			elseif( substr($elementOfDir, 0, 6) == "bload." ) {
				$arBLoad[] = $libDirPath."/".$elementOfDir;	
			} 
			elseif( substr($elementOfDir, 0, 6) == "dload." ) {
				$arDLoad[] = $libDirPath."/".$elementOfDir; 
			}
		}
		if($debug) {
			if (count($newClassesPaths)>0) {
				echo "<b>Found new classes: </b>"; echo "<pre>"; print_r($newClassesPaths);echo "</pre>";
			}
			else echo "<b>New classes not found.</b><br />";
		}
		self::$classesPaths = array_merge_recursive(self::$classesPaths, $newClassesPaths);
		
		// Перед классами загружаем bload.
		if($debug) echo "Loadgin BLOAD files: <br />";
		foreach($arBLoad as $filePathBLoad) {
			if($debug) echo "bload: $filePathBLoad <br />";
			require_once $filePathBLoad;
		}
		/**
		 * Если установлена опция прямой загрузки, то загружамем все полученные файлы классов.
		 * Важно было сначала наполнить пул имен классов, что бы не возникла ситуация когда 
		 * подключается файл в котором класс наследует другой класс, файл которого ещё не подключен.
		 * Наполнив в первую очередь пул, мы обеспечили работу ф-ии self::autoloadClasses.
		 * Так что в крайнем случае, сработает __autoload и догрузит необходимые классы из пула
		 */
		if($argDirectLoadClasses) {
			if($debug) echo "DIRECT_LOAD_CLASSES==TRUE. Loading new CLASS files.<br />";
			foreach($newClassesPaths as $className => $classFilePath) {
				if(!class_exists($className, false)) {
					if($debug) echo "load: $className - $classFilePath <br />";
					require_once $classFilePath;
				}
				elseif($debug) echo "load: $className - already included in $classFilePath <br />";
			}
		}
		// После классов загружаем dload.
		if($debug) echo "Loadgin DLOAD files: <br />";
		foreach($arDLoad as $filePathDLoad) {
			if($debug) echo "dload: $filePathDLoad <br />";
			require_once $filePathDLoad;
		}
		return true;
	}
	
	static public function includeModule($moduleID) {
		if(!self::$bInit) {
			return false;
		}
		$moduleID = strtolower($moduleID);
		//echo "<b>call ".__CLASS__."::includeModule(MODULE_ID: ".$moduleID.")</b>".endl;
		if ( !key_exists($moduleID, self::$modules) ) {
			$CurentModulePath = MODULES_PATH_FULL."/".$moduleID;
			@include_once $CurentModulePath."/description.php";
			$MODULE["MODULE_ID"] = $moduleID;
			self::$modules[$moduleID] = $MODULE;
			if (self::$modules[$moduleID]["NAME"] != "") {
				$MODULE = array(); // Очищаем переменную $MODULE
				// Инифиализация перед подключением библиотеки
				if ( file_exists($CurentModulePath."/init.php") ) {
					include_once $CurentModulePath."/init.php";
				}
				Lang::getLangFiles($CurentModulePath."/lang/".LANG_ID."/");
			}
			else {
				return false;
			}
			/*echo "<pre>";
			print_r(self::$classesPaths);
			print_r(self::$modules);
			echo "</pre>";*/
		}
		return true;
	}

	/**
	 * предопределять переменную self::$classesPaths
	 * Пример использования.
	 * CModules::addAutoloadClasses(
	 * 		"catalog",
	 * 		array(
	 * 			"SomeClass1" => "lib/fileWithSomeClasses.php",
	 * 			"SomeClass1" => "lib/fileWithSomeClasses.php",
	 * 		)
	 * );
	 * Пути попадают сразу в self::$classesPaths,
	 * /!\ при чем перезаписывая те значения,
	 *     которые были сформированы автоматически ф-ией self::includeLibFiles()
	 */
	static public function setAutoloadClasses($moduleID, $arClassesFiles, $argDirectLoadClasses = self::global_set, $debug = self::global_set ) {
		if(!self::$bInit) {
			return false;
		}
		if($debug === self::global_set) $debug = self::$debugMe;
		$dctLoadSetSrc = "";
		if($argDirectLoadClasses === self::global_set ) {
			$argDirectLoadClasses = (bool) self::$directLoadClasses;
			$dctLoadSetSrc = "global set";
		}
		else {
			$argDirectLoadClasses = (bool) $argDirectLoadClasses;
			$dctLoadSetSrc = "argument set";
		}

		$moduleID = strtolower($moduleID);
		if(strlen($moduleID)==0) {
			if($debug) echo 'Module name not set. Class-files path is "'.DOC_ROOT.'".'.endl;
			$CurentModulePath = DOC_ROOT;
		}
		elseif(is_dir(MODULES_PATH_FULL."/".$moduleID)) {
			$CurentModulePath = MODULES_PATH_FULL."/".$moduleID;
		}
		else {
			if($debug) echo 'Module name "'.$moduleID.'" is incorrect.'.endl;
			return false;
		}
		if($debug) echo '<b>CALL Modules::setAutoloadClasses('.(($moduleID)?$moduleID:'false').',...)</b>'.endl;
		foreach($arClassesFiles as $className => $arClassFileNameModRelated) {
			if( file_exists($CurentModulePath."/".$arClassFileNameModRelated) ) {
				fixNamespaceName($className);
				if( !class_exists($className, false) ) {
					if($debug) echo "Class not loaded yet. Adding to autoload list: <u>$className</u>".endl;
					self::$classesPaths[$className] = $CurentModulePath."/".$arClassFileNameModRelated;
					if($argDirectLoadClasses) {
						if($debug) echo "Direct loading class($dctLoadSetSrc): $className - ".self::$classesPaths[$className].endl;
						include_once self::$classesPaths[$className];
					}
				}
			}
			else if($debug) echo "File with class <u>$className</u> not found: ".$CurentModulePath."/".$arClassFileNameModRelated.endl;
		}
		//sort(self::$classesPaths);
	}

	static public function autoloadClasses($className) {
		if(!self::$bInit) {
			return false;
		}
		$className = '\\'.$className;
		if(self::$debugMe) echo "<b>autoload:</b> <u>".$className."</u> - ".self::$classesPaths[$className]."<br />";

		if( !@isset(self::$classesPaths[$className]) ) {
			return false;
		}
		if (file_exists(self::$classesPaths[$className])) {
			include_once self::$classesPaths[$className];
			return true;
		}
		return false;
	}
}

class GetSet
{
	protected $prefixGet = "_get_";
	protected $prefixSet = "_set_";
	protected $_DEFAULT_GETTER_ = "DEFAULT_";
	protected $_DEFAULT_SETTER_ = "DEFAULT_";
	
	function & __get($varName) {
		//echo $varName."<br />\n";
		try {
			if( method_exists($this, $this->prefixGet.$varName) ) {
				$funcName = $this->prefixGet.$varName;
			}
			elseif( method_exists($this, $this->prefixGet.$this->_DEFAULT_GETTER_) ) {
				$funcName = $this->prefixGet.$this->_DEFAULT_GETTER_;
			}
			else {
				throw new AppErrorException("Getter for variable \"".$varName."\" does not defined.");
				return false;
			}
			return $this->$funcName();
		}
		catch(AppException $except) {
			$except->catchException();
		}
	}

	function __set($varName, $varValue) {
		try {
			if( method_exists($this, $this->prefixSet.$varName) ) {
				$funcName = $this->prefixSet.$varName;
			}
			elseif( method_exists($this, $this->prefixGet.$this->_DEFAULT_SETTER_) ) {
				$funcName = $this->prefixGet.$this->_DEFAULT_SETTER_;
			}
			else {
				throw new AppErrorException("Setter for variable \"".$varName."\" does not defined.");
				return false;
			}
			return $this->$funcName($varValue);
		}
		catch(AppException $except) {
			$except->catchException();
		}
	}
	
	function __isset($varName) {
		if( method_exists($this, $this->prefixGet.$varName)
			||
			method_exists($this, $this->prefixSet.$varName)
		) {
			return true;
		}
		else {
			return false;
		}
	}
	/**
	 * Этот метод необходим для удобства.
	 * Например в наследуемом классе можно написать ф-ию function getSomeCoolVariable($someParam1, $someParam2) { ... }
	 * При этом будет возможность вызвать этот метод вот так: SomeCoolVariable($someParam1, $someParam2);
	 * Но есть ограничение на максимальное кол-во аргуметов ф-ии = 20. :) Не видел ф-ий в которых больше 6-ти...
	 * 
	 * TODO: Проверить как будет вызываться метод след вида
	 * function & getSomeCoolVariable($someParam1, $someParam2) { ... }
	 * Помоему ссылка передана не будет
	 * 
	 * @param string $funcName
	 * @param array $arArguments
	 */
	function & __call($funcName, $arArguments) {
		if(method_exists($this, 'get'.$funcName)) {
			$funcName = 'get'.$funcName;
			switch(count($arArguments)) {
				case 1:
					return $this->{$funcName}($arArguments[0]);
					break;
				case 2:
					return $this->{$funcName}($arArguments[0], $arArguments[1]);
					break;
				case 3:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2]);
					break;
				case 4:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3]);
					break;
				case 5:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4]);
					break;
				case 6:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5]);
					break;
				case 7:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6]);
					break;
				case 8:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7]);
					break;
				case 9:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8]);
					break;
				case 10:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9]);
					break;
				case 11:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10]);
					break;
				case 12:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11]);
					break;
				case 13:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12]);
					break;
				case 14:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13]);
					break;
				case 15:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14]);
					break;
				case 16:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14], $arArguments[15]);
					break;
				case 17:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14], $arArguments[15], $arArguments[16]);
					break;
				case 18:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14], $arArguments[15], $arArguments[16], $arArguments[17]);
					break;
				case 19:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14], $arArguments[15], $arArguments[16], $arArguments[17], $arArguments[18]);
					break;
				case 20:
				default:
					return $this->{$funcName}($arArguments[0], $arArguments[1], $arArguments[2], $arArguments[3], $arArguments[4], $arArguments[5], $arArguments[6], $arArguments[7], $arArguments[8], $arArguments[9], $arArguments[10], $arArguments[11], $arArguments[12], $arArguments[13], $arArguments[14], $arArguments[15], $arArguments[16], $arArguments[17], $arArguments[18], $arArguments[19]);
					break;
			}
		}
		return $this->__call_undefinedMethod($funcName);
	}
	/**
	 * Если мы в наледуемом классе хотим определить
	 * другую реакцию на выхов несуществующего метода, то 
	 * переопределяем в нем ф-ию __call_undefinedMethod()
	 * в которой можем сделать throw new AppErrorException("Вызван не существующий метод."),
	 * тем самым вызвав фатальное исключение на уровне PHP.
	 */
	function __call_undefinedMethod($funcName) {
		//throw new AppErrorException("Вызван не существующий метод: ".$funcName);
		return false;
	}
}

/**
 * TODO: Добавить помимо Message и Error
 * 			Notice и Warnig 
 * @author pr0n1x
 */
interface ILogger
{
	// Добавить сообщение в хранилище
	function addMessage($msg);
	// Добавить ошибку в хранилище
	function addError($err);

	// Получить все сообщенеи хранилиша
	function getMessages();
	// Получить все ошибки из хранилища
	function getErrors();

	// Получить последнее сообщение
	function getLastMessage();
	// Получить последнюю ошибку
	function getLastError();
	
	// Очистить цепочку сообщений
	function clearMessages();
	// Очистить цепочку ошибок
	function clearErrors();
	
	// Сохранить Ошибки в сессию, что бы использовать при следующем хите
	function saveSessionErrors($key = 0);
	// Сохранить Сообщения в сессию, что бы использовать при следующем хите
	function saveSessionMessages($key = 0);
	// Сохранить и ошибки и сообщения для использования на следубщем хите
	function saveSession($key = 0);
	
	// Востановить пул ошибок из сессии
	function restoreSessionErrors($key = 0);
	// Востановить пул сообщений из сессии
	function restoreSessionMessages($key = 0);
	// Восстановить пулы ошиок и сообщений из сессии
	function restoreSession($key = 0);

	function clearSessionErrors($key = 0);
	function clearSessionMessages($key = 0);
	function clearSession($key = 0);
}

/**
 * Класс для работы с пулом сообщений
 * @var array MESSAGES - пул сообщений
 * @var array ERRORS - пул ошибок
 * @var String ERROR - Последняя ошибка. Если используется слева от "=", заносит ошибку в пул.
 * @var String MESSAGE - Последнее сообщение. Если используется слева от "=", заносит сообщение в пул.
 * @author pr0n1x
 */
class AbstractLogger extends GetSet implements ILogger
{
	const _LOGGER_SESSION_KEY_DEFAULT_ = "_LOGGER_SESSION_KEY_DEFAULT_";
	
	protected $_arLOGGER_MESSAGE = array();
	protected $_arLOGGER_ERROR = array();
	protected $_arLOGGER_NOTICE = array();
	protected $_arLOGGER_WARNING = array();
	protected $_defaultLoggerSessionSaveKey = _LOGGER_SESSION_KEY_DEFAULT_;

	const ERROR = 1;
	const WARNING = 2;
	const NOTICE = 4;
	const MESSAGE = 8;

	public function addMessage($msg) {
		$this->_arLOGGER_MESSAGE[] = htmlspecialchars($msg);
		return htmlspecialchars($msg);
	}

	public function addError($err) {
		$this->_arLOGGER_ERROR[] = htmlspecialchars($err);
		return htmlspecialchars($err);
	}

	public function getMessages() {
		return $this->_arLOGGER_MESSAGE;
	}

	public function getErrors() {
		return $this->_arLOGGER_ERROR;
	}

	public function getLastMessage() {
		if ( count($this->_arLOGGER_MESSAGE) > 0 ) {
			return $this->_arLOGGER_MESSAGE[count($this->_arLOGGER_MESSAGE)-1];
		}
		return false;
	}
	public function getLastError() {
		if ( count($this->_arLOGGER_ERROR) > 0 ) {
			return $this->_arLOGGER_ERROR[count($this->_arLOGGER_ERROR)-1];
		}
		return false;
	}
	
	public function clearErrors() {
		$this->_arLOGGER_ERROR = array();
	}
	
	public function clearMessages() {
		$this->_arLOGGER_MESSAGE = array();
	}
	
	public function saveSessionErrors($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		$_SESSION["SCD_LOGGER"][$key]["ERRORS"] = $this->_arLOGGER_ERROR;
	}

	public function saveSessionMessages($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		$_SESSION["SCD_LOGGER"][$key]["MESSAGES"] = $this->_arLOGGER_MESSAGE;
	}
	
	public function restoreSessionErrors($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		if(isset($_SESSION["SCD_LOGGER"][$key]["ERRORS"]) && is_array($_SESSION["SCD_LOGGER"][$key]["ERRORS"])) {
			$this->_arLOGGER_ERROR = $_SESSION["SCD_LOGGER"][$key]["ERRORS"];
		}
	}
	
	public function restoreSessionMessages($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		if(isset($_SESSION["SCD_LOGGER"][$key]["MESSAGES"]) && is_array($_SESSION["SCD_LOGGER"][$key]["MESSAGES"])) {
			$this->_arLOGGER_MESSAGE = $_SESSION["SCD_LOGGER"][$key]["MESSAGES"];
		}
	}
	
	public function clearSessionErrors($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		if( isset($_SESSION["SCD_LOGGER"][$key]["ERRORS"]) ) {
			$_SESSION["SCD_LOGGER"][$key]["ERRORS"] = array();
		}
	}
	public function clearSessionMessages($key = 0) {
		if($key === 0) {
			$key = $this->_defaultLoggerSessionSaveKey;
		}
		if( isset($_SESSION["SCD_LOGGER"][$key]["MESSAGES"]) ) {
			$_SESSION["SCD_LOGGER"][$key]["MESSAGES"] = array();
		}
	}
	
	public function saveSession($key = 0) {
		$this->saveSessionErrors($key);
		$this->saveSessionMessages($key);
	}
	
	public function restoreSession($key = 0) {
		$this->restoreSessionMessages($key);
		$this->restoreSessionErrors($key);
	}
	public function clearSession($key = 0) {
		$this->clearSessionErrors($key);
		$this->clearSessionMessages($key);
	}
	
		
	
	// GetSet
	
	public function _get_defaultLoggerSessionSaveKey() {
		return $this->_defaultLoggerSessionSaveKey;
	}
	public function _set_defaultLoggerSessionSaveKey($value) {
		$this->_defaultLoggerSessionSaveKey = $value;
	}
	
	public function _get_MESSAGES() {
		return $this->_arLOGGER_MESSAGE;
	}
	
	public function _get_ERRORS() {
		return $this->_arLOGGER_ERROR;
	}
	
	public function _get_LAST_ERROR() {
		return $this->getLastError();
	}
	
	public function _get_ERROR() {
		return $this->getLastError();
	}
	
	public function _get_LAST_MESSAGE() {
		return $this->getLastMessage();
	}
	
	public function _get_MESSAGE() {
		return $this->getLastMessage();
	}
	
	/*public function _get_DEFAULT_() {
		return false;
	}*/
	
	public function _set_ERROR($value) {
		$this->addError($value);
	}
	
	public function _set_MESSAGE($value) {
		$this->addMessage(($value));
	}
	
	public function _set_MESSAGES($value) {
		if( is_array($value) && count($value)==0 ) {
			$this->clearMessages();
		}
		elseif($value == 0) {
			$this->clearMessages();
		}
	}
	
	public function _set_ERRORS($value) {
		if( is_array($value) && count($value)==0 ) {
			$this->clearErrors();
		}
		elseif($value == 0) {
			$this->clearErrors();
		}
	}
}
final class Logger extends AbstractLogger {
	function __construct($defaultLoggerSessionSaveKey = self::_LOGGER_SESSION_KEY_DEFAULT_) {
		$this->_defaultLoggerSessionSaveKey = $defaultLoggerSessionSaveKey;
	}
}

/**
 * Класс задумывался как конфиг, но оказался не очень удобен для такой задачи
 * из-за ограниченных возможностей PHP (нельзя сказать static function __get или static function __set),
 * потому был переименован из Config в StaticStorage.
 * 
 * Тем не менее может успешно использоваться для хранения данных,
 * которые остаются неизменными во время всего периода исполнения.
 * Преимущесто перед стандартными константами в том, что тут можно
 * хранить сложные данные, объекты и прочее.
 * 
 * Примеры использования.
 * StaticStorage::define("DB_HOST", "localhost");
 * //Config::define("DB_HOST", "localhost"); // Это вызовет исключени и прекратит исполнение приложения - Данные надежно защищены.
 * echo StaticStorage::get()->DB_HOST."!<br />";
 * echo StaticStorage::get("DB_HOST")."!<br />";
 * 
 * StaticStorage::define(
 *	"SOME_NOT_SIMPLE_VALUE",
 *	array(
 * 		"VAL1" => 1,
 * 		"VAL2" => 2
 * 	)
 * );
 * StaticStorage::define("SOME_USER", new User);
 * StaticStorage::get("SOME_USER")->GetID();
 * или так
 * StaticStorage::get()->SOME_USER->GetID();
 * Если смущает длинное написание можно сделать так:
 * 		final class DS extends StaticStorage {}
 * 		DS::get()->SOME_USER->GetID();
 * 		Примечание: Оба класса будут работать с одими и теми же данными. Singleton Pattern.
 * 		Пока не удалось разделить данные при элементарном наследовании.
 * 		Ибо работаем со статическими переменными и функциями.
 * @author pr0n1x
 */
class Storage
{
	const ERR_NO_VALUE = 1;
	const ERR_CANOT_CH_CNF_VAL = 2;
	const ERR_CANOT_USE_MIXED_KEY = 3;
	const ERR_KEY_ALREADY_DEFINED = 4;
	
	protected static $arSTORAGE = array();
	protected static $obSTORAGE;
	protected static $bInit = false;
	
	
	private function __construct(){}
	private function __clone(){}
	
	public function & get($confKey = "__NOT_DEFINED_CONF_KEY__") {
		if(!self::$bInit) {
			self::$obSTORAGE = new self;
			self::$bInit = true;
		}
		if($confKey == "__NOT_DEFINED_CONF_KEY__") {
			return self::$obSTORAGE;
		}
		return self::$obSTORAGE->$confKey;
	}

	public static function add($confKey, $confValue = null) {
		//echo $confKey." ".$confValue."<br />";
		try {
			if( !is_numeric($confKey) && !is_string($confKey)) {
				throw new \Exception("Allowed only String or Numeric "._CLASS__." keys.", self::ERR_CANOT_USE_MIXED_KEY);
			}
			if(self::exists($confKey)) {
				throw new \Exception(__CLASS__." key \"$confKey\" already defined.", self::ERR_KEY_ALREADY_DEFINED);
			}
			self::$arSTORAGE[$confKey] = $confValue;					
		}
		catch(\Exception $except) {
			self::showExcaptionText($except);
		}
	}
	
	public static function exists($varName) {
		if(isset(self::$arSTORAGE[$varName])) {
			return true;
		}
		return false; 
	}
	
	/**
	 * TODO: Здесь необходимо подумать.
	 * Если обращение в прототипе ф-ии стоит амперсанд, то
	 * Вызывая так
	 * StaticStorage::get()->SOME_NOT_SIMPLE_VALUE["VAL1"] = 3;
	 * данные изменятся, если не будет амперсанда, то весь массив SOME_NOT_SIMPLE_VALUE
	 * будет доступен только для чтения.
	 * Один минус. Если амперсанда не будет,
	 * то массив при возврате будет копироваться, что приведет к
	 * потерям в памяти при работе с большими массивами
	 */
	function & __get($varName) {
		//echo $varName;
		//print_r(self::$arSTORAGE);
		try {
			if(!self::exists($varName)) {
				throw new \Exception("".get_class($this)." key \"$varName\" does not exist.", self::ERR_NO_VALUE);
			}
			else {
				return self::$arSTORAGE[$varName];
			}
		}
		catch(\Exception $except) {
			self::showExcaptionText($except);
		}
	}
	
	function __set($varName, $value) {
		try {
			if(self::exists($varName)) {
				throw new \Exception("Can't modify ".__CLASS__." values.", self::ERR_CANOT_CH_CNF_VAL);
			}
			else {
				self::add($varName, $value);
			}
		}
		catch(\Exception $except) {
			self::showExcaptionText($except);
		}
		
	}
	
	function __isset($varName) {
		return self::exists($varName);
	}
	
	protected static function showExcaptionText(\Exception $except) {
		$return = "Exception code ".$except->getCode().": \n";
		$return .= $except->getMessage()." <br />\n";
		//$return .= " in line ".$except->getLine()." in file ".$except->getFile()."<br />\n";
		$return .= "Trace: <br />\n";
		$return .= str_replace("\n", "<br />\n", $except->getTraceAsString());
		echo $return;
		exit;
	}
}

/**
 * enum() :)
 * It's not necessary, but can come in handy from time to time.
 * using: 
 * enum('ONE','TWO','THREE');
 * echo ONE, ' ', TWO, ' ', THREE;
 */
function enum() {
	try {
		$args = func_get_args();
		foreach($args as $key=>$arg) {
			if(defined($arg)) {
				throw new \Exception("Redefinition of defined constant.");
			}
			define($arg, $key);
		}
	}
	catch(\Exception $except) {
		echo "Exception code: ".$except->getCode()." in line ".$except->getLine().": "
			.$except->getMessage()."<br />";
		exit;
	}
}

final class Bench {
	static protected $_arStartTimes = array();
	static protected $_arStopTimes = array();
	static public function startTime($key) {
		self::$_arStartTimes[$key] = microtime(true);
	}
	static public function stopTime($key) {
		self::$_arStopTimes[$key] = microtime(true);
		return (self::$_arStopTimes[$key] - self::$_arStartTimes[$key]);
	}
	static public function getTime($key) {
		return (self::$_arStopTimes[$key] - self::$_arStartTimes[$key]);
	}
}
//Bench::startTime('_this_need_for_correct_bench_at_first_call');
//Bench::stopTime('_this_need_for_correct_bench_at_first_call');

$GLOBAL['_GLOBAL_BENCH_START_TIMES'] = array();
$GLOBAL['_GLOBAL_BENCH_STOP_TIMES'] = array();
function startBench($key) {
	global $_GLOBAL_BENCH_START_TIMES;
	$_GLOBAL_BENCH_START_TIMES[$key] = microtime(true);
}

function stopBench($key) {
	global $_GLOBAL_BENCH_START_TIMES, $_GLOBAL_BENCH_STOP_TIMES;
	$_GLOBAL_BENCH_STOP_TIMES[$key] = microtime(true);
	return ($_GLOBAL_BENCH_STOP_TIMES[$key] - $_GLOBAL_BENCH_START_TIMES[$key]);
}
function getBench($key) {
	return ($_GLOBAL_BENCH_STOP_TIMES[$key] - $_GLOBAL_BENCH_START_TIMES[$key]);
}
?>