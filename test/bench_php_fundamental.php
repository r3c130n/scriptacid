<?php namespace ScriptAcid;
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';
App::page(function() {?>
<?php
// Не знаю в чем прикол, но надо обязательно вызвать первый раз, что бы тесты не искажались.
startBench('_this_need_for_correct_bench_at_first_call');
stopBench('_this_need_for_correct_bench_at_first_call');
Bench::startTime('_this_need_for_correct_bench_at_first_call');
Bench::stopTime('_this_need_for_correct_bench_at_first_call');


define('IF_DEFINE', true);
define('FORCE_DEFINE', true);

Bench::startTime('IF_DEFINE');
if(!defined('IF_DEFINE')) {
	define('IF_DEFINE', true);
}
d(Bench::stopTime('IF_DEFINE'));

Bench::startTime('FORCE_DEFINE');
@define('FORCE_DEFINE', true);
d(Bench::stopTime('FORCE_DEFINE'));


?>
	
<?php }); // end of makePage?>