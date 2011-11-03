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
defined('_JEXEC') or die( 'Restricted access' );

$browsers = array(
	'[unspecified]' => JText::_('COM_FEEDBACK_TROUBLE_SELECT_BROWSER'),
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
	'[unspecified]' => JText::_('COM_FEEDBACK_TROUBLE_SELECT_OS'),
	'Windows' => 'Windows',
	'Mac OS' => 'Mac OS',
	'Linux' => 'Linux',
	'Unix' => 'Unix',
	'Google Chrome OS' => 'Google Chrome OS',
	'Other' => 'Other'
);

$topics = array(
	'???' => 'Unsure/Don\'t know',
	'Access Denied' => 'Access Denied',
	'Account/Login' => 'Account/Login',
	'Content' => 'Content',
	'Contributions' => 'Contributions',
	'Online Meetings' => 'Online Meetings',
	'Tools' => 'Tools',
	'other' => 'other'
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<p class="information"><?php echo JText::_('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?></p>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_FIELDS'); ?></p>
<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=report_problems'); ?>" id="hubForm" method="post" enctype="multipart/form-data">
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_TROUBLE_OTHER_OPTIONS'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_FEEDBACK_TROUBLE_USER_INFORMATION'); ?></legend>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="sendreport" />
			<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />
			
			<input type="hidden" name="problem[referer]" value="<?php echo htmlspecialchars($this->problem['referer']); ?>" />
			<input type="hidden" name="problem[tool]" value="<?php echo htmlspecialchars($this->problem['tool']); ?>" />
			<input type="hidden" name="problem[osver]" value="<?php echo htmlspecialchars($this->problem['osver']); ?>" />
			<input type="hidden" name="problem[browserver]" value="<?php echo htmlspecialchars($this->problem['browserver']); ?>" />
			<input type="hidden" name="problem[short]" value="<?php echo htmlspecialchars($this->problem['short']); ?>" />
			
			<input type="hidden" name="no_html" value="0" />
<?php if ($this->verified) { ?>
			<input type="hidden" name="botcheck" value="" />
<?php } ?>
			
			<label>
				<?php echo JText::_('COM_FEEDBACK_USERNAME'); ?>
				<input type="text" name="reporter[login]" value="<?php echo (isset($this->reporter['login'])) ? $this->reporter['login'] : ''; ?>" id="reporter_login" />
			</label>
			
			<label<?php echo ($this->getError() && $this->reporter['name'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_NAME'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="reporter[name]" value="<?php echo (isset($this->reporter['name'])) ? $this->reporter['name'] : ''; ?>" id="reporter_name" />
			</label>
<?php if ($this->getError() && $this->reporter['name'] == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_NAME'); ?></p>
<?php } ?>

			<label>
				<?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>
				<input type="text" name="reporter[org]" value="<?php echo (isset($this->reporter['org'])) ? $this->reporter['org'] : ''; ?>" id="reporter_org" />
			</label>

			<label<?php echo ($this->getError() && $this->reporter['email'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_EMAIL'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<input type="text" name="reporter[email]" value="<?php echo (isset($this->reporter['email'])) ? $this->reporter['email'] : ''; ?>" id="reporter_email" />
			</label>
<?php if ($this->getError() && $this->reporter['email'] == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_EMAIL'); ?></p>
<?php } ?>
			<div class="group">
				<label<?php echo ($this->getError() && $this->problem['os'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('COM_FEEDBACK_OS'); ?>
					<select name="problem[os]" id="problem_os">
<?php
					foreach ($oses as $avalue => $alabel)
					{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['os'] || $alabel == $this->problem['os']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
					}
?>
					</select>
				</label>
				
				<label<?php echo ($this->getError() && $this->problem['browser'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
					<?php echo JText::_('COM_FEEDBACK_BROWSER'); ?>
					<select name="problem[browser]" id="problem_browser">
<?php
					foreach ($browsers as $avalue => $alabel)
					{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->problem['browser'] || $alabel == $this->problem['browser']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
					}
?>
					</select>
				</label>
			</div><!-- / .group -->
		</fieldset><div class="clear"></div>
		
		<fieldset>
			<legend><?php echo JText::_('COM_FEEDBACK_TROUBLE_YOUR_PROBLEM'); ?></legend>
			
			<label<?php echo ($this->getError() && $this->problem['long'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_TROUBLE_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<textarea name="problem[long]" cols="40" rows="10" id="problem_long"><?php echo (isset($this->problem['long'])) ? stripslashes($this->problem['long']) : ''; ?></textarea>
			</label>
			<?php if ($this->getError() && (!isset($this->problem['long']) || $this->problem['long'] == '')) { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_DESCRIPTION'); ?></p>
			<?php } ?>
			
			<label>
				<?php echo JText::_('Attach a screenshot'); ?>:
				<small>(.<?php echo str_replace(",", ", .", $this->file_types); ?>)</small>
				<input type="file" name="upload" id="trUpload" />
				
			</label>
		</fieldset><div class="clear"></div>
<?php if (!$this->verified) { ?>
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_MATH_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('Human Check'); ?></legend>
			
			<label id="fbBotcheck-label" for="fbBotcheck">
				<?php echo JText::_('Please leave this field blank.'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
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
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_BAD_CAPTCHA_ANSWER'); ?></p>
			<?php } ?>
		</fieldset><div class="clear"></div>
<?php } ?>
		<p class="submit"><input type="submit" name="submit" value="<?php echo JText::_('COM_FEEDBACK_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->

