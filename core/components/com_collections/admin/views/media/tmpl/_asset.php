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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		<a class="icon-delete delete" data-id="<?php echo $this->asset->get('id'); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&asset=' . $this->asset->get('id') . '&no_html=' . $this->no_html . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
			<?php echo Lang::txt('JACTION_DELETE'); ?>
		</a>
	</span>
</p>