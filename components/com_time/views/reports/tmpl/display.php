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
     ->css('reports')
     ->js('reports');

$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
$options = array();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_reports">
		<?php if (count($this->reports) > 0) : ?>
			<div class="report-select-type">
				<form action="<?php echo JRoute::_($base); ?>">
					<label for="report-type"><?php echo JText::_('COM_TIME_REPORTS_SELECT_REPORT_TYPE'); ?>: </label>
					<?php foreach ($this->reports as $report) : ?>
						<?php $options[] = JHTML::_('select.option', $report->name, ucwords($report->name), 'value', 'text'); ?>
					<?php endforeach; ?>
					<?php echo JHTML::_('select.genericlist', $options, 'report_type', null, 'value', 'text', $this->report_type); ?>
					<button class="btn btn-success"><?php echo JText::_('COM_TIME_REPORTS_BEGIN'); ?></button>
				</form>
			</div>
			<div class="report-content">
				<?php if (isset($this->content)) : ?>
					<?php echo (isset($this->content)) ? $this->content : ''; ?>
				<?php else : ?>
					<div class="make-selection">
						<?php echo JText::_('COM_TIME_REPORTS_PLEASE_SELECT_REPORT_TYPE'); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="no_reports">
				<?php echo JText::_('COM_TIME_REPORTS_NO_REPORT_TYPES'); ?>
			</div>
		<?php endif; ?>
	</section>
</div>