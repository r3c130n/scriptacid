<?php
namespace ScriptAcid;
class Panel {
	static private $arPanelItems = Array();
	static private $brCnt = 0;
	
	/**
     * Получить пункты меню панели
     * @return array пункты панели
     */
	static public function getItems() {
        // TODO: добавить обработку групп пользователя - для отображения каждой группе своего меню
		return self::$arPanelItems;
	}

    /**
     * Добавить пункт меню панели
     * @param string $url
     * @param string $name
     */
	static public function setItem($url, $name = '') {
        // TODO: добавить обработку групп пользователя - для отображения каждой группе своего меню
		if ($url == '<br>') {
			self::$arPanelItems['br_'.self::$brCnt++] = '<br>';
		} else {
			self::$arPanelItems[$name] = $url;
		}
	}
}
?>