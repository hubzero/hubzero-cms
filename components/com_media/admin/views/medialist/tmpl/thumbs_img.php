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

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div class="imgBorder center">
					<a class="img-preview" href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" style="display: block; width: 100%; height: 100%">
						<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" alt="<?php echo Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->_tmp_img->title, MediaHelper::parseSize($this->_tmp_img->size)); ?>" width="<?php echo $this->_tmp_img->width_60; ?>" height="<?php echo $this->_tmp_img->height_60; ?>" />
					</a>
				</div>
			</div>
			<div class="controls">
			<?php if (User::authorise('core.delete', 'com_media')):?>
				<a class="delete-item" target="_top" href="<?php echo Route::url('index.php?option=com_media&task=file.delete&tmpl=index&' . Session::getFormToken() . '=1&folder=' . $this->state->folder . '&rm[]=' . $this->_tmp_img->name); ?>" rel="<?php echo $this->_tmp_img->name; ?>">
					<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/images/remove.png" alt="<?php echo Lang::txt('JACTION_DELETE'); ?>" height="16" width"16" />
				</a>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; ?>" />
			<?php endif;?>
			</div>
			<div class="imginfoBorder">
				<a href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" class="preview">
					<?php echo $this->escape(substr($this->_tmp_img->title, 0, 10) . (strlen($this->_tmp_img->title) > 10 ? '...' : '')); ?>
				</a>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));