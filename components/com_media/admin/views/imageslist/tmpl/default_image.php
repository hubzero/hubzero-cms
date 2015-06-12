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
		<div class="item">
			<a href="javascript:ImageManager.populateFields('<?php echo $this->_tmp_img->path_relative; ?>')" title="<?php echo $this->_tmp_img->name; ?>" >
				<img src="<?php echo Request::root(); ?>/components/com_media/admin/assets/<?php echo $this->baseURL.'/'.$this->_tmp_img->path_relative; ?>" alt="<?php echo Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->_tmp_img->title, MediaHelper::parseSize($this->_tmp_img->size)); ?>" height="<?php echo $this->_tmp_img->height_60; ?>" width"<?php echo $this->_tmp_img->width_60; ?>" />
				<span title="<?php echo $this->_tmp_img->name; ?>"><?php echo $this->_tmp_img->title; ?></span>
			</a>
		</div>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
