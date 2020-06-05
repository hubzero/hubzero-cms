<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>

<div class="watch">
	<?php if ($this->watched) { ?>
		<p>
			<a href="<?php echo Route::url($this->project->link() . '&active=watch&action=manage'); ?>" class="showinbox"><?php echo Lang::txt('PLG_PROJECTS_WATCH_MANAGE'); ?></a>
		</p>
	<?php } else { ?>
		<p>
			<a href="<?php echo Route::url($this->project->link() . '&active=watch&action=manage'); ?>" class="showinbox"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_WATCH_SUBSCRIBE')); ?></a>
		</p>
	<?php } ?>
</div>