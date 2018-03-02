<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div align="center" class="imgBorder">
					<a class="folder-item" href="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->currentFolder['path']); ?>" target="folderframe">
						<?php echo Lang::txt('COM_MEDIA_FOLDER'); ?>
					</a>
				</div>
			</div>
			<div class="imginfoBorder">
			<?php if (User::authorise('core.delete', 'com_media')):?>
				<input type="checkbox" name="rm[]" value="<?php echo $this->currentFolder['name']; ?>" />
			<?php endif;?>
				<a href="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->currentFolder['path']); ?>" target="folderframe"><?php echo substr($this->currentFolder['name'], 0, 10) . (strlen($this->currentFolder['name']) > 10 ? '...' : ''); ?></a>
			</div>
		</div>
