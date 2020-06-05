<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="youtube_feed_<?php echo $this->id; ?>" class="youtube_<?php echo $this->params->get('layout') . ' ' . $this->params->get('moduleclass_sfx'); ?>">
	<?php if ($this->lazy) { ?>
		<?php echo Lang::txt('MOD_YOUTUBE_LOADING_FEED'); ?>
		<noscript><p class="error"><?php echo Lang::txt('MOD_YOUTUBE_ERROR_JAVASCRIPT_REQUIRED'); ?></p></noscript>
	<?php } else { ?>
		<?php echo $this->html; ?>
	<?php } ?>
</div>
