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

$dateFormat = '%m/%d/%Y';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = null;
}

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start='.$this->filters['start'] : '';
$imgasc  = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_asc.png">';
$imgdesc = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_desc.png">';
$sortcol = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderby", 'orderby', 'id');
$dir     = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderdir", 'orderdir', 'desc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
?>

<div id="plg_time_records">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=new'); ?>">
					<?php echo JText::_('PLG_TIME_RECORDS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="container">
		<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'); ?>">
			<div class="search-box">
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&search='); ?>">
					<span class="clear-button"><?php echo JText::_('PLG_TIME_RECORDS_CLEAR'); ?></span>
				</a>
				<input class="search-submit" type="submit" value="<?php echo JText::_('PLG_TIME_RECORDS_SEARCH'); ?>" />
				<fieldset class="search-text">
					<input id="search-input" type="text" name="search" placeholder="<?php echo JText::_('PLG_TIME_RECORDS_SEARCH_EXPLANATION'); ?>" value="<?php 
							echo (is_array($this->filters['search']) && !empty($this->filters['search'][0])) ? implode(" ", $this->filters['search']) : ''; ?>" />
				</fieldset>
			</div><!-- / .search-box -->
		</form>
		<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'); ?>">
			<div id="add-filters">
				<p>Filter results:
					<select name="q[column]" id="filter-column">
						<option value="user_id">User</option>
						<option value="task_id">Task</option>
					</select>
					<select name="q[operator]" id="filter-operator">
						<option value="e">equals (&#61;)</option>
						<option value="de">doesn't equal (&#8800;)</option>
						<option value="gt">is greater than (&#62;)</option>
						<option value="lt">is less than (&#60;)</option>
						<option value="gte">is greater than or equal to (&#62;&#61;)</option>
						<option value="lte">is less than or equal to (&#60;&#61;)</option>
					</select>
					<select name="q[value]" id="filter-value">
					</select>
					<input id="filter-submit" type="submit" value="<?php echo JText::_('+ Add filter'); ?>" />
				</p>
			</div><!-- / .filters -->
		</form>
		<?php
			if(!empty($this->filters['q']) || (is_array($this->filters['search']) && !empty($this->filters['search'][0])))
			{
				echo '<div id="applied-filters">';
				echo '<p>Applied filters:</p>';
				echo '<ul class="filters-list">';
				if(!empty($this->filters['q']))
				{
					foreach($this->filters['q'] as $q)
					{
						echo '<li><a href="' .
							JRoute::_('index.php?option='.$this->option.'&active=records&q[column]=' . $q['column'] .
								'&q[operator]=' . $q['operator'] . '&q[value]=' . $q['value'] . '&q[delete]') .
							'" class="filters-x">x</a><i>' . $q['human_column'] . ' ' . $q['human_operator'] . '</i>: ' .
							$q['human_value'] . '</li>';
					}
				}
				if(is_array($this->filters['search']) && !empty($this->filters['search'][0]))
				{
					echo '<li><a href="' .
						JRoute::_('index.php?option='.$this->option.'&active=records&search=') .
						'" class="filters-x">x</a><i>Search</i>: ' . implode(" ", $this->filters['search']) . '</li>';
				}
				echo '</ul>';
				echo '</div>';
			}
		?>
		<table class="entries">
			<caption><?php echo JText::_('PLG_TIME_RECORDS_CAPTION'); ?></caption>
			<thead>
				<tr>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=id&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_RECORDS_ID'); ?>
						</a>
						<?php if($sortcol == 'id') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=uname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_RECORDS_USER'); ?>
						</a>
						<?php if($sortcol == 'uname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td class="col-time">
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=time&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_RECORDS_TIME'); ?>
						</a>
						<?php if($sortcol == 'time') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=date&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_RECORDS_DATE'); ?>
						</a>
						<?php if($sortcol == 'date') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active='.$this->active.'&orderby=pname&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_RECORDS_TASK'); ?>
						</a>
						<?php if($sortcol == 'pname') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td><?php echo JText::_('PLG_TIME_RECORDS_DESCRIPTION'); ?></td>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->records) > 0) {
					foreach($this->records as $record) { 
						// Cut the description off if it's too long
						if(strlen($record->description) > 25)
						{
							$record->description = trim(substr($record->description,0,25))."...";
						}
						// Highlight search words if set
						if(!empty($this->filters['search']))
						{
							foreach($this->filters['search'] as $arg)
							{
								$record->description = str_ireplace($arg, "<span class=\"highlight\">{$arg}</span>", $record->description);
								$record->pname       = str_ireplace($arg, "<span class=\"highlight\">{$arg}</span>", $record->pname);
							}
						}
						?>
					<tr<?php if($record->billed == 1) { echo ' class="finalized" title="'.JText::_('PLG_TIME_RECORDS_FINALIZED').'"'; } ?>>
						<td>
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=readonly&id='.$record->id); ?>">
								<?php echo $record->id; ?>
							</a>
						</td>
						<td><?php echo $record->uname; ?></td>
						<td class="col-time"><?php echo $record->time; ?></td>
						<td><?php echo JHTML::_('date', $record->date, $dateFormat, $tz); ?></td>
						<td><?php echo $record->pname; ?></td>
						<td class="last"><?php echo $record->description; ?></td>
					</tr>
					<?php } // close foreach
				} else { // else count > 0 ?>
					<tr>
						<td colspan="7" class="no_records"><?php echo JText::_('PLG_TIME_RECORDS_NONE_TO_DISPLAY'); ?></td>
					</tr>
				<?php } // close else ?>
			</tbody>
		</table>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records'); ?>">
			<?php echo $this->pageNav; ?>
		</form>
		<div class="loading"></div>
	</div>
</div>
