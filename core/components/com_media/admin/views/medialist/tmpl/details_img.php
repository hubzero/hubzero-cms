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

$this->_tmp_img->name = ltrim($this->_tmp_img->name, DS);
$this->_tmp_img->title = ltrim($this->_tmp_img->title, DS);

Event::trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>
		<tr>
			<td>
				<a class="img-preview" href="<?php echo COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>">
					<?php echo Html::asset('image', COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative, Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->_tmp_img->title, MediaHelper::parseSize($this->_tmp_img->size)), array('width' => $this->_tmp_img->width_16, 'height' => $this->_tmp_img->height_16)); ?>
				</a>
			</td>
			<td class="description">
				<a href="<?php echo  COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative; ?>" title="<?php echo $this->_tmp_img->name; ?>" rel="preview"><?php echo $this->escape($this->_tmp_img->title); ?></a>
				<br /><span><?php echo str_replace(rtrim(Request::root(), '/'), '', COM_MEDIA_BASEURL.'/'.$this->_tmp_img->path_relative); ?></span>
			</td>
			<td>
				<?php echo Lang::txt('COM_MEDIA_IMAGE_DIMENSIONS', $this->_tmp_img->width, $this->_tmp_img->height); ?>
			</td>
			<td class="filesize">
				<?php echo MediaHelper::parseSize($this->_tmp_img->size); ?>
			</td>
		<?php if (User::authorise('core.delete', 'com_media')):?>
			<td>
				<input type="checkbox" name="rm[]" value="<?php echo $this->_tmp_img->name; ?>" />
			</td>
		<?php endif;?>
		</tr>
<?php
Event::trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
