<?php namespace ScriptAcid;
define('_LIB_LOAD_DEBUG', false);
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';

App::get()->makePage(function() {?>
	<?php $bLoaded = false;?>

	<?php true && $bLoaded = Modules::setAutoloadClasses(
		false,
		array(
			'\\TestNS\\TestSubNS\\TestNSClass' => 'test/core_test/class_modules/testlib/class.TestNS-TestSubNS-TestNSClass.php',
			'TestNS\\TestSubNS\\TestNSClass' => 'test/core_test/class_modules/testlib/class.ScriptAcid-TestNS-TestSubNS-TestNSClass.php',
		),
		false, true
	)?>
	<?php //d(Modules::getClassesArray());?>
	<?php false && $bLibLoaded = Modules::includeLibFiles('test/core_test/class_modules/testlib', false, true);?>

	<?php \TestNS\TestSubNS\TestNSClass::test();?>
	<?php TestNS\TestSubNS\TestNSClass::test();?>

<?php });?>
