<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$this->css();
?>
<?php if ($this->recipient === '') { ?>
	<p class="error"><?php echo JText::_('MOD_RAPID_CONTACT_ERROR_NO_RECIPIENT'); ?></p>
<?php } else { ?>
	<form method="post" action="<?php echo $this->url; ?>" id="contactform" class="<?php echo $this->mod_class_suffix; ?>">
		<fieldset>
			<legend><?php echo JText::_('MOD_RAPID_CONTACT_FORM'); ?></legend>

			<?php if ($this->replacement) { ?>
				<p class="passed"><?php echo $this->replacement; ?></p>
			<?php } ?>
			<?php if ($this->pre_text) { ?>
				<p><?php echo $this->pre_text; ?></p>
			<?php } ?>
			<?php if ($this->error) { ?>
				<p class="error"><?php echo $this->error; ?></p>
			<?php } ?>

			<label for="contact-name">
				<?php echo $this->name_label; ?>
			</label>
			<span class="input">
				<input type="text" id="contact-name" name="rp[name]" value="<?php echo $this->escape($this->posted['name']); ?>" />
			</span>

			<label for="contact-email">
				<?php echo $this->email_label; ?>
			</label>
			<span class="input">
				<input type="text" id="contact-email" name="rp[email]" value="<?php echo $this->escape($this->posted['email']); ?>" />
			</span>

			<label for="contact-subject">
				<?php echo $this->subject_label; ?>
			</label>
			<span class="input">
				<input type="text" id="contact-subject" name="rp[subject]" value="<?php echo $this->escape($this->posted['subject']); ?>" />
			</span>

			<label for="contact-comments">
				<?php echo $this->message_label; ?>
			</label>
			<span class="input">
				<textarea name="rp[message]" id="contact-comments" cols="35" rows="10"><?php echo $this->escape($this->posted['message']); ?></textarea>
			</span>

			<?php if ($this->enable_anti_spam) { ?>
				<label for="contact-antispam">
					<?php echo $this->anti_spam_q; ?>
				</label>
				<span class="input">
					<input type="text" id="contact-antispam" name="rp[anti_spam_answer]" />
				</span>
			<?php } ?>

			<span class="submit"><input type="submit" name="submit" value="<?php echo $this->button_text; ?>" /></span>
		</fieldset>
	</form>
<?php } ?>