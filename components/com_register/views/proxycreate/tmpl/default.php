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

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<p class="passed">
		This proxy account has been created successfully. You may add to the text below, but you MUST 
		send an email including all of this text to the new user at <?php echo $this->target_xprofile->get('email'); ?>:
	</p>
	<blockquote>
<pre>An account has been created on your behalf at <?php echo $this->hubShortName; ?> by <?php echo $this->xprofile->get('name'); ?>.

Your initial new account username is: <?php echo $this->target_juser->get('username'); ?>
Your initial new account password is: <?php echo $this->target_xprofile->get('proxy_password'); ?>
You must click the following link to confirm your email address and activate your account:
<?php echo $this->hubLongURL . JRoute::_('index.php?option='.$this->option.'&task=registration&view=confirm&confirm='. -$this->target_xprofile->get('emailConfirmed')); ?>

(Do not reply to this email.  Replying to this email  will not confirm or activate your account.)

After confirming your account, you may click the following link to set a new password:

<?php echo $this->hubLongURL . JRoute::_('index.php?option=com_members&id='.$this->target_juser->get('id').'&task=changepassword'); ?>
</pre>
	</blockquote>
	<p>New user's profile page: <a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->target_juser->get('id')); ?>"><?php echo $this->target_juser->get('name'); ?> (<?php echo $this->target_juser->get('username'); ?>)</a></p>
</div><!-- / .main section -->