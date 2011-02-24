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

//set vars for button and title
$form_btn = 'Submit Event';
$form_title = 'Add Event';

//set form vals
$id = "";
$title = "";
$details = "";
$start_date = date("m/d/Y");
$start_time = "08:00";
$end_date = date("m/d/Y",strtotime("+1 DAYS"));
$end_time = "08:00";

//if there is an even passed in
if($this->event->id) {
	$form_btn = 'Update Event';
	$form_title = 'Edit Event';
	
	$id = $this->event->id;
	$title = $this->event->title;
	$details = $this->event->details;
	$start_date = date("m/d/Y", strtotime($this->event->start));
	$start_time = date("H:i", strtotime($this->event->start));
	$end_date = date("m/d/Y", strtotime($this->event->end));
	$end_time = date("H:i", strtotime($this->event->end));
}
?>

<?php if($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

	
<form name="editevent" action="" method="post" id="hubForm">
	<div class="explaination">
		<p>Here you can add events to your group calendar. These events are seperate from the HUB events.</p>
		<p><a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&active=calendar'); ?>">&lsaquo; Back to Group Calendar</a></p>
	</div>
	<fieldset>
		<h3><?php echo $form_title; ?></h3>
		<label>Event Title <span class="required">Required</span>
			<input type="text" name="event[title]" value="<?php echo $title; ?>" />
		</label>
		
		<label>Event Details: <span class="optional">Optional</span>
			<textarea name="event[details]" rows="10"><?php echo $details; ?></textarea>
		</label>
		
		<label>Event Start: <span class="required">Required</span></label>
			<input type="text" name="event[start_date]" id="event_start_date" value="<?php echo $start_date; ?>" />
			<span class="cal-date-help">( Date Format mm/dd/yyyy )<br></span>
			<input type="text" name="event[start_time]" id="event_start_time" value="<?php echo $start_time; ?>" /> ( 24 hour time ex. 17:30 for 5:30pm )
		
		<label>Event End: <span class="required">Required</span></label>
			<input type="text" name="event[end_date]" id="event_end_date" value="<?php echo $end_date; ?>" />
			<span class="cal-date-help">( Date Format mm/dd/yyyy )<br></span>
			<input type="text" name="event[end_time]" id="event_end_time" value="<?php echo $end_time; ?>" /> ( 24 hour time ex. 17:30 for 5:30pm )
		
		<input type="hidden" name="event[id]" value="<?php echo $id; ?>"
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="active" value="calendar" />
		<input type="hidden" name="task" value="save" />
	</fieldset>
	<br class="clear" />
	<p class="submit"><input type="submit" name="event_submit" value="<?php echo $form_btn; ?>" /></p>
</form>