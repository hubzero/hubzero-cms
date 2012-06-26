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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

Your initial new account username is: <?php echo $this->target_juser->get('username'); ?><br />
Your initial new account password is: <?php echo $this->target_xprofile->get('proxyPassword'); ?><br />
You must click the following link to confirm your email address and activate your account:
<?php echo $this->live_site . JRoute::_('index.php?option='.$this->option.'&task=confirm&confirm='. -$this->target_xprofile->get('emailConfirmed')); ?>

(Do not reply to this email.  Replying to this email  will not confirm or activate your account.)

After confirming your account, you may click the following link to set a new password:

<?php echo $this->live_site . JRoute::_('index.php?option=com_members&id='.$this->target_juser->get('id').'&task=changepassword'); ?>
</pre>
	</blockquote>
	<p>New user's profile page: <a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->target_juser->get('id')); ?>"><?php echo $this->target_juser->get('name'); ?> (<?php echo $this->target_juser->get('username'); ?>)</a></p>
</div><!-- / .main section -->

