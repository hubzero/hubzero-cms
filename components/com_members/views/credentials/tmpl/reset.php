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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<header id="content-header">
	<h2><?php echo JText::_('COM_MEMBERS_CREDENTIALS_RESET'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo JRoute::_('index.php?option=com_members&controller=credentials&task=resetting'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				<?php echo JText::sprintf(
					'Forgot your username? Go <a href="%s">here</a> to recover it.',
					JRoute::_('index.php?option=com_members&task=remind')
				); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_MEMBERS_CREDENTIALS_EMAIL_VERIFICATION_TOKEN'); ?></legend>

			<p>
				<?php echo JText::_('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_DESCRIPTION'); ?>
			</p>
			<label for="username">
				<?php echo JText::_('COM_MEMBERS_CREDENTIALS_RESET_PASSWORD_LABEL'); ?>:
				<span class="required"><?php echo JText::_('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="username" />
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo JText::_('Submit'); ?></button></p>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</section>