<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$params = new \Hubzero\Config\Registry;

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>
		<tr>
			<td>
				<a title="<?php echo $this->_tmp_doc->name; ?>">
					<?php if (file_exists(PATH_CORE . '/components/com_media/admin/assets/images/' . $this->_tmp_doc->icon_16)) { ?>
						<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/<?php echo $this->_tmp_doc->icon_16; ?>" alt="<?php echo $this->_tmp_doc->title; ?>" width="16" height="16" />
					<?php } else { ?>
						<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/con_info.png" alt="<?php echo $this->_tmp_doc->name; ?>" width="16" height="16" />
					<?php } ?>
				</a>
			</td>
			<td class="description"  title="<?php echo $this->_tmp_doc->name; ?>">
				<?php echo $this->_tmp_doc->title; ?>
			</td>
			<td>
				&#160;
			</td>
			<td class="filesize">
				<?php echo MediaHelper::parseSize($this->_tmp_doc->size); ?>
			</td>
		<?php if (User::authorise('core.delete', 'com_media')):?>
			<td>
				<a class="delete-item" target="_top" href="<?php echo Route::url('index.php?option=com_media&task=file.delete&tmpl=index&' . Session::getFormToken() . '=1&folder=' . $this->state->folder . '&rm[]=' . $this->_tmp_doc->name); ?>" rel="<?php echo $this->_tmp_doc->name; ?>">
					<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/remove.png" alt="<?php echo Lang::txt('JACTION_DELETE'); ?>" height="16" width"16" />
				</a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_doc->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
