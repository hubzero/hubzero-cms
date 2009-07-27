<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

$login_valid = true;
	$email_valid = true;

	if ($reset) {
		if (empty($login) || empty($email)) {
			//$html .= '<div class="error">Missing required information:'.n;
			//$html .= t.'<ul>'.n;
			if (empty($login)) {
				//$html .= t.' <li>User Login</li>'.n;
				$login_valid = false;
			}
			if (empty($email)) {
				//$html .= t.' <li>Valid E-Mail</li>'.n;
				$email_valid = false;
			}
			//$html .= t.'</ul></div>'.n;
		} else {
			if (!XRegistrationHelper::validlogin($login)) {
				//$html .= PasswordHtml::error(JText::_('Invalid login name. Please use only alphanumeric characters.')).n;
				$login_valid = false;
			}
			if (!XRegistrationHelper::validemail($email)) {
				//$html .= PasswordHtml::error(JText::_('Invalid email address. Please correct and try again.')).n;
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

<div id="content-header" class="full">
	<h2><?php echo JText::_('Reset Password'); ?></h2>
</div>
<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->_option.'&task='.$this->_task); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you do not know your username, first go to <a href="<?php echo JRoute::_('index.php?option='.$this->_option.'&task=lostusername'); ?>">Username Recovery</a> and you can get your username emailed to you.  You can then proceed with this page to reset your password.
			</p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('Email New Password'); ?></h3>
			
			<p>Enter your login and email address exactly as listed on your account here, and a random new password will be emailed to you immediately.  You may then login with that password and change your password to something else.</p>
			
			<?php if ($this->getError()) { ?> <p class="error"><?php echo $this->getError(); ?></p> <?php } ?>
			
			<label<?php echo $fieldclass1; ?>>
				<?php echo JText::_('Username'); ?>:
				<input name="login" id="login" type="text" size="25" value="<?php echo htmlentities($login,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
			<?php
			if ($reset && empty($login)) {
				?><p class="error"><?php echo JText::_('Please provide your login.'); ?></p><?php
			} elseif (!$login_valid) {
				?><p class="error"><?php echo JText::_('Invalid username. Please use only alphanumeric characters.'); ?></p><?php
			}
			?>
			
			<label<?php echo $fieldclass2; ?>>
				<?php echo JText::_('Registered E-mail'); ?>:
				<input name="email" id="email" type="text" size="25" value="<?php echo htmlentities($email,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
			<?php
			if ($reset && empty($email)) {
				?><p class="error"><?php echo JText::_('Please provide a valid e-mail address.'); ?></p><?php
			} elseif (!$email_valid) {
				?><p class="error"><?php echo JText::_('Invalid email address. Example: someone@somewhere.com'); ?></p><?php
			}			
			?>
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="option" value="<?php echo $this->_option; ?>" />
		<input type="hidden" name="view" value="<?php echo $this->_task; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->_task; ?>" />
		
		<p class="submit"><input type="submit" name="reset" value="Send Email with New Password" /></p>
	</form>
</div>