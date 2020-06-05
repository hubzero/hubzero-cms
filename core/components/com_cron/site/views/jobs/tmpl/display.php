<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->no_html) {
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_CRON'); ?></h2>
</header>

<section class="main section">

	<p>tock...</p>
	<pre><?php echo json_encode($this->output); ?></pre>

</section><!-- / .main section -->
<?php
} else {
	session_write_close();
	ob_clean();
	header('Content-type: application/json');
	echo json_encode($this->output);
	exit();
}
