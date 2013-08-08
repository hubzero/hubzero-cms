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
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=calendars'); ?>">
			<?php echo JText::_('Back to Manage Calendars'); ?>
		</a>
	</li>
</ul>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=savecalendar'); ?>" id="hubForm" method="post">
	
	<fieldset>
		<legend><?php echo JText::_('Group Calendar'); ?></legend>
		
		<label><?php echo JText::_('Title:'); ?> <span class="required">Required</span>
			<input type="text" name="calendar[title]" value="<?php echo $this->calendar->title; ?>" />
		</label>
		
		<label><?php echo JText::_('URL:'); ?> <span class="optional">Optional</span>
			<input type="text" name="calendar[url]" value="<?php echo $this->calendar->url; ?>" />
			<span class="hint"><?php echo JText::_('This is used to fetch remote calendar events from other services such as a Google Calendar.'); ?></span>
		</label>
		
		<label><?php echo JText::_('Color:'); ?> <span class="optional">Optional</span>
			<?php $colors = array('red','orange','yellow','green','blue','purple','brown'); ?>
			<select name="calendar[color]">
				<option value="">- Select Color &mdash;</option>
				<?php foreach($colors as $color) : ?>
					<?php $sel = ($this->calendar->color == $color) ? 'selected="selected"' : ''; ?>
					<option <?php echo $sel; ?> value="<?php echo $color; ?>"><?php echo ucfirst($color); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		
		<label><?php echo JText::_('Publish Events to Subscribers?:'); ?>
			<select name="calendar[published]">
				<option value="1">Yes</option>
				<option <?php echo ($this->calendar->published == 0) ? 'selected="selected"' : ''; ?> value="0">No</option>
			</select>
		</label>
	</fieldset>
	<br class="clear" />
	<p class="submit">
		<input type="submit" value="Submit" />
	</p>
	
	<input type="hidden" name="option" value="com_groups" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="calendar" />
	<input type="hidden" name="action" value="savecalendar" />
	<input type="hidden" name="calendar[id]" value="<?php echo $this->calendar->id; ?>" />
</form>