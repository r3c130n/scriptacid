<?php namespace ScriptAcid;
	/**
	 * Здесь будет рзмещен код для вот такого использования
	 * ./run_cli_task.php taskname [params]
	 * Привер:
	 * ./run_task_cli.php import_data --from=%DOC_ROOT%/file.csv --into=tbl_name
	 * Теперь разберем данный подход
	 * Все задачи описываем в файле DOC_ROOT/scriptacid/config/tasks.php
	 * В файле создаем ф-ию 
	 * DOC_ROOT/scriptacid/config/tasks.php:
	 * [php]
	 * ...
	 * function import_data($argv) {
	 * 		$arCliArgs = parseCliArgs($argv);
	 * 		// Получили массив параметров
	 * 		// ... какой-то код некоего импорта
	 * }
	 * ...
	 * [/php]
	 * 
	 * Ещё можно так
	 * ./run_task_cli.php SomeMegaImportClass::import_data --from=%DOC_ROOT%/file.csv --into=tbl_name
	 * Тогда
	 * DOC_ROOT/scriptacid/config/tasks.php:
	 * [php]
	 * ...
	 * 	Class SomeMegaImportClass {
	 * 		const CLI = true; // Данный клас содержит задачи только для исполнения через консоль
	 * 		const CGI = false; // Запрещение исполнения ф-ий данного класса через веб-браузер
	 *		function import_data($argv) {
	 * 			$arCliArgs = parseCliArgs($argv);
	 * 			// Получили массив параметров
	 * 			// ... какой-то код некоего импорта
	 * 		}
	 * 	}
	 * ...
	 * [/php]
	 * 
	 * Так же есть ещё файл run_task_cgi.php
	 * По сути тоже самое, только вызывается через браузер
	 * http://scriptacid.ru/scriptacid/run_task_cgi?TASK=SomeMegaImportClass::import_data&from=file.csv&into=tbl_name
	 * Может или не может ф-ия быть выполнена через cli или cgi задается в значениях констант класса
	 * const CLI = true; // разрешает выполнение через консоль
	 * const CGI = true; // разрешает исполнение через веб-браузер
	 * 
	 * В случае с простыми ф-иями(НЕметодами) можно передавать в ф-ию доп-параметры
	 * И непосредственно в самой задаче вызывать иселючение в случае вызова задачи 
	 * через неугодный механизм, коим может быть, на мой взгляд, именно cgi.
	 * Потому как,
	 * потенциально, задачи, выполняемые через веб-браузер более уязызвимы,
	 * ибо доступы на исполнение кому угодно с правами веб-сервера. 
	 */
?>