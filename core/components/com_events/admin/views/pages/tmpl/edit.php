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

$text = ($this->task == 'edit' ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW'));

Toolbar::title(Lang::txt('COM_EVENTS_PAGE' ) . ': ' . $text, 'event.png');
Toolbar::save();
Toolbar::cancel();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('text'); ?>
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_EVENTS_PAGE'); ?></span></legend>

			<div class="input-wrap">
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $this->event->id); ?>">
					<?php echo $this->escape(stripslashes($this->event->title)); ?>
				</a>
			</div>

			<div class="input-wrap">
				<label for="title"><?php echo Lang::txt('COM_EVENTS_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="title" id="title" value="<?php echo $this->escape(stripslashes($this->page->title)); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_EVENTS_ALIAS_HINT'); ?>">
				<label for="alias"><?php echo Lang::txt('COM_EVENTS_ALIAS'); ?>:</label>
				<input type="text" name="alias" id="alias" value="<?php echo $this->escape(stripslashes($this->page->alias)); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_EVENTS_ALIAS_HINT'); ?></span>
			</div>

			<div class="input-wrap">
				<label for="pagetext"><?php echo Lang::txt('COM_EVENTS_PAGE_TEXT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('pagetext', $this->escape(stripslashes($this->page->pagetext)), 40, 20); ?>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_PAGE_ORDERING'); ?></th>
					<td><?php echo $this->page->ordering; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_PAGE_CREATED'); ?></th>
					<td><?php echo $this->page->created; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_PAGE_CREATED_BY'); ?></th>
					<td><?php echo $this->page->created_by; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED'); ?></th>
					<td><?php echo $this->page->modified; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_PAGE_LAST_MODIFIED_BY'); ?></th>
					<td><?php echo $this->page->modified_by; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="event" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->page->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
