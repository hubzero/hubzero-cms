<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');

$text = ($this->row->id ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW'));

Toolbar::title(Lang::txt('COM_WISHLIST') . ': ' . Lang::txt('COM_WISHLIST_WISH') . ': ' . $text, 'wishlist');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('wish');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$data = array();
$i = 0;
if ($this->ownerassignees)
{
	foreach ($this->ownerassignees as $k => $items)
	{
		foreach ($items as $v)
		{
			$data[] = array($k, $v->id, $v->name);
		}
	}
}
?>
<script type="application/json" id="owner-data">
	{
		"data": <?php echo json_encode($data); ?>
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WISHLIST_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-wishlist"><?php echo Lang::txt('COM_WISHLIST_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[wishlist]" id="field-wishlist" class="required">
						<option value="0"><?php echo Lang::txt('COM_WISHLIST_NONE'); ?></option>
						<?php if ($this->lists) { ?>
							<?php foreach ($this->lists as $list) { ?>
								<option value="<?php echo $list->id; ?>"<?php echo ($this->row->get('wishlist') == $list->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($list->get('title'))); ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-subject"><?php echo Lang::txt('COM_WISHLIST_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[subject]" id="field-subject" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->get('subject'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-about"><?php echo Lang::txt('COM_WISHLIST_DESCRIPTION'); ?>:</label><br />
					<?php echo $this->editor('fields[about]', $this->escape(preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', stripslashes($this->row->get('about')))), 50, 30, 'field-about', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				</div>

				<div class="input-wrap">
					<label for="field-tags"><?php echo Lang::txt('COM_WISHLIST_TAGS'); ?>:</label><br />
					<input type="text" name="fields[tags]" id="field-tags" maxlength="150" value="<?php echo $this->escape(stripslashes($this->row->tags('string'))); ?>" />
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WISHLIST_PLAN'); ?></span></legend>

				<?php if ($this->row->plan->get('id')) { ?>
					<div class="input-wrap">
						<input type="checkbox" class="option" name="plan[create_revision]" id="plan-create_revision" value="1" />
						<label for="plan-create_revision"><?php echo Lang::txt('COM_WISHLIST_PLAN_NEW_REVISION'); ?></label>
					</div>
				<?php } ?>

				<fieldset>
					<legend><?php echo Lang::txt('COM_WISHLIST_DUE'); ?>:</legend>

					<div class="input-wrap">
						<input class="option" type="radio" name="fields[due]" id="field-due-never" value="0" <?php echo (!$this->row->get('due') || $this->row->get('due') == '0000-00-00 00:00:00') ? 'checked="checked"' : ''; ?> />
						<label for="field-due-never"><?php echo Lang::txt('COM_WISHLIST_DUE_NEVER'); ?></label>
						<br />
						<strong><?php echo Lang::txt('COM_WISHLIST_OR'); ?></strong>
						<br />
						<input class="option" type="radio" name="fields[due]" id="field-due-on" value="0" <?php echo ($this->row->get('due') && $this->row->get('due') != '0000-00-00 00:00:00') ? 'checked="checked"' : ''; ?> />
						<label for="field-due-on"><?php echo Lang::txt('COM_WISHLIST_DUE_ON'); ?></label>

						<input class="option" type="text" name="fields[due]" id="field-due" size="10" maxlength="19" value="<?php echo $this->escape($this->row->get('due')); ?>" />
					</div>
				</fieldset>

				<div class="input-wrap">
					<label for="fieldassigned"><?php echo Lang::txt('COM_WISHLIST_ASSIGNED'); ?>:</label>
					<select name="fields[assigned]" id="fieldassigned">
						<option value="0"><?php echo Lang::txt('COM_WISHLIST_UNASSIGNED'); ?></option>
						<?php if ($this->assignees) { ?>
							<?php foreach ($this->assignees as $assignee) { ?>
								<option value="<?php echo $assignee->id; ?>"<?php echo ($this->row->get('assigned') == $assignee->id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape(stripslashes($assignee->name)); ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="plan-pagetext"><?php echo Lang::txt('COM_WISHLIST_PAGETEXT'); ?>:</label>
					<?php echo $this->editor('plan[pagetext]', $this->escape(preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', stripslashes($this->row->plan->pagetext))), 50, 30, 'plan-pagetext', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				</div>

				<input type="hidden" name="plan[id]" id="plan-id" value="<?php echo $this->row->plan->id; ?>" />
				<input type="hidden" name="plan[wishid]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="plan[version]" value="<?php echo $this->row->plan->version; ?>" />
				<input type="hidden" name="plan[approved]" value="<?php echo $this->row->plan->approved; ?>" />
				<?php if (!$this->row->plan->get('id')) { ?>
					<input type="hidden" name="plan[create_revision]" id="plan-create_revision" value="0" />
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_WISHLIST_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATED'); ?>:</th>
						<td>
							<time datetime="<?php echo $this->row->get('proposed'); ?>"><?php echo Date::of($this->row->get('proposed'))->toLocal(); ?></time>
							<input type="hidden" name="fields[proposed]" id="field-proposed" value="<?php echo $this->row->get('proposed'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_WISHLIST_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor = User::getInstance($this->row->get('proposed_by'));
							echo $this->escape($editor->get('name', Lang::txt('COM_WISHLIST_UNKNOWN')));
							?>
							<input type="hidden" name="fields[proposed_by]" id="field-proposed_by" value="<?php echo $this->row->get('proposed_by'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_WISHLIST_FIELD_RANKING'); ?>:</th>
						<td>
							<?php echo $this->row->get('ranking'); ?>
							<input type="hidden" name="fields[ranking]" id="field-ranking" value="<?php echo $this->row->get('ranking'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WISHLIST_PARAMETERS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" <?php echo $this->row->get('anonymous') ? 'checked="checked"' : ''; ?> />
					<label for="field-anonymous"><?php echo Lang::txt('JANONYMOUS'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="checkbox" name="fields[private]" id="field-private" value="1" <?php echo $this->row->get('private') ? 'checked="checked"' : ''; ?> />
					<label for="field-private"><?php echo Lang::txt('COM_WISHLIST_PRIVATE'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="checkbox" name="fields[accepted]" id="field-accepted" value="1" <?php echo $this->row->get('accepted') ? 'checked="checked"' : ''; ?> />
					<label for="field-accepted"><?php echo Lang::txt('COM_WISHLIST_ACCEPTED'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="field-points"><?php echo Lang::txt('COM_WISHLIST_POINTS'); ?></label>
					<input type="text" name="fields[points]" id="field-points" value="<?php echo $this->escape($this->row->get('points')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-status"><?php echo Lang::txt('COM_WISHLIST_STATUS'); ?></label>
					<select name="fields[status]" id="field-status">
						<option value="0"<?php echo ($this->row->get('status') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATUS_PENDING'); ?></option>
						<option value="1"<?php echo ($this->row->get('status') == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATUS_GRANTED'); ?></option>
						<option value="2"<?php echo ($this->row->get('status') == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATUS_DELETED'); ?></option>
						<option value="3"<?php echo ($this->row->get('status') == 3) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATUS_REJECTED'); ?></option>
						<option value="4"<?php echo ($this->row->get('status') == 4) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WISHLIST_STATUS_WITHDRAWN'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php /*
		<?php if ($canDo->get('core.admin')): ?>
			<div class="col span12">
				<fieldset class="panelform">
					<legend><span><?php echo Lang::txt('COM_WISHLIST_FIELDSET_RULES'); ?></span></legend>
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			</div>
			<div class="clr"></div>
		<?php endif; ?>
	*/ ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="wishlist" value="<?php echo $this->wishlist; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
