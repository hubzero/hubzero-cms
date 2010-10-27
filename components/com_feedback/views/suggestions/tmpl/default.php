<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$selects = array(
	'general' => 'General',
	'tool' => 'Simulation Tools',
	'learningmodule' => 'Learning Modules',
	'lecture' => 'Lectures',
	'workshop' => 'Workshops'
);
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=suggestions'); ?>" id="hubForm" method="post">
		<div class="explaination">
			<p><?php echo JText::_('COM_FEEDBACK_SUGGESTION_EXPLANATION'); ?></p>
		</div>
		<fieldset>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="sendsuggestions" />
		<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />
		<input type="hidden" name="krhash" value="<?php echo $this->suggestion['key']; ?>" />
<?php if ($this->verified) { ?>
		<input type="hidden" name="answer" value="<?php echo $this->suggestion['sum']; ?>" />
<?php } ?>
		<h3><?php echo JText::_('COM_FEEDBACK_SUGGESTION_USER_INFORMATION'); ?></h3>
		
		<label>
			<?php echo JText::_('COM_FEEDBACK_USERNAME'); ?>
			<input type="text" name="suggester[login]" value="<?php echo $this->user['login']; ?>" size="30" id="suggester_login" />
		</label>
		
		<label<?php echo ($this->getError() && $this->user['name'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
			<?php echo JText::_('COM_FEEDBACK_NAME'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
			<input type="text" name="suggester[name]" value="<?php echo $this->user['name']; ?>" size="30" id="suggester_name" />
		</label>
<?php if ($this->getError() && $this->user['name'] == '') { ?>
		<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_NAME'); ?></p>
<?php } ?>
		
		<label<?php echo ($this->getError() && $this->user['org'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
			<?php echo JText::_('COM_FEEDBACK_ORGANIZATION'); ?>
			<input type="text" name="suggester[org]" value="<?php echo $this->user['org']; ?>" size="40" id="suggester_org" />
		</label>

		<label<?php echo ($this->getError() && $this->user['email'] == '' || $this->getError() == 2) ? ' class="fieldWithErrors"' : ''; ?>>
			<?php echo JText::_('COM_FEEDBACK_EMAIL'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
			<input type="text" name="suggester[email]" value="<?php echo $this->user['email']; ?>" size="40" id="suggester_email" />
		</label>
<?php if ($this->getError() && $this->user['email'] == '') { ?>
		<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_MISSING_EMAIL'); ?></p>
<?php } ?>
		
		</fieldset><div class="clear"></div>
		
<?php if ($this->verified != 1) { ?>
		<div class="explaination">
			<h4><?php echo JText::_('COM_FEEDBACK_WHY_THE_MATH_QUESTION'); ?></h4>
			<p><?php echo JText::_('COM_FEEDBACK_MATH_EXPLANATION'); ?></p>
		</div>
<?php } ?>
		<fieldset>
			<h3><?php echo JText::_('COM_FEEDBACK_SUGGESTION_YOUR_COMMENTS'); ?></h3>
			
			<label<?php echo ($this->getError() && $this->suggestion['for'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_SUGGESTION_TOPIC'); ?>
				<select name="suggestion[for]" id="suggestion_for">
<?php
				foreach ($selects as $avalue => $alabel) 
				{
?>
					<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->suggestion['for'] || $alabel == $this->suggestion['for']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
				}
?>
				</select>
			</label>

			<label<?php echo ($this->getError() && $this->suggestion['idea'] == '') ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::_('COM_FEEDBACK_SUGGESTION_DESCRIPTION'); ?> <span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
				<textarea name="suggestion[idea]" rows="40" cols="10" id="suggestion_idea"><?php echo $this->suggestion['idea']; ?></textarea>
			</label>
			<?php if ($this->getError() && $this->suggestion['idea'] == '') { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_SUGGESTION_MISSING_DESCRIPTION'); ?></p>
			<?php } ?>

<?php if ($this->verified != 1) { ?>
			<label<?php echo ($this->getError() == 3) ? ' class="fieldWithErrors"' : ''; ?>>
				<?php echo JText::sprintf('COM_FEEDBACK_TROUBLE_MATH', $this->suggestion['operand1'], $this->suggestion['operand2']); ?> 
				<input type="text" name="answer" value="" size="3" id="answer" class="option" /> 
				<span class="required"><?php echo JText::_('COM_FEEDBACK_REQUIRED'); ?></span>
			</label>
			<?php if ($this->getError() == 3) { ?>
			<p class="error"><?php echo JText::_('COM_FEEDBACK_ERROR_BAD_CAPTCHA_ANSWER'); ?></p>
			<?php } ?>
<?php } ?>

		</fieldset><div class="clear"></div>
		<p class="submit"><input type="submit" name="submit" value="<?php echo JText::_('COM_FEEDBACK_SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->
