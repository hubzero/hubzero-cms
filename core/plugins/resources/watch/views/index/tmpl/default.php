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

<div class="item-watch <?php echo $this->watched->get('id') ? 'watching' : ''; ?>">
	<?php if ($this->watched->get('id')) { ?>
		<p>
			<button type="submit" form="hz-post-action" formaction="<?php echo Route::url($this->link . '&action=unsubscribe'); ?>" class="btn unsubscribe"><?php echo Lang::txt('PLG_RESOURCES_WATCH_UNSUBSCRIBE'); ?></button>
		</p>
	<?php } else { ?>
		<p>
			<button type="submit" form="hz-post-action" formaction="<?php echo Route::url($this->link . '&action=subscribe'); ?>" class="btn subscribe"><?php echo Lang::txt('PLG_RESOURCES_WATCH_SUBSCRIBE'); ?></button>
		</p>
	<?php } ?>

	<p>
		<?php echo Lang::txt('PLG_RESOURCES_WATCH_EXPLAIN'); ?>
	</p>
</div>