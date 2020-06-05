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
<div class="item-watch <?php echo $this->watched ? 'watching' : ''; ?>">
	<?php if ($this->watched) { ?>
		<p>
			<a class="btn unsubscribe" href="<?php echo Route::url($this->publication->link('version') . '&active=watch&confirm=1&action=unsubscribe'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE'); ?></a>
		</p>
	<?php } else { ?>
		<p>
			<a class="btn subscribe" href="<?php echo Route::url($this->publication->link('version') . '&active=watch&confirm=1&action=subscribe'); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE'); ?></a>
		</p>
	<?php } ?>

	<p>
		<?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>
	</p>
</div>
