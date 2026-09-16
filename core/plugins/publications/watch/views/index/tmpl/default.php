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
			<form action="<?php echo Route::url($this->publication->link('version') . '&active=watch&confirm=1&action=unsubscribe'); ?>" method="post" class="inline-form">
				<?php echo Html::input('token'); ?>
				<button type="submit" class="btn unsubscribe"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE'); ?></button>
			</form>
		</p>
	<?php } else { ?>
		<p>
			<form action="<?php echo Route::url($this->publication->link('version') . '&active=watch&confirm=1&action=subscribe'); ?>" method="post" class="inline-form">
				<?php echo Html::input('token'); ?>
				<button type="submit" class="btn subscribe"><?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE'); ?></button>
			</form>
		</p>
	<?php } ?>

	<p>
		<?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>
	</p>
</div>
