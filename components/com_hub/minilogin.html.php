<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
