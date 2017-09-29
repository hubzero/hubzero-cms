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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Jobs\Helpers\Permissions::getActions('category');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_JOBS') . ': ' . Lang::txt('COM_JOBS_CATEGORIES') . ': ' . $text, 'category');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('item-form');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if (form.category.value == '') {
		alert('<?php echo Lang::txt('COM_JOBS_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="item-form" name="adminForm">
	<?php if ($this->task == 'edit') { ?>
	<p class="warning">
		<?php echo Lang::txt('COM_JOBS_WARNING_EDIT_CATEGORY'); ?>
	</p>
	<?php } ?>
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="category"><?php echo Lang::txt('COM_JOBS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="category" id="category" size="30" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
		</div>
		<div class="input-wrap">
			<label for="description"><?php echo Lang::txt('COM_JOBS_FIELD_DESCRIPTION'); ?>: </label>
			<input type="text" name="description" id="description"  maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
		</div>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>