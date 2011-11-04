<?php
namespace ScriptAcid;
if(!defined("APPLICATION_INCLUDED") || APPLICATION_INCLUDED!==true)die();

/**
 * Здесь можно задать альтернативный класс приложения
 * или альтернативный класс компонентов
 * или определить события
 */

// Тестовые событие

Event::add("main", "OnBeforeApplicationTemplate", function() {
	//echo "Closure call test. OnBeforeApplicationTemplate.".endl;
});

Event::add("main", "OnAfterApplicationTemplate", function() {
	//echo "Closure call test. OnAfterApplicationTemplate.".endl;
});

function ___testOnBeforeApplicationTemplate() {
	//echo "function call test. OnBeforeApplicationTemplate.".endl;
}
Event::add("main", "OnBeforeApplicationTemplate", "___testOnBeforeApplicationTemplate");

function ___testOnAfterApplicationTemplate() {
	//echo "function call test. OnAfterApplicationTemplate.".endl;
}
Event::add("main", "OnAfterApplicationTemplate", "___testOnAfterApplicationTemplate");

class ___testEvents {
	public function ___testOnBeforeApplicationTemplate() {
		//echo "method call test. OnBeforeApplicationTemplate.".endl;
	}

	public function ___testOnAfterApplicationTemplate() {
		//echo "method call test. OnAfterApplicationTemplate.".endl;
	}
}
Event::add("main", "OnBeforeApplicationTemplate", array("___testEvents", "___testOnBeforeApplicationTemplate"));
Event::add("main", "OnAfterApplicationTemplate", array("___testEvents", "___testOnAfterApplicationTemplate"));


?>