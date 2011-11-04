<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

/**
 * События
 * @author r3c130n
 */
class Event {
	protected static $_arEvents = Array(); // Список зарегистрированных событий

	/**
	 * Регистрация обработчика события
	 * @param string $moduleName
	 * @param string $eventName
	 * @param string $eventHandler
	 */
	public static function add($moduleName, $eventName, $eventHandler) {
		self::$_arEvents[$moduleName][$eventName][] = $eventHandler;
	}

	/**
	 * Получение списка зарегистрированных событий
	 * @param string $moduleName
	 * @return array
	 */
	public static function getList($moduleName) {
		$arEvt = self::$_arEvents[$moduleName];
		if (empty($arEvt)) {
			$arEvt = Array();
		}
		return $arEvt;
	}

	/**
	 * Выполнение всех обработчиков события
	 * @param string $moduleName
	 * @param string $eventName
	 * @param array $arParams
	 */
	public static function run($moduleName, $eventName, &$arParams = array()) {
		if (empty(self::$_arEvents[$moduleName][$eventName])) {
			return false;
		}
		foreach (self::$_arEvents[$moduleName][$eventName] as &$event) {
			$callable_name = "";
			if(!is_callable($event, false, $callable_name) ) {
				if(is_array($event) && count($event) == 2) {
					fixNamespaceName($event[0]);
				}
				elseif(is_string($event)) {
					fixNamespaceName($event);
				}
				if(!is_callable($event, false, $callable_name) ) {
					continue;
				}
			}
			call_user_func($event, &$arParams);
		}
	}
}

/******************************
 *			HOW TO:
 ******************************
 * 1. В классе в нужном месте вызвать метод:
 *  Event::Run('main', 'OnBeforeLoad', $arParams);
 *
 * 2. Добавить в файл /usr_include/application_init.php запись:
 * Примеры создания событий:
 * Event::add("main", "OnBeforeApplicationTemplate", function() {
 * 	//echo "Closure call test. OnBeforeApplicationTemplate.".endl;
 * });
 *
 * Event::add("main", "OnAfterApplicationTemplate", function() {
 * 	//echo "Closure call test. OnAfterApplicationTemplate.".endl;
 * });
 *
 * function ___testOnBeforeApplicationTemplate() {
 * 	//echo "function call test. OnBeforeApplicationTemplate.".endl;
 * }
 * Event::add("main", "OnBeforeApplicationTemplate", "___testOnBeforeApplicationTemplate");
 *
 * function ___testOnAfterApplicationTemplate() {
 * 	//echo "function call test. OnAfterApplicationTemplate.".endl;
 * }
 * Event::add("main", "OnAfterApplicationTemplate", "___testOnAfterApplicationTemplate");
 *
 * class ___testEvents {
 * 	public function ___testOnBeforeApplicationTemplate() {
 * 		//echo "method call test. OnBeforeApplicationTemplate.".endl;
 * 	}
 *
 * 	public function ___testOnAfterApplicationTemplate() {
 * 		//echo "method call test. OnAfterApplicationTemplate.".endl;
 * 	}
 * }
 * Event::add("main", "OnBeforeApplicationTemplate", array("___testEvents", "___testOnBeforeApplicationTemplate"));
 * Event::add("main", "OnAfterApplicationTemplate", array("___testEvents", "___testOnAfterApplicationTemplate"));
 */

?>