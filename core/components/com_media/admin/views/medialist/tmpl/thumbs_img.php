<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$params = new \Hubzero\Config\Registry;
use Components\Media\Admin\Helpers\MediaHelper; // Move to controller
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
