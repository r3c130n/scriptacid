<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
/**
 * Управление файлами
 * @author r3c130n
 */
class File {
	private static $arFile;
	private static $file_db = 'b_file';
	private static $FILE_ID = false;
	private static $bExists = false;
	public  static $cFilesCount = 0;
	private static $arUploadErrors = Array(
		// http://www.php.net/manual/en/features.file-upload.errors.php
		UPLOAD_ERR_OK 			=> "Успешно",
		UPLOAD_ERR_INI_SIZE  	=> "Размер файла больше, чем upload_max_filesize",
		UPLOAD_ERR_FORM_SIZE 	=> "Размер файла больше, чем MAX_FILE_SIZE",
		UPLOAD_ERR_PARTIAL 		=> "Частично загружен",
		UPLOAD_ERR_NO_FILE 		=> "Файл отсутствует",
		UPLOAD_ERR_NO_TMP_DIR	=> "Не задана временная папка",
		UPLOAD_ERR_CANT_WRITE	=> "Не могу писать на диск",
		UPLOAD_ERR_EXTENSION 	=> "Загрузка файла остановлена расширением"
	);

	//public function  __construct() {

	//}

	public static function MakeFileArray($FILE_ID = null) {
		return Array();
	}

	public static function GetPath($FILE_ID = null) {
		if ($FILE_ID != null) {
			$arFile = self::GetByID($FILE_ID);
			if(!empty($arFile)) {
				return $arFile['SRC'];
			}
		}
	}

	public static function GetByID($FILE_ID = null) {
		global $DB;
		if ($FILE_ID != null) {
			$sql = "SELECT * FROM `" . self::$file_db . "` WHERE `ID` = '" . intVal($FILE_ID) . "';";
			$DB->Query($sql);
			if ($arFile = $DB->Fetch()) {
				$arFile['SRC'] = '/upload/' . $arFile['MODULE_ID'] . '/' . $arFile['FILE_NAME'];
				return $arFile;
			}
		}
		return false;
	}

	public static function GetHash($arFields = Array()) {
		$hash = md5(substr((base64_encode(md5(GetRandom(5) . time() . join(':', $arFields)))),0,10));
		return $hash;
	}

	public static function ReadFile($file) {
		if(!is_file($_SERVER["DOCUMENT_ROOT"] . $file)) return false;
		return file_get_contents($_SERVER["DOCUMENT_ROOT"] . $file);
	}

	public static function WriteFile($file, $content, $mode = 'php') {
		global $cacheHtml;
		if(!is_file($_SERVER["DOCUMENT_ROOT"] . $file)) return false;
		$old_content = self::ReadFile($file);
		$old = self::Parse($old_content, 'php');
		$new_content = str_replace(trim($old), $content, $old_content);
		$fp = fopen($_SERVER["DOCUMENT_ROOT"] . $file, 'w');
		fwrite($fp, $new_content);
		fclose($fp);
	}

	public static function GetFileType($file = NULL) {
		if(!is_file($file)) return false;
		if($arImg = self::GetImageInfo($file)) {
			return Array('TYPE' => 'IMAGE', 'PROPS' => $arImg);
		} else {
			//TODO: дописать File::GetFileType()
			d('N');
		}
	}

	public static function GetImageInfo($file = NULL) {
		if(!is_file($file)) return false;

		if(!$data = getimagesize($file) or !$filesize = filesize($file)) return false;

		$extensions = array(1 => 'gif',    2 => 'jpg',
						  3 => 'png',    4 => 'swf',
						  5 => 'psd',    6 => 'bmp',
						  7 => 'tiff',    8 => 'tiff',
						  9 => 'jpc',    10 => 'jp2',
						  11 => 'jpx',    12 => 'jb2',
						  13 => 'swc',    14 => 'iff',
						  15 => 'wbmp',    16 => 'xbmp');

		$result = array('width'     =>    $data[0],
					  'height'		=>    $data[1],
					  'extension'   =>    $extensions[$data[2]],
					  'size'        =>    $filesize,
					  'mime'        =>    $data['mime']);

		return $result;
	}

	public static function UploadFile($arFile, $module = 'main', $bRename = true, $uploadsDir = UPLOAD_PATH_FULL) {
		$cut = strrpos($arFile['name'], '.');
		$type = substr($arFile['name'], $cut + 1, strlen($arFile['name']) - $cut);
		if (file_exists($arFile['tmp_name'])) {
			if (!$bRename) {
				$newFile = $uploadsDir . "/" . basename($arFile['name']);
			} else {
				$newName = self::GetHash(Array(basename($arFile['name']))). '.' . $type;
				if(!is_dir($uploadsDir . $module)) {
					mkdir($uploadsDir . $module);
				}

				$newFile = $uploadsDir . $module . '/' . $newName;
			}
			
			if(move_uploaded_file($arFile['tmp_name'], $newFile)) {
				return Array('STATUS' => 'SUCCESS', 'PATH' => $newFile, 'NAME' => $newName);
			} else {

				return Array('STATUS' => 'ERROR', 'RESULT' => self::$arUploadErrors[$arFile['error']]);
			}
		}
	}

	public function SaveFile($arFile = Array(), $arFields = Array()) {
		global $DB;

		$arFileFields = Array();

		$arFields['MODULE_ID'] = empty($arFields['MODULE_ID']) ? 'main' : $arFields['MODULE_ID'];

		$arUpload = self::UploadFile($arFile, $arFields['MODULE_ID']);
		if ($arUpload['STATUS'] == 'SUCCESS') {
			$arFileFields['FILE_NAME'] = $arUpload['NAME'];

			$arFileInfo = self::GetFileType($arUpload['PATH']);

			$arFileFields['TIMESTAMP_X']	=	date("Y-m-d H:i:s");
			$arFileFields['MODULE_ID']		=	$arFields['MODULE_ID'];

			if ($arFileInfo['TYPE'] == 'IMAGE') {
				$arFileFields['HEIGHT']		=	$arFileInfo['PROPS']['height'];
				$arFileFields['WIDTH']		=	$arFileInfo['PROPS']['width'];
			}


			$arFileFields['FILE_SIZE']		=	$arFile['size'];
			$arFileFields['CONTENT_TYPE']	=	$arFile['type'];

			//$arFileFields['SUBDIR']			=	'';

			$arFileFields['ORIGINAL_NAME']	=	$arFile['name'];

			$arFileFields['DESCRIPTION']	=	empty($arFields['DESCRIPTION']) ? '' : $arFields['DESCRIPTION'];

			$sql = addSql(self::$file_db, $arFileFields);

			if ($DB->Query($sql)) {
				$newID = $DB->LastID();
				if ($newID > 0) {
					return $newID;
				} else {
					return false;
				}
			}
		} else {
			return $arUpload['RESULT'];
		}
	}

	public function GetCount() {
		global $DB;
		$sql = "SELECT COUNT(*) FROM `".self::$file_db."`";
		$DB->Query($sql);
		if ($arFile = $DB->Fetch()) {
			return array_shift($arFile);
		} else {
			return false;
		}
	}

	public function GetList($arFilter = Array(), $arPaginator = Array()) {
		global $DB;

		// Постраничка
		if (!empty($arPaginator)) {
			$page = !empty($arPaginator['PAGE']) ? (int)$arPaginator['PAGE'] : 1;
			$per_page = $arPaginator['PAGE_COUNT'];
			$total_count = $this->GetCount();
			$pagination = new Paginator($page, $per_page, $total_count);
			$arFiles['PAGINATOR'] = $pagination->GetHtml();
		}

		// TODO: Добавить фильтр
		$sql = "SELECT * FROM `".self::$file_db."`";
		if (!empty($arPaginator)) {
			$sql .= "LIMIT {$per_page} ";
			$sql .= "OFFSET {$pagination->Offset()}";
		}
		$DB->Query($sql);
		while ($arFile = $DB->Fetch()) {
			$arFiles['FILES'][] = $arFile;
		}

		if (empty($arFiles))
			return false;

		return $arFiles;
	}
}

function getPhpHtml($matches) {
	global $cacheHtml;
	if(!empty($matches[1])) {
		$cacheHtml  .= '<?' . $matches[1] . "?>\n";
	}
}
?>