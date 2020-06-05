<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<?php if ($this->title) { ?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>
<?php } ?>

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
</div><!-- / .section -->