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

$this->css('media.css')
     ->js('jquery.fileuploader.js', 'system')
     ->js('courses.fileupload.js');

$base = rtrim(Request::base(true), '/');
?>
<div id="file_browser">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div id="themanager" class="manager">
				<iframe src="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;task=listfiles&amp;listdir=<?php echo $this->listdir; ?>" name="imgManager" id="imgManager" width="99%" height="180"></iframe>
			</div>
		</fieldset>
		<fieldset>
			<div id="ajax-uploader" data-action="<?php echo $base; ?>/index.php?option=com_courses&amp;task=ajaxupload&amp;listdir=<?php echo $this->listdir; ?>&amp;no_html=1">
				<noscript>
					<p><input type="file" name="upload" id="upload" /></p>
					<p><input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" /></p>
				</noscript>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="no_html" value="1" />
		</fieldset>
	</form>
</div>
