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
     ->css('hubs')
     ->js('hubs');

$app = JFactory::getApplication();

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start=' . $this->filters['start'] : '';
$sortcol = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderby",  'orderby',  'name');
$dir     = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderdir", 'orderdir', 'asc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_hubs">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="add icon-add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>">
						<?php echo JText::_('COM_TIME_HUBS_NEW'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<table class="entries">
				<caption><?php echo JText::_('COM_TIME_HUBS_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td>
							<a <?php if ($sortcol == 'name') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=name&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_HUBS_NAME'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'liaison') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=liaison&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_HUBS_LIAISON'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'anniversary_date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=anniversary_date&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_HUBS_ANNIVERSARY_DATE'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'support_level') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=support_level&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_HUBS_SUPPORT_LEVEL'); ?>
							</a>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php if (count($this->hubs) > 0) : ?>
						<?php foreach ($this->hubs as $hub) : ?>
						<tr>
							<td>
								<a class="view" id="<?php echo $hub->id; ?>" href="<?php echo JRoute::_($base . '&task=readonly&id=' . $hub->id); ?>">
									<?php echo $hub->name; ?>
								</a>
							</td>
							<td><?php echo $hub->liaison; ?></td>
							<td><?php echo ($hub->anniversary_date != '0000-00-00') ? JHTML::_('date', $hub->anniversary_date, 'm/d/y', null) : ''; ?></td>
							<td><?php echo $hub->support_level; ?></td>
						</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="7" class="no_hubs"><?php echo JText::_('COM_TIME_HUBS_NONE_TO_DISPLAY'); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<form action="<?php echo JRoute::_($base); ?>">
				<?php echo $this->pageNav; ?>
			</form>
		</div>
	</section>
</div>