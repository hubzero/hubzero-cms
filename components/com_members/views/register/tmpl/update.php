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

$this->css('register')
     ->js('register');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
<?php if ($this->self) { ?>
	<p class="passed">Your account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>Thank you for updating your account. In order to continue to use this account you must verify your new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. You must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<p class="passed">The account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>The user of this account has been notified of the change. In order to continue to use this account they will need to verify the new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. They must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } ?>
</section><!-- / .main section -->

