<?php 
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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
