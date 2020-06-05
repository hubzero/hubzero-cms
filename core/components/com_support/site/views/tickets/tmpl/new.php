<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.ui.css', 'system')
     ->js('jquery.timepicker.js', 'system')
     ->js('new.js');

// are we remotely loading ticket form
$tmpl = (Request::getString('tmpl', '')) ? '&tmpl=component' : '';
$no_html = Request::getInt('no_html');

// are we trying to assign a group
$group = Request::getString('group', '');

// Populate the row for users that are not logged and have issues with the page
if (User::isGuest()):
	$this->row->submitter->set('username', Request::getString('reporter[login]', null, 'post'));
	$this->row->set('report', Request::getString('problem[long]', null, 'post'));
	$this->row->set('name', Request::getString('reporter[name]', null, 'post'));
	$this->row->set('email', Request::getString('reporter[email]', null, 'post'));
endif;

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<p class="info"><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>

	<?php if ($this->getError()): ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php endif ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new' . $tmpl); ?>" id="hubForm" method="post" enctype="multipart/form-data" <?php echo ($no_html) ? ' class="full"' : ''; ?>>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="verified" value="<?php echo $this->escape($this->row->get('verified')); ?>" />

		<input type="hidden" name="problem[referer]" value="<?php echo $this->escape($this->row->get('referrer')); ?>" />
		<input type="hidden" name="problem[tool]" value="<?php echo $this->escape($this->row->get('tool')); ?>" />
		<input type="hidden" name="problem[short]" value="<?php echo $this->escape($this->row->get('short')); ?>" />

		<input type="hidden" name="no_html" value="<?php echo $no_html; ?>" />
		<?php if ($this->row->get('verified')): ?>
			<input type="hidden" name="botcheck" value="" />
		<?php endif; ?>

	<?php if (User::isGuest()): ?>
		<?php if (!$tmpl && !$no_html): ?>
		<div class="explaination">
			<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_OTHER_OPTIONS'); ?></p>
		</div>
		<?php endif ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_SUPPORT_TROUBLE_USER_INFORMATION'); ?></legend>

			<div class="form-group">
				<label for="reporter_login">
					<?php echo Lang::txt('COM_SUPPORT_USERNAME'); ?>
					<input type="text" name="reporter[login]" class="form-control" value="<?php echo $this->escape($this->row->get('login', $this->row->submitter->get('username'))); ?>" id="reporter_login" />
				</label>
			</div>

			<div class="form-group">
				<label for="reporter_name"<?php echo ($this->getError() && !$this->row->get('name')) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo Lang::txt('COM_SUPPORT_NAME'); ?> <span class="required"><?php echo Lang::txt('COM_SUPPORT_REQUIRED'); ?></span>
					<input type="text" name="reporter[name]" class="form-control" value="<?php echo $this->escape($this->row->get('name', $this->row->submitter->get('name'))); ?>" id="reporter_name" />
				</label>
				<?php if ($this->getError() && !$this->row->get('name')): ?>
					<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_NAME'); ?></p>
				<?php endif; ?>
			</div>

			<div class="form-group">
				<label for="reporter_email"<?php echo ($this->getError() && !$this->row->get('email')) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo Lang::txt('COM_SUPPORT_EMAIL'); ?> <span class="required"><?php echo Lang::txt('COM_SUPPORT_REQUIRED'); ?></span>
					<input type="email" name="reporter[email]" class="form-control" value="<?php echo $this->escape($this->row->get('email', $this->row->submitter->get('email'))); ?>" id="reporter_email" />
				</label>
				<?php if ($this->getError() && !$this->row->get('email')): ?>
					<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_EMAIL'); ?></p>
				<?php endif; ?>
			</div>

			<input type="hidden" name="reporter[org]" value="<?php echo $this->escape($this->row->get('organization', $this->row->submitter->get('organization'))); ?>" id="reporter_org" />
		</fieldset><div class="clear"></div>
	<?php else: ?>
		<input type="hidden" name="reporter[login]" value="<?php echo $this->escape($this->row->get('login', $this->row->submitter->get('username'))); ?>" id="reporter_login" />
		<input type="hidden" name="reporter[name]" value="<?php echo $this->escape($this->row->get('name', $this->row->submitter->get('name'))); ?>" id="reporter_name" />
		<input type="hidden" name="reporter[email]" value="<?php echo $this->escape($this->row->get('email', $this->row->submitter->get('email'))); ?>" id="reporter_email" />
	<?php endif; ?>

		<fieldset>
			<legend><?php echo Lang::txt('COM_SUPPORT_TROUBLE_YOUR_PROBLEM'); ?></legend>

			<div class="form-group">
				<label for="problem_long"<?php echo ($this->getError() && !$this->row->get('report')) ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo Lang::txt('COM_SUPPORT_TROUBLE_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<textarea name="problem[long]" cols="40" rows="10" class="form-control" id="problem_long"><?php echo $this->row->get('report'); ?></textarea>
				</label>
				<?php if ($this->getError() && !$this->row->get('report')): ?>
					<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_DESCRIPTION'); ?></p>
				<?php endif; ?>
			</div>

			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
				<?php
				$tmp = ('-' . time());
				$this->js('jquery.fileuploader.js', 'system');
				$jbase = rtrim(Request::base(true), '/');
				?>
				<div class="form-group">
					<div id="ajax-uploader" data-instructions="<?php echo Lang::txt('COM_SUPPORT_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=upload&amp;ticket=<?php echo $tmp; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=list&amp;ticket=<?php echo $tmp; ?>">
						<noscript>
							<div class="form-group">
								<label for="upload">
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE'); ?>:
									<input type="file" name="upload[]" id="upload" class="form-control-file" multiple="multiple" />
								</label>
							</div>

							<div class="form-group">
								<label for="field-description">
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE_DESCRIPTION'); ?>:
									<input type="text" name="description" id="field-description" class="form-control" value="" />
								</label>
							</div>
						</noscript>
					</div>
					<div class="file-list" id="ajax-uploader-list"></div>
					<input type="hidden" name="tmp_dir" id="ticket-tmp_dir" value="<?php echo $tmp; ?>" />

					<span class="hint">(.<?php echo str_replace(',', ', .', $this->file_types); ?>)</span>
				</div>
			</fieldset>
		</fieldset><div class="clear"></div>

		<?php if ($this->row->get('verified') && $this->acl->check('update', 'tickets') > 0): ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_DETAILS'); ?></legend>

				<div class="form-group">
					<label for="tags">
						<?php echo Lang::txt('COM_SUPPORT_COMMENT_TAGS'); ?>:<br />
						<?php
						$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', '')));

						if (count($tf) > 0):
							echo $tf[0];
						else: ?>
							<input type="text" name="tags" id="tags" class="form-control" value="" size="35" />
						<?php endif; ?>
					</label>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="form-group">
							<label for="acgroup">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_GROUP'); ?>:
								<?php
								$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'problem[group_id]', 'acgroup', '', $this->escape($group), '', 'ticketowner')));
								if (count($gc) > 0):
									echo $gc[0];
								else: ?>
									<input type="text" name="group_id" id="acgroup" class="form-control" value="" autocomplete="off" />
								<?php endif; ?>
							</label>
						</div>
					</div>
					<div class="col span6 omega">
						<div class="form-group">
							<label for="problemowner">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_OWNER'); ?>:
								<?php echo $this->lists['owner']; ?>
							</label>
						</div>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="form-group">
							<label for="ticket-field-severity">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEVERITY'); ?>
								<select name="problem[severity]" id="ticket-field-severity" class="form-control">
									<?php foreach ($this->lists['severities'] as $severity): ?>
										<option value="<?php echo $severity; ?>"<?php if ($severity == 'normal') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						</div>
					</div>
					<div class="col span6 omega">
						<div class="form-group">
							<label for="ticket-field-status">
								<?php
								echo Lang::txt('COM_SUPPORT_COMMENT_STATUS'); ?>:
								<select name="problem[status]" id="ticket-field-status" class="form-control">
									<optgroup label="<?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_OPEN'); ?>">
										<option value="0" selected="selected"><?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_NEW'); ?></option>
										<?php foreach (Components\Support\Models\Status::allOpen()->rows() as $status): ?>
											<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
										<?php endforeach; ?>
									</optgroup>
									<optgroup label="<?php echo Lang::txt('COM_SUPPORT_CLOSED'); ?>">
										<option value="0"><?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'); ?></option>
										<?php foreach (Components\Support\Models\Status::allClosed()->rows() as $status): ?>
											<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
										<?php endforeach; ?>
									</optgroup>
								</select>
							</label>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="field-target_date">
						<?php echo Lang::txt('COM_SUPPORT_COMMENT_TARGET_DATE'); ?>:
						<input type="text" name="problem[target_date]" class="datetime-field form-control" id="field-target_date" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::getRoot()) / 60); ?>" placeholder="YYYY-MM-DD hh:mm:ss" value="" />
					</label>
				</div>

				<?php if (isset($this->lists['categories']) && $this->lists['categories']): ?>
					<div class="form-group">
						<label for="ticket-field-category">
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_CATEGORY'); ?>
							<select name="problem[category]" id="ticket-field-category" class="form-control">
								<option value=""><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
								<?php
								foreach ($this->lists['categories'] as $category):
									?>
									<option value="<?php echo $this->escape($category->alias); ?>"><?php echo $this->escape(stripslashes($category->title)); ?></option>
									<?php
								endforeach;
								?>
							</select>
						</label>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label for="acmembers">
						<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'); ?>:
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', '')));
						if (count($mc) > 0):
							echo '<span class="hint">'.Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE').'</span>'.$mc[0];
						else: ?>
							<span class="hint"><?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
							<input type="text" name="cc" id="acmembers" class="form-control" value="" size="35" />
						<?php endif; ?>
					</label>
				</div>
			</fieldset>
		<?php else: ?>
			<?php if ($group): ?>
				<input type="hidden" name="group_id" value="<?php echo $this->escape($group); ?>" />
			<?php endif; ?>
		<?php endif; ?>

		<?php if (!$this->row->get('verified')): ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_SUPPORT_MATH_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_HUMAN_CHECK'); ?></legend>

				<label id="fbBotcheck-label" for="fbBotcheck">
					<?php echo Lang::txt('COM_SUPPORT_LEAVE_FIELD_BLANK'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<input type="text" name="botcheck" id="fbBotcheck" value="" />
				</label>
				<?php
				if (count($this->captchas) > 0):
					foreach ($this->captchas as $captcha):
						echo $captcha;
					endforeach;
				endif;
				?>
				<?php if ($this->getError() == 3): ?>
					<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_BAD_CAPTCHA_ANSWER'); ?></p>
				<?php endif; ?>
			</fieldset><div class="clear"></div>
		<?php endif; ?>

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" name="submit" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
