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
// get configurations/ defaults
$developer_site = $this->config->get('developer_site', 'hubFORGE');
$live_site = rtrim(JURI::base(),'/');
$developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#','',$live_site);
$project_path 	= $this->config->get('project_path', '/tools/');
$dev_suffix 	= $this->config->get('dev_suffix', '_dev');

$dateFormat = '%d %b %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'H:i p';
	$tz = true;
}

ximport('Hubzero_View_Helper_Html');
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=create'); ?>"><?php echo JText::_('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline'); ?>" method="get">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_TOOLS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<label for="search">
					<?php echo JText::_('COM_TOOLS_FIND_TOOL'); ?>
				</label>
				<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="pipeline" />

				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
				<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); ?>" />
			</fieldset>
		</div><!-- / .container data-entry -->
		
		<div class="container">
			<ul class="entries-menu order-options">
				<?php if ($this->admin) { ?>	
				<li>
					<a class="sort-status<?php if ($this->filters['sortby'] == 'f.state, f.priority, f.toolname') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state, f.priority, f.toolname') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_STATUS'); ?>
					</a>
				</li>
				<?php } else { ?>
				<li>
					<a class="sort-status<?php if ($this->filters['sortby'] == 'f.state, f.registered') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state, f.registered') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_STATUS'); ?>
					</a>
				</li>
				<?php } ?>
				<li>
					<a class="sort-date<?php if ($this->filters['sortby'] == 'f.registered') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.registered') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_SORTBY_REG'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_DATE'); ?>
					</a>
				</li>
				<li>
					<a class="sort-name<?php if ($this->filters['sortby'] == 'f.toolname') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.toolname') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_SORTBY_NAME'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_ALIAS'); ?>
					</a>
				</li>
				<?php if ($this->admin) { ?>
				<li>
					<a class="sort-priority<?php if ($this->filters['sortby'] == 'f.priority') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.priority') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_PRIORITY'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_PRIORITY'); ?>
					</a>
				</li>
				<li>
					<a class="sort-change <?php if ($this->filters['sortby'] == 'f.state_changed DESC') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state_changed DESC') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_LAST_STATUS_CHANGE'); ?>">
						&darr; <?php echo JText::_('COM_TOOLS_STATUS_CHANGE'); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
			
			<ul class="entries-menu filter-options">
				<li>
					<a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=all&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FILTER_ALL'); ?>">
						<?php echo JText::_('COM_TOOLS_ALL'); ?>
					</a>
				</li>
				<li>
					<a class="filter-mine<?php if ($this->filters['filterby'] == 'mine') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=mine&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FILTER_MINE'); ?>">
						<?php echo JText::_('COM_TOOLS_MINE'); ?>
					</a>
				</li>
				<li>
					<a class="filter-published<?php if ($this->filters['filterby'] == 'published') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=published&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FILTER_PUBLISHED'); ?>">
						<?php echo JText::_('COM_TOOLS_PUBLISHED'); ?>
					</a>
				</li>
				<?php if ($this->admin) { ?>
				<li>
					<a class="filter-dev<?php if ($this->filters['filterby'] == 'dev') { echo ' active'; } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=dev&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FILTER_ALL_DEV'); ?>">
						<?php echo JText::_('COM_TOOLS_DEVELOPMENT'); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
			
			<table class="tools entries" summary="<?php echo JText::_('COM_TOOLS_IN_THE_PIPELINE'); ?>">
				<caption>
					<?php echo JText::_('COM_TOOLS_CONTRIBTOOL_FILTER_' . strtoupper($this->filters['filterby'])); ?> 
					<span>
						(<?php echo ($this->filters['start'] + 1); ?> - <?php echo $this->filters['start'] + count($this->rows); ?> of <?php echo $this->pageNav->total; ?>)
					</span>
				</caption>
				<thead>
					<tr>
						<th scope="col"></th>
						<th scope="col"><?php echo JText::_('COM_TOOLS_TITLE'); ?></th>
						<th scope="col"><?php echo JText::_('COM_TOOLS_ALIAS'); ?></th>
						<th scope="col"><?php echo JText::_('COM_TOOLS_STATUS'); ?></th>
						<!-- <th scope="col"><?php echo JText::_('COM_TOOLS_LAST_STATUS_CHANGE'); ?></th> -->
						<th scope="col"><?php echo JText::_('COM_TOOLS_LINKS'); ?></th>
					</tr>
				</thead>
				<tbody>
<?php
$k = 0;

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$row->state_changed = ($row->state_changed != '0000-00-00 00:00:00') ? $row->state_changed : $row->registered;
	$row->title .= ($row->version) ? ' v' . $row->version : '';
	
	ToolsHelperHtml::getStatusName($row->state, $status);
?>
					<tr class="<?php echo strtolower($status); if (!$this->admin) { echo (' user-submitted'); } ?>">
						<th>
							<span class="entry-id">
								<?php echo $this->escape($row->id); ?>
							</span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a><br />
							<span class="entry-details">
								<?php echo JText::_('COM_TOOLS_REGISTERED'); ?>
								<span class="entry-date"><?php echo JHTML::_('date', $row->registered, $dateFormat, $tz); ?></span>
							</span>
						</td>
						<td>
							<a class="entry-alias" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo $this->escape($row->toolname); ?>
							</a>
						</td>
						<td style="white-space: nowrap;">
							<a class="entry-status" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo JText::_('COM_TOOLS_' . $status); ?>
							</a><br />
							<span class="entry-details">
								<span class="entry-time"><?php echo Hubzero_View_Helper_Html::timeAgo($row->state_changed) . ' ' . JText::_('COM_TOOLS_AGO'); ?></span>
							</span>
						</td>
						<td style="white-space: nowrap;" <?php if (!ToolsHelperHtml::toolEstablished($row->state)) { echo ' class="disabled_links" ';} ?>>
						<?php if (!ToolsHelperHtml::toolActive($row->state)) { ?>
							<span class="entry-page">
								<?php echo JText::_('COM_TOOLS_RESOURCE'); ?>
							</span>
						<?php } else { ?>
							<a class="entry-page" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $row->toolname); ?>">
								<?php echo JText::_('COM_TOOLS_RESOURCE'); ?>
							</a>
						<?php } ?>
							|
							<a class="entry-history" href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id=' . $row->ticketid); ?>">
								<?php echo strtolower(JText::_('COM_TOOLS_HISTORY')); ?>
							</a>
							|
						<?php if (strtolower($status) == 'abandoned') { ?>
							<span class="entry-wiki">
								<?php echo strtolower(JText::_('COM_TOOLS_PROJECT')); ?>
							</span>
						<?php } else { ?>
							<a class="entry-wiki" href="<?php echo $developer_url . $project_path . $row->toolname; ?>/wiki" rel="external">
								<?php echo strtolower(JText::_('COM_TOOLS_PROJECT')); ?>
							</a>
						<?php } ?>
						</td>
					</tr>
<?php
	$k = 1 - $k;
}
?>
				</tbody>
			</table>
		
			<?php 
			$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);
			$this->pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
			$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
			
			echo $this->pageNav->getListFooter();
			?>
			<div class="clearfix"></div>
		</div><!-- / .container -->
	</form>
</div><!-- /.main section -->