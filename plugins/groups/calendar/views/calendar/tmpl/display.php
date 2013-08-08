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

<?php if (in_array($this->juser->get('id'), $this->members)) : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add" title="<?php echo JText::_('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=add'); ?>">
				<?php echo JText::_('PLG_GROUPS_CALENDAR_ADD_NEW_LINK_TEXT'); ?>
			</a>
			<?php if ($this->authorized == 'manager') : ?>
				<a class="icon-date btn date" title="<?php echo JText::_('Manage Calendars'); ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=calendars'); ?>">
					<?php echo JText::_('Manage Calendars'); ?>
				</a>
			<?php endif; ?>
		</li>
	</ul>
<?php endif; ?>

<div class="" id="calendar-box">
	<form id="goto_date" name="goto_date" action="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar'); ?>" method="get">
		<div id="calendar-nav">
			<div class="date-title">
				<?php echo date("F Y", mktime(0,0,0,$this->month,1,$this->year)); ?>
			</div>
			<select name="month" id="month-picker">
				<?php 
					for($i=1, $n=12; $i<=$n; $i++)
					{
						$sel = ($i == $this->month) ? 'selected' : '';
						$val = (strlen((string)$i) == 1) ? '0' . $i : $i;
						echo "<option {$sel} value=\"{$val}\">" . date("F",mktime(0,0,0,$i,1,2020)) . "</option>";
					}
				?>
			</select>
			<select name="year" id="year-picker">
				<?php
					$year_start = date("Y");
					$year_end = $year_start + 15;
					for ($i=($year_start-1); $i<$year_end; $i++) 
					{
						$sel = ($i == $this->year) ? 'selected' : '';
						echo "<option {$sel} value=\"{$i}\">" . date("Y",mktime(0,0,0,1,1,$i)) . "</option>";
					}
				?>
			</select>
			<noscript>
				<input type="submit" value="Go" />
			</noscript>
			<!--[if IE 8]>
				<input type="submit" value="Go" />
			<![endif]-->
			
			<label>
				<!--[if IE 8]>
					<input type="submit" value="Go" />
				<![endif]-->
				<select name="calendar" id="calendar-picker">
					<option value="0"><?php echo JText::_('All Calendars'); ?></option>
					<?php foreach ($this->calendars as $calendar) : ?>
						<?php $sel = ($calendar->id == $this->calendar) ? 'selected="selected"' : ''; ?>
						<option <?php echo $sel; ?> data-img="/plugins/groups/calendar/images/swatch-<?php echo ($calendar->color) ? strtolower($calendar->color) : 'gray'; ?>.png" value="<?php echo $calendar->id; ?>"><?php echo $calendar->title; ?></option>
					<?php endforeach; ?>
				</select>
			</label>
			
			<br class="clear" />
		</div>
		
		<?php echo $this->calendarHTML; ?>	
		<?php
			$thisCalendar            = new stdClass;
			$thisCalendar->id        = 0;
			$thisCalendar->published = 1;
			$thisCalendar->title     = "All Calendars";
			foreach($this->calendars as $calendar)
			{
				if ($calendar->id == $this->calendar)
				{
					$thisCalendar = $calendar;
				}
			}
		?>
		<?php if ($this->params->get('allow_subscriptions', 1)) : ?>
			<div id="subscribe-nav">
				<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=groups&extension=calendar&page=subscriptions') ;?>">
					<?php echo JText::_('Need Help?'); ?>
				</a>
				<div class="title">
					<?php echo JText::_('Subscribe'); ?>
				</div>
			</div>
			<div id="subscribe">
				
				<p class="info">
					<?php echo JText::_('If you are prompted to enter a username & password when subscribing to a calendar, enter your HUB credentials.'); ?>
				</p>
				<br />
				<p><strong><?php echo JText::_('Select the calendars you wish to subscribe to:'); ?></strong></p>
				
				<label>
					<input type="checkbox" value="0" checked="checked" />
					<img src="/plugins/groups/calendar/images/swatch-gray.png" />
					<?php echo JText::_('Uncategorized Events'); ?>
				</label>
				<?php $cals = array(0); ?>
				<?php foreach ($this->calendars as $calendar) : ?>
					<?php
						$enabled = false;
						if ($calendar->published == 1)
						{
							$enabled = true;
							$cals[] = $calendar->id;
						}
					?>
					<label <?php echo (!$enabled) ? 'class="disabled"' : '' ?>>
						<input <?php echo (!$enabled) ? 'disabled="disabled"' : 'checked="checked"'; ?> name="subscribe[]"  type="checkbox" value="<?php echo $calendar->id; ?>" />
						<?php if ($calendar->color) : ?>
							<img src="/plugins/groups/calendar/images/swatch-<?php echo $calendar->color; ?>.png" />
						<?php else : ?>
							<img src="/plugins/groups/calendar/images/swatch-gray.png" />
						<?php endif; ?>
						<?php echo $calendar->title; ?>
						<?php
							if(!$enabled)
							{
								echo JText::_('(Calendar is not publishing events.)');
							}
						?>
					</label>
				<?php endforeach; ?>
				
				<?php
					$link = $_SERVER['HTTP_HOST'] . DS . 'groups' . DS . $this->group->get('cn') . DS . 'calendar' . DS . 'subscribe' . DS . implode(',', $cals) . '.ics';
					$httpsLink = 'https://' . $link;
					$webcalLink = 'webcal://' . $link;
				?>
				<br />
				<label id="subscribe-link"><strong><?php echo JText::_('Click the subscribe button to the right or add the link below to add as a calendar subscription:'); ?></strong>
					<input type="text" value="<?php echo $httpsLink; ?>" /> 
					<a class="btn feed download https" href="<?php echo $httpsLink; ?>">Download</a>
					<a class="btn feed subscribe-webcal webcal" href="<?php echo $webcalLink; ?>">Subscribe</a>
				</label>
			</div>
		<?php endif; ?>
</div><!-- / subject -->
