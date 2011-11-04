<?php namespace ScriptAcid;
require_once $_SERVER['DOCUMENT_ROOT'].'/scriptacid/core/application.php';

// making parent-child relation table
function getRelationTableFromFlatTree(&$arFlatTree, $DEPTH_KEY = 'DEPTH_LEVEL', $CHILDS_KEY = 'CHILDS', $PARENT_KEY = 'PARENT' ) {
	$iItems = 0;
	$itemsCount = count($arFlatTree);
	$curPointer = &$arTree;
	$curDepth = 1;
	$prevKey = 0;
	$parentKey = 0;
	$arLastKeyInDepth = array();
	$arParents = array();
	$arChilds = array();
	foreach($arFlatTree as $key => &$item) {
		$iItems++;
		if($item[$DEPTH_KEY] > $curDepth) {
			$parentKey = $prevKey;
			$curDepth = $item[$DEPTH_KEY];
		}
		elseif($item[$DEPTH_KEY] < $curDepth) {
			$curDepth = $item[$DEPTH_KEY];
			$parentKey = $arLastKeyInDepth[$curDepth-1];
		}
		$arChilds[$key] = array(
			$DEPTH_KEY => $curDepth,
			$PARENT_KEY => $parentKey
		);
		if(!$parentKey) $parentKey = 0;
		$arParents[$parentKey][$CHILDS_KEY][] = $key;
		$prevKey = $key;
		$arLastKeyInDepth[$item[$DEPTH_KEY]] = $prevKey;
	}
	//d($arParents, '$arParents');
	//d($arChilds, '$arChilds');
	
	$arRelations = array();
	$arRelations[0] = $arParents[0];
	foreach($arChilds as $childKey => $arChild) {
		$arRelations[$childKey] = $arChild;
		$arRelations[$childKey][$CHILDS_KEY] = array();
		$arRelations[$childKey][$CHILDS_KEY] = $arParents[$childKey][$CHILDS_KEY];
	}
	//d($arRelations, '$arRelations');
	
	return $arRelations;
}

App::page(function() {?>

<?php 
	
	$array1 = array(
		"KEY1" => array(
			"NAME" => "EL1",
			"DEPTH_LEVEL" => "1"
		),
		"KEY2" => array(
			"NAME" => "EL2",
			"DEPTH_LEVEL" => "1"
		),
		"KEY21" => array(
			"NAME" => "EL21",
			"DEPTH_LEVEL" => "2"
		),
		"KEY22" => array(
			"NAME" => "EL22",
			"DEPTH_LEVEL" => "2"
		),
		"KEY221" => array(
			"NAME" => "EL221",
			"DEPTH_LEVEL" => "3"
		),
		"KEY222" => array(
			"NAME" => "EL222",
			"DEPTH_LEVEL" => "3"
		),
		"KEY23" => array(
			"NAME" => "EL23",
			"DEPTH_LEVEL" => "2"
		),
		"KEY24" => array(
			"NAME" => "EL24",
			"DEPTH_LEVEL" => "2"
		),
		"KEY241" => array(
			"NAME" => "EL241",
			"DEPTH_LEVEL" => "3"
		),
		"KEY2411" => array(
			"NAME" => "EL2411",
			"DEPTH_LEVEL" => "4"
		),
		"KEY3"  => array(
			"NAME" => "EL3",
			"DEPTH_LEVEL" => "1"
		),
	);

	$arResultTree = getRelationTableFromFlatTree($array1);
	d($arResultTree);

	
	

?>
	
<?php }); // end of makePage?>