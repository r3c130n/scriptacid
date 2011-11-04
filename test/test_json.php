<?php
namespace ScriptAcid;
require_once $_SERVER["DOCUMENT_ROOT"]."/scriptacid/core/application.php";

class __TestJsonSerialize implements \Serializable
{
	protected $_var1 = 1234.1234;
	protected $_var2 = " as sdf sd";
	protected $_var3;
	protected $_var4;
	protected $_var5;
	protected $_var6;
	public function __construct() {
		$this->_var3 = App::USER();
		$this->_var4 = App::DB();
		Modules::includeModule("catalog");
		$this->_var5 = new CatalogElement();
		$this->_var6 = array(
			"CATALOG_ID" => 3,
			"CATALOG_TYPE" => "some_catalog"
		);
	}
	
	public function getVar($num) {
		return $this->{"_var".$num};
	}
	
	public function serialize() {
		$arThisVars = get_object_vars($this);
		foreach($arThisVars as &$arThisVar) {
			if(is_object($arThisVar)) {
				$arThisVar = array(
					"__SERIALIZED_OBJECT__" => serialize($arThisVar)
				);
			}
		}
		$serializedThis = json_encode($arThisVars);
		return $serializedThis;
	}
	public function unserialize($serializedThis) {
		$arThisVars = get_class_vars(__CLASS__);
		$arSerrializedThis = json_decode($serializedThis, true);
		foreach($arThisVars as $varNameThis => &$arThisVar) {
			if(
				is_array($arSerrializedThis[$varNameThis])
				&&
				count($arSerrializedThis[$varNameThis]) == 1
				&&
				@isset($arSerrializedThis[$varNameThis]["__SERIALIZED_OBJECT__"])
			) {
				$arSerrializedThis[$varNameThis] = unserialize($arSerrializedThis[$varNameThis]["__SERIALIZED_OBJECT__"]);
			}
			$this->{$varNameThis} = $arSerrializedThis[$varNameThis];
		}
		return $arSerrializedThis;
	}
}

App::makePage(function (&$arPageParams) {?>
	
	<?php
	
		$testObj = new __TestJsonSerialize();
		//d($testObj);
		$serTestObj = serialize($testObj);
		$testObj = null;
		$testObj = unserialize($serTestObj);
		//d($testObj);
		for($i=1; $i <= 6; $i++) {
			d($testObj->getVar($i));
		}
	?>
	
<?php }); // end of makePage?>