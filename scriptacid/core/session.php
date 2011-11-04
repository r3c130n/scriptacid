<?php
namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

/**
 * Сессии пользователей
 */
session_name("SACID_SESSION_ID");
session_start();

?>