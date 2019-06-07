<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = Components\Developer\Helpers\Permissions::getActions('application');

// Title & toolbar
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_DEVELOPER') . ': ' . Lang::txt('COM_DEVELOPER_APPLICATIONS') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<?php if ($this->getErrors()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_DEVELOPER_FIELD_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[name]" id="field-name" maxlength="250" class="required" value="<?php echo $this->escape(stripslashes($this->row->get('name'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_DEVELOPER_FIELD_DESCRIPTION'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<textarea name="fields[description]" id="field-description" class="required" rows="10"><?php echo $this->escape($this->row->get('description')); ?></textarea>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_DEVELOPER_FIELD_REDIRECT_URI_HINT'); ?>">
					<label for="field-redirect_uri"><?php echo Lang::txt('COM_DEVELOPER_FIELD_REDIRECT_URI'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php
						$uris = explode(' ', $this->row->get('redirect_uri'));
						$uris = implode(PHP_EOL, $uris);
					?>
					<textarea name="fields[redirect_uri]" id="field-redirect_uri" class="required" rows="3"><?php echo $this->escape($uris); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<?php if ($this->row->get('id')) : ?>
				<table class="meta">
					<tbody>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_DEVELOPER_FIELD_CREATED'); ?>:</th>
							<td>
								<?php echo $this->escape(stripslashes($this->row->creator->get('name', 'System User'))); ?>
								<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_DEVELOPER_FIELD_CREATED_BY'); ?>:</th>
							<td>
								<?php echo Date::of($this->row->get('created'))->toLocal(); ?>
								<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_DEVELOPER_FIELD_CLIENT_ID'); ?>:</th>
							<td>
								<?php echo $this->escape(stripslashes($this->row->get('client_id'))); ?>
								<input type="hidden" name="fields[client_id]" id="field-client_id" value="<?php echo $this->escape($this->row->get('client_id')); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_DEVELOPER_FIELD_CLIENT_SECRET'); ?>:</th>
							<td>
								<?php echo $this->escape(stripslashes($this->row->get('client_secret'))); ?>
								<input type="hidden" name="fields[client_secret]" id="field-client_secret" value="<?php echo $this->escape($this->row->get('client_secret')); ?>" />
							</td>
						</tr>
					</tbody>
				</table>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

					<div class="input-wrap">
						<label for="field-state"><?php echo Lang::txt('COM_DEVELOPER_FIELD_STATE'); ?>:</label><br />
						<select name="fields[state]" id="field-state">
							<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
							<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
							<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
						</select>
					</div>
				</fieldset>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_DEVELOPER_FIELDSET_TEAM'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_DEVELOPER_FIELD_ADD_TEAM_HINT'); ?>">
						<label for="acmembers"><?php echo Lang::txt('COM_DEVELOPER_FIELD_ADD_TEAM'); ?>:</label><br />
						<?php
						// get team and format for autocompletor
						$currentTeam = array();
						foreach ($this->row->team() as $member)
						{
							$profile = \Hubzero\User\User::oneOrNew($member->get('uidNumber'));

							$currentTeam[] = $profile->get('name') . ' (' . $profile->get('id') . ')';
						}

						// output member autocompletor
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'team', 'acmembers', '', implode(', ', $currentTeam))));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
							<input type="text" name="team" id="acmembers" value="" size="35" />
						<?php } ?>
					</div>
				</fieldset>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>