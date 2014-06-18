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

$this->css('register')
     ->js('register');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
<?php if ($this->getError() && $this->getError() == 'login mismatch') : ?>
	<p class="warning">
		You are currently logged in as <strong><?php echo $this->login; ?></strong>. If you're trying to activate a different account,
		you may do so by <a href="<?php echo $this->redirect; ?>">confirming a different email address</a>.
	</p>
<?php elseif ($this->getError()) : ?>
	<div class="subject">
		<div class="error">
			<h4><?php echo JText::_('Invalid Confirmation'); ?></h4>
			<p>The email confirmation link you followed is no longer valid. Your email address "<?php echo $this->escape($this->email); ?>" has not been confirmed.</p>
			<p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resend'); ?>">resend a new confirmation email</a>.</p>
		</div>
	</div><!-- / .subject -->
	<aside class="aside">
		<h4>Never received or cannot find the confirmation email?</h4>
		<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resend&return='.$this->return); ?>">clicking here</a>.</p>
	</aside><!-- / .aside -->
<?php else : ?>
	<p class="passed">Your email address "<?php echo $this->escape($this->email); ?>" has already been confirmed. You should be able to use <?php echo $this->sitename; ?> now. Thank you.</p>
<?php endif; ?>
</section><!-- / .section -->
