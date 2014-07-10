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

$this->css('register.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="grid">
		<div class="col span-half">
			<div class="<?php echo ($this->getError() ? 'error' : 'success'); ?>-message">
				<p><?php echo ($this->getError() ? JText::_('COM_MEMBERS_REGISTER_ERROR_OCCURRED') : JText::_('COM_MEMBERS_REGISTER_ACCOUNT_CREATED')); ?></p>
			</div>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php } else if ($this->xprofile->get('emailConfirmed') < 0){ ?>
				<div class="account-activation">
					<div class="instructions">
						<p><?php echo JText::sprintf('COM_MEMBERS_REGISTER_ACCOUNT_CREATED_MESSAGE', $this->sitename, \Hubzero\Utility\String::obfuscate($this->xprofile->get('email'))); ?></p>
						<ol>
							<li><?php echo JText::_('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_FIND_EMAIL'); ?></li>
							<li><?php echo JText::_('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_ACTIVATE'); ?></li>
							<li><?php echo JText::_('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_LOGIN'); ?></li>
							<li><?php echo JText::_('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_SUCCESS'); ?></li>
						</ol>
					</div>
					<div class="notes">
						<p><?php echo JText::sprintf('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_NOTE', JRoute::_('index.php?option=com_support')); ?></p>
					</div>
				</div>
			<?php } ?>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->
