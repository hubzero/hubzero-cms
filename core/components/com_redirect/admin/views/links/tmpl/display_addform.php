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
?>

	<fieldset class="batch">
		<legend><?php echo Lang::txt('COM_REDIRECT_HEADING_UPDATE_LINKS'); ?></legend>

		<div class="grid">
			<div class="col span8">
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>">
					<label for="new_url"><?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?></label>
					<input type="text" name="new_url" id="new_url" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC'); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>">
					<label for="comment"><?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?></label>
					<input type="text" name="comment" id="comment" value="" size="50" title="<?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC'); ?>" />
				</div>
			</div>
			<div class="col span4">
				<div class="input-wrap">
					<button type="button" onclick="this.form.task.value='activate';this.form.submit();"><?php echo Lang::txt('COM_REDIRECT_BUTTON_UPDATE_LINKS'); ?></button>
				</div>
			</div>
		</div>
	</fieldset>
