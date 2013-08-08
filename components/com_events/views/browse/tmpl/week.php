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

$dateFormat = '%d %b %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
        $dateFormat = 'd M Y';
        $tz = false;
}

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<?php if ($this->authorized) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add'); ?>"><?php echo JText::_('EVENTS_ADD_EVENT'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

	<ul class="sub-menu">
		<li<?php if ($this->task == 'year') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></span></a></li>
		<li<?php if ($this->task == 'month') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></span></a></li>
		<li<?php if ($this->task == 'week') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></span></a></li>
		<li<?php if ($this->task == 'day') { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day); ?>"><span><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY'); ?></span></a></li>
	</ul>

<div class="main section">
	<div class="aside">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$this->year.'&month='.$this->month.'&day='.$this->day.'&task=week'); ?>" method="get" id="event-categories">
			<fieldset>
				<select name="category">
					<option value=""><?php echo JText::_('EVENTS_ALL_CATEGORIES'); ?></option>
<?php
			if ($this->categories) {
				foreach ($this->categories as $id=>$title)
				{
?>
					<option value="<?php echo $id; ?>"<?php if ($this->category == $id) { echo ' selected="selected"'; } ?>><?php echo stripslashes($title); ?></option>
<?php
				}
			}
?>
				</select>
				<input type="submit" value="<?php echo JText::_('EVENTS_GO'); ?>" />
			</fieldset>
		</form>
			<div class="calendarwrap">
				<p class="datenav">
					<?php
					$this_date = new EventsDate();
					$this_date->setDate( $this->year, $this->month, 0 );

					$prev_year = clone($this_date);
					$prev_year->addMonths( -12 );
					$next_year = clone($this_date);
					$next_year->addMonths( +12 );
					?>
					<a class="prv" href="<?php echo JRoute::_('index.php?option='.$this->option.'&'.$prev_year->toDateURL($this->task)); ?>" title="<?php echo JText::_('EVENTS_CAL_LANG_PREVIOUSYEAR'); ?>">&lsaquo;</a> 
					<a class="nxt" href="<?php echo JRoute::_('index.php?option='.$this->option.'&'.$next_year->toDateURL($this->task)); ?>" title="<?php echo JText::_('EVENTS_CAL_LANG_NEXTYEAR'); ?>">&rsaquo;</a> 
					<?php echo $this->year; ?>
				</p>
			</div><!-- / .calendarwrap -->
			
			<div class="calendarwrap">
				<?php
				$view = new JView( array('name'=>'browse','layout'=>'calendar') );
				$view->option = $this->option;
				$view->task = $this->task;
				$view->year = $this->year;
				$view->month = $this->month;
				$view->day = $this->day;
				$view->offset = $this->offset;
				$view->shownav = 1;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$view->display();
				?>
			</div><!-- / .calendarwrap -->
			
			<div class="calendarwrap">
				<p class="datenav">
					<?php
					$this_date = new EventsDate();
					$this_date->setDate( $this->year, $this->month, $this->day );

					$prev_week = clone($this_date);
					$prev_week->addDays( -7 );
					$next_week = clone($this_date);
					$next_week->addDays( +7 );
					?>
					<a class="prv" href="<?php echo JRoute::_('index.php?option='.$this->option.'&'.$prev_week->toDateURL($this->task)); ?>" title="<?php echo JText::_('EVENTS_CAL_LANG_PREVIOUSWEEK'); ?>">&lsaquo;</a> 
					<a class="nxt" href="<?php echo JRoute::_('index.php?option='.$this->option.'&'.$next_week->toDateURL($this->task)); ?>" title="<?php echo JText::_('EVENTS_CAL_LANG_NEXTWEEK'); ?>">&rsaquo;</a> 
					<?php echo $this->startdate.' to '.$this->enddate; ?>
				</p>
			</div><!-- / .calendarwrap -->
		</div><!-- / .aside -->
		<div class="subject">
<?php
if (count($this->rows) > 0) {
?>
			<ul class="events">
<?php
	foreach ($this->rows as $rows)
	{
			if ($rows['week']['month'] == strftime( "%m", time()+($this->offset*60*60) )
			 && $rows['week']['year'] == strftime( "%Y", time()+($this->offset*60*60) )
			 && $rows['week']['day'] == strftime( "%d", time()+($this->offset*60*60) )) {
				$cls = ' class="today"';
			} else {
				$cls = '';
			}

			$countprint = 0;
?>
				<li<?php echo $cls; ?>>
					<dl class="event-details">
						<dt><?php echo JHTML::_('date',$rows['week']['year'].'-'.$rows['week']['month'].'-'.$rows['week']['day'].' 00:00:00', $dateFormat, $tz); ?></dt>
					</dl>
					<div class="ewrap">
<?php
			if (count($rows['events']) > 0) {
				$e = array();

				foreach ($rows['events'] as $row)
				{
					/*$event_up = new EventsDate( $row->publish_up );
					$event_up->day   = sprintf( "%02d", $event_up->day);
					$event_up->month = sprintf( "%02d", $event_up->month);
					$event_up->year  = sprintf( "%4d",  $event_up->year);*/

					$checkprint = new EventsRepeat($row, $rows['week']['year'], $rows['week']['month'], $rows['week']['day']);
					if ($checkprint->viewable == true) {
						$view = new JView( array('name'=>'browse','layout'=>'item') );
						$view->option = $this->option;
						$view->task = $this->task;
						//$view->event_up = $event_up;
						$view->row = $row;
						$view->fields = $this->fields;
						$view->categories = $this->categories;
						$view->showdate = 0;
						if ($this->getError()) {
							$view->setError( $this->getError() );
						}
						$e[] = $view->loadTemplate();
						//$e[] = EventsHtml::eventRow($row, $event_up, $this->option, $this->fields, $categories, 0);
						$countprint++;
					}
				}
				if ($countprint == 0) {
					echo '<p>'.JText::_('EVENTS_CAL_LANG_NO_EVENTS').'</p>'."\n";
				} else {
					echo '<ul class="events">'."\n";
					echo implode("",$e);
					echo '</ul>'."\n";
				}
	    	} else {
				echo '<p>'.JText::_('EVENTS_CAL_LANG_NO_EVENTS').'</p>'."\n";
			}
?>
					</div>
				</li>
<?php
	}
?>
			</ul>
<?php } else { ?>
			<p class="warning"><?php echo JText::_('EVENTS_CAL_LANG_NO_EVENTFOR').' <strong>'.EventsHtml::getDateFormat($this->year,$this->month,'',3).'</strong>'; ?></p>
<?php } ?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
