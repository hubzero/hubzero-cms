<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<html>
	<body>
		<p><a id="runapplink" href="<?php echo $this->escape($this->url); ?>">Run app</a></p>
		<p>This page should go back ot the hub application page automatically. If it doesn't, click <a href="<?php echo $this->escape($this->rurl); ?>">here.</a></p>
		<script>
		document.getElementById('runapplink').click();
		window.setTimeout(function(){
			window.location = <?php echo json_encode((string) $this->rurl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?>;
		},1000);
		</script>
	</body>
</html>