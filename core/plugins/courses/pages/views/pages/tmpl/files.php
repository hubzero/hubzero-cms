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

$base = $this->offering->link() . '&active=pages';
?>
<div id="attachments">
	<form action="<?php echo Route::url($base); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div id="themanager" class="manager">
				<iframe style="border:1px solid #eee;margin-top: 0;overflow-y:auto;" src="<?php echo Route::url($base . '&action=list&tmpl=component&page=' . $this->page->get('id') . '&section_id=' . $this->page->get('section_id')); ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
			</div>
		</fieldset>

		<fieldset>
			<table>
				<tbody>
					<tr>
						<td><input type="file" name="upload" id="upload" /></td>
					</tr>
					<tr>
						<td><input type="submit" value="<?php echo Lang::txt('PLG_COURSES_PAGES_UPLOAD'); ?>" /></td>
					</tr>
				</tbody>
			</table>

			<?php echo Html::input('token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
			<input type="hidden" name="page" value="<?php echo $this->escape($this->page->get('id')); ?>" />
			<input type="hidden" name="section_id" value="<?php echo $this->escape($this->page->get('section_id')); ?>" />
			<input type="hidden" name="active" value="pages" />
			<input type="hidden" name="action" value="upload" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
		</fieldset>
	</form>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
</div>