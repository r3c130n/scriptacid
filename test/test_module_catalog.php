<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";

App::makePage(function (&$arPageParams) {?>

	<?php

		class FormElement {
			public $htmlTpl = '<label for="#ID#">#LABEL#: <input type="text" id="#ID#" size="#SIZE#" name="#NAME#" class="#CLASS#" value="#VALUE#" /> <span>#DESCRIPTION#</span></label>';
			public $arTV = Array(
				'ID', 'LABEL', 'SIZE', 'NAME', 'CLASS', 'VALUE', 'DESCRIPTION'
			);
			public $arAttributes = Array();

			public function __construct($name, $label, $value, $arProps) {
				$this->arAttributes['NAME'] = $name;
				$this->arAttributes['LABEL'] = $label;
				$this->arAttributes['VALUE'] = $value;

				if (!empty($arProps)) {
					foreach ($arProps as $key => $value) {
						$this->arAttributes[$key] = $value;
					}
				}
			}

			public function ReplaceVars() {
				$outHtml = $this->htmlTpl;
				foreach ($this->arTV as $tv) {
					if (array_key_exists($tv, $this->arAttributes)) {
						$outHtml = str_replace('#'.$tv.'#', $this->arAttributes[$tv], $outHtml);
					} else {
						$outHtml = str_replace('#'.$tv.'#', '', $outHtml);
					}
				}

				return $outHtml;
			}

			public function getFormHtml() {
				return $this->ReplaceVars();
			}
		}



		class FieldType {
			public $name = 'Название';
			public $description = 'Описание';
			public $regCheckString = '/^(.*)$/';
			public $type = 'S';
			public $arSettings = Array();
			
			public function Validate($value) {
				if (preg_match($this->regCheckString, $value)) {
					return true;
				}
				return false;
			}

			public function getSettings() {
				return $this->arSettings;
			}


			public function prepareSaveSettings() {
				
			}

			public function prepareDisplaySettings() {

			}

			public function getHtml($name, $label, $value, $arAttributes) {
				return '<b>HTML формы ввода.</b>';
			}

			public function getSettingsHtml($name) {
				return '<b>Параметры типа свойства.</b>';
			}

		}

		class FieldN extends FieldType {
			public $name = 'Число';
			public $description = 'Любое число';
			public $regCheckString = '/^([0-9\.]*)$/';
			public $type = 'N';

			public function getHtml($name, $label, $value, $arAttributes) {
				$number = new FormElement($name, $label, $value, $arAttributes);
				return $number->getFormHtml();
			}
		}

		class FieldM extends FieldType {
			public $name = 'E-mail';
			public $description = 'E-mail адрес';
			public $regCheckString = '/^([0-9a-zA-Z\.]*)@([0-9a-zA-Z\.]*)\.([a-zA-Z]{2,7})$/';
			public $type = 'M';

			public function getHtml($name, $label, $value, $arAttributes) {
				$number = new FormElement($name, $label, $value, $arAttributes);
				return $number->getFormHtml();
			}
		}

		class FieldL extends FieldType {
			public $name = 'Список';
			public $description = 'Список значений';
			public $regCheckString = '/^(.*)$/';
			public $type = 'L';

			public function getHtml($name, $label, $value, $arAttributes) {
				$number = new FormElement($name, $label, $value, $arAttributes);
				return $number->getFormHtml();
			}
		}



		$str = new FieldL();
		$arAttributes = Array(
			'SIZE' => '50',
			'CLASS' => '',
			'DESCRIPTION' => 'Введите в это поле e-mail.'
		);
		echo $str->getHtml('number', 'E-mail', 'qw@er.ty', $arAttributes);



	?>

<?php }); // end of makePage?>