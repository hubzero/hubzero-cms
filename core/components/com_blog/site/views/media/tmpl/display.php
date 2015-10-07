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

$base = rtrim(Request::base(true), '/');

$this->css();
?>
<div id="attachments">
	<form action="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=<?php echo $this->controller; ?>&amp;task=upload" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div id="themanager" class="manager">
				<iframe src="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=<?php echo $this->controller; ?>&amp;task=list&amp;scope=<?php echo urlencode($this->archive->get('scope')); ?>&amp;id=<?php echo $this->archive->get('scope_id'); ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
			</div>
		</fieldset>
		<fieldset>
			<p><input type="file" name="upload" id="upload" /></p>
			<p><input type="submit" value="<?php echo Lang::txt('COM_BLOG_UPLOAD'); ?>" /></p>

			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="scope" value="<?php echo $this->escape($this->archive->get('scope')); ?>" />
			<input type="hidden" name="id" value="<?php echo $this->escape($this->archive->get('scope_id')); ?>" />
			<input type="hidden" name="tmpl" value="component" />

			<?php echo Html::input('token'); ?>
		</fieldset>
	</form>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>
