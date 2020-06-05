<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->tool) { ?>
		<?php if ($this->row->codeaccess=='@OPEN') { ?>
			<p><?php echo Lang::txt('COM_RESOURCES_OPEN_SOURCE', $this->row->version); ?></p>
		<?php } else { ?>
			<p><?php echo Lang::txt('COM_RESOURCES_CLOSED_SOURCE', $this->row->version); ?></p>
		<?php } ?>
	<?php } ?>
</header><!-- / #content-header.full -->
<section class="main section">
	<?php if ($this->row->license) { ?>
		<pre><?php echo $this->row->license; ?></pre>
	<?php } else { ?>
		<p class="warning"><?php echo Lang::txt('COM_RESOURCES_NO_LICENSE_TEXT'); ?></p>
	<?php } ?>
</section><!-- / .main section -->