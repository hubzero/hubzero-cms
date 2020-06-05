<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	$this->title .= ($this->reported->anon) ? '(' . Lang::txt('JANONYMOUS') . ')':'';

	$link = str_replace('/administrator', '', $this->reported->href);
}

Html::behavior('modal', 'a.modals');

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
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
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_COL_DATE'); ?></th>
						<td><?php echo $this->report->created; ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_REPORT_REPORTED_BY'); ?></th>
						<td><?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : Lang::txt('COM_SUPPORT_UNKNOWN'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_COL_REASON'); ?></th>
						<td><?php echo $this->escape(stripslashes($this->report->report ? $this->report->report : $this->report->subject)); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col span5">
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
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->report->id; ?>" />
	<input type="hidden" name="parentid" value="<?php echo $this->parentid; ?>" />

	<?php echo Html::input('token'); ?>
</form>
