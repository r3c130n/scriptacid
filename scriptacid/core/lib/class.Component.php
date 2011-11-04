<?php namespace ScriptAcid;
/**
 * Компоненты
 * @author pr0n1x, r3c130n
 */

if(!defined('_DEFAULT_COMPONENT_TEMPLATE'))			define('_DEFAULT_COMPONENT_TEMPLATE',		'_default');
if(!defined('_DEFAULT_COMPONENT_TEMPLATE_SKIN'))	define('_DEFAULT_COMPONENT_TEMPLATE_SKIN',	'default');
if(!defined('_DEFAULT_COMPONENT_NAMESPACE'))		define('_DEFAULT_COMPONENT_NAMESPACE',		'system');


class Component {
	const MAIN_COMPONENT_CLASS = true;
	// Имя компонента по умолчанию. Зависит от конфига системы
	const DEFAULT_TEMPLATE	= _DEFAULT_COMPONENT_TEMPLATE;
	// Имя скина по умолчанию. Зависит от конфига.
	const DEFAULT_TEMPLATE_SKIN = _DEFAULT_COMPONENT_TEMPLATE_SKIN;
	// Именованная область компонентов по умолчанию. Зависит от конфига.
	const DEFAULT_NAMESPACE	= _DEFAULT_COMPONENT_NAMESPACE;
	// Имя файла шаблона по умолчанию.
	const DEFAULT_TEMPLATE_FILENAME = 'template';
	// Имя файла с настройками.
	const DEFAULT_SETTINGS_FILENAME = '_settings';
	
	// ERRORS
	const E_WRONG_COMPONENT_NAME = 1;
	const E_COMPONENT_FILE_NOT_EXISTS = 2;
	
	const E_WRONG_TEMPLATE_NAME = 4;
	const E_WRONG_TEMPLATE_FILE_NAME = 8;
	const E_TEMPLATE_FILE_NOT_EXIST = 16;
	
	

	// Именованная область компонентов
	protected $_componentNamespace = self::DEFAULT_NAMESPACE;
	// Имя компонента
	protected $_componentName = null;
	// Путь до компонента
	protected $_componentPath = null;
	// Описание компонента
	protected $_componentDescription = null;
	
	// Имя шаблона
	protected $_templateName = self::DEFAULT_TEMPLATE;
	// Скин шаблона
	protected $_templateSkin = self::DEFAULT_TEMPLATE_SKIN;
	protected $_templateSkinInherited = null;
	// Путь до шаблона
	protected $_templatePath = null;
	// Описание шаблона
	protected $_templateDescription = null;
	// Список доступных скинов
	protected $_templateSkinsList = array();
	
	// Описание параметров в файле _parameters.php,
	// а если точно, то в self::DEFAULT_PARAMETERS_FILENAME.'.php'
	protected $_arSettings = array();
	// Параметры переданные в вызов компонента
	protected $_arParams = array();
	// Параметры переданные в вызов компонента. Но в этом массиве перменные _GET, _POST... не заменяются
	protected $_arParamsCurrent = array();
	// Резкльтат работы логики компонента
	protected $_arResult = array();
	
	// Бит - режим AJAX
	protected $_bAjaxMode = false;
	// Бит готовности для исполнения в режиме AJAX 
	protected $_bReadyToExecute = true;
	// Ключ вызова компонента
	protected $_componentCallKey = null;
	
	// Счеткик вызовов компонтов
	static protected $_componentsCount = 0;
	static protected $_arComponentsCount = array();

	/*
	 * Список параметров от которых зависит компонент
	 */
	protected $_arRequestDeps = array(
		'GET' => array(),
		'POST' => array(),
		'COOKIE' => array()
	);

	static protected $_arSystemSettings = array(
		// Ключ вызова. Служебный.
		'COMPONENT_CALL_KEY' => '',
		// Готов для вызова через аджкс. Служебный.
		'COMPONENT_AJAX_READY' => 'OFF',
		// Посылать POST-запрос страницы в компонент при первом асинхронном вызове компонента. Служебный.
		'COMPONENT_AJAX_SEND_PAGE_POST' => 'N',
	);

	public function __construct() {
		
		if(!defined('SESS_COMPONENTS_CALL_KEYS')) {
			AppException::throwException(AppErrorException, 'Не определена константа "SESS_COMPONENTS_CALL_KEYS"');
		}
		
		if( $this->_templateSkin != App::get()->getTemplateSkin() ) {
			$this->_templateSkin = App::get()->getTemplateSkin();
		}
	}

	/**
	 * @deprecated использовать Component::call
	 */
	static public function callComponent($componentName, $templateName, $arComponentParams, $obParentComponent = null) {
		$component = new self();
		try {
			return $component->start($componentName, $templateName, $arComponentParams, $obParentComponent);
		}
		catch(ComponentException $except) {
			
			echo 'Error: '.$except->getMessage().'.'.endl;
		}
		unset($component);
	}

	/**
	 * Вызов компонента
	 * @param string $componentName
	 * @param string $templateName default: _default
	 * @param array $arComponentParams
	 * @param [object $obParentComponent]
	 */
	static public function call($componentName, $templateName, $arComponentParams, $obParentComponent = null) {
		/**
		 * TODO: $obParentComponent - Для использования комплексных компонентов
		 */
		$component = new self();
		try {
			return $component->start($componentName, $templateName, $arComponentParams, $obParentComponent);
		}
		catch(ComponentException $except) {
			
			echo 'Error: '.$except->getMessage().'.'.endl;
		}
		unset($component);
	}

    public function start($componentName, $templateName, $arComponentParams, $obParentComponent = null) {
		global $CTLANG;
		
		$APPLICATION = $APP = App::get();
		$USER = App::USER();
		
		if($templateName == '') {
			$templateName = self::DEFAULT_TEMPLATE;
		}
		
		if( !$this->_setComponentName($componentName) ) {
			//throw new AppErrorException('startComponent(): Неверно задано имя компонента.');
			ShowError('Ошибка: Имя компонента "<b>'.$componentName.'</b>" задано не верно!');
			return false;
		}
		$componentName = $this->_componentNamespace.':'.$this->_componentName;
		if( !$this->_setTemplateName($templateName) ) {
			//ShowError('Ошибка: Имя шаблона <b>'.$templateName.'</b> компонента "<b>'.$componentName.'</b>" задано не верно!');
			//return false;
		}
		$templateName = $this->_templateName.':'.$this->_templateSkin;
		
		$this->_arParams = $arComponentParams;
		unset($arComponentParams);
		
		// Создаем ссылки для видимости внутри файлов компонента и шаблона
		$arResult = &$this->_arResult;
		$arParams = &$this->_arParams;
		
		$this->_setCallKey();
		
		$this->_registerComponent();
		
		$this->_connectCssAndJsFiles();
		
		$this->_manageComponentParams();
		
		$arFaceParams = Array(
			'NAME' => $componentName,
			'TPL' => $this->_templateName,
			'CMP_PATH' => $this->_componentPath,
			'TPL_PATH' => $this->_templatePath,
			'PARAMETERS' => $this->_arParams
		);		
		
		
		if( ! $this->_bAjaxMode ) {
				$this->_showComponenEditHead();
				$this->_execute();
				$this->_showComponenEditFoot();
		}
		else {
			if($this->_bReadyToExecute) {
				$this->_execute();
			}
			else {
				$this->_showComponenEditHead();
				$this->_executePrepare();
				$this->_showComponenEditFoot();
			}
		}
		
		return $this->_componentCallKey;
	}
	
	/**
	 * Задать Ключ компонента
	 */
	protected function _setCallKey() {
		// Если этот ключ передан в параметрах, задаем  
		if($this->_arParams['COMPONENT_CALL_KEY']) {
			$this->_componentCallKey = $this->_arParams['COMPONENT_CALL_KEY'];
		}
		// если не задан, формируем
		else {
			$this->_componentCallKey = 
			md5(
			//str_replace('.', '-',
				$this->_componentNamespace
				//.'--'
				.$this->_componentName
				//.'--'
				.$this->_templateName
				//.'--'
				.$this->_templateSkin
				//.'--'
				.self::$_arComponentsCount[$this->_componentNamespace.':'.$this->_componentName]
			);
		}
		return $this->_componentCallKey;
	}
	
	protected function _registerComponent() {
		self::$_componentsCount++;
		self::$_arComponentsCount[$this->_componentNamespace.':'.$this->_componentName]++;
		if(APP_DISPLAY_MODE == 'EDIT') {
			ComponentTools::registerComponentInFile(array(
				'NAMESPACE' => $this->_componentNamespace,
				'NAME' => $this->_componentName,
				'TEMPLATE_NAME' => $this->_templateName,
				'TEMPLATE_SKIN' => $this->_templateSkin,
				'CALL_KEY' => $this->_componentCallKey
			));	
		}
		
	}
	
	/**
	 * Обработка параметров
	 */
	protected function _manageComponentParams() {
		
		// Заполняем системные параметры, если не заданы в вызове
		foreach(self::$_arSystemSettings as $sysParameterName => &$sysParameterValue) {
			if(!@isset($this->_arParams[$sysParameterName])) {
				$this->_arParams[$sysParameterName] = $sysParameterValue;
			}
		}
		
		if(
			$this->_arParams['COMPONENT_AJAX_MODE'] != 'AJAX'
			//|| $this->_arParams['COMPONENT_AJAX_MODE'] != 'AJAX-JSON'
		) {
			$this->_arParams['COMPONENT_AJAX_MODE'] = 'OFF';
		}
		else {
			$this->_bAjaxMode = true;
			if($this->_arParams['COMPONENT_AJAX_READY'] != 'Y') {
				$this->_arParams['COMPONENT_AJAX_READY'] = 'N';
				$this->_bReadyToExecute = false;
			}
		}		
		
		// Если ещё не готов для выполнения в режиме аджакс
		// (т.е. это выполнение на странице, а не асинхронный вызов),
		// зеачит надо подготовить.
		if($this->_bAjaxMode && !$this->_bReadyToExecute) {
			$arComponentRequest = array(
				'NAME' => $this->_componentNamespace.':'.$this->_componentName,
				'TEMPLATE' => $this->_templateName.':'.$this->_templateSkin,
				'PARAMS' => $this->_arParams,
			);
			$_SESSION[SESS_COMPONENTS_CALL_KEYS][$this->_componentCallKey] = $arComponentRequest;
		}
		
		//d($this->_arParams);
		$this->_arParamsCurrent = $this->_arParams;
		foreach($this->_arParams as $paramName => &$paramValue) {
			$arMatches = array();
			if(
				is_string($paramValue)
				&&
				( preg_match('#\{\%\_(GET|POST|REQUEST|COOKIE)\[([a-zA-Z0-9\_\-]*)\]#', $paramValue, $arMatches) )
			) {
				if(!$this->_bReadyToExecute) {
					//d($arMatches);
				}
				$REQUESTType = $arMatches[1]; //(GET|POST|REQUEST|COOKIE)
				$REQUESTParamName = $arMatches[2];
				//d($REQUESTParamName);
				//d($paramName);
				//d($paramValue);

				// Передаем реальные значения в параметры компонента
				if( @isset($GLOBALS['_'.$REQUESTType][$REQUESTParamName]) ) {
					$this->_arParams[$paramName] = null;
					$this->_arParams[$paramName] = $GLOBALS['_'.$REQUESTType][$REQUESTParamName];
				}
				
				// Определяем зависимости от параметра от типа параметра запроса
				switch($REQUESTType) {
					case 'REQUEST':
						$this->_arRequestDeps['GET'][$REQUESTParamName] = true;
						$this->_arRequestDeps['POST'][$REQUESTParamName] = true;
						break;
					case 'GET':
						$this->_arRequestDeps['GET'][$REQUESTParamName] = true;
						break;
					case 'POST':
						$this->_arRequestDeps['POST'][$REQUESTParamName] = true;
						break;
				}
			}
		}
		
		if($this->_arParams['COMPONENT_AJAX_SEND_PAGE_POST'] == 'Y') {
			foreach($_POST as $_POST_key => &$_POST_val) {
				$this->_arRequestDeps ['POST'][$_POST_key] = true;
			}
		}
		
		//d($this->_arParams);
		//d($this->_arRequestDeps);
	}
	
	/**
	 * Вставить контейнер в котором будет отображаться компонент полученный через AJAX
	 */
	protected function _executePrepare() {
		// HTML {{{
		?>
<div id="sacid-cmp-ajax-call-key-<?php echo $this->_componentCallKey?>">
	
</div>
	<script type="text/javascript">
		SACID.Components.addAjaxComponent(
			"<?php echo $this->_componentNamespace.':'.$this->_componentName;?>",
			"<?php echo $this->_componentCallKey;?>",
			<?php echo json_encode($this->_arRequestDeps, JSON_FORCE_OBJECT);?>
		);
	</script>
		<?php 
		// HTML }}}
	}
	
	protected function _showComponenEditHead() {
		if(APP_DISPLAY_MODE == 'EDIT'):
			$componentInFile = ComponentTools::getCurrentFileName();
			$inFileComponentNum = ComponentTools::findInFileComponentNumByCallKey($componentInFile, $this->_componentCallKey);
			if($inFileComponentNum<0) {
				throw new AppErrorException('cant\'t find component in file', 0);
			}
			$jsArComponentCall = array(
				'componentName' => $this->_componentNamespace.':'.$this->_componentName,
				'templateName' => $this->_templateName,
				'templateSkin' => $this->_templateSkin,
				'templateSkinsList' => $this->_templateSkinsList,
				'componentCallKey' => $this->_componentCallKey,
				'inFile' => $componentInFile,
				'inFileComponentNum' => $inFileComponentNum,
				'obArParams' => $this->_arParamsCurrent,
			);
			
			$cmpTitle = ''
				.'Компонент: '.$this->_componentNamespace.':'.$this->_componentName.'; '."; \n"
				.'Шаблон: '
					.$this->_templateName.':'.$this->_templateSkin
					.(($this->_templateSkinInherited !== null)?'['.$this->_templateSkinInherited.']':'')
				."; \n"
				.'Файл: '.$componentInFile."; \n"
				.'Номер в файле:'.$inFileComponentNum."; \n"
			;
			
			//d($jsArComponentCall, '$jsArComponentCall');
		?>
<div class="sacid-cmp-edit">
	<div class="sacid-cmp-edit-panel" style="">
		<a id="edit-cmp-key-<?php echo $this->_componentCallKey?>" href="javascript: void(0);"
			title="<?php echo $cmpTitle?>"
		>настроить</a>
		<script type="text/javascript">
			var $settingsLink = jQuery('#edit-cmp-key-<?php echo $this->_componentCallKey;?>');
			$settingsLink.click(function() {
				SACID.Components.showEditForm(<?php echo json_encode($jsArComponentCall, JSON_FORCE_OBJECT);?>);
			});
			$settingsLink.hover(
				function() {
					
				},
				function() {
					
				}
			);
		</script>
	</div>
	<div class="sacid-cmp-edit-content">
		<?php endif;
		
	}
	
	protected function _showComponenEditFoot() {
		if(APP_DISPLAY_MODE == 'EDIT'):?>
		<div class='clflow'><span></span></div>
	</div>
</div>
		<?php endif;
	}
	
	protected function _execute() {
		global $CTLANG;
		$APPLICATION = $APP = App::get();
		$USER = App::USER();

		$arParams = &$this->_arParams;
		$arResult = &$this->_arResult;
		
		foreach($arParams as $paramName => &$paramValue) {
			$arMatches = array();
			if( is_string($paramValue) && preg_match('#\{\%\_GET\[\"(.*)\"\]#', $paramValue, $arMatches) ) {
				$GETParamName = $arMatches[1];
				$arParams[$paramName] = $_GET[$GETParamName];
			}
		}
		
		$componentName = $this->_componentNamespace.':'.$this->_componentName;
		$templateName = $this->_templateName.':'.$this->_templateSkin;
		
		// Ищем и подключаем нужные языковые файлы
		$langFilesPath = DOC_ROOT.$this->_componentPath.'/lang/' . LANG_ID;
		if (is_dir($langFilesPath)) {
			Lang::getLangFilesLocale($langFilesPath);
		}
		
		// Подключаем файл компонента
		if (file_exists(DOC_ROOT.$this->_componentPath . '/component.php')) {
			/*/
			// Если в шаблоне есть файл before.php то подключаем его перед файлом компонента
			if (file_exists(DOC_ROOT.$this->_templatePath . '/before.php')) {
				include DOC_ROOT.$this->_templatePath . '/before.php';
			}
			//*/
			include DOC_ROOT.$this->_componentPath . '/component.php';
		} else {
			ShowError('Ошибка: Компонент "<b>'.$this->_componentNamespace.':'.$this->_componentName.'</b>" не найден!');
		}
	}
	
	/**
	 * Разобрать имя компонента
	 * @param unknown_type $componentNameFArg
	 */
	static public function parseComponentName($componentName) {
		$componentName = strtolower($componentName);
		$componentName = str_replace(array('/..', '../', "\0"), '', $componentName);
		
		$arComponentName = array();
		
		$arComponentNameExplode = explode(':', $componentName);
		// Это условие выполняется если в имени отсутствует ':'
		if(!@isset($arComponentNameExplode[1])) {
			return false;
		}
		$arComponentName['NAMESPACE']	= trim($arComponentNameExplode[0]);
		$arComponentName['NAME']		= trim($arComponentNameExplode[1]);
		unset($arComponentNameExplode);
		
		if( strlen($arComponentName['NAMESPACE'])<1 ) {
			$arComponentName['NAMESPACE'] = self::DEFAULT_NAMESPACE;
		}
		
		$arComponentName['PATH'] = false;
		if( file_exists(COMPONENTS_PATH_FULL.'/'.$arComponentName['NAMESPACE'].'/'.$arComponentName['NAME'].'/component.php') ) {
			$arComponentName['PATH'] = COMPONENTS_PATH.'/'.$arComponentName['NAMESPACE'].'/'.$arComponentName['NAME'];
		}
		
		return $arComponentName;
	}
	
	/**
	 * Задает занчения _componentNamespace, _componentName, _componentPath
	 * @param String $componentName
	 * @return boolean - статус существования компонента.
	 */
	protected function _setComponentName($componentName) {
		$arComponentName = $this->parseComponentName($componentName);
		$this->_componentNamespace	= $arComponentName['NAMESPACE'];
		$this->_componentName		= $arComponentName['NAME'];
		$this->_componentPath		= $arComponentName['PATH'];
		if($arComponentName['PATH']) {
			return true;
		}
		else {
			return false;	
		}
	}
	
	/**
	 * Разобрать имя шаблона компонента
	 * @param String $templateName - Имя шаблона
	 * @param Array $arComponentName - массив-имя компонента
	 * 			array(
	 * 					'NAMESPACE' => '...',
	 * 					'NAME' => '...',
	 * 					'PATH' => '...'
	 * 			)
	 */
	static public function parseTemplateName($templateName, $arComponentName) {
		// Подчистим от нежелательных элементов.
		$templateNameFArg = strtolower($templateName);
		$templateNameFArg = str_replace(array('/..', '../', "\0"), '', $templateNameFArg);
		
		// Парсим имя шаблона и скин
		$arTemplateName = array();
		$arTemplateNameExplode = explode(':', $templateNameFArg);
		if( !@isset($arTemplateNameExplode[1]) ) $arTemplateNameExplode[1] = '';
		$arTemplateName['NAME'] = trim($arTemplateNameExplode[0]);
		$arTemplateName['SKIN'] = trim($arTemplateNameExplode[1]);
		unset($arTemplateNameExplode);
		
		if( strlen($arTemplateName['NAME'])<1 ) {
			$arTemplateName['NAME'] = self::DEFAULT_TEMPLATE;
		}
		if( strlen($arTemplateName['SKIN'])<1 ) {
			$arTemplateName['SKIN'] = self::DEFAULT_TEMPLATE_SKIN;
		}

		$templateFolderInComponent = SYS_ROOT
			.'/components'
			.'/'.$arComponentName['NAMESPACE']
			.'/'.$arComponentName['NAME']
			.'/templates'
			.'/'.$arTemplateName['NAME']
		;
		
		$templateFolderInAppTemplate = App::get()->getTemplatePath()
			.'/components'
			.'/'.$arComponentName['NAMESPACE']
			.'/'.$arComponentName['NAME']
			.'/'.$arTemplateName['NAME']
		;

		$arTemplateName['PATH'] = false;
		// Проверяем наличие шаблона компонента в шаблоне приложения
		if( is_dir(DOC_ROOT.$templateFolderInAppTemplate) ) {
			$arTemplateName['PATH'] = $templateFolderInAppTemplate;
		}
		// Проверяем наличие шаблона компонента в компоненте
		elseif( is_dir(DOC_ROOT.$templateFolderInComponent) ) {
			$arTemplateName['PATH'] = $templateFolderInComponent;
		}
		
		return $arTemplateName;
	}
	/**
	 * Задает значения _templateName, _templateSkin, _templatePath
	 * @return boolean - стутус существования шаблона
	 * @param String $templateName - Имя шаблона
	 */
	protected function _setTemplateName($templateName) {
		
		$arComponentName = array(
			'NAMESPACE' => $this->_componentNamespace,
			'NAME' => $this->_componentName,
			'PATH' => $this->_componentPath
		);
		$arTemplateName = self::parseTemplateName($templateName, $arComponentName);
		$this->_templateName = $arTemplateName['NAME'];
		$this->_templateSkin = $arTemplateName['SKIN'];
		$this->_templatePath = $arTemplateName['PATH'];

		// Если шаблон наследуется, то устанавливаем из шаблона приложения
		if($this->_templateSkin == 'inherit') {
			$this->_templateSkinInherited = App::get()->getTemplateSkin();
		}

		if( $arTemplateName['PATH'] ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Подключить шаблон компонента
	 * @global array $CTLANG
	 * @global object $USER
	 * @param string $componentTemplateFileName
	 * @return bool
	 */
	protected function connectComponentTemplate($componentTemplateFileName = self::DEFAULT_TEMPLATE_FILENAME) {
		global $CTLANG;
		$APPLICATION = $APP = App::get();
		$USER = App::USER();
		
		
		if(!$this->_templatePath) {
			throw new ComponentException(
				'Шаблон "<b>'.$this->_templateName.'</b>" компонента "<b>'.$this->_componentNamespace.':'.$this->_componentName.'</b>" не существует',
				ComponentException::E_WRONG_TPL_NAME
			);
			return false;
		}
		if( !preg_match('#^[a-zA-Z0-9\-\_\.]{1,30}$#', $componentTemplateFileName) ) {
			throw new ComponentException(
				'Неверно указано имя файла шаблона',
				ComponentException::E_WRONG_CMP_NAME
			);
			return false;
		}
		// Создаем ссылки для видимости внутри файлов компонента и шаблона
		$arParams = &$this->_arParams;
		$arResult = &$this->_arResult;
		
		// Подрубаем шаблон
		if ( !file_exists(DOC_ROOT.$this->_templatePath.'/'.$componentTemplateFileName.'.php') ) {
			throw new ComponentException(
				'Файл <b>'.$componentTemplateFileName.'</b> шаблона "<b>'.$this->_templateName.'</b>" компонента "<b>'.$this->_componentNamespace.':'.$this->_componentName.'</b>" не найден',
				ComponentException::E_WRONG_TPL_NAME
			);
			return false;
		}
		
		// Подрубаем язык шаблона
		$langFilesPath = DOC_ROOT.$this->_templatePath.'/lang/'.LANG_ID;
		if (is_dir($langFilesPath)) {
			Lang::getLangFilesLocale($langFilesPath);
		}
		
		include DOC_ROOT.$this->_templatePath.'/'.$componentTemplateFileName.'.php';

		/*/
		// Если указали имя файла-шаблона, то ищем сначала файл <имя.файла-шаблона>.after.php
		if ( file_exists(DOC_ROOT.$this->_templatePath.'/'.$componentTemplateFileName.'.after.php') ) {
			include DOC_ROOT.$this->_templatePath . $componentTemplateFileName.'.after.php';
		}
		// Если имя файла шаблона стандартное, то можем подрубить файл after.php без префикса в имени
		elseif(
			$componentTemplateFileName == self::DEFAULT_TEMPLATE_FILENAME
			&&
			file_exists(DOC_ROOT.$this->_templatePath.'/after.php')
		) {
			include DOC_ROOT.$this->_templatePath.'/after.php';
		}
		//*/
		
		// Очищаем данные отработавшего компонента.
		$arResult = Array();
		$arParams = Array();
	}
	
	/**
	 * Подключаем CSS и JS файлы в шаблон приложения.
	 * @return void
	 */
	protected function _connectCssAndJsFiles() {
		$APPLICATION = $APP = App::get();

		$templateSkin = $this->_templateSkin;
		if($this->_templateSkin == 'inherit' && $this->_templateSkinInherited !== null) {
			$templateSkin = $this->_templateSkinInherited;
		}

		$APP->addCSS($this->_templatePath.'/style_common.css');
		if( $templateSkin == 'common' || !$APP->addCSS($this->_templatePath.'/style_'.$templateSkin.'.css') ) {
			 $APP->addCSS($this->_templatePath.'/style_'.self::DEFAULT_TEMPLATE_SKIN.'.css');
		}
		
		$APP->addJS($this->_templatePath.'/script_common.js');
		if( $templateSkin == 'common' || !$APP->addJS($this->_templatePath.'/script_'.$templateSkin.'.js') ) {
			 $APP->addJS($this->_templatePath.'/script_'.self::DEFAULT_TEMPLATE_SKIN.'.js');
		}
	}
	
	/**
	 * Аналог ф-ии Application::redirectTo
	 * Но в зависимости от того используется ли
	 * аджакс режим работы компонента
	 * делает редирект через РНР - в режиме без аджакс
	 * делает редирект через JavaScript - в режиме аджакс
	 * @param String $url
	 * @return void
	 */
	public function redirectTo($url) {
		if($this->_bAjaxMode) {
			?>
			<script type="text/javascript">
				//console.log('redirect to: <?php echo $url;?>');
				window.location = '<?php echo $url;?>';
			</script>
			<?php
			return true;
		}
		return App::get()->redirectTo($url); 
	}

	
	/**
	 * Получить массив описания параметров компонента
	 * @return Array - массив настроек компонента
	 */
	public function getSettings() {
		$arComponentName = array(
			'NAMESPACE' => &$this->_componentNamespace,
			'NAME' => &$this->_componentName,
			'PATH' => &$this->_componentPath,
			'DESCRIPTION' => &$this->_componentDescription
		);
		$arTemplateName = array(
			'NAME' => &$this->_templateName,
			'SKIN' => &$this->_templateSkin,
			'PATH' => &$this->_templatePath,
			'DESCRIPTION' => &$this->_templateDescription,
		);
		$arSettings = ComponentTools::_getSettingsByName($arComponentName, $arTemplateName);
		ComponentTools::setParametersListForEachGroup($arSettings);
		return $arSettings;
	}
	
	/**
	 * Включаемая область. Очень упрощенноый вариант компонента
	 * @param String $fileName - Имя файла с кодом относительно DOCUMENT_ROOT
	 * @param String $arParams - аналог параметров компонента
	 */
	static public function includeArea($fileName, $arParams) {
		
	}
}
?>