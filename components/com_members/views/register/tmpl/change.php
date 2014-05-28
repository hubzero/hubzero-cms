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
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->success) { ?>
	<p class="passed"><?php echo JText::_('Your account has been updated successfully.'); ?></p>
<?php } else { ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=change'); ?>" method="post" id="hubForm">
	<?php if (($this->email_confirmed != 1) && ($this->email_confirmed != 3)) { ?>
		<div class="explaination">
			<h4>Never received or cannot find the confirmation email?</h4>
			<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resend&return=' . $this->return); ?>">clicking here</a>.</p>
		</div>
	<?php } ?>
		<fieldset>
			<h3><?php echo JText::_('Correct Email Address'); ?></h3>
			<label<?php if (!$this->email || !MembersHelperUtility::validemail($this->email)) { echo' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('Valid E-mail:'); ?>
				<input name="email" id="email" type="text" size="51" value="<?php echo $this->escape($this->email); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="change" />
		<input type="hidden" name="act" value="show" />

		<p class="submit"><input type="submit" name="update" value="<?php echo JText::_('Update Email'); ?>" /></p>
	</form>
<?php } ?>
</section><!-- / .section -->
