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

//
$thisCalendar            = new stdClass;
$thisCalendar->id        = 0;
$thisCalendar->published = 1;
$thisCalendar->title     = "All Calendars";
foreach($this->calendars as $calendar)
{
	if ($calendar->get('id') == $this->calendar)
	{
		$thisCalendar = $calendar;
	}
}
?>

<div class="subject group-calendar-subject subscribe">
	<div class="container">
		<h3>
			<?php echo JText::_('Subscribe'); ?>
			<a class="popup subscribe-help" href="<?php echo JRoute::_('index.php?option=com_help&component=groups&extension=calendar&page=subscriptions') ;?>">
				<?php echo JText::_('Need Help?'); ?>
			</a>
		</h3>

		<div class="subscribe-content">
			<p class="info">
				<?php echo JText::_('If you are prompted to enter a username & password when subscribing to a calendar, enter your HUB credentials.'); ?>
			</p>

			<p>
				<strong><?php echo JText::_('Select the calendars you wish to subscribe to:'); ?></strong>
			</p>

			<label>
				<input type="checkbox" value="0" checked="checked" />
				<img src="/plugins/groups/calendar/images/swatch-gray.png" />
				<?php echo JText::_('Uncategorized Events'); ?>
			</label>

			<?php $cals = array(0); ?>
			<?php foreach ($this->calendars as $calendar) : ?>
				<?php
					$enabled = false;
					if ($calendar->get('published') == 1)
					{
						$enabled = true;
						$cals[] = $calendar->get('id');
					}
				?>
				<label <?php echo (!$enabled) ? 'class="disabled"' : '' ?>>
					<input <?php echo (!$enabled) ? 'disabled="disabled"' : 'checked="checked"'; ?> name="subscribe[]"  type="checkbox" value="<?php echo $calendar->get('id'); ?>" />
					<?php if ($calendar->get('color')) : ?>
						<img src="/plugins/groups/calendar/images/swatch-<?php echo $calendar->get('color'); ?>.png" />
					<?php else : ?>
						<img src="/plugins/groups/calendar/images/swatch-gray.png" />
					<?php endif; ?>
					<?php echo $calendar->get('title'); ?>
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
	</div>
</div>