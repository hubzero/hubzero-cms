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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css($this->controller);
?>

<div class="com_time_permissions_container">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="POST">
		<div class="permissions-box">
			<fieldset class="permissions">
				<?php echo $this->permissions->label; ?>
				<?php echo $this->permissions->input; ?>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="scope" value="<?php echo $this->scope; ?>" />
		<input type="hidden" name="scope_id" value="<?php echo $this->scope_id; ?>" />
		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_TIME_PERMISSIONS_SAVE'); ?>" />
			<button type="button" class="btn btn-secondary cancel"><?php echo Lang::txt('COM_TIME_PERMISSIONS_CANCEL'); ?></button>
		</p>
	</form>
</div>