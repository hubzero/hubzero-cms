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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//get path info
$info = pathinfo($this->folder);
?>

<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=dorenamefolder&no_html=1'); ?>" method="post" class="hubForm">
	<fieldset>
		<legend><?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME_FOLDER'); ?></legend>
		<label>
			<?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME_CURRENT_NAME'); ?>:<br />
			<input type="hidden" name="folder" value="<?php echo $this->escape($this->folder); ?>" />

			<input type="text" name="name" value="<?php echo $this->escape($info['basename']); ?>" />
		</label>
		<p class="controls">
			<?php echo Html::input('token'); ?>
			<button type="submit" class="btn icon-edit"><?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME'); ?></button>
		</p>
	</fieldset>
</form>