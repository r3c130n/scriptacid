<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

class CatalogUtils {

	public static $arTypes = Array(
			"S" => "Текст",
			"L" => "Список",
			"H" => "Текст/HTML",
			"N" => "Число",
			"EL" => "CATALOG_ELEMENT",
			"SL" => "CATALOG_SECTION",
			"F" => "FILE",
			"C" => "Чекбокс",
			"R" => "Радио-кнопка",
			"TAG" => "Теги",
			"H" => "Скрытое"
		);

	public static function GetMainFieldsArray() {
		$arFields = Array();

		$arFields['NAME'] = Array(
			"TYPE" => "S"
		);

		$arFields['CATALOG_SECTION_ID'] = Array(
			"TYPE" => "SL",
		);

		$arFields['SID'] = Array(
			"TYPE" => "SIL"
		);

		$arFields['CODE'] = Array(
			"TYPE" => "S"
		);		

		$arFields['ACTIVE'] = Array(
			"TYPE" => "C",
			"VALUES" => Array(
				"Y" => "Да",
				"N" => "Нет",
			),
			"DEFAULT" => "Y"
		);

		$arFields['SORT'] = Array(
			"TYPE" => "N",
			"DEFAULT" => "500"
		);

		$arFields['PREVIEW_TEXT'] = Array(
			"TYPE" => "S"
		);

		$arFields['PREVIEW_TEXT_TYPE'] = Array(
			"TYPE" => "L",
			"VALUES" => Array(
				"html" => "HTML",
				"text" => "ТЕКСТ",
			),
			"DEFAULT" => "html"
		);

		$arFields['PREVIEW_PICTURE'] = Array(
			"TYPE" => "F"
		);

		$arFields['DETAIL_TEXT'] = Array(
			"TYPE" => "S"
		);

		$arFields['DETAIL_TEXT_TYPE'] = Array(
			"VALUES" => Array(
				"html" => "HTML",
				"text" => "ТЕКСТ",
			),
			"DEFAULT" => "html"
		);

		$arFields['DETAIL_PICTURE'] = Array(
			"TYPE" => "F"
		);

		$arFields['TAGS'] = Array(
			"TYPE" => "TAG"
		);

		return $arFields;
	}
}
?>