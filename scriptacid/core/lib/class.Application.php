<?php
namespace ScriptAcid;
if(!defined('KERNEL_INCLUDED') || KERNEL_INCLUDED!==true)die();


if( !defined('_APP_DEFAULT_APPLICATION_CLASS') ) {
	define('_APP_DEFAULT_APPLICATION_CLASS', __NAMESPACE__."\\Application");
}
if( !defined('_APP_DEFAULT_COMPONENT_CLASS') ) {
	define('_APP_DEFAULT_COMPONENT_CLASS', __NAMESPACE__."\\Component");
}
if( !defined('_DEFAULT_APPLICATION_TEMPLATE') ) {
	define('_DEFAULT_APPLICATION_TEMPLATE', '_default');
}
if( !defined('_DEFAULT_APPLICATION_TEMPLATE_SKIN') ) {
	define('_DEFAULT_APPLICATION_TEMPLATE_SKIN', 'default');
}

/**
 * Класс приложения.
 * @author pr0n1x, r3c130n
 *
 */
class Application extends AbstractLogger
{
	const MAIN_APPLICATION_CLASS = true;
	const TEMPLATE_FILENAME = 'application_template';
	const DEFAULT_TEMPLATE = _DEFAULT_APPLICATION_TEMPLATE;
	const DEFAULT_TEMPLATE_SKIN = _DEFAULT_APPLICATION_TEMPLATE_SKIN;
	
	const DEFAULT_COMPONENT_CLASS = _APP_DEFAULT_COMPONENT_CLASS;
	private $_COMPONENT_CLASS = _APP_DEFAULT_COMPONENT_CLASS;
	
	
	/**
     * Получить версию системы
     * 
     * @return string версия системы
     */
	static public function getVersion() {
		return '0.1';
	}
	
	/**
	 * Объект синглтона.
	 * @var Application
	 */
	static protected $_instance = false;
	
	/**
	 * Объект-Пользователь
	 * @var User
	 */
	protected $_user;
	
	/**
	 * Объект-БазаДанных
	 * @var Database
	 */
	protected $_database;

	/**
	 * Вернуть синглтон-объект
	 * @return Application
	 */
	static public function & getInstance() {
		if( !self::$_instance || !(self::$_instance instanceof self) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	protected $_pageIsCallable = false;
	protected $_pageFunction = null;
	protected $_pageCallableName = null; 
	protected $_pageFunctionAlreadyCreated = false;
	protected $_arPageParams = array();

	protected $_arScheduledFunctions = array();
	
	protected $_templateName = self::DEFAULT_TEMPLATE;
	protected $_templatePath = null;
	protected $_templateSkin = self::DEFAULT_TEMPLATE_SKIN;
	
	protected $_arTemplateVariables = array();
	
	/**
	* Массив для модификации содержимого страницы.
	* Пример использования - для фильтра матов, подсветки синтаксиса и т.д.
	*/
	protected $_arTemplateTextModifiers = array();

	protected $_arPageJS = array();
	protected $_arPageCSS = array();

	protected $__strCSSFileItemTemplate = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{CSS_FILE_PATH}\" media=\"screen\" />\n";
	protected $__strJSFileItemTemplate = "<script type=\"text/javascript\" src=\"{JS_FILE_PATH}\"></script>\n";
	protected $__strMetaItemTemplate = "";
	

	/**
	 * Потоки данных для self::ob_wrapper();
	 * Передается а ob_start() как обработчик.
	 */
	// Поток данных до обработки
	protected $_streamBefore = '';
	// Поток данных после обработки
	protected $_streamAfter = '';
	
	// Определители переменных шаблона
	const tplVarPrepend = '{';
	const tplVarAppend = '}';
	
	protected function __construct() {
		$this->defaultLoggerSessionSaveKey = self::_LOGGER_SESSION_KEY_DEFAULT_;
		$this->_database = new Database(DBCONN_NAME);
	}
	protected function __clone() {}
	
	
	public function setComponentClass($ComponentClass) {
		if(
			class_exists($ComponentClass, true)
			&&
			defined($ComponentClass.'::MAIN_COMPONENT_CLASS')
			&&
			get_parent_class($ComponentClass) == __NAMESPACE__."\\Component"
		) {
			$this->_COMPONENT_CLASS = $ComponentClass;
			return true;
		}
		AppException::throwException(AppErrorException, 'wrong component class');
		die();
	}
	public function _set_COMPONENT_CLASS($ComponentClass) {
		$this->setComponentClass($ComponentClass);
	}
	public function callComponent($componentName, $templateName, $arComponentParams, $obParentComponent = false) {
		$component = new $this->_COMPONENT_CLASS;
		try {
			return $component->start($componentName, $templateName, $arComponentParams, $obParentComponent);
		}
		catch(ComponentException $except) {
			
			echo 'Error: '.$except->getMessage().'.'.endl;
		}
		unset($component);
	}
	
	public function _get_USER() {
		if( !($this->_user instanceof User) ) {
			$this->_user = new User();
		}
		return $this->_user;
	}
	
	public function _get_DB() {
		if( !($this->_database instanceof Database) ) {
			$this->_database = new Database();
		}
		return $this->_database;
	}
	
	public function _get_TEMPLATE_FILENAME() {
		return self::TEMPLATE_FILENAME;
	}
	
	protected function checkPageFunction(&$makePageFunction) {
		$callable_name = '';
		if(!is_callable($makePageFunction, false, $callable_name) ) {
			if(is_array($makePageFunction) && count($makePageFunction) == 2) {
				$makePageFunction[0] = __NAMESPACE__."\\".$makePageFunction[0];
			}
			elseif(is_string($event)) {
				$makePageFunction = __NAMESPACE__."\\".$makePageFunction;
			}
			if(!is_callable($makePageFunction, false, $makePageFunction) ) {
				//die('Неверный агрумент ф-ии Application::makePage(). Агрумент должен быть или анонимной ф-ией или строкой, содержащей имя ф-ии.');
				return false;
			}
		}
		$this->_pageIsCallable = true;
		$this->_pageFunction = $makePageFunction;
		return true;
	}
	
	public function tryIt($makePageFunction) {
		try {
			$this->checkPageFunction($makePageFunction);
			if( !$this->_pageIsCallable ) {
				throw new AppErrorException(__METHOD__.'Страница не является функцией');
			}
			
			if( $this->_pageFunctionAlreadyCreated ) {
				throw new AppErrorException(__METHOD__.'Нельзя создать одну страницу больше одного раза :)');
			}
			$this->_pageFunctionAlreadyCreated = true;
			
			$this->showPage();
		}
		catch (AppException $except) {
			$except->catchException();
		}
		// Это место можно считать окончаничем работы системы. Востанавливаем обработчик ошибок
		ErrorHandlers::restoreErrorHandler();
		
		// Опционально можем вообще останавливать после отрботки.
		if(!defined(FINISH_STREAM) || FINISH_STREAM != 'N') {
		    exit;
		}
	}
	
	public function makePage($makePageFunction, $autoConnectTemplate = true) {
		try {
			$this->checkPageFunction($makePageFunction);
			if( !$this->_pageIsCallable ) {
				throw new AppErrorException(__METHOD__.'Страница не является функцией');
			}
			
			if( $this->_pageFunctionAlreadyCreated ) {
				throw new AppErrorException(__METHOD__.'Нельзя создать одну страницу больше одного раза :)');
			}
			$this->_pageFunctionAlreadyCreated = true;
			
			$this->addCSS(CORE_PATH.'/css/base.css');
			$this->addCSS(CORE_PATH.'/css/visual.css');
			$this->addCSS(CORE_PATH.'/css/ui-sui.css');
			$this->addJS(CORE_PATH.'/js/jquery/jquery.js');
			$this->addJS(CORE_PATH.'/js/jquery/plugins/jquery-ui.js');
			$this->addJS(CORE_PATH.'/js/jquery/plugins/jquery.ba-hashchange.js');
			$this->addJS(CORE_PATH.'/js/visual.js');
			$this->addJS(CORE_PATH.'/js/core.js');
			$this->setProperty('__SYSTEM_IMPORTANT_JAVASCRIPT__', ''
				.'<script type="text/javascript">'
				.'SACID.Components.ob_POST = '.json_encode($_POST, JSON_FORCE_OBJECT).';'
				.'</script>'
			);

			if(APP_DISPLAY_MODE == 'EDIT')
				ComponentTools::setCurrentFileName('application_template');

			if($autoConnectTemplate) {
				$this->connectTemplate($this->_templateName);
			}
		}
		catch (AppException $except) {
			$except->catchException();
		}
		// Это место можно считать окончаничем работы системы. Востанавливаем обработчик ошибок
		ErrorHandlers::restoreErrorHandler();
		
		// Опционально можем вообще останавливать после отрботки.
		if(defined('PAGE_FINISH_STREAM') && PAGE_FINISH_STREAM === true) {
		    exit;
		}
	}
	
	public function getPageParams() {
		return $this->_arPageParams;
	}
	public function setPageParams($arPageParams) {
		$this->_arPageParams = array_merge($this->_arPageParams, $arPageParams);
	}
	public function _get_arPageParams() {
		return $this->_arPageParams;
	}
	public function _set_arPageParams($arPageParams) {
		$this->_arPageParams = array_merge($this->_arPageParams, $arPageParams);
	}
	
	public function connectTemplate($appTemplateName = false) {

		static $isAlreadyConnected = false;
		
		if($isAlreadyConnected) {
			//throw new AppErrorException('connectTemplate(): Нельзя подключить шаблон приложения дважды.');
			return false;
		}
		
		if($this->_pageIsCallable) {
			
			$appTemplateNameFArg = $appTemplateName;
			if($appTemplateNameFArg) {
				if( !$this->setTemplateName($appTemplateNameFArg) ) {
					throw new AppErrorException('connectTemplate(): Не верно указано имя шаблона приложения: '.$appTemplateNameFArg);
				}
			}

			$appTemplateName = &$this->_templateName;
			$appTemplateSkin = &$this->_templateSkin;

			$APPLICATON = $APP = self::getInstance();
			$USER = $this->_get_USER();
			ob_start(array(&$this, 'ob_processing'));
			Event::run('main', 'OnBeforeApplicationTemplate');
			
			require TEMPLATES_PATH_FULL.'/'.$appTemplateName.'/application_template.php';
			
			$APP->addCSS(TEMPLATES_PATH.'/'.$appTemplateName.'/styles_common.css');
			if( $appTemplateSkin == 'common' || !$APP->addCSS(TEMPLATES_PATH.'/'.$appTemplateName.'/styles_'.$appTemplateSkin.'.css') ) {
				 $APP->addCSS(TEMPLATES_PATH.'/'.$appTemplateName.'/styles_'.self::DEFAULT_TEMPLATE_SKIN.'.css');
			}
			
			$APP->addJS(TEMPLATES_PATH.'/'.$appTemplateName.'/script_common.js');
			if( $appTemplateSkin == 'common' || !$APP->addJS(TEMPLATES_PATH.'/'.$appTemplateName.'/script_'.$appTemplateSkin.'.js') ) {
				 $APP->addJS(TEMPLATES_PATH.'/'.$appTemplateName.'/script_'.self::DEFAULT_TEMPLATE_SKIN.'.js');
			}
			
			Event::run('main', 'OnAfterApplicationTemplate');

			ob_end_flush();
			$isAlreadyConnected = true;
			return true;
		}
		else {
			throw new AppErrorException('connectTemplate(): Страница не создана');
		}
	}

	/**
	 * Получить путь к шаблону
	 * @return String
	 */
	public function getTemplatePath() {
		if($this->_templatePath == null) {
			$this->_templatePath = TEMPLATES_PATH.'/'.$this->_templateName;
		}
		return $this->_templatePath;
	}
	/**
	 * Геттер. Получить путь к шаблону
	 * @return String
	 */
	protected function _get_templatePath() {
		return $this->getTemplatePath();
	}
	/**
	 * Получить имя шаблона
	 * @return String
	 */
	public function getTemplateName() {
		return $this->_templateName;
	}
	/**
	 * Геттер. Получить имя шаблона
	 * @return String
	 */
	protected function _get_templateName() {
		return $this->_templateName;
	}

	/**
	 * Задать имя шаблона приложения
	 * @param String $appTemplateNameFArg
	 * @return bool
	 */
	public function setTemplateName($appTemplateName) {

		// Подчистим от нежелательных элементов.
		$appTemplateNameFArg = $appTemplateName;
		$appTemplateNameFArg = strtolower($appTemplateNameFArg);
		$appTemplateNameFArg = str_replace(array('/..', '../', "\0"), '', $appTemplateNameFArg);

		// Парсим имя шаблона и скин
		$arTemplateName = explode(':', $appTemplateNameFArg);
		if( !@isset($arTemplateName[1]) ) $arTemplateName[1] = '';
		$appTemplateName = trim($arTemplateName[0]);
		$appTemplateSkin = trim($arTemplateName[1]);
		unset($arTemplateName);
		if( strlen($appTemplateName)<1 ) {
			$appTemplateName = $this->_templateName;
		}
		if( strlen($appTemplateSkin)<1 ) {
			$appTemplateSkin = $this->_templateSkin;
		}

		// Проверяем наличие шаблона
		if(
			!file_exists(TEMPLATES_PATH_FULL.'/'.$appTemplateName.'/'.self::TEMPLATE_FILENAME.'.php')
		) {
			return false;
		}
		$this->_templateName = $appTemplateName;
		$this->_templateSkin = $appTemplateSkin;
		return true;
	}
	/**
	 * Сеттер. адать имя шаблона приложения
	 * @param String $appTemplateNameFArg
	 * @return bool
	 */
	function _set_templateName($appTemplateName) {
		$this->setTemplateName($appTemplateName);
	}

	/**
	 * Получить имя скина
	 * @return String
	 */
	public function getTemplateSkin() {
		return $this->_templateSkin;
	}
	/**
	 * Геттер. Получить имя скина
	 * @return String
	 */
	protected function _get_TemplateSkin() {
		return $this->_templateSkin;
	}
	/**
	 * Обработчик ob_start
	 * @param String & $stream
	 * @return & String
	 */
	public function & ob_processing(&$stream) {
		//global $arMeta, $arTV, $streamBefore, $streamAfter;
		$this->_streamBefore = $stream;
	
		// META
		foreach($this->_arTemplateVariables as $key => $value) {
			$stream = str_replace(self::tplVarPrepend.$key.self::tplVarAppend, $value, $stream);
		}
		// TV
		foreach($this->_arTemplateTextModifiers as $key => $value) {
			$stream = str_replace($key, $value, $stream);
		}

		// HEADER JS
		$stream = str_replace(self::tplVarPrepend.'__APP_TEMPLATE_JS_HEADER__'.self::tplVarAppend, $this->getJSHeader(), $stream);
		// HEADER CSS
		$stream = str_replace(self::tplVarPrepend.'__APP_TEMPLATE_CSS_HEADER__'.self::tplVarAppend, $this->getCSSHeader(), $stream);
	
		$this->_streamAfter = $stream;
		return $stream;
	}
	
	public function showPage() {
		if($this->_pageFunctionAlreadyCreated) {
			Event::run('main', 'OnBeforeShowPage');

			if(APP_DISPLAY_MODE == 'EDIT')
				ComponentTools::setCurrentFileName('public_page');

			call_user_func($this->_pageFunction, &$this->_arPageParams);

			Event::run('main', 'OnAfterShowPage');
			
			if(APP_DISPLAY_MODE == 'EDIT')
				ComponentTools::setCurrentFileName('application_template');
		}
	}
	
	function __call_undefinedMethod($funcName) {
		throw new AppErrorException('Вызван не существующий метод: '.$funcName);
		return false;
	}
	
	/////////// PAGE MANIPULATION ///////////////

	/**
	 * @param string $cssFilePath
	 */
	public function addCSS($cssFilePath) {
		if(
			file_exists(DOC_ROOT.$cssFilePath)
			&&
			strtoupper(getFileExt($cssFilePath)) == 'CSS'
		) {
			$this->_arPageCSS[] = $cssFilePath;
			return true;
		}
		return false;
	}


	/**
	 * @param string $jsFilePath
	 */
	public function addJS($jsFilePath) {
		if(
			file_exists(DOC_ROOT.$jsFilePath)
			&&
			strtoupper(getFileExt($jsFilePath)) == 'JS'
		) {
			$this->_arPageJS[] = $jsFilePath;
			return true;
		}
		return false;
	}

	public function getCSSHeader() {
		$strCSSTemplateHeader = '';
		foreach($this->_arPageCSS as &$cssFilePath) {
			$strCSSTemplateHeader .= str_replace('{CSS_FILE_PATH}', $cssFilePath, $this->__strCSSFileItemTemplate);
		}
		return $strCSSTemplateHeader;
	}

	public function getJSHeader() {
		$strJSTemplateHeader = '';
		foreach($this->_arPageJS as &$jsFilePath) {
			$strJSTemplateHeader .= str_replace('{JS_FILE_PATH}', $jsFilePath, $this->__strJSFileItemTemplate);
		}
		return $strJSTemplateHeader;
	}

	/**
	 * Вставить TV для подключения JavaScript
	 */
	public function showJSHeader() {
		echo self::tplVarPrepend.'__APP_TEMPLATE_JS_HEADER__'.self::tplVarAppend;
		$this->showProperty('__SYSTEM_IMPORTANT_JAVASCRIPT__');
	}

	/**
	 * Вставить TV для подключения JavaScript
	 */
	public function showCSSHeader() {
		echo self::tplVarPrepend.'__APP_TEMPLATE_CSS_HEADER__'.self::tplVarAppend;
	}

	/**
	 * Вставить TV для подключения JS, CSS, meta...
	 */
	public function showHeader() {
		$this->showCSSHeader();
		$this->showJSHeader();
	}

	/**
	 * Геттер. Возврящает массив всез CSS-ок.
	 * @return Array
	 */
	public function _get_arTemplateCSS() {
		return $this->_arPageCSS;
	}

	/**
	 * Геттер. Возврящает массив всез JS-ок.
	 * @return Array
	 */
	public function _get_arTemplateJS() {
		return $this->_arPageJS;
	}

	/**
	 * Показать системную панель
	 * @return html
	 */
	function showPanel() {
		return Face::EditorPanel($_SERVER['PHP_SELF']);
	}
	
	/**
	 * Получить <title>
	 * @global array $arMeta
	 * @param bool $bOriginal
	 * @return array
	 */
	function getTitle() {
		if(!@isset($this->_arTemplateVariables['TITLE'])) {
			return '';
		}
		return $this->_arTemplateVariables['TITLE'];
    }
	/**
	 * Задать <title>
	 * @global array $arMeta
	 * @param string $title
	 * @return array
	*/
	function setTitle($title) {
		return $this->_arTemplateVariables['TITLE'] = $title;
	}
	function showTitle() {
		if(!@isset($this->_arTemplateVariables['TITLE'])) {
			$this->_arTemplateVariables['TITLE'] = '';
		}
		echo self::tplVarPrepend.'TITLE'.self::tplVarAppend;
	}
    
	/**
	 * Задать <description>
	 * @global array $arMeta
	 * @param string $descr
	 * @return array
	 */
	function setDescr($descr) {
		return $this->_arTemplateVariables['DESCRIPTION'] = $descr;
	}
	/**
	 * Получить <description>
	 * @global array $arMeta
	 * @return array
	 */
	function getDescr() {
		if(!@isset($this->_arTemplateVariables['DESCRIPTION'])) {
			return '';
		}
        return $this->_arTemplateVariables['DESCRIPTION'];
    }
	function showDescr() {
		if(!@isset($this->_arTemplateVariables['DESCRIPTION'])) {
    		$this->_arTemplateVariables['DESCRIPTION'] = '';
    	}
		echo self::tplVarPrepend.'DESCRIPTION'.self::tplVarAppend;
	}
    
	/**
	 * Задать любое META-свойство
	 * @global array $arMeta
	 * @param string $key
	 * @param string $value
	 * @return array
	 */
	function setProperty($propKey, $propValue) {
        return $this->_arTemplateVariables[$propKey] = $propValue;
	}
	/**
	 * Получить любое свойство заданное SetProperty()
	 * @global array $arMeta
	 * @param string $property
	 * @return array
	 */
	function getProperty($property) {
		if(!@isset($this->_arTemplateVariables[$property])) {
			return '';
		}
        return $this->_arTemplateVariables[$property];
    }
    function showProperty($property) {
    	if(!@isset($this->_arTemplateVariables[$property])) {
    		$this->_arTemplateVariables[$property] = '';
    	}
    	echo self::tplVarPrepend.$property.self::tplVarAppend;
    }
    
	/**
	 * Задать модификатор текста по парам: $key => $value
	 * @param string $key
	 * @param string $value
	 * @return array
	 */
	function TextModifier($key, $value) {
		return $this->_arTemplateTextModifiers[$key] = $value;
    }
    
	/**
	 * Получить путь до текущей директории относительно корня сайта
	 * @return string путь
	 */
	function getCurDir() {
		return substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1);
	}
	
	/**
	 * Получить путь до текущей директории относительно DOCUMENT_ROOT
	 * @return string путь
	 */
	function getCurPath() {
		return  $_SERVER['DOCUMENT_ROOT'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
	
	/**
	 * Получить путь до текущей страницы относительно корня сайта
	 * @return string путь
	 */
	function getCurPage() {
		return $_SERVER['PHP_SELF'];
	}
	
	/**
	 * TODO: Написать ф-ию getCurPageParam
	 * @param unknown_type $strParam - параметры для добавления
	 * @param unknown_type $arParamKill - параметры для удаления из URL
	 * @param unknown_type $get_index_page
	 */
	function GetCurPageParam($strParam = '', $arParamKill = array(), $get_index_page=true) {
		
	}

	/**
	 * Добавить сообщение для вывода пользователю в дальнейшем с помощью ShowMsg()
	 * TODO: Переписать с использованием Logger::_set_MESSAGE('Тест сообщения')
	 * @param string $sMsg
	 */
	function addMsg($sMsg = '') {
		$_SESSION['MESSAGE'] = $sMsg;
	}
	
	/**
	 * Вывод сообщения пользователю, добавленного с помощью функции AddMsg()
	 * TODO: Переписать с использованием Logger::_get_MESSAGE('Тест сообщения')
	 * @param string $sMsg
	 * @param bool $bEcho
	 * @return string
	 */
	function showMsg($sMsg = '', $bEcho = true) {
		if (!empty($sMsg)) {
			$msgText = $sMsg;
		} elseif (isset($_SESSION['MESSAGE']) AND !empty($_SESSION['MESSAGE'])) {
			$msgText = $_SESSION['MESSAGE'];
			unset($_SESSION['MESSAGE']);
		} else {
			return '';
		}
		$str = '<div class="msg">';
		$str .= $msgText;
		$str .= '</div>';
		if ($bEcho) {
			echo $str;
		}
		return $str;
	}
	
	/**
     * Вывод сообщений об ошибке пользователю
     * @param array $arErrors
     * @param bool $bEcho
     * @return string
     */
    function showError($arErrors, $bEcho = true) {
    	$str = '<div class="error">';
    	if (!empty($arErrors) AND is_array($arErrors)) {
	    	$str .= join('<br />' ,$arErrors);
    	} elseif (!empty($arErrors)) {
    		$str .= $arErrors;
    	} else {
    		return '';
    	}
    	$str .= '</div>';
		if ($bEcho) {
			echo $str;
		}
	    return $str;
    }
    
	/**
     *
     * @param string $url
     * @param int $timeOut в секундах
     * @param bool $bJS
     * @return string | bool
     */
    function redirectTo($url, $timeOut = 0, $bJS = false) {
    	/*/
    	if (!empty($url) AND $timeOut == 0) {
    		header('Location: '.$url);
    	} elseif (!empty($url) AND $timeOut == 0 AND !$bJS) {
            header('refresh:'.$timeOut.';url='.$url);
        } elseif (!empty($url) AND $timeOut > 0 AND $bJS) {
    		$js = ''
    			.'<script type="text/javascript">'
    			.'RedirectTo("'.$url.'", '.$timeOut.'000);'
    			.'</script>'
    		;
    		echo $js;
    	} else {
    		return false;
    	}
    	//*/
    	
    	
    	if(empty($url)) {
    		return false;
    	}
    	
    	if($timeOut>0) {
    		if($bJS) {
    			$js = ''
	    			.'<script type="text/javascript">'
	    			.'RedirectTo("'.$url.'", '.$timeOut.'000);'
	    			.'</script>'
	    		;
	    		echo $js;
    		}
    		else {
    			header('refresh:'.$timeOut.';url='.$url);
    		}
    	}
    	else {
    		header('Location: '.$url);
    	}
    }

}
?>