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

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'editcat') ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW');
Toolbar::title(Lang::txt('COM_EVENTS_EVENT') . ': ' . $text . ' ' . Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'), 'event.png');
Toolbar::spacer();
Toolbar::save();
//Toolbar::spacer();
//Toolbar::media_manager();
Toolbar::cancel();

if ($this->row->image == '')
{
	$this->row->image = 'blank.png';
}
?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton, section)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	if (document.adminForm.name.value == ''){
		alert("<?php echo Lang::txt('Category must have a name'); ?>");
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_EVENTS_DETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-title"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_TITLE'); ?>:</td>
			<input type="text" name="category[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" maxlength="50" />
		</div>
		<div class="input-wrap">
			<label for="field-alias"><?php echo Lang::txt('COM_EVENTS_CATEGORY_ALIAS'); ?>:</label>
			<input type="text" name="category[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" maxlength="255" />
		</div>
		<div class="input-wrap">
			<label><?php echo Lang::txt('COM_EVENTS_CAL_LANG_CATEGORY_ORDERING'); ?>:</td>
			<?php echo $this->orderlist; ?>
		</div>
		<div class="input-wrap">
			<label for="field-description"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:</label>
			<?php echo $this->editor('category[description]', $this->escape($this->row->description), '', '', 50, 15, false, 'field-description', null, null, array('class' => 'minimal no-footer')); ?>
		</div>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<input type="hidden" name="category[extension]" value="com_events" />
	<input type="hidden" name="category[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="category[access]" value="<?php echo $this->row->access; ?>" />

	<?php echo Html::input('token'); ?>
</form>
