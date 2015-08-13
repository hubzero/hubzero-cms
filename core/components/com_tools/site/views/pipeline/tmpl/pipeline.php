<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();
// get configurations/ defaults
$developer_site = $this->config->get('developer_site', 'hubFORGE');
$live_site = rtrim(Request::base(),'/');
$developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#','',$live_site);
$project_path  = $this->config->get('project_path', '/tools/');
$dev_suffix    = $this->config->get('dev_suffix', '_dev');

$this->css('pipeline.css')
     ->js('pipeline.js');

// Initiate paging
$pageNav = $this->pagination(
	$this->total,
	$this->filters['start'],
	$this->filters['limit']
);
$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
$pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last"><a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline'); ?>" method="get">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_TOOLS_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<label for="search">
					<?php echo Lang::txt('COM_TOOLS_FIND_TOOL'); ?>
				</label>
				<input type="text" name="search" id="entry-search-text" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="pipeline" />

				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
				<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); ?>" />
			</fieldset>
		</div><!-- / .container data-entry -->

		<div class="container cf">
			<nav class="entries-filters">
				<ul class="entries-menu order-options" data-label="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY'); ?>">
					<?php if ($this->admin) { ?>
					<li>
						<a class="sort-status<?php if ($this->filters['sortby'] == 'f.state, f.priority, f.toolname') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state, f.priority, f.toolname') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
							<?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
						</a>
					</li>
					<?php } else { ?>
					<li>
						<a class="sort-status<?php if ($this->filters['sortby'] == 'f.state, f.registered') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state, f.registered') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_STATUS'); ?>">
							<?php echo Lang::txt('COM_TOOLS_STATUS'); ?>
						</a>
					</li>
					<?php } ?>
					<li>
						<a class="sort-date<?php if ($this->filters['sortby'] == 'f.registered') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.registered') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_REG'); ?>">
							<?php echo Lang::txt('COM_TOOLS_DATE'); ?>
						</a>
					</li>
					<li>
						<a class="sort-name<?php if ($this->filters['sortby'] == 'f.toolname') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.toolname') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_SORTBY_NAME'); ?>">
							<?php echo Lang::txt('COM_TOOLS_ALIAS'); ?>
						</a>
					</li>
					<?php if ($this->admin) { ?>
					<li>
						<a class="sort-priority<?php if ($this->filters['sortby'] == 'f.priority') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.priority') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_PRIORITY'); ?>">
							<?php echo Lang::txt('COM_TOOLS_PRIORITY'); ?>
						</a>
					</li>
					<li>
						<a class="sort-change <?php if ($this->filters['sortby'] == 'f.state_changed DESC') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=' . $this->filters['filterby'] . '&sortby=' . urlencode('f.state_changed DESC') . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'); ?>">
							<?php echo Lang::txt('COM_TOOLS_STATUS_CHANGE'); ?>
						</a>
					</li>
					<?php } ?>
				</ul>

				<ul class="entries-menu filter-options" data-label="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER'); ?>">
					<li>
						<a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=all&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_ALL'); ?>">
							<?php echo Lang::txt('COM_TOOLS_ALL'); ?>
						</a>
					</li>
					<li>
						<a class="filter-mine<?php if ($this->filters['filterby'] == 'mine') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=mine&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_MINE'); ?>">
							<?php echo Lang::txt('COM_TOOLS_MINE'); ?>
						</a>
					</li>
					<li>
						<a class="filter-published<?php if ($this->filters['filterby'] == 'published') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=published&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_PUBLISHED'); ?>">
							<?php echo Lang::txt('COM_TOOLS_PUBLISHED'); ?>
						</a>
					</li>
					<?php if ($this->admin) { ?>
					<li>
						<a class="filter-dev<?php if ($this->filters['filterby'] == 'dev') { echo ' active'; } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=pipeline&limit=' . $this->filters['limit'] . '&filterby=dev&sortby=' . urlencode($this->filters['sortby']) . '&search=' . $this->escape(urlencode($this->filters['search']))); ?>" title="<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_DEV'); ?>">
							<?php echo Lang::txt('COM_TOOLS_DEVELOPMENT'); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</nav>

			<table class="tools entries">
				<caption>
					<?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FILTER_' . strtoupper($this->filters['filterby'])); ?>
					<span>
						(<?php echo (count($this->rows) > 0) ? $this->filters['start'] + 1 : 0; ?> - <?php echo $this->filters['start'] + count($this->rows); ?> of <?php echo $pageNav->total; ?>)
					</span>
				</caption>
				<thead>
					<tr>
						<th scope="col" class="priority-5"></th>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
						<th scope="col" class="priority-4"><?php echo Lang::txt('COM_TOOLS_ALIAS'); ?></th>
						<th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?></th>
						<!-- <th scope="col"><?php echo Lang::txt('COM_TOOLS_LAST_STATUS_CHANGE'); ?></th> -->
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_LINKS'); ?></th>
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

					\Components\Tools\Helpers\Html::getStatusName($row->state, $status);
				?>
					<tr class="<?php echo strtolower($status); if (!$this->admin) { echo (' user-submitted'); } ?>">
						<th class="priority-5">
							<span class="entry-id">
								<?php echo $this->escape($row->id); ?>
							</span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a><br />
							<span class="entry-details">
								<?php echo Lang::txt('COM_TOOLS_REGISTERED'); ?>
								<span class="entry-date"><?php echo Date::of($row->registered)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></span>
							</span>
						</td>
						<td class="priority-4">
							<a class="entry-alias" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo $this->escape($row->toolname); ?>
							</a>
						</td>
						<td class="priority-3">
							<a class="entry-status" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $row->toolname); ?>">
								<?php echo $status; ?>
							</a><br />
							<span class="entry-details">
								<span class="entry-time"><?php echo \Components\Tools\Helpers\Html::timeAgo($row->state_changed) . ' ' . Lang::txt('COM_TOOLS_AGO'); ?></span>
							</span>
						</td>
						<td style="white-space: nowrap;" <?php if (!\Components\Tools\Helpers\Html::toolEstablished($row->state)) { echo ' class="disabled_links" ';} ?>>
							<?php if (!\Components\Tools\Helpers\Html::toolActive($row->state)) { ?>
								<span class="entry-page">
									<?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
								</span>
							<?php } else { ?>
								<a class="entry-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $row->toolname); ?>">
									<?php echo Lang::txt('COM_TOOLS_RESOURCE'); ?>
								</a>
							<?php } ?>
								|
								<a class="entry-history" href="<?php echo Route::url('index.php?option=com_support&task=ticket&id=' . $row->ticketid); ?>">
									<?php echo strtolower(Lang::txt('COM_TOOLS_HISTORY')); ?>
								</a>
								|
							<?php if (strtolower($status) == 'abandoned') { ?>
								<span class="entry-wiki">
									<?php echo strtolower(Lang::txt('COM_TOOLS_PROJECT')); ?>
								</span>
							<?php } else { ?>
								<a class="entry-wiki" href="<?php echo $developer_url . $project_path . $row->toolname; ?>/wiki" rel="external">
									<?php echo strtolower(Lang::txt('COM_TOOLS_PROJECT')); ?>
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
			echo $pageNav->render();
			?>
		</div><!-- / .container -->
	</form>
</section><!-- /.main section -->