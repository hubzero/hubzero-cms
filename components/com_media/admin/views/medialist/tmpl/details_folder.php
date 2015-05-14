<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>
		<tr>
			<td class="imgTotal">
				<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->_tmp_folder->path_relative); ?>" target="folderframe">
					<?php echo JHtml::_('image', 'media/folder_sm.png', $this->_tmp_folder->name, array('width' => 16, 'height' => 16), true); ?>
				</a>
			</td>
			<td class="description">
				<a href="<?php echo Route::url('index.php?option=com_media&view=mediaList&tmpl=component&folder=' . $this->_tmp_folder->path_relative); ?>" target="folderframe"><?php echo $this->_tmp_folder->name; ?></a>
			</td>
			<td>
				&#160;
			</td>
			<td>
				&#160;
			</td>
		<?php if (User::authorise('core.delete', 'com_media')):?>
			<td>
				<a class="delete-item" target="_top" href="<?php echo Route::url('index.php?option=com_media&task=folder.delete&tmpl=index&folder=' . $this->state->folder . '&' . Session::getFormToken() . '=1&rm[]=' . $this->_tmp_folder->name); ?>" rel="<?php echo $this->_tmp_folder->name; ?>' :: <?php echo $this->_tmp_folder->files+$this->_tmp_folder->folders; ?>"><?php echo JHtml::_('image', 'media/remove.png', Lang::txt('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?></a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_folder->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
