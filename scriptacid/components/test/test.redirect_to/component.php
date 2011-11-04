<?php
namespace ScriptAcid;
if(!defined("KERNEL_INCLUDED") || KERNEL_INCLUDED!==true)die();

if(false && $_GET["REDIRECT_TO"]) {
	//$this->redirectTo($_GET["REDIRECT_TO"]);
	if($this->_bAjaxMode) {
		?>
		<script type="text/javascript">
			//console.log('redirect to: <?php echo $_GET["REDIRECT_TO"];?>');
			window.location = '<?php echo $_GET["REDIRECT_TO"];?>';
		</script>
		<?php
	}
	else {
		redirectTo($_GET["REDIRECT_TO"]);
	}
}

if($_GET["REDIRECT_TO"]) {
	$this->redirectTo($_GET["REDIRECT_TO"]);
}
?>

<a href="#!REDIRECT_TO=/">AJAX Идти в корень.</a><br />
<a href="#!REDIRECT_TO=/test/">AJAX Идти в /test/.</a><br />
<a href="#!REDIRECT_TO=<?php echo urlencode('/test/test_get_component_params.php?ID=7&asdf=1234');?>">AJAX Идти в /test/test_get_component_params.php?ID=7.</a><br />

<a href="?REDIRECT_TO=/">Идти в корень.</a><br />
<a href="?REDIRECT_TO=/test/">Идти в /test/.</a><br />
<a href="?REDIRECT_TO=/test/test_get_component_params.php?ID=7">Идти в /test/test_get_component_params.php?ID=7.</a><br />
