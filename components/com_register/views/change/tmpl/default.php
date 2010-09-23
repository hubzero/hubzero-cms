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

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->success) { ?>
	<p class="passed"><?php echo JText::_('Your account has been updated successfully.'); ?></p>
<?php } else { ?>
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=change'); ?>" method="post" id="hubForm">
<?php if (($this->email_confirmed != 1) && ($this->email_confirmed != 3)) { ?>
		<div class="explaination">
			<h4>Never received or cannot find the confirmation email?</h4>
			<p>You can have a new confirmation email sent to "<?php echo htmlentities($this->email,ENT_COMPAT,'UTF-8'); ?>" by <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resend&return='.$this->return); ?>">clicking here</a>.</p>
		</div>
<?php } ?>
		<fieldset>
			<h3><?php echo JText::_('Correct Email Address'); ?></h3>
			<label<?php if (!$this->email || !Hubzero_Registration_Helper::validemail($this->email)) { echo' class="fieldWithErrors"'; } ?>>
				<?php echo JText::_('Valid E-mail:'); ?>
				<input name="email" id="email" type="text" size="51" value="<?php echo htmlentities($email,ENT_COMPAT,'UTF-8'); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="change" />
		<input type="hidden" name="act" value="show" />
		<p class="submit"><input type="submit" name="update" value="<?php echo JText::_('Update Email'); ?>" /></p>
	</form>
<?php } ?>
</div><!-- / .section -->