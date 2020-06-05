<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->tmpl)
{
	Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . Lang::txt('Batch Process'), 'support');
	Toolbar::save('process');
	Toolbar::cancel();
	Toolbar::spacer();
	Toolbar::help('ticket');
}

Html::behavior('tooltip');
$this->css()
	->js('tickets.js');

$cc = array();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="<?php echo ($this->tmpl == 'component') ? 'component-form' : 'item-form'; ?>" enctype="multipart/form-data">
	<?php if ($this->tmpl == 'component') { ?>
		<fieldset>
			<div class="configuration">
				<div class="configuration-options">
					<button type="button" id="btn-save" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('Save'); ?></button>
					<button type="button" id="btn-cancel"><?php echo Lang::txt('Cancel'); ?></button>
				</div>
				<?php echo Lang::txt('Batch Process'); ?>
			</div>
		</fieldset>
		<input type="hidden" name="no_html" value="1" />
	<?php } ?>
	<div class="width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="actags"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_TAGS'); ?></label>
				<?php
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', '')));
				if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="actags" value="" />
				<?php } ?>
			</div>

			<div class="grid">
				<div class="col span6">
					<div class="input-wrap">
						<label for="acgroup"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:</label>
						<?php
						$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'fields[group]', 'acgroup','','','','owner')));
						if (count($gc) > 0) {
							echo $gc[0];
						} else { ?>
						<input type="text" name="group" value="" id="acgroup" value="" size="30" autocomplete="off" />
						<?php } ?>
					</div>
				</div>
				<div class="col span6">
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER'); ?></label>
						<?php echo $this->lists['owner']; ?>
					</div>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<div class="input-wrap">
						<label for="field-severity"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEVERITY'); ?></label>
						<select name="fields[severity]" id="field-severity">
							<option value=""><?php echo Lang::txt('Select...'); ?></option>
							<option value="critical"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_CRITICAL'); ?></option>
							<option value="major"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_MAJOR'); ?></option>
							<option value="normal"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_NORMAL'); ?></option>
							<option value="minor"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_MINOR'); ?></option>
							<option value="trivial"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_TRIVIAL'); ?></option>
						</select>
					</div>
				</div>
				<div class="col span6">
					<div class="input-wrap">
						<label for="field-status"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?></label>
						<select name="fields[status]" id="field-status">
							<option value=""><?php echo Lang::txt('Select...'); ?></option>
							<?php $row = new \Components\Support\Models\Ticket(); ?>
							<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
								<?php foreach ($row->statuses('open') as $status) { ?>
									<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
								<?php } ?>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
								<option value="0"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
								<?php foreach ($row->statuses('closed') as $status) { ?>
									<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
								<?php } ?>
							</optgroup>
						</select>
					</div>
				</div>
			</div>

			<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
				<div class="input-wrap">
					<label for="field-category">
						<?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
						<select name="fields[category]" id="field-category">
							<option value=""><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
							<?php
							foreach ($this->lists['categories'] as $category)
							{
								?>
							<option value="<?php echo $this->escape($category->alias); ?>"><?php echo $this->escape(stripslashes($category->title)); ?></option>
								<?php
							}
							?>
						</select>
					</label>
				</div>
			<?php } ?>

			<?php /*
			<div class="input-wrap">
				<label for="comment-field-access" class="private hasTip" title="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?>">
						<input type="checkbox" name="access" id="comment-field-access" value="1" />
						<span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS'); ?></span>
					</label>
				<label for="comment-field-content">
					<span class="label"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS'); ?></span>
					<textarea name="comment" id="comment-field-comment" cols="75" rows="5"></textarea>
				</label>
			</div>

			<div class="input-wrap">
				<label for="comment-field-message">
					<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?> <?php
					$mc = Event::trigger('hubzero.onGetMultiEntry', array(
						array(
							'members',   // The component to call
							'cc',        // Name of the input field
							'comment-field-message', // ID of the input field
							'',          // CSS class(es) for the input field
							implode(', ', $cc) // The value of the input field
						)
					));
					if (count($mc) > 0) {
						echo '<span class="hint">' . Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
					} else { ?> <span class="hint"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
						<input type="text" name="cc" id="comment-field-message" value="<?php echo implode(', ', $cc); ?>" />
					<?php } ?>
				</label>
			</div>
			<div class="grid">
			<div class="col span6">
				<div class="input-wrap">
					<label for="email_submitter">
						<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
						<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
					</label>
				</div>
			</div>
			<div class="col span6">
				<div class="input-wrap">
					<label for="email_owner">
						<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
						<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER'); ?>
					</label>
				</div>
			</div>
			</div>*/ ?>
		</fieldset>
	</div>

	<?php foreach ($this->ids as $id) { ?>
		<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
	<?php } ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process" />

	<?php echo Html::input('token'); ?>
</form>