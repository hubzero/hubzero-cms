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

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('records')
     ->js('records');

$app = JFactory::getApplication();

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start=' . $this->filters['start'] : '';
$sortcol = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderby",  'orderby',  'id');
$dir     = $app->getUserStateFromRequest("{$this->option}.{$this->controller}.orderdir", 'orderdir', 'desc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
$base    = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_records">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>">
						<?php echo JText::_('COM_TIME_RECORDS_NEW'); ?>
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
			<form method="get" action="<?php echo JRoute::_($base); ?>">
				<div class="search-box">
					<a href="<?php echo JRoute::_($base . '&search='); ?>">
						<button type="button" class="clear-button btn btn-warning"><?php echo JText::_('COM_TIME_RECORDS_CLEAR'); ?></button>
					</a>
					<input class="search-submit btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_RECORDS_SEARCH'); ?>" />
					<fieldset class="search-text">
						<input id="search-input" type="text" name="search" placeholder="<?php echo JText::_('COM_TIME_RECORDS_SEARCH_EXPLANATION'); ?>" value="<?php
								echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
					</fieldset>
				</div><!-- / .search-box -->
			</form>
			<form method="get" action="<?php echo JRoute::_($base); ?>">
				<div id="add-filters">
					<p>Filter results:
						<select name="q[column]" id="filter-column">
							<?php foreach ($this->cols as $c) : ?>
								<option value="<?php echo $c['raw']; ?>"><?php echo $c['human']; ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo $this->operators; ?>
						<select name="q[value]" id="filter-value">
						</select>
						<input id="filter-submit" class="btn btn-success" type="submit" value="<?php echo JText::_('+ Add filter'); ?>" />
						<input type="hidden" value="time_records" id="filter-table" />
					</p>
				</div><!-- / .filters -->
			</form>
			<?php if (!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0]))) : ?>
				<div id="applied-filters">
					<p>Applied filters:</p>
					<ul class="filters-list">
						<?php if (!empty($this->filters['q'])) : ?>
							<?php foreach ($this->filters['q'] as $q) : ?>
								<li>
									<a href="<?php echo JRoute::_($base . '&q[column]=' . $q['column'] .
										'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]'); ?>"
										class="filters-x">x
									</a>
									<i><?php echo $q['human_column'] . ' ' . $q['human_operator']; ?></i>: <?php echo $q['human_value']; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (is_array($this->filters['search']) && !empty($this->filters['search'][0])) : ?>
							<li>
								<a href="<?php echo JRoute::_($base . '&search='); ?>" class="filters-x">x</a>
								<i>Search</i>: <?php echo implode(" ", $this->filters['search']); ?>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
			<table class="entries">
				<caption><?php echo JText::_('COM_TIME_RECORDS_CAPTION'); ?></caption>
				<thead>
					<tr>
						<td>
							<a <?php if ($sortcol == 'id') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=id&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_RECORDS_ID'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'uname') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=uname&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_RECORDS_USER'); ?>
							</a>
						</td>
						<td class="col-time">
							<a <?php if ($sortcol == 'time') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=time&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_RECORDS_TIME'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'date') { echo ($dir == 'asc') ? 'class="sort_asc num"' : 'class="sort_desc num"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=date&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_RECORDS_DATE'); ?>
							</a>
						</td>
						<td>
							<a <?php if ($sortcol == 'pname') { echo ($dir == 'asc') ? 'class="sort_asc alph"' : 'class="sort_desc alph"'; } ?>
								href="<?php echo JRoute::_($base . '&orderby=pname&orderdir=' . $newdir); ?>">
									<?php echo JText::_('COM_TIME_RECORDS_TASK'); ?>
							</a>
						</td>
						<td><?php echo JText::_('COM_TIME_RECORDS_DESCRIPTION'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php if (count($this->records) > 0) {
						foreach ($this->records as $record) {
							// Cut the description off if it's too long and highlight search terms
							$originalDescription = $record->description;
							$record->description = \Hubzero\Utility\String::truncate($record->description, 25);
							$record = TimeFilters::highlight($record, $this->filters);
							?>
						<tr>
							<td>
								<a href="<?php echo JRoute::_($base . '&task=readonly&id=' . $record->id); ?>">
									<?php echo $record->id; ?>
								</a>
							</td>
							<td><?php echo $record->uname; ?></td>
							<td class="col-time"><?php echo $record->time; ?></td>
							<td><?php echo JHTML::_('date', $record->date, 'm/d/y', null); ?></td>
							<td><?php echo $record->pname; ?></td>
							<td class="last" title="<?php echo $originalDescription; ?>"><?php echo $record->description; ?></td>
						</tr>
						<?php } // close foreach
					} else { // else count > 0 ?>
						<tr>
							<td colspan="7" class="no_records"><?php echo JText::_('COM_TIME_RECORDS_NONE_TO_DISPLAY'); ?></td>
						</tr>
					<?php } // close else ?>
				</tbody>
			</table>
			<form action="<?php echo JRoute::_($base); ?>">
				<?php echo $this->pageNav; ?>
			</form>
		</div>
	</section>
</div>