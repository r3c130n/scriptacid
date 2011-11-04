<?php
namespace ScriptAcid;
class ComponentException extends AppException {

	// Неверно указано имя компонента
	const E_WRONG_CMP_NAME = 1;
	// Неверно указано имя шаблона компонента
	const E_WRONG_TPL_NAME = 2;
	//// Исключения Редактирования компонента ////
	// Неверное имя файла
	const E_CMP_EDIT_WRONG_FILE = 4;
	// Ошика разбора
	const E_CMP_EDIT_PARSE_FAIL = 8;
	// Неверный массив вызова компонента
	const E_CMP_EDIT_WRONG_CALL_ARRAY = 16;
	// Неверный параметр номера компонента в файле
	const E_CMP_EDIT_WRONG_CMP_NUM = 32;
	// Невозможно записать данные в файл.
	const E_CMP_EDIT_WRITE_FILE_FAIL = 64;


	// Общий код ошибок. Ориентироваться по тексту сообщения
	const E_COMMON_FAIL = 4096;
	
	
	public function __construct($message, $code, $arComponentCall = array()) {
		$this->setComponentCallArray($arComponentCall);
		parent::__construct($message, $code, $previous);
	}
	
	protected $_arComponentCall;
	public function setComponentCallArray($arComponentCall) {
		
		$this->_arComponentCall = $arComponentCall;
	}
	public function getComponentCallArray() {
		return $this->_arComponentCall;
	}
}