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

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
<?php if ($this->getError()) { ?>
	<div class="aside">
		<h4>Never received or cannot find the confirmation email?</h4>
		<p>You can have a new confirmation email sent to "<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" by <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resend&return='.$this->return); ?>">clicking here</a>.</p>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="error">
			<h4><?php echo JText::_('Invalid Confirmation'); ?></h4>
			<p>The email confirmation link you followed is no longer valid. Your email address "<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" has not been confirmed.</p>
			<p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resend'); ?>">resend a new confirmation email</a>.</p>
		</div>
	</div><!-- / .subject -->
<?php } else { ?>
	<p class="passed">Your email address "<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" has already been confirmed. You should be able to use <?php echo $this->hubShortName; ?> now. Thank you.</p>
<?php } ?>
</div><!-- / .section -->
