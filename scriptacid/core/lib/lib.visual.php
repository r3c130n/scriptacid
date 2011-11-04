<?php namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();
/**
 * Визуальное управление системой
 */
class Face {

	public static function GetParameterHTML($sName, $arParameter, $existingValue, $bShowInTable = true) {
		$html = '';
		$arParameter = TildaArray($arParameter);
		if ($bShowInTable) {
			$html .= '<tr><td>';
		}

		$parameterValue = empty($existingValue) ? $arParameter['DEFAULT'] : $existingValue;

		switch ($sName) {
			case 'AJAX_MODE':
				break;
			case 'CACHE_TIME':
				break;
			default:
				$html .= $arParameter['NAME'] . '</td><td>';
				switch ($arParameter['TYPE']) {
					case 'CHECKBOX':
						$html .= '<input type="checkbox" name="' . $sName . '" value="' . $parameterValue . '"';
						if ($parameterValue == "Y") $html .= ' checked=checked';
						if ($arParameter['REFRESH'] == 'Y')
							$html .= ' onChange="RefreshParamsDialog({\'key\': \'' . $sName . '\', \'value\': this.checked == true ? \'Y\' : \'N\'})"';
						$html .= '/>';
						break;
					case 'LIST':
						if (is_array($arParameter['VALUES'])) {
							$html .= '<select name="' . $sName . '"';
							if ($arParameter['MULTIPLE'] == 'Y') $html .= ' multiple="multiple" size="5"';
							if ($arParameter['REFRESH'] == 'Y')
							$html .= ' onChange="RefreshParamsDialog({\'key\': \'' . $sName . '\', \'value\': this.value})"';
							$html .= '>';
							foreach ($arParameter['VALUES'] as $key => $value) {
								$html .= '<option value="' . $key . '"';
								if ($parameterValue == $key) $html .= ' selected=selected';
									$html .= '>' . $value . '</option>';
							}
							$html .= '</select>';
						}
						break;
					case 'STRING':
					default:
						$html .= '<input type="text" name="' . $sName . '" value="' . $parameterValue . '"/>';
						break;
				}

				if ($bShowInTable) {
					$html .= '</td></tr>';
				}
				break;
		}

		return $html;
	}

	public static function EditorPanel($fileName) {
		//$js = '<div id="pageEditorDiv"><form name="editor" action="" method="post">';
		//$js .= self::GetEditor('pageEditor', self::Parse(self::ReadFile($fileName), 'php'));
		//$js .= '<input type="submit" name="edit_page_content" value="Сохранить" /></form></div>';
		$html = '<div id="siteEditorPanel">';
		$js = '<div id="dialog-window-form"></div>';
		$arPanelItems = Panel::getItems();
		if (count($arPanelItems) > 0) {
			$html .= '<ul>';
			foreach ($arPanelItems as $name => $link) {
				if (substr($link, 0, 4) == '<js>') {
					$url = 'javascript:void(0)" onClick="' . str_replace('<js>', '', $link);
				} elseif ($link == '<br>') {
					$name = ' | ';
					$url = 'javascript:void(0)';
				} else {
					$url = $link;
				}
				$html .= '<li><a href="' . $url . '">' . $name . '</a></li>';
			}
			$html .= '</ul>';
		}
		$html .= '</div>';
		$html .= '<div id="panelCloser"></div>';
		if (isset($_POST['edit_page_content'])) {
		//	self::WriteFile($fileName, $_POST['pageEditor']);
			//d($_POST['pageEditor']);
		}

		return $js.$html;
	}

	public static function GetEditor($name = 'elm1', $text = '', $style = '') {
		$js = '
		<script type="text/javascript">
			tinyMCE.init({
				// General options
				mode : "textareas",
				language : "ru",
				editor_selector: "editor_'.$name.'",
				theme : "advanced",
				plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",

				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,

				// Example content CSS (should be your site CSS)
				content_css : "css/content.css",

				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js",

				// Replace values for the template plugin
				template_replace_values : {
					username : "Some User",
					staffid : "991234"
				}
			});
		</script>';
		$html = '<textarea id="'.$name.'" name="'.$name.'" '.$style.' class="editor_'.$name.'" rows="15" cols="80" style="width: 80%">' . $text . '</textarea>';
		return $js.$html;
	}

	public static function Parse($content, $mode = 'clear') {

		switch ($mode) {
			case 'html':
				$content = htmlentities($content);
				break;
			case 'php':
				$pattern = Array();
				//$pattern[] = "/\[img\]http:\/\/([a-z0-9A-Z\.\/\_\-\ ]+[.gif|.jpg|.jpeg|.GIF|.JPG|.JPEG|.png|.PNG])\[\/img\]/";
				//$pattern[] = '/\<\?([ ]){0,5}require_once([ ]){1,5}\$\_SERVER\["DOCUMENT_ROOT"\]([ ]){0,5}\.([ ]){0,5}"\/scriptacid\/core\/prolog.php"([ ]){0,5}\?\>/'; //
				//$pattern[] = '/\<\?([ ]){0,5}require_once([ ]){1,5}\$\_SERVER\["DOCUMENT_ROOT"\]([ ]){0,5}\.([ ]){0,5}"\/scriptacid\/core\/epilog.php"([ ]){0,5}\?\>/'; //
				//$pattern[] = '/\<\?([ ]){0,5}SetTitle\("([a-zA-Z0-9\.\,\ \-\/\_\?])"\)\;\?\>/';
				$pattern[] = '/\<\?(.*)\?\>/';

				$content = preg_replace_callback($pattern,
					'getPhpHtml',
					$content
				);

				//$content = htmlentities($content);
				break;
			case 'text':
			default:
				$content = $content;
				break;
		}

		return $content;
	}

	public static function GetPageParametersForm($pageUrl, $arParams) {
		$html = "";
		$parser = new Parser($pageUrl);
		if ($parser) {
			$html = "";//'<script type="text/javascript" src="/scriptacid/lib/gpl/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>';
			$html .= '<script type="text/javascript">
tinyMCE.init({
		// General options
		mode : "textareas",
		language : "ru",
		editor_selector: "editor_' .$arParams['editorName'] .'",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
		valid_elements: "*[*]",
		invalid_elements: "object,applet,iframe",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});</script>';
			$html .= '<form onSubmit="SavePage($(\'#'.$arParams['editorName'].'\').val());return false;">';
			$html .= '<textarea id="'.$arParams['editorName'].'" name="'.$arParams['editorName'].'" class="editor_' .$arParams['editorName'] .'" rows="20" cols="30" style="width: 100%">' . $parser->pageContent . '</textarea>';;
			$html .= '</form>';
			return $html;
		} else {
			return "No content";
		}
		
		
		$arPageParameters = Array();
		$form = '<form class="dialog-window-form-form">';

		$form .= '<table class="dialog-window-form-table styled">';

		foreach ($arPageParameters['PARAMETERS'] as $name => $parameter) {
			$form .= Face::GetParameterHTML($name, $parameter, $arParams[$name]);
		}

		$form .= '</table>';
		$form .= '</form>';

		return $form;
	}
}

/**
 * Конструктор таблиц
 */
class Table {
	public $arRows = Array();
	public $arCols = Array();
	public $tableAttr = '';
	public $headAttr = '';
	public $bodyAttr = '';
	public $footAttr = '';
	private $toogleTdAttr = Array();

	public function  __construct($strAttributes = '') {
		$this->tableAttr = $strAttributes;
	}

	public function addRow($arValues) {
		$this->arRows[] = $arValues;
	}

	public function addCol($type, $value, $params = '') {
		$this->arCols[] = Array("TYPE" => $type, "VALUE" => $value, "PARAMS" => $params);
	}

	public function setToogleTr($firstAttr = '', $secondAttr = '') {
		$this->toogleTdAttr = Array($firstAttr,	$secondAttr);
	}

	public function getTable() {
		$table = '<table '.$this->tableAttr.'>';
		$table .= $this->getTHead();
		$table .= $this->getTBody();
		$table .= $this->getTFoot();
		$table .= '</table>';
		return $table;
	}

	private function getTHead() {
		if(!empty($this->arCols)) {
			$th = "<thead>";
			$th .= '<tr '.$this->headAttr.'>';
			foreach($this->arCols as $arCol) {
				$th .= '<th>' . $arCol['VALUE'] . '</th>';
			}
			$th .= '</tr>';
			$th .= "</thead>";
			return $th;
		} else {
			return '';
		}
	}

	private function getTBody() {
		if(!empty($this->arRows)) {
			$tb = "<tbody>";
			$trCounter = 0;
			foreach($this->arRows as $arRow) {
				$toogleAttr = '';
				if(!empty($this->toogleTdAttr)) {
					if($trCounter == 0) {
						$toogleAttr = $this->toogleTdAttr[0];
						$trCounter++;
					} else {
						$toogleAttr = $this->toogleTdAttr[1];
						$trCounter--;
					}
				}

				$tb .= '<tr '.$this->bodyAttr.'>';
				foreach($this->arCols as $arCol) {
					$tb .= '<td '.$toogleAttr.' '.$arCol['PARAMS'].'>' . $arRow[$arCol['TYPE']] . '</td>';
				}
				$tb .= '</tr>';
			}
			$tb .= "</tbody>";
			return $tb;
		} else {
			return '';
		}
	}

	private function getTFoot() {
		return '<tfoot></tfoot>';
	}
}
?>