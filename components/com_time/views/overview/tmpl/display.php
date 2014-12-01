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

$this->css()
     ->css('overview')
     ->css('fullcalendar')
     ->js('overview');

\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect.css');
\Hubzero\Document\Assets::addSystemScript('flot/jquery.flot.min');
\Hubzero\Document\Assets::addSystemScript('flot/jquery.flot.pie.min');
\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemScript('moment.min');
\Hubzero\Document\Assets::addSystemScript('jquery.fullcalendar.min');

$utc   = JFactory::getDate();
$now   = JHTML::_('date', $utc, JText::_('g:00a'));
$then  = JHTML::_('date', strtotime($now . ' + 1 hour'), JText::_('g:00a'));
$start = JHTML::_('date', $utc, JText::_('G'));
$end   = JHTML::_('date', strtotime($now . ' + 1 hour'), JText::_('G'));

?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_overview">
		<div class="overview-container">
			<div class="section-header"><h3><?php echo JText::_('COM_TIME_OVERVIEW_TODAY'); ?></h3></div>
			<div class="calendar"></div>
			<div class="details">
				<div class="details-inner">
					<div class="details-explanation">
						<p>
							Drag and select a time-range from the calendar on the left to create a new time entry,
							or click an existing entry to edit.
						</p>
					</div>
					<form action="<?php echo JRoute::_('/api/time/postRecord'); ?>" class="details-data" method="POST">
						<div class="grouping" id="hub-group">
							<label for="hub"><?php echo JText::_('COM_TIME_OVERVIEW_HUB'); ?>:</label>
							<?php echo TimeHTML::buildSmartHubsList($this->controller); ?>
						</div>
						<div class="grouping" id="task-group">
							<label for="task"><?php echo JText::_('COM_TIME_OVERVIEW_TASK'); ?>:</label>
							<?php echo TimeHTML::buildSmartTasksList($this->controller); ?>
						</div>

						<div class="grouping" id="description-group">
							<label for="description"><?php echo JText::_('COM_TIME_OVERVIEW_DESCRIPTION'); ?>:</label>
							<textarea name="description" id="description" rows="6" cols="50" tabIndex="3"></textarea>
						</div>

						<input type="hidden" name="id" class="details-id" value="" />
						<input type="hidden" name="start" class="details-start" value="" />
						<input type="hidden" name="end" class="details-end" value="" />

						<p class="submit">
							<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_OVERVIEW_SAVE'); ?>" tabIndex="4" />
							<a href="#" class="details-cancel">
								<button type="button" class="btn btn-secondary">
									<?php echo JText::_('COM_TIME_OVERVIEW_CANCEL'); ?>
								</button>
							</a>
						</p>
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="plots-container">
			<div class="hourly-wrap">
				<div class="section-header"><h3><?php echo JText::_('COM_TIME_OVERVIEW_HOURS_THIS_WEEK'); ?></h3></div>
				<div class="hourly">
					<div class="pie-half1">
					<div class="pie-half2">
						<div class="inner-pie">
							<div class="hours">0hrs</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			<div class="week-overview-wrap">
				<div class="section-header"><h3><?php echo JText::_('COM_TIME_OVERVIEW_ENTRIES_THIS_WEEK'); ?></h3></div>
				<div class="week-overview"></div>
			</div>
		</div>
	</section>
</div>