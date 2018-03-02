<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

$params = new \Hubzero\Config\Registry;
use Components\Media\Admin\Helpers\MediaHelper; // Move to controller

//$this->_tmp_img->name = ltrim($this->_tmp_img->name, DS);
//$this->_tmp_img->title = ltrim($this->_tmp_img->title, DS);

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>
		<div class="imgOutline">
			<div class="imgTotal">
				<div class="imgBorder center">
					<a class="img-preview" href="<?php echo '/app/site/media/' . $this->currentImg['path']; ?>" title="<?php echo $this->currentImg['name']; ?>" style="display: block; width: 100%; height: 100%">
						<img src="<?php echo '/app/site/media/' . $this->currentImg['path']; ?>" alt="<?php echo Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->currentImg['name'], MediaHelper::parseSize($this->currentImg['size'])); ?>" width="<?php echo '60'; //echo $this->_tmp_img->width_60; ?>" height="<?php echo '60'; //echo $this->_tmp_img->height_60; ?>" />
					</a>
				</div>
			</div>
			<div class="imginfoBorder">
			<?php if (User::authorise('core.delete', 'com_media')):?>
				<input type="checkbox" name="rm[]" value="<?php echo $this->currentImg['name']; ?>" />
			<?php endif;?>
				<a title="<?php echo $this->currentImg['name']; ?>" class="preview">
					<?php echo $this->escape(substr($this->currentImg['name'], 0, 10) . (strlen($this->currentImg['name']) > 10 ? '...' : '')); ?>
				</a>
			</div>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
