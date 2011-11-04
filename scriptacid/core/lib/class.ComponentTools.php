<?php namespace ScriptAcid;
class ComponentTools {

	// Параметры общие для всех компонентов.
	static protected $_arComponentSettingsDefaults = array(
		'TEMPLATE' => array(
			'SKINS_LIST' => array(
				'default' => 'Скин по умолчанию',
				'inherit' => 'Наследовать скин',
			)
		),
		// Группы параметров
		'PARAMETERS_GROUPS' => array(
			'MAIN_PARAMS' => array('NAME' => 'Основные параметры', 'SORT' => 200),
			'MAIN_DATASOURCE_PARAMS' => array('NAME' => 'Основные натсройки источника данных', 'SORT' => 300),
			'ADDITIONAL_PARAMS' => array('NAME' => 'Дополнительные параметры', 'SORT' => 400),
			'TEMPLATE_PARAMS' => array('NAME' => 'Параметры шаблона компонента', 'SORT' => 500),
			'LINK_TEMPLATES' => array('NAME' => 'Шаблоны ссылок', 'SORT' => 600),
			'HIDDEN' => array('NAME' => 'Служебюные/вспомогательные параметры', 'SORT' => 10000),
			'EXPERIMENTAL' => array('NAME' => 'Экспериментальные параметры', 'SORT' => 10001),
		),
		// Парметры
		'PARAMETERS' => array(
			'CACHE_TIME' => array(
				'NAME' => 'Время кеширования',
				'GROUP' => 'MAIN_PARAMS',
				'TYPE' => 'STRING',
				'DEFAULT' => '3600',
				'SORT' => 200
			),
			'SET_TITLE' => array(
				'NAME' => 'Установить заголовок',
				'GROUP' => 'MAIN_PARAMS',
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => "N",
				'SORT' => 200
			),
			// Параметр Режима AJAX
			'COMPONENT_AJAX_MODE' => array(
				// Имя парметра
				'NAME' => 'Режим AJAX',
				// Группа параметра
				'GROUP' => 'MAIN_PARAMS',
				// Тип отображения в форме редактирования параметров
				// варианты = (STRING|LIST|CHECKBOX||TEXT|HIDDEN)
				'TYPE' => 'LIST',
				// Опции для формы типа список
				'LIST_ITEMS' => array(
					'OFF' => 'Выключен',
					'AJAX' => 'Стандартный AJAX (XML или (X)HTML)',
					'AJAX-JSON' => 'AJAX через JSON (AJAJ)',
				),
				'LIST_SETTINGS' => array(
					'MULTIPLE' => 'N',
					'RADIO' => 'N'
				),
				'DEFAULT' => 'OFF',
				'SORT' => 100
			),

			'COMPONENT_AJAX_SEND_PAGE_POST' => array(
				'NAME' => 'Посылать POST-запрос страницы в компонент при первом асинхронном вызове компонента. Служебный.',
				'GROUP' => 'HIDDEN',
				'TYPE' => 'HIDDEN',
				'VALUE' => 'N',
				'SORT' => 20000
			),

			'DELAY_COMPONENT' => array(
				'NAME' => 'Отложить выполнение компонента',
				'GROUP' => 'EXPERIMENTAL',
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
				'SORT' => 20010
			),
			'DELAY_TEMPLATE' => array(
				'NAME' => 'Отложить выполнение компонента',
				'GROUP' => 'EXPERIMENTAL',
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
				'SORT' => 20010
			),
		),
		/* === Группа параметров ['GROUP'] ===
		 * 		Передается один из ключей секции 'PARAMETERS_GROUPS'
		 *
		 * === Занчение(по умолчанию) ['VALUE'] или ['DEFAULT'] ===
		 * ['DEFAULT'] - используетася когда ['TYPE'] == (STRING|LIST|CHECKBOX|TEXT)
		 * ['VALUE'] - используетася когда ['TYPE'] == (HIDDEN)
		 * Однако если перепутать, значение все равно будет взято корректно.
		 *
		 * === Имя параметра ['NAME'] ===
		 * Просто название компонента на языке локализации.
		 *
		 * === Типы параметров ['TYPE'] ===
		 * = основные типы =
		 * STRING - просто строка <input type="text" ... />
		 * LIST - список <select ... >
		 *		LIST_ITEMS - список значений значение
		 *		LIST_SETTINGS - параметры списка
		 *			LIST_SETTINGS
		 *			LIST_SETTINGS['MULTIPLE'] = (Y|N) - множественный выбор
		 *			LIST_SETTINGS['RADIO'] = (Y|N) - в виде radio-button
		 *				Если активны и LIST_SETTINGS['MULTIPLE'] и LIST_SETTINGS['RADIO']
		 *				тогда отображается список чекбоксов для этого параметра
		 *			LIST_SETTINGS['LINES'] = (int) число строк если LIST_SETTINGS['MULTIPLE'] = 'Y'
		 *
		 * CHECKBOX - <input type="checkbox" ... />
		 * TEXT - <textarea ... >
		 * HIDDEN - это когда параметр не отображается и редактировать его нельзя
		 *		/!\ Очень важное примечание.
		 *		будут добавлены к списку параметров даже
		 *		если параметр не указан в вызове компонента.
		 * 		Их значения при каждом визуальном редактировании устанавливаются в ['VALUE'] или в ['DEFAULT']
		 *
		 * === Перманетный параметр ['PERMANENT'] ===
		 * 		это когда параметр отображается, но отредактировать его нельзя.
		 *
		 *
		 */

	);

	/**
	 * Счетчик компонентов выполненных в определенных файлах.
	 * Этими файлами могут быть:
	 * 1. Публичные страницы
	 * 2. Шаблон приложения
	 * 3. Вклчаемая область
	 * 4. Шаблон компонента
	 * @var Array
	 */
	static protected $_arComponentsCallsListInFile = array();
	static protected $_arComponentsCallsCountInFile = array();
	static protected $_currentFile = null;
	
	/**
	 * Проверить существование и задать имя файла
	 * @param String $fileName
	 */
	static public function checkFile(&$fileName) {
		if($fileName == 'application_template') {
			$fileName = App::get()->getTemplatePath().'/'.App::get()->TEMPLATE_FILENAME.'.php';
		}
		elseif($fileName == 'public_page') {
			$fileName = App::get()->getCurPage();
		}
		
		if( is_file(DOC_ROOT.'/'.$fileName) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Задать имя файла, в которых происходят вызовы компонентов
	 * @param $fileName - имя файла
	 * @return bool - статус выполнения - удалось или нет
	 */
	static public function setCurrentFileName($fileName) {
		if(self::checkFile($fileName)) {
			self::$_currentFile = $fileName;
			return true;
		}
		return false;
	}
	
	/**
	 * Вернуть имя файла в котором происходят вызовы компонентов
	 */
	static public function getCurrentFileName() {
		return self::$_currentFile;
	}
	
	/**
	 * Зарегистрировать компонент 
	 * @param Array $arComponent
	 * @return void
	 */
	static public function registerComponentInFile($arComponentCall) {
		if(self::$_currentFile == null) {
			self::$_currentFile = 'unknown';
		}
		if(!@isset(self::$_arComponentsCallsCountInFile[self::$_currentFile])) {
			self::$_arComponentsCallsCountInFile[self::$_currentFile] = 0;
		}
		$arComponentCall['CALL_NUMBER'] = self::$_arComponentsCallsCountInFile[self::$_currentFile];
		self::$_arComponentsCallsListInFile[self::$_currentFile][$arComponentCall['CALL_NUMBER']] = $arComponentCall;
		self::$_arComponentsCallsCountInFile[self::$_currentFile]++;
	}
	
	static public function getComponentListInFile($fileName = 'all') {
		if($fileName == 'all') {
			return self::$_arComponentsCallsListInFile;
		}
		if(!self::checkFile($fileName)) {
			return false;
		}
		return self::$_arComponentsCallsListInFile[$fileName];
	}
	
	/**
	 * Находит компонент по ключу вызова в файле
	 * @param String $fileName - Имя файла
	 * @param String $callKey - ключ вызова
	 * @return int - Номер вызова в файле || -1 если не найдено
	 */
	static public function findInFileComponentNumByCallKey($fileName, $callKey) {
		if(!self::checkFile($fileName)) {
			return -1;
		}
		if(!@isset(self::$_arComponentsCallsListInFile[$fileName])) {
			return -1;
		}
		foreach(self::$_arComponentsCallsListInFile[$fileName] as $arComponentCall) {
			if($arComponentCall['CALL_KEY'] == $callKey) {
				return $arComponentCall['CALL_NUMBER']; 
			}
		}
		return -1;
	}
	
	/////////// PHP-PARSING ///////////

	/**
	 * Распарсить строку(HTML+PHP)
	 * @param String $strPhpCode - строка содержащая PHP-код для парсинга
	 * @param String [$findTarget = 'all'] -
	 * 		Что ищем: (component|all|all_in_one|custom)
	 * 			component - разбираем на компоненты
	 * 			all - разбираем на все возможные php-теги
	 *  		all_on_one - разбирает на все возможные php-теги,
	 *  					 но возвращает отдельные массивы с компонентами и с costom-вхождениями
	 *  		custom - разираем по $customPattern
	 * @param String [$customPattern = null] -
	 * 		Пользовательской регулярное выражени для поиска 
	 * 		Используется совместно с $findTarget = (all_in_one|custom) 
	 * 		В других ситуациях игнорируется
	 * @return Array - массив с вхождениями
	 */
	static public function parseString(&$strPhpCode, $findTarget = 'all', $customPattern = null) {
		/**
		 * TODO: Сделать отдельное вхождение для переменной в которую компонент
		 * сохраняет значение ComponentCallKey. Любой компонент возвращает это значение
		 * вот так: $thisCmpCallKey = App::callComponent(...)
		 * Оно необходимо для связывания компонентов между собой.
		 * вот нам необходимо запоминать вот это вхождение ($thisCmpCallKey = )
		 * Для того, что бы при его наличии возвращать его обратно на страницу.
		 * @var $componentPattern
		 */
		$componentPattern = 
			'((?#componentPattern)'
				//.'(?P<CALLKEYVARS>'
				.'(?:'
					.'(?#returnedComponentCallKeyVariable)'
					.'(\$[a-zA-Z]{1}?[a-zA-Z0-9\_]+)'
					.'(?#assignmentOperator)'
					.'(?:[\s|\n|\t]?\=[\s|\n|\t]?)'
				.')?'
				.'(?:(?#componentCall)'
					.'App\:\:callComponent\((?:[\s\S]*?)'
					.'|'
					.'$APP-\>callComponent\((?:[\s\S]*?)'
					.'|'
					.'$APPLICATION-\>callComponent\((?:[\s\S]*?)'
					.'|'
					.'App\:\:get\(\)-\>callComponent\((?:[\s\S]*?)'
				.')'
			.')'
		;
		$allPattern = '(?:(?#allPattern)[\s\S]*?)';
		$findTargetPattern = '(?#findTarget)';
		
		switch($findTarget) {
			case 'all':
				$findTargetPattern = $allPattern;
				break;
				
			case 'component':
				//$componentPattern = str_replace('(?#componentPattern)', '?:(?#componentPattern)', $componentPattern);
				$findTargetPattern = $componentPattern;
				break;
								
			case 'all_in_one':
				$findTargetPattern = (($customPattern!=null)?$customPattern.'|':'').$componentPattern.'|'.$allPattern;
				break;
				
			case 'custom':
				if(!$customPattern) {
					return false;
				}
				$findTargetPattern = $customPattern;
				break;
			
			default:
				return false;
		}

		$phpEntriesPreg =  '~(?P<WHITESPACES>[\t\ ]*?)<\?'
			.'(?:php[\s|\n|\t]+)?'
			.'(?:'
				.$findTargetPattern
			.')'
		.'\?>~mi';
		$arPhpEntries = array(
			'MATCHES' => array(),
			'SPLIT_STRING' => array()
		);
		if( preg_match_all($phpEntriesPreg, $strPhpCode, $arMatches, PREG_OFFSET_CAPTURE) ) {
			//d($arMatches);
			$arPhpEntries['MATCHES'] = $arMatches[0];
			$arPhpEntries['MATCHES_ALL'] = $arMatches;
			$arPhpEntries['SPLIT_STRING'] = preg_split($phpEntriesPreg, $strPhpCode, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
		}
		return $arPhpEntries;
	}
	/**
	 * Разобрать php-файл функцией self::parseString()
	 * @param $fileName - имя файла для парсинга
	 * @param $findTarget - что ищем - подробности в описании self::parseString()
	 * @param $customPattern - дополнительный шаблон для поиска - подробности в описании self::parseString()
	 * @return Array - массив вхождений
	 */
	static public function parseFile($fileName, $findTarget = 'all', $customPattern = null) {
		//echo $fileName;
		if(!self::checkFile($fileName)) {
			return false;
		}
		//echo $fileName;

		$APP = $APPLICATION = App::get();
		$ApplicationClass = App::getApplicationClass();

		$strFileCode = file_get_contents(DOC_ROOT.'/'.$fileName);
		return self::parseString($strFileCode, $findTarget, $customPattern);
	}
	
	/**
	 * Заменить компонент в файле. 
	 * @param int $componentNum - номер компонента в данном файле
	 * @param String $arComponent - массив описания вызова компонента, который надо поставить на место $componentNum
	 * @param Sreing $fileName - файл, в котором необходимо поменять вызов компонента
	 * @return String - строка c php-кодом файла после замены параметров искомого компонента
	 */
	static public function replaceComponentInFileContent($componentNum, $arComponentCall, $fileName) {
		if( !self::checkFile($fileName) ) {
			throw new ComponentException('wrong file name for replace component', ComponentException::E_CMP_EDIT_WRONG_FILE);
			return false;
		}
		if( !($arParse4Cmp = self::parseFile($fileName, 'component')) ) {
			throw new ComponentException('file parse fail', ComponentException::E_CMP_EDIT_PARSE_FAIL);
			return false;
		}
		//test//
		//d($arParse4Cmp['MATCHES_ALL']);
		//throw new ComponentException('test exception', self::E_PARSE_FAIL, $arParse4Cmp);
		//return false;
		if( !@isset($arParse4Cmp['MATCHES'][$componentNum]) ) {
			//d($arParse4Cmp['MATCHES']);
			throw new ComponentException('wrong component number', ComponentException::E_CMP_EDIT_WRONG_CMP_NUM, $arComponentCall);
			return false;
		}
		if(
			empty($arComponentCall['COMPONENT_NAMESPACE'])
			|| empty($arComponentCall['COMPONENT_NAME'])
			|| empty($arComponentCall['TEMPLATE_NAME'])
			|| empty($arComponentCall['TEMPLATE_SKIN'])
			|| !is_array($arComponentCall['PARAMS'])
		) {
			throw new ComponentException('wrong component call array', ComponentException::E_CMP_EDIT_WRONG_CALL_ARRAY);
			return false;
		}

		// Это просто кол-во отступов(табов, пробелов) перед началом вызова компонента.
		$whiteOffset = '';

		$whiteOffset = $arParse4Cmp['MATCHES_ALL']['WHITESPACES'][$componentNum][0];
		$arParse4Cmp['MATCHES'][$componentNum][0] = 
			$whiteOffset."<?php App::callComponent(\n"
				.$whiteOffset."\t'".$arComponentCall['COMPONENT_NAMESPACE'].':'.$arComponentCall['COMPONENT_NAME']."',\n"
				.$whiteOffset."\t'".$arComponentCall['TEMPLATE_NAME'].':'.$arComponentCall['TEMPLATE_SKIN']."',\n"
				.$whiteOffset."\t".self::convertArray2PhpCode($arComponentCall['PARAMS'], $whiteOffset."\t")."\n"
			.$whiteOffset.');?>'
		;
		
		$strResult = '';
		foreach($arParse4Cmp['SPLIT_STRING'] as $key => &$arStrPart) {
			if($arParse4Cmp['MATCHES'][$key][1] < $arStrPart[1]) {
				$strResult .= $arParse4Cmp['MATCHES'][$key][0].$arStrPart[0];
			}
			else {
				$strResult .= $arStrPart[0].$arParse4Cmp['MATCHES'][$key][0];
			}
		}

		return $strResult;
	}
	/**
	 * Возвращает строку с php-кодом массива переданного на вход
	 * @param Array $array - входной массив, для вывода в виде php-кода
	 * @param String $whiteOffset - отступ от начала каждй строки(для красоты)
	 */
	static protected function convertArray2PhpCode($array, $whiteOffset = '') {
		$strResult = "array(\n";
		foreach($array as $paramName => &$paramValue) {
			if(!is_array($paramValue)) {
				$strResult .= $whiteOffset."\t'".$paramName."' => '".$paramValue."',\n";
			}
			else {
				$strResult .= $whiteOffset."\t'".$paramName."' => ".self::convertArray2PhpCode($paramValue, $whiteOffset."\t").",\n";
			}
		}
		$strResult .= $whiteOffset.")";
		return $strResult;
	}
	
	/**
	 * то же, что и self::replaceComponentInFileContent()
	 * Но не просто возвращает содержимео, но и сохраняет файл с этим содержимым
	 * @param int $componentNum - номер компонента в данном файле
	 * @param String $arComponent - массив описания вызова компонента, который надо поставить на место $componentNum
	 * @param Sreing $fileName - файл, в котором необходимо поменять вызов компонента
	 * @return bool - Статус выполнения - удачно или нет.
	 */
	static public function replaceComponentInFile($componentNum, $arComponentCall, $fileName) {
		if( !self::checkFile($fileName) ) {
			throw new ComponentException('wrong file name', self::E_WRONG_FILE);
			return false;
		}
		$phpFileContentReplacedCmp = self::replaceComponentInFileContent($componentNum, $arComponentCall, $fileName);
		if(!$phpFileContentReplacedCmp) {
			return false;
		}
		//echo $fileName;
		//echo $phpFileContentReplacedCmp;
		if(!@file_put_contents(DOC_ROOT.$fileName,$phpFileContentReplacedCmp)) {
			throw new ComponentException('can\'t write to file', ComponentException::E_CMP_EDIT_WRITE_FILE_FAIL);
			return false;
		}
		return true;
	}

	/**
	 * Создать массив описания вызова компонента по имени компонента
	 * [и по шаблону если указан]
	 * @param String $componentName
	 * @param String $templateName
	 * @param Array $arParams
	 * @return Array - массив вызова компонента
	 */
	static public function makeComponentCallArray($componentName, $templateName, $arParams) {
		$arComponentName = Component::parseComponentName($componentName);
		//d($arComponentName);
		$arTemplateName = Component::parseTemplateName($templateName, $arComponentName);
		//d($arTemplateName);
		$arComponentCall = array(
			'COMPONENT_NAMESPACE' => $arComponentName['NAMESPACE'],
			'COMPONENT_NAME' => $arComponentName['NAME'],
			'COMPONENT_EXISTS' => 'Y',
			'TEMPLATE_NAME' => $arTemplateName['NAME'],
			'TEMPLATE_SKIN' => $arTemplateName['SKIN'],
			'TEMPLATE_EXISTS' => 'Y',
			'PARAMS' => $arParams
		);
		if(!$arComponentName['PATH']) {
			//throw new ComponentException('component does not exist', ComponentException::E_WRONG_CMP_NAME);
			$arComponentCall['COMPONENT_EXISTS'] = 'N';
		}
		if(!$arTemplateName['PATH']) {
			//throw new ComponentException('component template does not exist', ComponentException::E_WRONG_TPL_NAME);
			$arComponentCall['TEMPLATE_EXISTS'] = 'N';
		}
		return $arComponentCall;
	}

	//////////////// COMPONENT SETTINGS //////////////////

	/**
	 * Получить массив параметров компонента по его имени. Не требует объекта.
	 * @param String $componentName - имя компонента вида 'component_namespace:component_name'
	 * @param String $templateName - имя шаблона вида 'template_name:skin_name'
	 * @return Array
	 */
	static public function getSettingsByName($componentName, $templateName = null) {
		$arComponentName = Component::parseComponentName($componentName);
		if(!$arComponentName['PATH']) {
			return array();
		}
		$arTemplateName = Component::parseTemplateName($templateName, $arComponentName);
		return self::_getSettingsByName($arComponentName, $arTemplateName);
	}
	static public function _getSettingsByName(&$arComponentName, &$arTemplateName) {

		if(!$arComponentName['PATH']) {
			return array();
		}

		$arSettings = array(
			// Информация о самом компоненте
			'COMPONENT' => $arComponentName,
			'TEMPLATE' => $arTemplateName,
			'PARAMETERS_GROUPS' => array(),
			'PARAMETERS' => array()
		);
		$arComponentSettings = array();

		// Подключаем файл настроек компонента
		if (is_file(DOC_ROOT.$arComponentName['PATH'].'/'.Component::DEFAULT_SETTINGS_FILENAME.'.php')) {
			@include DOC_ROOT.$arComponentName['PATH'].'/'.Component::DEFAULT_SETTINGS_FILENAME.'.php';
		}

		if( @isset($arComponentSettings['DESCRIPTION']) ) {
			$arSettings['COMPONENT']['DESCRIPTION'] =  $arComponentSettings['DESCRIPTION'];
		}
		if( isset($arComponentSettings['PARAMETERS_GROUPS']) ) {
			//$arSettings['PARAMETERS_GROUPS'] = array_merge(self::$_arComponentSettingsDefaults['PARAMETERS_GROUPS'], $arComponentSettings['PARAMETERS_GROUPS']);
			$arSettings['PARAMETERS_GROUPS'] = self::_mergeParametersWithDefault(
				$arComponentSettings['PARAMETERS_GROUPS'],
				self::$_arComponentSettingsDefaults['PARAMETERS_GROUPS']
			);
		}
		else {
			$arSettings['PARAMETERS_GROUPS'] = self::$_arComponentSettingsDefaults['PARAMETERS_GROUPS'];
		}
		if( isset($arComponentSettings['PARAMETERS']) ) {
			//$arSettings['PARAMETERS'] = array_merge(self::$_arComponentSettingsDefaults['PARAMETERS'], $arComponentSettings['PARAMETERS']);
			$arSettings['PARAMETERS'] = self::_mergeParametersWithDefault(
				$arComponentSettings['PARAMETERS'],
				self::$_arComponentSettingsDefaults['PARAMETERS']
			);
		}
		else {
			$arSettings['PARAMETERS'] = self::$_arComponentSettingsDefaults['PARAMETERS'];
		}
		unset($arComponentSettings);

		// Подключаем файл настроек шаблона компонента
		$arTemplateSettings = self::_getTemplateSettingsByName($arComponentName, $arTemplateName);
		//d($arTemplateSettings);
		self::_mergeComponentAndTemplateSettings($arSettings, $arTemplateSettings);
		unset($arTemplateSettings);


		//$___sortCompare_default100 = function($valA, $valB) {
		//	if(!$valA['SORT']) $valA['SORT'] = 100;
		//	if(!$valB['SORT']) $valB['SORT'] = 100;
		//	return ($valA['SORT'] < $valB['SORT']) ? -1 : 1;
		//};
		$sortCompare_default100 = "\\".__CLASS__."::_sortCompare_default100";
		//d($arComponentName, '$arComponentName');
		// Сортируем массив в порядке поля SORT
		//d('PARAMETERS_GROUPS');
		uasort($arSettings['PARAMETERS_GROUPS'], $sortCompare_default100);
		//d('PARAMETERS');
		uasort($arSettings['PARAMETERS'], $sortCompare_default100);


		return $arSettings;
	}
	/**
	 * Получить массив настроек компонента по имени
	 * @param String $componentName - имя компонента
	 * @param String $templateName - имя шаблона компонента
	 * @return Array - настройки компонента
	 */
	static public function getTemplateSettingsByName($componentName, $templateName) {
		$arComponentName = Component::parseComponentName($componentName);
		if(!$arComponentName['PATH']) {
			return array();
		}
		$arTemplateName = Component::parseTemplateName($templateName, $arComponentName);
		if(!$arTemplateName['PATH']) {
			return array();
		}
		return self::_getTemplateSettingsByName($arComponentName, $arTemplateName);
	}
	/**
	 * Получить массив настроек компонента по имени
	 * @param Array &$arComponentName - имя компонента
	 * @param Array &$arTemplateName - имя шаблона компонента
	 * @return Array - настройки компонента
	 */
	static protected function _getTemplateSettingsByName(&$arComponentName, &$arTemplateName) {
		if($arTemplateName['PATH']) {
			$arTemplateSettingsResult = $arTemplateName;
			if (is_file(DOC_ROOT.$arTemplateName['PATH'].'/'.Component::DEFAULT_SETTINGS_FILENAME.'.php')) {
				@include DOC_ROOT.$arTemplateName['PATH'].'/'.Component::DEFAULT_SETTINGS_FILENAME.'.php';
				if(isset($arTemplateSettings)) {
					$arTemplateSettingsResult = array_merge($arTemplateSettingsResult, $arTemplateSettings);
				}
			}
			else {
				$arTemplateSettingsResult = $arTemplateName;
			}
			if(@isset($arTemplateSettings['SKINS_LIST'])) {
				$arTemplateSettingsResult['SKINS_LIST'] = (
					array_merge(self::$_arComponentSettingsDefaults['TEMPLATE']['SKINS_LIST'], $arTemplateSettingsResult['SKINS_LIST'])
				);
			}
			else {
				$arTemplateSettingsResult['SKINS_LIST'] = self::$_arComponentSettingsDefaults['TEMPLATE']['SKINS_LIST'];
			}
			return $arTemplateSettingsResult;
		}
		return array();
	}
	/**
	 * Объединить массив параметров компонента и массив параметров шаблона компонента
	 * @param Array $arSettings - настройки
	 * @param Array $arTemplateSettings
	 */
	static protected function _mergeComponentAndTemplateSettings(&$arSettings, &$arTemplateSettings) {
		$arSettings['TEMPLATE']['NAME'] = $arTemplateSettings['NAME'];
		$arSettings['TEMPLATE']['SKIN'] = $arTemplateSettings['SKIN'];
		$arSettings['TEMPLATE']['PATH'] = $arTemplateSettings['PATH'];
		if(@isset($arTemplateSettings['PARAMETERS'])) {
			foreach($arTemplateSettings['PARAMETERS'] as $tplParamName => &$arTplParam) {
				// Если переопределяется один из параметров компонента, то присваиваем соответствующую группу
				if(!@isset($arSettings['PARAMETERS'][$tplParamName])) {
					$arTplParam['GROUP'] = 'TEMPLATE_PARAMS';
					$arSettings['PARAMETERS'][$tplParamName] = $arTplParam;
				}
			}
		}
		if(@isset($arTemplateSettings['DESCRIPTION'])) {
			$arSettings['TEMPLATE']['DESCRIPTION'] =  $arTemplateSettings['DESCRIPTION'];
		}
		$arSettings['TEMPLATE']['SKINS_LIST'] =  $arTemplateSettings['SKINS_LIST'];
	}
	/**
	 * Объединить параметры компонента с системными параметрами компонента
	 * @param <type> $arParameters
	 * @param <type> $arDefaults
	 * @return <type>
	 */
	static protected function _mergeParametersWithDefault(&$arParameters, &$arDefaults) {
		$arResult = $arParameters;
		//d($arParameters);
		foreach($arDefaults as $defaultKey => &$arDefaultItem) {
			if(
				!is_array($arParameters[$defaultKey])
				||
				count($arParameters[$defaultKey])
			) {
				$arResult[$defaultKey] = $arDefaultItem;
			}
			else {
				$arResult[$defaultKey] = $arParameters[$defaultKey];
			}
		}
		return $arResult;
	}
	// Ф-ия сравнения для cортировке по полю 'SORT'
	static protected function _sortCompare_default100(&$valA, &$valB) {
		if(!$valA['SORT']) $valA['SORT'] = 100;
		if(!$valB['SORT']) $valB['SORT'] = 100;
		//d('!A:'.$valA['NAME']);
		//d('!B:'.$valB['NAME']);
		if($valA['SORT'] == $valB['SORT']) {
			return 0;
		}
		return ($valA['SORT'] < $valB['SORT']) ? -1 : 1;
	}

	/**
	 * Ф-ия для подсчета кол-ва параметров относящихся к группе
	 * Для кждй группы формирует список параметров относящихся к этой группе
	 * @param Array $arSettings
	 */
	static public function setParametersListForEachGroup(&$arSettings) {
		$___setParametersInfoForGroup = function($arParameter, $paramName) use (&$arSettings) {
			$arPARAMETERS_GROUPS = &$arSettings['PARAMETERS_GROUPS'];
			if( isset($arPARAMETERS_GROUPS[$arParameter['GROUP']])) {
				$arPARAMETERS_GROUPS[$arParameter['GROUP']]['PARAMETERS_COUNT']++;
				$arPARAMETERS_GROUPS[$arParameter['GROUP']]['PARAMETERS_LIST'][] = $paramName;
			}
			else {
				$arPARAMETERS_GROUPS['ADDITIONAL_PARAMS']['PARAMETERS_COUNT']++;
				$arPARAMETERS_GROUPS['ADDITIONAL_PARAMS']['PARAMETERS_LIST'][] = $paramName;
			}
		};
		array_walk($arSettings['PARAMETERS'], $___setParametersInfoForGroup);
	}

	/**
	 * Получить список шаблонов компонента
	 * @param String $componentName - имя компонента
	 * @return Array - список шаблонов компонента
	 */
	static public function getTemplatesList($componentName) {
		$arComponentName = Component::parseComponentName($componentName);
		if(!$arComponentName['PATH']) {
			return array();
		}
		return self::_getTemplatesList($arComponentName);
	}
	static public function _getTemplatesList(&$arComponentName) {
		$templateFolderInComponent = $arComponentName['PATH'].'/templates';

		$templateFolderInAppTemplate = App::get()->getTemplatePath()
			.'/components'
			.'/'.$arComponentName['NAMESPACE']
			.'/'.$arComponentName['NAME']
			.'/'.$arTemplateName['NAME']
		;

		//d($templateFolderInAppTemplate);
		$arTemplates = array();
		$arTemplatesByName = array();
		// Шаблоны из самого компонента
		if( is_dir(DOC_ROOT.$templateFolderInComponent) ) {
			$dirTemplates = opendir(DOC_ROOT.$templateFolderInComponent);
			while ( $elementOfDir = readdir($dirTemplates) ) {
				if (
					$elementOfDir != ".."
					&& $elementOfDir != "."
					&& $elementOfDir != ".svn"
				) {
					$arTemplateName = array(
						'NAME' => $elementOfDir,
						'PATH' => $templateFolderInComponent.'/'.$elementOfDir
					);
					$arTemplateSettings = self::_getTemplateSettingsByName($arComponentName, $arTemplateName);
					//d($arTemplateName);
					//d($arTemplateSettings);
					$arTemplateSettings['PLACEMENT'] = 'component';
					$curIndex = count($arTemplates);
					if( !@isset($arTemplatesByName[$arTemplateSettings['NAME']]) ) {
						$arTemplates[$curIndex] = self::_getOnlyTemplateDesc($arTemplateSettings);
						$arTemplatesByName[$arTemplateSettings['NAME']] = $curIndex;
					}
					else {
						$replaceIndex = $arTemplatesByName[$arTemplateSettings['NAME']];
						$arTemplates[$replaceIndex] = self::_getOnlyTemplateDesc($arTemplateSettings);
					}
				}
			}
		}
		// Шаблоны из шаблона приложения
		if( is_dir(DOC_ROOT.$templateFolderInAppTemplate) ) {
			$dirTemplates = opendir(DOC_ROOT.$templateFolderInAppTemplate);
			while ( $elementOfDir = readdir($dirTemplates) ) {
				if (
					$elementOfDir != ".."
					&& $elementOfDir != "."
					&& $elementOfDir != ".svn"
				) {
					$arTemplateName = array(
						'NAME' => $elementOfDir,
						'PATH' => $templateFolderInAppTemplate.'/'.$elementOfDir
					);
					$arTemplateSettings = self::_getTemplateSettingsByName($arComponentName, $arTemplateName);
					//d($arTemplateName);
					//d($arTemplateSettings);
					$arTemplateSettings['PLACEMENT'] = 'app_template';
					$curIndex = count($arTemplates);
					if( !@isset($arTemplatesByName[$arTemplateSettings['NAME']]) ) {
						$arTemplates[$curIndex] = self::_getOnlyTemplateDesc($arTemplateSettings);
						$arTemplatesByName[$arTemplateSettings['NAME']] = $curIndex;
					}
					else {
						$replaceIndex = $arTemplatesByName[$arTemplateSettings['NAME']];
						$arTemplates[$replaceIndex] = self::_getOnlyTemplateDesc($arTemplateSettings);
					}
				}
			}
		}
		return $arTemplates;
	}
	static protected function _getOnlyTemplateDesc(&$arTemplateSettings) {
		$arTemplateDesc = array(
			'NAME' => $arTemplateSettings['NAME'],
			'PATH' => $arTemplateSettings['PATH'],
		);
		if(
			@isset($arTemplateSettings['DESCRIPTION'])
		) {
			$arTemplateDesc['DESCRIPTION'] = $arTemplateSettings['DESCRIPTION'];
		}
		$arTemplateDesc['PLACEMENT'] = $arTemplateSettings['PLACEMENT'];
		return $arTemplateDesc;
	}
}
?>