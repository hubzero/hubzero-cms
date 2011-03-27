<?php 
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2009-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php if ($modrapidcontact->recipient === '') { ?>
	<p class="error">No recipient specified</p>
<?php } else { ?>
<form method="post" action="<?php echo $modrapidcontact->url; ?>" id="contactform" class="<?php echo $modrapidcontact->mod_class_suffix; ?>">
	<fieldset>
		<legend>Contact Form</legend>

<?php if ($modrapidcontact->replacement) { ?>
		<p class="passed"><?php echo $modrapidcontact->replacement; ?></p>
<?php } ?>
<?php if ($modrapidcontact->pre_text) { ?>
		<p><?php echo $modrapidcontact->pre_text; ?></p>
<?php } ?>
<?php if ($modrapidcontact->error) { ?>
		<p class="error"><?php echo $modrapidcontact->error; ?></p>
<?php } ?>

		<label for="contact-name">
			<?php echo $modrapidcontact->name_label; ?>
		</label>
		<span class="input">
			<input type="text" id="contact-name" name="rp[name]" value="<?php echo $modrapidcontact->posted['name']; ?>" />
		</span>

		<label for="contact-email">
			<?php echo $modrapidcontact->email_label; ?>
		</label>
		<span class="input">
			<input type="text" id="contact-email" name="rp[email]" value="<?php echo $modrapidcontact->posted['email']; ?>" />
		</span>

		<label for="contact-subject">
			<?php echo $modrapidcontact->subject_label; ?>
		</label>
		<span class="input">
			<input type="text" id="contact-subject" name="rp[subject]" value="<?php echo $modrapidcontact->posted['subject']; ?>" />
		</span>

		<label for="contact-comments">
			<?php echo $modrapidcontact->message_label; ?>
		</label>
		<span class="input">
			<textarea name="rp[message]" id="contact-comments" cols="35" rows="10"><?php echo $modrapidcontact->posted['message']; ?></textarea>
		</span>

<?php if ($modrapidcontact->enable_anti_spam) { ?>
		<label for="contact-antispam">
			<?php echo $modrapidcontact->anti_spam_q; ?>
		</label>
		<span class="input">
			<input type="text" id="contact-antispam" name="rp[anti_spam_answer]" />
		</span>
<?php } ?>

		<span class="submit"><input type="submit" name="submit" value="<?php echo $modrapidcontact->button_text; ?>" /></span>
	</fieldset>
</form>
<?php } ?>

