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
defined('_JEXEC') or die( 'Restricted access' );

$step = (int) JRequest::getInt('step', 1);
?>

<header id="content-header">
	<h2>Account Setup</h2>
</header>

<div class="prompt-wrap">
	<div class="prompt-container prompt1" style="display:<?php echo ($step === 1) ? 'block': 'none'; ?>">
		<div class="prompt">
			Have you ever logged into <?php echo $this->sitename; ?> before?
		</div>
		<div class="responses">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=link&step=2'); ?>">
				<div data-step="1" class="button next forward">Yes</div>
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_members&controller=register&task=update'); ?>">
				<div data-step="1" class="button backwards">No</div>
			</a>
		</div>
	</div>

	<div class="prompt-container prompt2" style="display:<?php echo ($step === 2) ? 'block': 'none'; ?>">
		<div class="prompt">
			Great! Did you want to link your <?php echo $this->display_name; ?> account to that existing account or create a new account?
		</div>
		<div class="responses">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=link&step=3'); ?>">
				<div data-step="2" class="button next link">Link</div>
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_members&controller=register&task=update'); ?>">
				<div data-step="2" class="button create-new">Create new</div>
			</a>
		</div>
	</div>

	<div class="prompt-container prompt3" style="display:<?php echo ($step === 3) ? 'block': 'none'; ?>">
		<div class="prompt">
			We can do that. Just login with that existing account now and we'll link them up!
		</div>
		<div class="responses">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=logout&return=' .
				base64_encode(JRoute::_('index.php?option=com_users&view=login&reset=1&return=' .
					base64_encode(JRoute::_('index.php?option=com_users&view=login&authenticator=' . $this->hzad->authenticator))))); ?>">
				<div data-step="3" class="button ok">OK</div>
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=link&step=2'); ?>">
				<div data-step="3" class="button previous back">Go back</div>
			</a>
		</div>
	</div>
</div>