<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/kernel.php";

class MyApplicationClass extends Application
{
	static public function getInstance() {
		if( !self::$_instance || !(self::$_instance instanceof self) ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function myAppMethod() {
		echo __CLASS__."::myAppMethod()";
	}
}


App::page(function() {?>
<pre><?php
App::setApplicationClass("ScriptAcid\MyApplicationClass");
App::myAppMethod();
echo App::Version();
?></pre>
<?php }); //end makePage?>