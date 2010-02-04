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

$fieldclass = '';
if ($this->getError()) {
	$fieldclass = ' class="fieldWithErrors"';
}

$jconfig =& JFactory::getConfig();
?>
<div id="content-header" class="full">
	<h2>Lost Username</h2>
</div>
<div class="main section">
<?php if ($this->passed) { ?>
	<p class="passed">Your account information has been emailed to you at "<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>". Please check your email for details on how to login.</p>
<?php } else { ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.$this->task); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you already know your username, and only need your password reset, <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=lostpassword'); ?>">go here now</a>.
			</p>
		</div>
		<fieldset>
			<h3>Recover Username(s)</h3>
			
			<label<?php echo $fieldclass; ?>>
				Registered E-mail:
				<input name="email" id="email" type="text" size="36" value="<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
			
			<p>
				Enter your email address and we will look up <strong>all</strong> accounts you have on 
				<?php echo $jconfig->getValue('config.sitename'); ?> and email you the login username information for all of them.
			</p>
			<div class="help">
			<h4>What if I have also lost my password?</h4>
			<p>
				Fill out this form to retrieve your username(s). The email you 
				receive will contain instructions on how to reset your password as well.
			</p>
			
			<h4>What if I have multiple accounts?</h4>
			<p>
				All accounts registered to your email address will be located, and you will be given a 
				list of all of those usernames.  <!-- We strongly encourage you to only maintain one account. 
				Please delete any old or unused accounts, which you can do from the My Account page after logging in. 
				This will help us free up resources for other users. -->
			</p>
			
			<h4>What if this cannot find my account?</h4>
			<p>
				It is possible you registered under a different email address.  Please try any other email 
				addresses you have.
			</p>
			</div>
		</fieldset>
		<div class="clear"></div>
		
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="view" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		
		<p class="submit"><input type="submit" name="resend" value="Send Email with Account Username" /></p>
	</form>
<?php } ?>
</div><!-- / .main section -->