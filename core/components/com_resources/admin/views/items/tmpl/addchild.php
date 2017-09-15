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

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_ADD_CHILD'), 'resources');
Toolbar::cancel();

Request::setVar('hidemainmenu', 1);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<h3><?php echo stripslashes($this->parent->title); ?></h3>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CHOOSE'); ?></span></legend>

		<?php if ($this->getError()) { echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>'; } ?>

		<div class="grid">
			<div class="col span6">
				<div class="input-wrap">
					<input type="radio" name="method" id="child_create" value="create" checked="checked" />
					<label for="child_create"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CREATE'); ?></label>
				</div>
			</div>
			<div class="col span6">
				<div class="input-wrap">
					<input type="radio" name="method" id="child_existing" value="existing" />
					<label for="child_existing"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_EXISTING'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="childid"><?php echo Lang::txt('COM_RESOURCES_FIELD_RESOURCE_ID'); ?>:</label>
					<input type="text" name="childid" id="childid" value="" />
				</div>
			</div>
		</div>

		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

		<?php echo Html::input('token'); ?>
	</fieldset>

	<p class="align-center"><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_RESOURCES_NEXT'); ?>" /></p>
</form>
