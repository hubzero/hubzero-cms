<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
		<p class="item-asset">
			<span class="asset-handle"></span>
			<span class="asset-file">
			<?php if ($this->asset->get('type') == 'link') { ?>
				<input type="text" name="assets[<?php echo $this->i; ?>][filename]" size="35" value="<?php echo $this->escape(stripslashes($this->asset->get('filename'))); ?>" placeholder="http://" />
			<?php } else { ?>
				<?php echo $this->escape(stripslashes($this->asset->get('filename'))); ?>
				<input type="hidden" name="assets[<?php echo $this->i; ?>][filename]" value="<?php echo $this->escape(stripslashes($this->asset->get('filename'))); ?>" />
			<?php } ?>
			</span>
			<span class="asset-description">
				<input type="hidden" name="assets[<?php echo $this->i; ?>][type]" value="<?php echo $this->asset->get('type'); ?>" />
				<input type="hidden" name="assets[<?php echo $this->i; ?>][id]" value="<?php echo $this->asset->get('id'); ?>" />
				<a class="icon-delete delete" data-id="<?php echo $this->asset->get('id'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&asset=' . $this->asset->get('id') . '&no_html=' . $this->no_html); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_DELETE_ASSET'); ?>">
					<?php echo Lang::txt('COM_COLLECTIONS_DELETE'); ?>
				</a>
			</span>
		</p>