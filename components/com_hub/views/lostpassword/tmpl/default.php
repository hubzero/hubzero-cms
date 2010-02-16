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
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('Reset Password'); ?></h2>
</div>
<div class="main section">
<?php if ($this->passed) { ?>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<p class="passed">
		Your password has been reset to a new password successfully.
		Your new password has been emailed to you at "<?php echo htmlentities($this->xprofile->get('email'),ENT_COMPAT,'UTF-8'); ?>". If you do not receive it or have any questions, please contact administrators at <a href="mailto:<?php echo htmlentities($this->jconfig->getValue('config.mailfrom'),ENT_COMPAT,'UTF-8'); ?>"><?php echo htmlentities($this->jconfig->getValue('config.mailfrom'),ENT_COMPAT,'UTF-8'); ?></a>.
	</p>
<?php 
} else { 
	$login_valid = true;
	$email_valid = true;

	if ($this->reset) {
		if (!$this->login || !$this->email) {
			if (!$this->login) {
				$login_valid = false;
			}
			if (!$this->email) {
				$email_valid = false;
			}
		} else {
			if (!XRegistrationHelper::validlogin($this->login)) {
				$login_valid = false;
			}
			if (!XRegistrationHelper::validemail($this->email)) {
				$email_valid = false;
			}
		}
	}

	$fieldclass1 = '';
	if (!$login_valid) {
		$fieldclass1 = ' class="fieldWithErrors"';
	}

	$fieldclass2 = '';
	if (!$email_valid) {
		$fieldclass2 = ' class="fieldWithErrors"';
	}
?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.$this->task); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you do not know your username, first go to <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=lostusername'); ?>">Username Recovery</a> and you can get your username emailed to you.  You can then proceed with this page to reset your password.
			</p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('Email New Password'); ?></h3>
			
			<p>Enter your login and email address exactly as listed on your account here, and a random new password will be emailed to you immediately.  You may then login with that password and change your password to something else.</p>
			
<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
			
			<label<?php echo $fieldclass1; ?>>
				<?php echo JText::_('Username'); ?>:
				<input name="login" id="login" type="text" size="25" value="<?php echo htmlentities($this->login,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
			<?php
			if ($this->reset && !$this->login) {
				?><p class="error"><?php echo JText::_('Please provide your login.'); ?></p><?php
			} elseif (!$login_valid) {
				?><p class="error"><?php echo JText::_('Invalid username. Please use only alphanumeric characters.'); ?></p><?php
			}
			?>
			
			<label<?php echo $fieldclass2; ?>>
				<?php echo JText::_('Registered E-mail'); ?>:
				<input name="email" id="email" type="text" size="25" value="<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
			<?php
			if ($this->reset && !$this->email) {
				?><p class="error"><?php echo JText::_('Please provide a valid e-mail address.'); ?></p><?php
			} elseif (!$email_valid) {
				?><p class="error"><?php echo JText::_('Invalid email address. Example: someone@somewhere.com'); ?></p><?php
			}			
			?>
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="view" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		
		<p class="submit"><input type="submit" name="reset" value="Send Email with New Password" /></p>
	</form>
<?php } ?>
</div><!-- / .main section -->
