<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . $text, 'support');
Toolbar::save();
//Toolbar::apply();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('ticket');

Html::behavior('tooltip');
$this->css();

$browser = new \Hubzero\Browser\Detector();

$cc = array();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="ticket[summary]" id="field-summary" value="<?php echo $this->escape($this->row->get('summary')); ?>" size="50" />

				<div class="input-wrap">
					<label for="field-login"><?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_LOGIN'); ?>:</label>
					<input type="text" name="ticket[login]" id="field-login" value="<?php echo $this->escape(trim($this->row->get('login'))); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_NAME'); ?>:</label>
					<input type="text" name="ticket[name]" id="field-name" value="<?php echo $this->escape(trim($this->row->get('name'))); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="field-email"><?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_EMAIL'); ?>:</label>
					<input type="email" name="ticket[email]" id="field-email" value="<?php echo $this->escape($this->row->get('email')); ?>" size="50" />
				</div>
				<div class="input-wrap">
					<label for="field-report"><?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_DESCRIPTION'); ?>:</label>
					<textarea name="ticket[report]" id="field-report" cols="75" rows="15"><?php echo $this->escape(trim($this->row->get('report') == null ? '' : $this->row->get('report'))); ?></textarea>
				</div>
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
							<label for="acgroup"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:</label></td>
							<?php
							$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'ticket[group_id]', 'acgroup','','','','owner')));
							if (count($gc) > 0) {
								echo $gc[0];
							} else { ?>
							<input type="text" name="ticket[group_id]" value="" id="acgroup" value="" size="30" autocomplete="off" />
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
							<select name="ticket[severity]" id="field-severity">
								<?php foreach (\Components\Support\Helpers\Utilities::getSeverities() as $severity) { ?>
									<option value="<?php echo $severity; ?>"<?php if ($severity == 'normal') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-status"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?></label>
							<select name="ticket[status]" id="field-status">
								<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
									<option value="0" selected="selected"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_NEW'); ?></option>
									<?php foreach (\Components\Support\Models\Status::allOpen()->rows() as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
								<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
									<option value="0"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
									<?php foreach (\Components\Support\Models\Status::allClosed()->rows() as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
							</select>
						</div>
					</div>
				</div>

				<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
					<div class="input-wrap">
						<label for="ticket-field-category">
							<?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
							<select name="ticket[category]" id="ticket-field-category">
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

				<div class="input-wrap">
					<label for="field-message"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?></label>
					<?php
					$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', implode(', ', $cc))));
					if (count($mc) > 0) {
						echo $mc[0];
					} else { ?>
					<input type="text" name="cc" id="acmembers" value="<?php echo implode(', ', $cc); ?>" size="35" />
					<?php } ?>
				</div>
				<input type="hidden" name="ticket[section]" value="1" />
			</fieldset>
		</div>
		<div class="col span4">
			<p><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?></p>
		</div>
	</div>

	<input type="hidden" name="ticket[referrer]" value="<?php echo Request::getString('HTTP_REFERER', null, 'server'); ?>" />
	<input type="hidden" name="ticket[os]" value="<?php echo $browser->platform(); ?>" />
	<input type="hidden" name="osver" value="<?php echo $browser->platformVersion(); ?>" />
	<input type="hidden" name="ticket[browser]" value="<?php echo $browser->name(); ?>" />
	<input type="hidden" name="browserver" value="<?php echo $browser->version(); ?>" />
	<input type="hidden" name="ticket[hostname]" value="<?php echo gethostbyaddr(Request::getString('REMOTE_ADDR', '', 'server')); ?>" />
	<input type="hidden" name="ticket[uas]" value="<?php echo Request::getString('HTTP_USER_AGENT', '', 'server'); ?>" />
	<input type="hidden" name="ticket[open]" value="1" />

	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="username" value="<?php echo $this->escape(User::get('username')); ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
