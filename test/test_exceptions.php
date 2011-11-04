<?php namespace ScriptAcid;

require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";
App::page(function(&$arPageParams) {?>
<?php 

trigger_error("some app deprecated error. TRIGGER.", E_USER_DEPRECATED);
trigger_error("some app warning error. TRIGGER.", E_USER_WARNING);
trigger_error("some app notice error. TRIGGER.", E_USER_NOTICE);
trigger_error("some app ERROR error. TRIGGER. It stops executing.", E_USER_ERROR);


throwException(AppDeprecatedException, "some app deprecated error");
throwException(AppNoticeException, "some app notice error");
throwException(AppWarningException, "some app warning error");
throwException(AppErrorException, "some app ERROR error. It stops executing");

throw new AppWarningException("some app error", "1");
throw new AppErrorException("some app fatal error", "1");

?>
<?php }); //end of makePage?>
