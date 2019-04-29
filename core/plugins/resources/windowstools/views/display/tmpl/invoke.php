<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<html>
	<body>
		<p><a id="runapplink" href="<?php echo $this->url; ?>">Run app</a></p>
		<p>This page should go back ot the hub application page automatically. If it doesn't, click <a href="<?php echo $this->rurl; ?>">here.</a></p>
		<script>
		document.getElementById('runapplink').click();
		window.setTimeout(function(){
			window.location = "<?php echo $this->rurl; ?>";
		},1000);
		</script>
	</body>
</html>