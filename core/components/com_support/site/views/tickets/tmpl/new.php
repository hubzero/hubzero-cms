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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.ui.css', 'system')
     ->js('jquery.timepicker.js', 'system')
     ->js('new.js');

$browsers = array(
	'[unspecified]' => Lang::txt('COM_SUPPORT_TROUBLE_SELECT_BROWSER'),
	'msie' => 'Internet Explorer',
	'chrome' => 'Google Chrome',
	'safari' => 'Safari',
	'firefox' => 'Firefox',
	'opera' => 'Opera',
	'mozilla' => 'Mozilla',
	'netscape' => 'Netscape',
	'camino' => 'Camino',
	'omniweb' => 'Omniweb',
	'shiira' => 'Shiira',
	'icab' => 'iCab',
	'flock' => 'Flock',
	'avant' => 'Avant Browser',
	'seamonkey' => 'SeaMonkey',
	'konqueror' => 'Konqueror',
	'lynx' => 'Lynx',
	'aol' => 'Aol',
	'amaya' => 'Amaya',
	'other' => 'Other'
);

$oses = array(
	'[unspecified]' => Lang::txt('COM_SUPPORT_TROUBLE_SELECT_OS'),
	'Windows' => 'Windows',
	'Mac OS' => 'Mac OS',
	'Linux' => 'Linux',
	'Unix' => 'Unix',
	'Google Chrome OS' => 'Google Chrome OS',
	'Android' => 'Android',
	'iOS' => 'iOS',
	'Other' => 'Other'
);

// are we remotely loading ticket form
$tmpl = (Request::getVar('tmpl', '')) ? '&tmpl=component' : '';

// are we trying to assign a group
$group = Request::getVar('group', '');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<p class="info"><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); //Lang::txt('COM_SUPPORT_ERROR_MISSING_FIELDS'); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new' . $tmpl); ?>" id="hubForm" method="post" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_SUPPORT_TROUBLE_USER_INFORMATION'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="verified" value="<?php echo $this->row->get('verified'); ?>" />

			<input type="hidden" name="problem[referer]" value="<?php echo $this->escape($this->row->get('referrer')); ?>" />
			<input type="hidden" name="problem[tool]" value="<?php echo $this->escape($this->row->get('tool')); ?>" />
			<input type="hidden" name="problem[osver]" value="<?php echo $this->escape($this->row->get('osver')); ?>" />
			<input type="hidden" name="problem[browserver]" value="<?php echo $this->escape($this->row->get('browserver')); ?>" />
			<input type="hidden" name="problem[short]" value="<?php echo $this->escape($this->row->get('short')); ?>" />

			<input type="hidden" name="no_html" value="0" />
			<?php if ($this->row->get('verified')) { ?>
				<input type="hidden" name="botcheck" value="" />
			<?php } ?>

			<label for="reporter_login">
				<?php echo Lang::txt('COM_SUPPORT_USERNAME'); ?>
				<input type="text" name="reporter[login]" value="<?php echo $this->row->get('login', $this->row->submitter('username')); ?>" id="reporter_login" />
			</label>

			<label for="reporter_name"<?php echo ($this->getError() && !$this->row->get('name')) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo Lang::txt('COM_SUPPORT_NAME'); ?> <span class="required"><?php echo Lang::txt('COM_SUPPORT_REQUIRED'); ?></span>
				<input type="text" name="reporter[name]" value="<?php echo $this->row->get('name', $this->row->submitter('name')); ?>" id="reporter_name" />
			</label>
			<?php if ($this->getError() && !$this->row->get('name')) { ?>
				<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_NAME'); ?></p>
			<?php } ?>

			<label for="reporter_email"<?php echo ($this->getError() && !$this->row->get('email')) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo Lang::txt('COM_SUPPORT_EMAIL'); ?> <span class="required"><?php echo Lang::txt('COM_SUPPORT_REQUIRED'); ?></span>
				<input type="text" name="reporter[email]" value="<?php echo $this->row->get('email', $this->row->submitter('email')); ?>" id="reporter_email" />
			</label>
			<?php if ($this->getError() && !$this->row->get('email')) { ?>
				<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_EMAIL'); ?></p>
			<?php } ?>

			<?php /*<label for="reporter_org">
				<?php echo Lang::txt('COM_SUPPORT_ORGANIZATION'); ?>
				<input type="text" name="reporter[org]" value="<?php echo (isset($this->reporter['org'])) ? $this->escape($this->reporter['org']) : ''; ?>" id="reporter_org" />
			</label>*/ ?>
			<input type="hidden" name="reporter[org]" value="<?php echo $this->row->get('organization', $this->row->submitter('organization')); ?>" id="reporter_org" />

			<div class="grid">
				<div class="col span6">
					<label for="problem_os"<?php echo ($this->getError() && !$this->row->get('os')) ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo Lang::txt('COM_SUPPORT_OS'); ?>
						<select name="problem[os]" id="problem_os">
						<?php foreach ($oses as $avalue => $alabel) { ?>
							<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->row->get('os') || $alabel == $this->row->get('os')) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($alabel); ?></option>
						<?php } ?>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="problem_browser"<?php echo ($this->getError() && $this->row->get('browser') == '') ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo Lang::txt('COM_SUPPORT_BROWSER'); ?>
						<select name="problem[browser]" id="problem_browser">
						<?php foreach ($browsers as $avalue => $alabel) { ?>
							<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->row->get('browser') || $alabel == $this->row->get('browser')) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($alabel); ?></option>
						<?php } ?>
						</select>
					</label>
				</div>
			</div><!-- / .group -->
		</fieldset><div class="clear"></div>

		<fieldset>
			<legend><?php echo Lang::txt('COM_SUPPORT_TROUBLE_YOUR_PROBLEM'); ?></legend>

			<label for="problem_long"<?php echo ($this->getError() && !$this->row->get('report')) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo Lang::txt('COM_SUPPORT_TROUBLE_DESCRIPTION'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<textarea name="problem[long]" cols="40" rows="10" id="problem_long"><?php echo $this->row->get('report'); ?></textarea>
			</label>
			<?php if ($this->getError() && !$this->row->get('report')) { ?>
				<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_MISSING_DESCRIPTION'); ?></p>
			<?php } ?>

			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
				<?php
				$tmp = ('-' . time());
				$this->js('jquery.fileuploader.js', 'system');
				$jbase = rtrim(Request::base(true), '/');
				?>
				<div class="field-wrap">
				<div id="ajax-uploader" data-instructions="<?php echo Lang::txt('COM_SUPPORT_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=upload&amp;ticket=<?php echo $tmp; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=list&amp;ticket=<?php echo $tmp; ?>">
					<noscript>
						<label for="upload">
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label for="field-description">
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE_DESCRIPTION'); ?>:
							<input type="text" name="description" id="field-description" value="" />
						</label>
					</noscript>
				</div>
				<div class="file-list" id="ajax-uploader-list">
				</div>
				<input type="hidden" name="tmp_dir" id="ticket-tmp_dir" value="<?php echo $tmp; ?>" />

				<span class="hint">(.<?php echo str_replace(',', ', .', $this->file_types); ?>)</span>
			</div>
			</fieldset>
		</fieldset><div class="clear"></div>

		<?php if ($this->row->get('verified') && $this->acl->check('update', 'tickets') > 0) { ?>
			<fieldset>
				<legend><?php echo Lang::txt('COM_SUPPORT_DETAILS'); ?></legend>

				<label>
					<?php echo Lang::txt('COM_SUPPORT_COMMENT_TAGS'); ?>:<br />
					<?php
					$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', '')));

					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" id="tags" value="" size="35" />
					<?php } ?>
				</label>

				<div class="grid">
					<div class="col span6">
						<label>
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_GROUP'); ?>:
							<?php
							$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'problem[group]', 'acgroup', '', $group, '', 'ticketowner')));
							if (count($gc) > 0) {
								echo $gc[0];
							} else { ?>
								<input type="text" name="group" value="" id="acgroup" value="" autocomplete="off" />
							<?php } ?>
						</label>
					</div>
					<div class="col span6 omega">
						<label>
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_OWNER'); ?>:
							<?php echo $this->lists['owner']; ?>
						</label>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<label for="ticket-field-severity">
							<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEVERITY'); ?>
							<select name="problem[severity]" id="ticket-field-severity">
								<?php foreach ($this->lists['severities'] as $severity) { ?>
									<option value="<?php echo $severity; ?>"<?php if ($severity == 'normal') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)); ?></option>
								<?php } ?>
							</select>
						</label>
					</div>
					<div class="col span6 omega">
						<label for="ticket-field-status">
							<?php
							echo Lang::txt('COM_SUPPORT_COMMENT_STATUS'); ?>:
							<select name="problem[status]" id="ticket-field-status">
								<optgroup label="<?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_OPEN'); ?>">
									<option value="0" selected="selected"><?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_NEW'); ?></option>
									<?php foreach ($this->row->statuses('open') as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
								<optgroup label="<?php echo Lang::txt('COM_SUPPORT_CLOSED'); ?>">
									<option value="0"><?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'); ?></option>
									<?php foreach ($this->row->statuses('closed') as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
							</select>
						</label>
					</div>
				</div>

				<label for="field-target_date">
					<?php echo Lang::txt('COM_SUPPORT_COMMENT_TARGET_DATE'); ?>:
					<input type="text" name="problem[target_date]" class="datetime-field" id="field-target_date" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::getRoot()) / 60); ?>" placeholder="YYYY-MM-DD hh:mm:ss" value="" />
				</label>

				<?php if (isset($this->lists['categories']) && $this->lists['categories'])  { ?>
				<label for="ticket-field-category">
					<?php echo Lang::txt('COM_SUPPORT_COMMENT_CATEGORY'); ?>
					<select name="problem[category]" id="ticket-field-category">
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
				<?php } ?>

				<label>
					<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'); ?>: <?php
					$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', '')));
					if (count($mc) > 0) {
						echo '<span class="hint">'.Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE').'</span>'.$mc[0];
					} else { ?> <span class="hint"><?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
					<input type="text" name="cc" id="acmembers" value="" size="35" />
					<?php } ?>
				</label>
			</fieldset>
		<?php } else { ?>
			<?php if ($group) { ?>
				<input type="hidden" name="group" value="<?php echo $group; ?>" />
			<?php } ?>
		<?php } ?>

		<?php if (!$this->row->get('verified')) { ?>
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
				if (count($this->captchas) > 0)
				{
					foreach ($this->captchas as $captcha)
					{
						echo $captcha;
					}
				}
				?>
				<?php if ($this->getError() == 3) { ?>
				<p class="error"><?php echo Lang::txt('COM_SUPPORT_ERROR_BAD_CAPTCHA_ANSWER'); ?></p>
				<?php } ?>
			</fieldset><div class="clear"></div>
		<?php } ?>

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" name="submit" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
