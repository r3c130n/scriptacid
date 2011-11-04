<?php namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
SetTitle('Проект "ScriptACID CMF": Squery class test.');
App::get()->makePage(function(&$arPageParams) {?>

<?php echo "DATABASE TYPE: ".SQuery::DB_TYPE.endl;?>
<?php

function ___testSQueryClass() {
	$start = microtime(true);
	//*/
	$start = microtime(true);
	$squery = new SQuery();
	$startSetTpl = microtime(true);
	$addedNewType = $squery->setType("sometype", function(&$arPLACEHOLDER, &$value){
		d($arPLACEHOLDER);
		d($value);
		exit;
	});
	echo (($addedNewType)?"Новый тип добавлен":"Новый тип не добавлен").endl;
	$squery->template = 
		'
			SELECT * FROM table
			WHERE
				id = ?int
				AND
				bvar = ?boolean
				AND
				floatvar = ?float
				AND
				name LIKE %?varchar
				AND
				bvar = ?bool
				
			LIMIT ?int2, ?int2;
			
			SELECT * FROM table
			WHERE
				id = ?int4
				AND
				somevar = ?ufloat
				AND
				name LIKE %?char
			LIMIT ?int1, ?int2;
			
			SELECT * FROM table
			WHERE
				id = ?int8
				AND
				somevar = ?float
				AND
				name LIKE %?char
			LIMIT ?int2, ?int4;
							
			SELECT * FROM table
			WHERE
				id = ?int
				AND
				somevar = ?ufloat
				AND
				name LIKE %?char
			LIMIT ?int2, ?int2;
		'
	;
	
	$stopSetTpl = microtime(true);
	$deltaSetTpl = $stopSetTpl - $startSetTpl;
	echo "Время задания шаблона: ".$deltaSetTpl.endl;
	//*/ 
	$startSetArg = microtime(true);
	$squery->arg(1)->arg("true")->arg(1111.111)->arg("CHAR_PARAM1111")->arg("false")->arg(50)->arg(100);
	$squery->arg("2")->arg(222.222)->arg("CHAR_PARAM2222")->arg(50)->arg(100);
	$squery->arg("3")->arg(333.333)->arg("CHAR_PARAM3333")->arg(50)->arg(100);
	//d((string)$squery); // Здесь в строке результата мы увидим строку "-- This query does not ready for use.\n". Потому как остались не заполненными ещё 4 параметра.
	$squery->arg("4")->arg(444.444)->arg("CHAR_PARAM4444")->arg(50)->arg(100);
	$stopSetArg = microtime(true);
	$deltaSetArg = $stopSetArg - $startSetArg;
	echo "Время задания всех параметров: ".$deltaSetArg.endl;	
	//*/
	$startOut = microtime(true);
	$string = ((string)$squery); // Выводим готовый запрос.
	echo 'TEMPLATE:'.$squery->template.endl;
	echo "SQL: ".$squery.endl;
	$stopOut = microtime(true);
	$deltaOut = $startOut - $stopOut;
	echo "Время вывода: ".$deltaSetArg.endl;
	//*/
	
	if(!$squery->isReady()) {
		d($squery->ERRORS);
	}
	
	//*/
	$stop = microtime(true);
	$delta = $stop - $start;
	echo "Время вызова один раз: ".$delta.endl.endl;
	
	/*/
	// Просто выводим аблон запроса
	echo $squery->template;
	// Меняем плейсхолдеры шаблона на значения и выводим
	echo ($squery->arg("15")->arg("varfgfg").isReady())?$squery:"!не ве плейсхолдеры заменены".endl;
	// полученный запрос + дополнение к тексту зароса присваивается как шаблон этому же объекту.
	// получаем новый массив плейсхолдеров
	$squery->template = $squery." LIMIT ?int, ?int";
	// Здесь мы переопределяем новые плейсхолдеры
	echo ($squery->arg("1")->arg("50").isReady())?$squery:"!не ве плейсхолдеры заменены".endl;
	//*/
}
function ___testSQueryClassLittleStrings() {
	$start = microtime(true);
	//*/
	$squery = new SQuery(
		'
		SELECT * FROM table
			WHERE
				id = ?int4
				AND
				name LIKE %?char
			LIMIT ?int1, ?int4
		'
	);
	$squery->arg(123123)->arg("asfdasd")->arg(1)->arg(50);
	$stop = microtime(true);
	$delta = $stop - $start;
	echo 'TEMPLATE:'.$squery->template.endl;
	echo "SQL: ".$squery.endl;
	echo "Время вызова один раз: ".$delta.endl;
}

//*/
echo "Тест на маленьких запросах в кол-ве 10 штук.".endl;
$start = microtime(true);
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
___testSQueryClassLittleStrings();
$stop = microtime(true);
$delta = $stop - $start;
echo "Общее время вызовов: ".$delta.endl.endl;
//*/

echo "Тест на огромных запросах в кол-ве 10 штук.".endl;
$start = microtime(true);
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
___testSQueryClass();
$stop = microtime(true);
$delta = $stop - $start;
echo "Общее время вызовов: ".$delta.endl;
?>

<?php }); // end of makePage?>