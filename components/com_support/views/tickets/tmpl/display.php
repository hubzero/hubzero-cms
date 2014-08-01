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
defined('_JEXEC') or die('Restricted access');

$live_site = rtrim(JURI::base(), '/');

$this->css()
     ->css('conditions.css')
     ->js()
     ->js('jquery.hoverIntent.js', 'system')
     ->js('json2.js')
     ->js('condition.builder.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($this->acl->check('read', 'tickets')) { ?>
			<li>
				<a class="icon-stats stats btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>">
					<?php echo JText::_('COM_SUPPORT_STATS'); ?>
				</a>
			</li>
		<?php } ?>
			<li class="last">
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>">
					<?php echo JText::_('COM_SUPPORT_NEW_TICKET'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section tickets">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>" method="post" id="ticketForm">
		<div id="page-sidebar">
			<fieldset class="filters">
				<label for="filter-search"><?php echo JText::_('COM_SUPPORT_FIND'); ?>:</label>
				<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_SUPPORT_SEARCH_THIS_QUERY'); ?>" />

				<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
				<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
				<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

				<input type="submit" value="<?php echo JText::_('COM_SUPPORT_GO'); ?>" />
			</fieldset>

			<h3><span><?php echo JText::_('COM_SUPPORT_QUERIES_COMMON'); ?></span></h3>
			<ul id="common-views" class="views">
				<?php if (count($this->queries['common']) > 0) { ?>
					<?php
					$i = 0;
					foreach ($this->queries['common'] as $query)
					{
						?>
							<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
								<a class="common-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0' . (intval($this->filters['show']) != $query->id ? '&search=' : '')); ?>">
									<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
								</a>
							<?php if ($this->acl->check('read', 'tickets')) { ?>
								<a class="modal copy" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('COM_SUPPORT_COPY_QUERY'); ?>">
									<?php echo JText::_('COM_SUPPORT_COPY_QUERY'); ?>
								</a>
							<?php } ?>
							<?php if ($i == 0) { ?>
								<ul class="views">
							<?php } ?>
							<?php if ($i == 2) { ?>
									</li>
								</ul>
							</li>
							<?php } else if ($i > 2) { ?>
							</li>
							<?php } ?>
						<?php
						$i++;
					}
					?>
				<?php } else { ?>
					<li>
						<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
					</li>
				<?php } ?>
			</ul>

			<h3><span><?php echo JText::_('COM_SUPPORT_QUERIES_MINE'); ?></span></h3>
			<ul id="my-views" class="views">
				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
						<a class="my-watchlist" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-1&limitstart=0' . (intval($this->filters['show']) != -1 ? '&search=' : '')); ?>">
							<?php echo $this->escape(JText::_('COM_SUPPORT_WATCH_LIST')); ?> <span><?php echo $this->watchcount; ?></span>
						</a>
					</li>
				<?php } ?>
				<?php if (count($this->queries['mine']) > 0) { ?>
					<?php foreach ($this->queries['mine'] as $query) { ?>
						<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
							<a class="my-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0' . (intval($this->filters['show']) != $query->id ? '&search=' : '')); ?>">
								<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
							</a>
						<?php if ($this->acl->check('read', 'tickets')) { ?>
							<a class="modal copy" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('COM_SUPPORT_COPY_QUERY'); ?>">
								<?php echo JText::_('COM_SUPPORT_COPY_QUERY'); ?>
							</a>
						<?php } ?>
						</li>
					<?php } ?>
				<?php } else if (!$this->acl->check('read', 'tickets')) { ?>
						<li>
							<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
						</li>
				<?php } ?>
			</ul>

			<?php if ($this->acl->check('read', 'tickets')) { ?>
				<h3><span><?php echo JText::_('COM_SUPPORT_QUERIES_CUSTOM'); ?></span></h3>
				<ul id="custom-views" class="views">
					<?php if (count($this->queries['custom']) > 0) { ?>
						<?php foreach ($this->queries['custom'] as $query) { ?>
							<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
								<a class="custom-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0' . (intval($this->filters['show']) != $query->id ? '&search=' : '')); ?>">
									<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
								</a>
								<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id); ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">
									<?php echo JText::_('JACTION_DELETE'); ?>
								</a>
								<a class="modal edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
									<?php echo JText::_('JACTION_EDIT'); ?>
								</a>
							</li>
						<?php } ?>
					<?php } else { ?>
						<li>
							<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
						</li>
					<?php } ?>
				</ul>
				<p>
					<a class="modal icon-add add btn" id="new-query" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=add'); ?>">
						<?php echo JText::_('COM_SUPPORT_ADD_QUERY'); ?>
					</a>
					<noscript>
						<?php echo JText::_('COM_SUPPORT_WARNING_JAVASCRIPT_REQUIRED'); ?>
					</noscript>
				</p>
			<?php } ?>
		</div>
		<div id="page-main">
			<table id="tktlist" style="clear: none;">
				<thead>
					<tr>
						<th scope="col">
							<?php echo JText::_('COM_SUPPORT_COL_NUM'); ?>
						</th>
						<th scope="col">
							<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
							<a class="sort-age<?php if ($this->filters['sort'] == 'created') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=created&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_AGE'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-status<?php if ($this->filters['sort'] == 'status') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=status&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_STATUS'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-severity<?php if ($this->filters['sort'] == 'severity') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=severity&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_SEVERITY'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-summary<?php if ($this->filters['sort'] == 'summary') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=summary&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_SUMMARY'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-group<?php if ($this->filters['sort'] == 'group') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=group&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_GROUP'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-owner<?php if ($this->filters['sort'] == 'owner') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=owner&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo JText::_('COM_SUPPORT_CLICK_TO_SORT'); ?>">
								<?php echo JText::_('COM_SUPPORT_COL_OWNER'); ?>
							</a>
						</th>
						<th class="tkt-severity"> </th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php
							$this->pageNav->setAdditionalUrlParam('show', $this->filters['show']);
							$this->pageNav->setAdditionalUrlParam('search', $this->filters['search']);
							echo $this->pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
			<?php
			$k = 0;
			$sc = new SupportComment($this->database);
			$st = new SupportTags($this->database);

			// Collect all the IDs
			$ids = array();
			if ($this->rows)
			{
				foreach ($this->rows as $row)
				{
					$ids[] = $row->id;
				}

				// Pull out the last activity date for all the IDs
				$lastactivities = array();
				if (count($ids))
				{
					$lastactivities = $sc->newestCommentsForTickets(true, $ids);
					$alltags = $st->checkTags($ids);
				}

				$users = array();

				$cls = 'even';

				for ($i=0, $n=count($this->rows); $i < $n; $i++)
				{
					$row = &$this->rows[$i];

					if (!($row instanceof SupportModelTicket))
					{
						$row = new SupportModelTicket($row);
					}

					$comments = 0;

					$lastcomment = '0000-00-00 00:00:00';
					if (isset($lastactivities[$row->get('id')]))
					{
						$lastcomment = $lastactivities[$row->get('id')]['lastactivity'];
					}
					// Was there any activity on this item?
					if ($lastcomment && $lastcomment != '0000-00-00 00:00:00')
					{
						$comments = 1;
					}

					$tags = '';
					if (isset($alltags[$row->get('id')]))
					{
						$tags = $st->get_tag_cloud(3, 1, $row->get('id'));
					}
					?>
					<tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
						<td>
							<span class="ticket-id">
								<?php echo $row->get('id'); ?>
							</span>
							<span class="<?php echo $row->status('class'); ?> status hasTip" title="<?php echo JText::_('COM_SUPPORT_DETAILS'); ?> :: <?php echo JText::_('COM_SUPPORT_COL_STATUS') . ': ' . $row->status('text'); echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>">
								<?php echo $row->status('text'); echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
							</span>
						</td>
						<td colspan="6">
							<p>
								<span class="ticket-author">
									<?php echo $this->escape($row->get('name')); echo ($row->submitter('id')) ? ' (<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->submitter('id')) . '">' . $this->escape($row->get('login')) . '</a>)' : ''; ?>
								</span>
								<span class="ticket-datetime">
									@ <time datetime="<?php echo $row->created(); ?>"><?php echo $row->created(); ?></time>
								</span>
							<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
								<span class="ticket-activity">
									<time datetime="<?php echo $lastcomment; ?>"><?php echo JHTML::_('date.relative', $lastcomment); ?></time>
								</span>
							<?php } ?>
							</p>
							<p>
								<a class="ticket-content" title="<?php echo $this->escape($row->content('parsed')); ?>" href="<?php echo JRoute::_($row->link() . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
									<?php echo ($row->content('clean') ? $this->escape($row->content('clean', 200)) : JText::_('COM_SUPPORT_NO_CONTENT_FOUND')); ?>
								</a>
							</p>
						<?php if ($tags || $row->isOwned() || $row->get('group')) { ?>
							<p class="ticket-details">
							<?php if ($this->acl->check('update', 'tickets') && $tags) { ?>
								<span class="ticket-tags">
									<?php echo $tags; ?>
								</span>
							<?php } ?>
							<?php if ($row->get('group')) { ?>
								<span class="ticket-group">
									<?php
									if ($this->acl->check('read', 'tickets'))
									{
										$queryid = $this->queries['common'][0]->id;
									}
									else
									{
										$queryid = $this->queries['mine'][0]->id;
									}
									echo '<a href="' . JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $queryid . '&find=' . urlencode('group:' . $this->escape(stripslashes($row->get('group'))))) . '">' . $this->escape(stripslashes($row->get('group'))) . '</a>';
									?>
								</span>
							<?php } ?>
							<?php if ($row->isOwned()) { ?>
								<span class="ticket-owner hasTip" title="<?php echo JText::_('COM_SUPPORT_ASSIGNED_TO'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $row->owner()->getPicture(); ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($row->owner('username'))); ?><br /><?php echo $this->escape(stripslashes($row->owner('organization', JText::_('COM_SUPPORT_UNKNOWN')))); ?>">
									<?php echo $this->escape(stripslashes($row->owner('name'))); ?>
								</span>
							<?php } ?>
							</p>
						<?php } ?>
						</td>
						<td class="tkt-severity">
							<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo JText::_('COM_SUPPORT_PRIORITY'); ?>:&nbsp;<?php echo $this->escape($row->get('severity', 'normal')); ?>">
								<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
							</span>
						<?php if ($this->acl->check('delete', 'tickets')) { ?>
							<a class="delete" href="<?php echo JRoute::_($row->link('delete')); ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">
								<?php echo JText::_('JACTION_DELETE'); ?>
							</a>
						<?php } ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
			} else {
			?>
					<tr class="odd noresults">
						<td colspan="7">
							<?php echo JText::_('COM_SUPPORT_NO_RESULTS_FOUND'); ?>
						</td>
					</tr>
			<?php
			}
			?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="display" />
	</form>
</section><!-- /.main section -->
