<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js('new.js');

$browsers = array(
	'[unspecified]' => JText::_('COM_SUPPORT_TROUBLE_SELECT_BROWSER'),
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
	'[unspecified]' => JText::_('COM_SUPPORT_TROUBLE_SELECT_OS'),
	'Windows' => 'Windows',
	'Mac OS' => 'Mac OS',
	'Linux' => 'Linux',
	'Unix' => 'Unix',
	'Google Chrome OS' => 'Google Chrome OS',
	'Other' => 'Other'
);

// are we remotely loading ticket form
$tmpl = (JRequest::getVar('tmpl', '')) ? '&tmpl=component' : '';

// are we trying to assign a group
$group = JRequest::getVar('group', '');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<p class="info"><?php echo JText::_('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo JText::_('COM_SUPPORT_ERROR_MISSING_FIELDS'); ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'.$tmpl); ?>" id="hubForm" method="post" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo JText::_('COM_SUPPORT_TROUBLE_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_SUPPORT_TROUBLE_USER_INFORMATION'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />

			<input type="hidden" name="problem[referer]" value="<?php echo $this->escape($this->problem['referer']); ?>" />
			<input type="hidden" name="problem[tool]" value="<?php echo $this->escape($this->problem['tool']); ?>" />
			<input type="hidden" name="problem[osver]" value="<?php echo $this->escape($this->problem['osver']); ?>" />
			<input type="hidden" name="problem[browserver]" value="<?php echo $this->escape($this->problem['browserver']); ?>" />
			<input type="hidden" name="problem[short]" value="<?php echo $this->escape($this->problem['short']); ?>" />
			<?php if ($group) { ?>
				<input type="hidden" name="group" value="<?php echo $group; ?>" />
			<?php } ?>
			<input type="hidden" name="no_html" value="0" />
			<?php if ($this->verified) { ?>
				<input type="hidden" name="botcheck" value="" />
			<?php } ?>

			<?php /*<label>
				<?php echo JText::_('COM_SUPPORT_USERNAME'); ?>
				<input type="text" name="reporter[login]" value="<?php echo (isset($this->reporter['login'])) ? $this->escape($this->reporter['login']) : ''; ?>" id="reporter_login" />
			</label> */ ?>

			<label<?php echo ($this->getError() && $this->reporter['name'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_SUPPORT_NAME'); ?> <span class="required"><?php echo JText::_('COM_SUPPORT_REQUIRED'); ?></span>
				<input type="text" name="reporter[name]" value="<?php echo (isset($this->reporter['name'])) ? $this->escape($this->reporter['name']) : ''; ?>" id="reporter_name" />
			</label>
			<?php if ($this->getError() && $this->reporter['name'] == '') { ?>
				<p class="error"><?php echo JText::_('COM_SUPPORT_ERROR_MISSING_NAME'); ?></p>
			<?php } ?>

			<label>
				<?php echo JText::_('COM_SUPPORT_ORGANIZATION'); ?>
				<input type="text" name="reporter[org]" value="<?php echo (isset($this->reporter['org'])) ? $this->escape($this->reporter['org']) : ''; ?>" id="reporter_org" />
			</label>

			<label<?php echo ($this->getError() && $this->reporter['email'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_SUPPORT_EMAIL'); ?> <span class="required"><?php echo JText::_('COM_SUPPORT_REQUIRED'); ?></span>
				<input type="text" name="reporter[email]" value="<?php echo (isset($this->reporter['email'])) ? $this->escape($this->reporter['email']) : ''; ?>" id="reporter_email" />
			</label>
			<?php if ($this->getError() && $this->reporter['email'] == '') { ?>
				<p class="error"><?php echo JText::_('COM_SUPPORT_ERROR_MISSING_EMAIL'); ?></p>
			<?php } ?>

			<div class="grid">
				<div class="col span6">
					<label<?php echo ($this->getError() && $this->problem['os'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo JText::_('COM_SUPPORT_OS'); ?>
						<select name="problem[os]" id="problem_os">
						<?php foreach ($oses as $avalue => $alabel) { ?>
							<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['os'] || $alabel == $this->problem['os']) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($alabel); ?></option>
						<?php } ?>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label<?php echo ($this->getError() && $this->problem['browser'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
						<?php echo JText::_('COM_SUPPORT_BROWSER'); ?>
						<select name="problem[browser]" id="problem_browser">
						<?php foreach ($browsers as $avalue => $alabel) { ?>
							<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['browser'] || $alabel == $this->problem['browser']) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($alabel); ?></option>
						<?php } ?>
						</select>
					</label>
				</div>
			</div><!-- / .group -->
		</fieldset><div class="clear"></div>

		<fieldset>
			<legend><?php echo JText::_('COM_SUPPORT_TROUBLE_YOUR_PROBLEM'); ?></legend>

			<label<?php echo ($this->getError() && $this->problem['long'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_SUPPORT_TROUBLE_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span>
				<textarea name="problem[long]" cols="40" rows="10" id="problem_long"><?php echo (isset($this->problem['long'])) ? $this->escape(stripslashes($this->problem['long'])) : ''; ?></textarea>
			</label>
			<?php if ($this->getError() && (!isset($this->problem['long']) || $this->problem['long'] == '')) { ?>
				<p class="error"><?php echo JText::_('COM_SUPPORT_ERROR_MISSING_DESCRIPTION'); ?></p>
			<?php } ?>

			<fieldset>
				<legend><?php echo JText::_('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
				<?php
				$tmp = ('-' . time());
				$this->js('jquery.fileuploader.js', 'system');
				$jbase = rtrim(JURI::getInstance()->base(true), '/');
				?>
				<div class="field-wrap">
				<div id="ajax-uploader" data-instructions="<?php echo JText::_('COM_SUPPORT_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=upload&amp;ticket=<?php echo $tmp; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=list&amp;ticket=<?php echo $tmp; ?>">
					<noscript>
						<label for="upload">
							<?php echo JText::_('COM_SUPPORT_COMMENT_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label for="field-description">
							<?php echo JText::_('COM_SUPPORT_COMMENT_FILE_DESCRIPTION'); ?>:
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

		<?php if ($this->verified && $this->acl->check('update', 'tickets') > 0) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_SUPPORT_DETAILS'); ?></legend>

				<label>
					<?php echo JText::_('COM_SUPPORT_COMMENT_TAGS'); ?>:<br />
					<?php
					JPluginHelper::importPlugin('hubzero');
					$dispatcher = JDispatcher::getInstance();
				$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags', '', '')));

				if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="tags" value="" size="35" />
				<?php } ?>
				</label>

				<div class="grid">
					<div class="col span6">
						<label>
							<?php echo JText::_('COM_SUPPORT_COMMENT_GROUP'); ?>:
							<?php
							$gc = $dispatcher->trigger('onGetSingleEntryWithSelect', array(array('groups', 'problem[group]', 'acgroup', '', '', '', 'ticketowner')));
							if (count($gc) > 0) {
								echo $gc[0];
							} else { ?>
								<input type="text" name="group" value="" id="acgroup" value="" autocomplete="off" />
							<?php } ?>
						</label>
					</div>
					<div class="col span6 omega">
						<label>
							<?php echo JText::_('COM_SUPPORT_COMMENT_OWNER'); ?>:
							<?php echo $this->lists['owner']; ?>
						</label>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<label for="ticket-field-severity">
							<?php echo JText::_('COM_SUPPORT_COMMENT_SEVERITY'); ?>
							<?php echo SupportHtml::selectArray('problem[severity]', $this->lists['severities'], 'normal'); ?>
						</label>
					</div>
					<div class="col span6 omega">
						<label for="ticket-field-status">
							<?php
							$row = new SupportModelTicket();
							echo JText::_('COM_SUPPORT_COMMENT_STATUS'); ?>:
							<select name="problem[status]" id="ticket-field-status">
								<optgroup label="<?php echo JText::_('COM_SUPPORT_COMMENT_OPT_OPEN'); ?>">
									<option value="0" selected="selected"><?php echo JText::_('COM_SUPPORT_COMMENT_OPT_NEW'); ?></option>
									<?php foreach ($row->statuses('open') as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
								<optgroup label="<?php echo JText::_('COM_SUPPORT_CLOSED'); ?>">
									<option value="0"><?php echo JText::_('COM_SUPPORT_COMMENT_OPT_CLOSED'); ?></option>
									<?php foreach ($row->statuses('closed') as $status) { ?>
										<option value="<?php echo $status->get('id'); ?>"><?php echo $this->escape($status->get('title')); ?></option>
									<?php } ?>
								</optgroup>
							</select>
						</label>
					</div>
				</div>

				<?php if (isset($this->lists['categories']) && $this->lists['categories'])  { ?>
				<label for="ticket-field-category">
					<?php echo JText::_('COM_SUPPORT_COMMENT_CATEGORY'); ?>
					<select name="problem[category]" id="ticket-field-category">
						<option value=""><?php echo JText::_('COM_SUPPORT_NONE'); ?></option>
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
					<?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'); ?>: <?php
					$mc = $dispatcher->trigger('onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', '')));
					if (count($mc) > 0) {
						echo '<span class="hint">'.JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE').'</span>'.$mc[0];
					} else { ?> <span class="hint"><?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
					<input type="text" name="cc" id="acmembers" value="" size="35" />
					<?php } ?>
				</label>
			</fieldset>
		<?php } ?>

		<?php if (!$this->verified) { ?>
			<div class="explaination">
				<p><?php echo JText::_('COM_SUPPORT_MATH_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('COM_SUPPORT_HUMAN_CHECK'); ?></legend>

				<label id="fbBotcheck-label" for="fbBotcheck">
					<?php echo JText::_('COM_SUPPORT_LEAVE_FIELD_BLANK'); ?> <span class="required"><?php echo JText::_('JREQUIRED'); ?></span>
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
				<p class="error"><?php echo JText::_('COM_SUPPORT_ERROR_BAD_CAPTCHA_ANSWER'); ?></p>
				<?php } ?>
			</fieldset><div class="clear"></div>
		<?php } ?>

		<?php echo JHTML::_('form.token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" name="submit" value="<?php echo JText::_('COM_SUPPORT_SUBMIT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
