<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
/**
 * Управление правами
 * @author r3c130n
 */
/**
 * TODO: переработать, чтобы можно было задавать для каждой группы права на:
 * чтение, запись, запрещено
 */
class Permitions {
	private static $arUserGroups = Array();
	private static $arAllowGroups = Array();
	private static $arDenyGroups = Array();
	private static $nobody = 2; // Самая бесправная группа

	public function SetAllowGroups($arGroups = Array()) {
		self::$arAllowGroups = $arGroups;
	}

	public function GetAllowGroups() {
		return self::$arAllowGroups;
	}

	public function SetDenyGroups($arGroups = Array()) {
		self::$arDenyGroups = $arGroups;
	}

	public function GetDenyGroups() {
		return self::$arDenyGroups;
	}

	public function CheckPathPerms($path) {
		$accessPath = self::FindAccessFiles($path);
		if($accessPath !== false) {
			require_once $_SERVER["DOCUMENT_ROOT"].$accessPath.'.access.php';
			if (!empty($arAllowGroups)) {
				self::SetAllowGroups($arAllowGroups);
			}
			if (!empty($arDenyGroups)) {
				self::SetDenyGroups($arDenyGroups);
			}
			return self::Check();
		} else {
			return true;
		}
	}

	public function GetUrlPieces($path) {
		$arPieces = Array();
		$arTmpPath = explode('/', $path);
		foreach ($arTmpPath as $tmp) {
			if ($tmp != '') {
				$arPath[] = $tmp;
			}
		}
		if (!empty($arPath)) {
			$pCnt = count($arPath);
			for($i=0; $i<count($arPath); $i++) {
				$pie = '';
				for($j=0; $j<$pCnt; $j++) {
					if ($arPath[$j] != '') {
						$pie .= '/' . $arPath[$j];
					}
				}
				$pCnt--;
				$arPieces[] = $pie;
			}
		}
		return $arPieces;
	}

	public function FindAccessFiles($path) {
			$arPath = self::GetUrlPieces($path);
			if (!empty($arPath)) {
				foreach ($arPath as $dir) {
					if (file_exists($_SERVER["DOCUMENT_ROOT"].$dir.'/.access.php')) {
						return $dir . '/';
					}
				}
			}
		return false;
	}

	public function Check() {
		global $USER;
		$bAllow = false;
		$bDeny = false;
		if ($USER->IsAuthorized()) {
			if ($arGroups = $USER->GetUserGroups()) {
				if (!empty($arGroups)) {
					self::$arUserGroups = $arGroups;
					foreach ($arGroups as $group) {
						if (in_array($group, self::$arAllowGroups)) {
							return true;
						}
						if (in_array($group, self::$arDenyGroups)) {
							return false;
						}
					}

					if (!empty(self::$arAllowGroups)) {
						return false;
					} else {
						return true;
					}
					
				} else {
					if (in_array(self::$nobody, self::$arDenyGroups)) {
						return false;
					} else {
						return true;
					}
				}
			}
		} else {
			if (in_array(self::$nobody, self::$arDenyGroups)) {
				return false;
			} else {
				if (in_array(self::$nobody, self::$arAllowGroups) OR empty(self::$arAllowGroups)) {
					return true;
				} else {
					return false;
				}
			}
		}
	}
}
?>