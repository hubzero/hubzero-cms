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

if (!empty($error_message))
		echo '<p class="error">'. $error_message . '</p>';
?>
 <form action="" method="post" id="hubForm_mini">
        <fieldset>		
		<label>
			<?php echo JText::_('_USERNAME'); ?>:
			<input type="text" tabindex="1" size="10" name="username" id="username"<?php if(!empty($usrnm)) { echo ' value="'.$usrnm.'"'; } ?> />
		</label>
		
		<p class="hint">
			<?php if ($realm == 'hzldap') { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_hub&task=lostusername'); ?>"><?php echo JText::_('_LOST_USERNAME');?></a><br />
			<?php } 
			echo JText::_('_NO_USERNAME'); 
			?>
			<a href="/register"><?php echo JText::_('_CREATE_ACCOUNT'); ?></a>
		</p>
		
		<label>
			<?php echo JText::_('_PASSWORD'); ?>:
			<input type="password" tabindex="2" name="passwd" id="passwd" />
		</label>
		
		<?php if ($realm == 'hzldap') { ?>
		<p class="hint">
			<a href="<?php echo JRoute::_('index.php?option=com_hub&task=lostpassword'); ?>"><?php echo JText::_('_LOST_PASSWORD'); ?></a>
		</p>
		<?php } ?>

		<label>
			<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" /> 
			<?php echo JText::_('_REMEMBER_ME'); ?>
		</label>

		<input type="hidden" name="realm" value="<?php echo $realm;?>" />
		<input type="hidden" name="la" value="<?php echo empty($login_attempts) ? 0 : $login_attempts; ?>" />
		<input type="hidden" name="option" value="com_hub" />
		<input type="hidden" name="view" value="login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="act" value="submit" />
		<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>	
		<p class="submit"><input type="submit" name="Submit"  value="<?php echo JText::_('_BUTTON_LOGIN'); ?>" /></p>
    </fieldset>
</form>