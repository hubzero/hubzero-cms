<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->exists())
{
	return;
}
?>
<div class="grid pictureframe js">
	<div class="col span3">
		<div id="project-image-box" class="project-image-box">
			<img id="project-image-content" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&alias=' . $this->model->get('alias') . '&media=master'); ?>" alt="" />
		</div>
		<?php if ($this->model->get('picture')) { ?>
		<p class="actionlink"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=deleteimg&alias=' . $this->model->get('alias') ); ?>" id="deleteimg">[ <?php echo Lang::txt('COM_PROJECTS_DELETE'); ?> ]</a></p>
		<?php } ?>
	</div>
	<div class="col span9 omega" id="ajax-upload" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=doajaxupload&no_html=1'); ?>">
		<h5><?php echo Lang::txt('COM_PROJECTS_UPLOAD_NEW_IMAGE'); ?> <span class="hint"><?php echo Lang::txt('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE'); ?></span></h5>
		<p id="status-box"></p>
		<label>
			<input name="upload" type="file" class="option uploader" id="uploader" />
		</label>
		<input type="button" value="<?php echo Lang::txt('COM_PROJECTS_UPLOAD'); ?>" class="btn" id="upload-file" />
	</div>
</div>