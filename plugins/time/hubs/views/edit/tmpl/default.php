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

// if count of contacts is greater than zero, set ccount true
$ccount = (count($this->contacts) > 0) ? true : false;
?>

<div id="dialog-confirm"></div>

<div id="plg_time_hubs">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="back icon-back btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs'.$this->start); ?>">
					<?php echo JText::_('PLG_TIME_HUBS_ALL_HUBS'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="outer-container">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=save'); ?>" method="post">
			<div class="title"><?php echo JText::_('PLG_TIME_HUBS_' . strtoupper($this->action)) . ': ' . $this->row->name; ?></div>
			<div class="grouping" id="name-group">
				<label for="name"><?php echo JText::_('PLG_TIME_HUBS_NAME'); ?>:</label>
				<input type="text" name="hub[name]" id="name" value="<?php echo htmlentities(stripslashes($this->row->name), ENT_QUOTES); ?>" size="50" />
			</div>

			<label for="contact"><?php echo JText::_('PLG_TIME_HUBS_CONTACTS'); ?>:</label>
			<?php $i = 0;
				if($ccount)
				{
					foreach($this->contacts as $contact)
					{ ?>
						<div class="grouping contact-grouping" id="contact-<?php echo $contact->id; ?>-group">
							<input type="text" name="contact[<?php echo $contact->id; ?>][name]" id="" value="<?php echo htmlentities(stripslashes($contact->name), ENT_QUOTES); ?>" />
							<input type="text" name="contact[<?php echo $contact->id; ?>][phone]" id="" value="<?php echo htmlentities(stripslashes($contact->phone), ENT_QUOTES); ?>" />
							<input type="text" name="contact[<?php echo $contact->id; ?>][email]" id="" value="<?php echo htmlentities(stripslashes($contact->email), ENT_QUOTES); ?>" />
							<input type="text" name="contact[<?php echo $contact->id; ?>][role]" id="" value="<?php echo htmlentities(stripslashes($contact->role), ENT_QUOTES); ?>" />
							<input type="hidden" name="contact[<?php echo $contact->id; ?>][id]" value="<?php echo $contact->id; ?>" />
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=deletecontact&id='.$contact->id); ?>" class="delete_contact" title="Delete contact"></a>
						</div>
						<?php $i++;
					} // close foreach contacts
				} // close if ccount ?>
				<div class="grouping" id="new-contact-group">
					<input type="text" name="contact[new][name]" id="new_name" value="name" class="new_contact" />
					<input type="text" name="contact[new][phone]" id="new_phone" value="phone" class="new_contact" />
					<input type="text" name="contact[new][email]" id="new_email" value="email" class="new_contact" />
					<input type="text" name="contact[new][role]" id="new_role" value="role" class="new_contact" />
					<a href="#" id="save_new_contact" class="save_contact" title="Save contact"></a>
				</div>

				<div class="grouping" id="liaison-group">
					<label for="liaison"><?php echo JText::_('PLG_TIME_HUBS_LIAISON'); ?>:</label>
					<input type="text" name="hub[liaison]" id="liaison" value="<?php echo htmlentities(stripslashes($this->row->liaison), ENT_QUOTES); ?>" size="50" />
				</div>

				<div class="grouping" id="anniversary-group">
					<label for="anniversary_date"><?php echo JText::_('PLG_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
					<input class="hadDatepicker" type="text" name="hub[anniversary_date]" id="anniversary_date" value="<?php echo htmlentities(stripslashes($this->row->anniversary_date), ENT_QUOTES); ?>" size="50" />
				</div>

				<div class="grouping" id="support-group">
					<label for="support_level"><?php echo JText::_('PLG_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
					<?php echo $this->slist; ?>
				</div>

				<div class="grouping" id="notes-group">
					<label for="notes"><?php echo JText::_('PLG_TIME_HUBS_NOTES'); ?>:</label>
					<?php
						// Import the HUBzero wiki editor
						ximport('Hubzero_Wiki_Editor');
						$editor = Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('hub[notes]', 'notes', stripslashes($this->row->notes), '', '50', '6');
					?>
				</div>

			<input type="hidden" name="hub[id]" value="<?php echo $this->row->id; ?>" id="hub_id" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="active" value="hubs" />
			<input type="hidden" name="action" value="save" />
	</div><!-- //container -->
			<p class="submit">
				<input type="submit" value="<?php echo JText::_('PLG_TIME_HUBS_SUBMIT'); ?>" />
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs'.$this->start); ?>"><span class="cancel-button"><?php echo JText::_('PLG_TIME_HUBS_CANCEL'); ?></span></a>
			</p>
		</form>
</div>