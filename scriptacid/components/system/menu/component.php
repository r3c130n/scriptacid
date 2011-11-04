<?php
namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
	if (!function_exists("\ScriptAcid\FillMenuItems")) {
		function FillMenuItems($arMenuItems) {
			if(is_array($arMenuItems) AND !empty($arMenuItems)) {
				$bCurrentFinded = false;
		        foreach($arMenuItems as $k => $arItem) {
					if (Permitions::CheckPathPerms($arItem[1])) {
						$arMenuItem = Array();
						$arMenuItem["NAME"] = $arItem[0];
						$arMenuItem["URL"] = $arItem[1];
						$arMenuItem["PARAM"] = $arItem[2];
						$arUrls[$k] = $arMenuItem["URL"];
						if (getCurDir() == $arItem[1] OR getCurPage() == $arItem[1]) {
							$arMenuItem["CURRENT"] = "Y";
							$bCurrentFinded = true;
						} else {
							$arMenuItem["CURRENT"] = "N";
						}
						$arMenuResultItems[$k] = $arMenuItem;
					}
		        }
		        
		        if(!empty($arUrls) AND !$bCurrentFinded) {
		        	$cur_url = ''; $cur = '';
		        	foreach ($arUrls as $n => $url) {
		        		if (strstr(getCurPage(), $url) OR strstr(getCurDir(), $url)) {
		        			if (strlen($cur_url) < strlen($url)) {
			        			$cur = $n;
			        			$cur_url = $url;
		        			}
		        		}
		        	}
		        	if ($cur != '') {
		        		$arMenuResultItems[$cur]["CURRENT"] = "Y";
		        	}
		        }
		        
		        return $arMenuResultItems;
		    }
		}
	}
	$dirs = split("/", getCurDir());
	$dir_path_tmp = '/';
	if (!empty($dirs)) {
		foreach ($dirs as $dir) {
			if (!empty($dir)) {
				$dir_path_tmp .= $dir.'/';
				$arDirs[] = $dir_path_tmp;
			}
		}
		if (!empty($arDirs))
			krsort($arDirs);
		$arDirs[] = '/';
		foreach ($arDirs as $dir_path) {
			//echo $dir_path;
			$menu_file_url = $_SERVER["DOCUMENT_ROOT"].$dir_path.'_menu.'.$arParams["TYPE"].'.php';
			if(file_exists($menu_file_url)) {
	    		require_once $menu_file_url;
	    		$arResult["ITEMS"] = FillMenuItems($arMenuItems);
	    		break;
			}
		}
	}
	if (empty($arResult["ITEMS"])) {
		$arResult["ITEMS"] = Array();
	}
	
	$this->connectComponentTemplate();
?>