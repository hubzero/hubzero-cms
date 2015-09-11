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

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_REPORTS'), 'support.png');
Toolbar::save();
//Toolbar::cancel();

$reporter = User::getInstance($this->report->created_by);

$link = '';

if (is_object($this->reported))
{
	$author = User::getInstance($this->reported->author);

	if (is_object($author) && $author->get('username'))
	{
		$this->title .= $author->get('username');
	}
	else
	{
		$this->title .= Lang::txt('COM_SUPPORT_UNKNOWN');
	}
	$this->title .= ($this->reported->anon) ? '(' . Lang::txt('COM_SUPPORT_ANONYMOUS') . ')':'';

	$link = str_replace('/administrator', '', $this->reported->href);
}

Html::behavior('modal', 'a.modals');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_SUPPORT_REPORT_ITEM_REPORTED_AS_ABUSIVE'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<h4><?php echo '<a class="modals" href="' . $link . '">'.$this->escape($this->title) . '</a>: '; ?></h4>
							<p><?php echo (is_object($this->reported)) ? stripslashes($this->reported->text) : ''; ?></p>
							<?php if (is_object($this->reported) && isset($this->reported->subject) && $this->reported->subject!='') {
								echo '<p>' . $this->escape(stripslashes($this->reported->subject)) . '</p>';
							} ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>

		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_SUPPORT_COL_DATE'); ?></th>
					<td><?php echo $this->report->created; ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_SUPPORT_REPORT_REPORTED_BY'); ?></th>
					<td><?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : Lang::txt('COM_SUPPORT_UNKNOWN'); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_SUPPORT_COL_REASON'); ?></th>
					<td><?php echo $this->escape(stripslashes($this->report->report ? $this->report->report : $this->report->subject)); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_SUPPORT_REPORT_TAKE_ACTION'); ?></span></legend>

			<?php if ($this->report->state == 0) { ?>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_REPORT_RELEASE_ITEM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-release" value="release" />
					<label for="field-task-release"><?php echo Lang::txt('COM_SUPPORT_REPORT_RELEASE_ITEM'); ?></label>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_REPORT_MARK_AS_SPAM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-spam" value="spam" />
					<label for="field-task-spam"><?php echo Lang::txt('COM_SUPPORT_REPORT_MARK_AS_SPAM'); ?></label>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_REPORT_DELETE_ITEM_HINT'); ?>">
					<input type="radio" name="task" id="field-task-remove" value="remove" />
					<label for="field-task-remove"><?php echo Lang::txt('COM_SUPPORT_REPORT_DELETE_ITEM'); ?></label>
					<span class="hint"><?php echo Lang::txt('COM_SUPPORT_REPORT_DELETE_ITEM_HINT'); ?></span><br />
					<textarea name="note" id="note" rows="5" cols="25"></textarea>
				</div>

				<div class="input-wrap">
					<input type="radio" name="task" value="cancel" id="field-task-cancel" checked="checked" />
					<label for="field-task-cancel"><?php echo Lang::txt('COM_SUPPORT_REPORT_DECIDE_LATER'); ?></label>
				</div>
			<?php } else { ?>
				<p class="warning"><?php echo Lang::txt('COM_SUPPORT_REPORT_ACTION_TAKEN'); ?></p>
				<input type="hidden" name="task" value="view" />
			<?php } ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->report->id; ?>" />
	<input type="hidden" name="parentid" value="<?php echo $this->parentid; ?>" />

	<?php echo Html::input('token'); ?>
</form>
