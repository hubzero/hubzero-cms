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
     ->js('overview');

// Get the Joomla document and add google JS API
$juser = JFactory::getUser();
$doc   = JFactory::getDocument();
$doc->addScript('https://www.google.com/jsapi');

// Generate script to draw chart and push to the page
$column  = ChartsHTML::drawColumn();
$pieHubs = ChartsHTML::drawPieHubs();
$pieUser = ChartsHTML::drawPieUser($juser->get('id'));
$bar     = ChartsHTML::drawBar();
$doc->addScriptDeclaration($column);
$doc->addScriptDeclaration($pieHubs);
$doc->addScriptDeclaration($pieUser);
$doc->addScriptDeclaration($bar);
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_overview">
		<table>
			<thead>
				<tr>
					<td><?php echo JText::_('COM_TIME_OVERVIEW_ACTIVE_HUBS'); ?></td>
					<td><?php echo JText::_('COM_TIME_OVERVIEW_ACTIVE_TASKS'); ?></td>
					<td><?php echo JText::_('COM_TIME_OVERVIEW_TOTAL_HOURS'); ?></td>
				<tr>
			</thead>
			<tr class="data">
				<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=hubs'); ?>"><?php echo $this->activeHubs; ?></a></td>
				<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=tasks'); ?>"><?php echo $this->activeTasks; ?></td>
				<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=records'); ?>"><?php echo $this->totalHours; ?></td>
			</tr>
		</table>

		<div id="chart_div_pie_hubs"></div>
		<div id="chart_div_pie_user"></div>
		<div class="clear"></div>
		<div id="chart_div_bar"></div>
		<div id="chart_div_column"></div>
	</section>
</div>