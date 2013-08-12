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

ximport('Hubzero_Document');
Hubzero_Document::addComponentScript($this->option, 'assets/js/json2');
Hubzero_Document::addComponentScript($this->option, 'assets/js/condition.builder');
Hubzero_Document::addComponentStylesheet($this->option, 'assets/css/conditions.css');
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
<?php if ($this->acl->check('read', 'tickets')) { ?>
		<li><a class="icon-stats stats btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>"><?php echo JText::_('Stats'); ?></a></li>
<?php } ?>
		<li class="last"><a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section tickets">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>" method="post" id="ticketForm">
		<div id="page-sidebar">
			<fieldset class="filters">
				<label for="filter-search"><?php echo JText::_('SUPPORT_FIND'); ?>:</label>
				<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="Search this query" />

				<input type="hidden" name="sort" value="<?php echo $this->filters['sort']; ?>" />
				<input type="hidden" name="sortdir" value="<?php echo $this->filters['sortdir']; ?>" />
				<input type="hidden" name="show" value="<?php echo $this->filters['show']; ?>" />

				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
<?php //if ($this->acl->check('read', 'tickets')) { ?>
			<h3><span>Common</span></h3>
			<ul id="common-views" class="views">
	<?php if (count($this->queries['common']) > 0) { ?>
		<?php 
		$i = 0;
		foreach ($this->queries['common'] as $query) 
		{ 
			?>
				<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
					<a class="common-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0'); ?>">
						<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
					</a>
				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<a class="modal copy" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
						<?php echo JText::_('Edit'); ?>
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
					<span class="none">(none)</span>
				</li>
	<?php } ?>
			</ul>
<?php //} ?>
			<h3><span>Mine</span></h3>
			<ul id="my-views" class="views">
			<?php if ($this->acl->check('read', 'tickets')) { ?>
				<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
					<a class="my-watchlist" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-1&limitstart=0'); ?>">
						<?php echo $this->escape(JText::_('Watch list')); ?> <span><?php echo $this->watchcount; ?></span>
					</a>
				</li>
			<?php } ?>
	<?php if (count($this->queries['mine']) > 0) { ?>
		<?php foreach ($this->queries['mine'] as $query) { ?>
				<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
					<a class="my-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0'); ?>">
						<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
					</a>
				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<a class="modal copy" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
						<?php echo JText::_('Edit'); ?>
					</a>
				<?php } ?>
				</li>
		<?php } ?>
	<?php } else if (!$this->acl->check('read', 'tickets')) { ?>
				<li>
					<span class="none">(none)</span>
				</li>
	<?php } ?>
			</ul>
<?php if ($this->acl->check('read', 'tickets')) { ?>
			<h3><span>Custom</span></h3>
			<ul id="custom-views" class="views">
	<?php if (count($this->queries['custom']) > 0) { ?>
		<?php foreach ($this->queries['custom'] as $query) { ?>
				<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
					<a class="custom-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]/", '', stripslashes($query->title))); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . '&limitstart=0'); ?>">
						<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
					</a>
					<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id); ?>" title="<?php echo JText::_('Delete'); ?>">
						<?php echo JText::_('Delete'); ?>
					</a>
					<a class="modal edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id); ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
						<?php echo JText::_('Edit'); ?>
					</a>
				</li>
		<?php } ?>
	<?php } else { ?>
				<li>
					<span class="none">(none)</span>
				</li>
	<?php } ?>
			</ul>
			<p>
				<a class="modal icon-add add btn" id="new-query" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=add'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
					<?php echo JText::_('Add query'); ?>
				</a>
				<noscript>
					<?php echo JText::_('Query building currently requires javascript.'); ?>
				</noscript>
			</p>
<?php } ?>
		</div>
		<div id="page-main">
			<table id="tktlist" style="clear: none;">
				<thead>
					<tr>
						<th scope="col">
							<?php echo JText::_('SUPPORT_COL_NUM'); ?>
						</th>
						<th scope="col">
							<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
							<a class="sort-age<?php if ($this->filters['sort'] == 'created') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=created&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_AGE'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-status<?php if ($this->filters['sort'] == 'status') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=status&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_STATUS'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-severity<?php if ($this->filters['sort'] == 'severity') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=severity&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_SEVERITY'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-summary<?php if ($this->filters['sort'] == 'summary') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=summary&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_SUMMARY'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-group<?php if ($this->filters['sort'] == 'group') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=group&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_GROUP'); ?>
							</a>
						</th>
						<th scope="col">
							<a class="sort-owner<?php if ($this->filters['sort'] == 'owner') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=owner&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="Click to sort by this column">
								<?php echo JText::_('SUPPORT_COL_OWNER'); ?>
							</a>
						</th>
<?php //if ($this->acl->check('delete', 'tickets')) { ?>
						<th class="tkt-severity"> </th>
<?php //} ?>
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

ximport('Hubzero_View_Helper_Html');
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$comments = 0;
	
	$lastcomment = '0000-00-00 00:00:00';
	if (isset($lastactivities[$row->id]))
	{
		$lastcomment = $lastactivities[$row->id]['lastactivity'];
	}
	// Was there any activity on this item?
	if ($lastcomment && $lastcomment != '0000-00-00 00:00:00')
	{
		$comments = 1;
	}

	switch ($row->open)
	{
		case 1:
			switch ($row->status)
			{
				case 2:
					$status = 'waiting';
				break;
				case 1:
					$status = 'open';
				break;
				case 0:
				default:
					$status = 'new';
				break;
			}
		break;
		case 0:
			$status = 'closed';
		break;
	}

	$row->severity = ($row->severity) ? $row->severity : 'normal';

	$lnk = '';
	$targetuser = null;
	if ($row->login) 
	{
		if (!isset($users[$row->login]))
		{
			//echo 'ffff';
			$targetuser =& JUser::getInstance($row->login);
			if (is_object($targetuser) && $targetuser->get('id'))
			{
				$users[$row->login] = $targetuser;
				$lnk = JRoute::_('index.php?option=com_members&id=' . $targetuser->get('id'));
			}
		}
		else
		{
			$targetuser = $users[$row->login]; 
			if (is_object($targetuser) && $targetuser->get('id'))
			{
				$lnk = JRoute::_('index.php?option=com_members&id=' . $targetuser->get('id'));
			}
		}
	}

	$row->summary = substr($row->report, 0, 200);
	if (strlen($row->summary) >= 200) 
	{
		$row->summary .= '...';
	}
	if (!trim($row->summary))
	{
		$row->summary = JText::_('(no content found)');
	}

	$tags = '';
	if (isset($alltags[$row->id]))
	{
		$tags = $st->get_tag_cloud(3, 1, $row->id);
	}
?>
					<tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
						<td>
							<span class="ticket-id">
								<?php echo $row->id; ?>
							</span>
							<span class="<?php echo $status; ?> status hasTip" title="<?php echo JText::_('Details'); ?> :: <?php echo '<strong>' . JText::_('SUPPORT_COL_STATUS') . ':</strong> ' . $status; echo ($row->open == 0) ? ' (' . $this->escape($row->resolved) . ')' : ''; ?>">
								<?php echo $status; echo ($row->open == 0) ? ' (' . $this->escape($row->resolved) . ')' : ''; ?>
							</span>
						</td>
						<td colspan="6">
							<p>
								<span class="ticket-author">
									<?php echo $this->escape($row->name); echo ($lnk) ? ' (<a href="' . $lnk . '">' . $this->escape($row->login) . '</a>)' : ''; ?>
								</span>
								<span class="ticket-datetime">
									@ <time datetime="<?php echo $row->created; ?>"><?php echo $row->created; ?></time>
								</span>
		<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
								<span class="ticket-activity">
									<time datetime="<?php echo $lastcomment; ?>"><?php echo Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($lastcomment)); ?></time>
								</span>
		<?php } ?>
							</p>
							<p>
								<a class="ticket-content" title="<?php echo $this->escape($row->report); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $row->id . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
									<?php echo $this->escape($row->summary); ?>
								</a>
							</p>
		<?php if ($tags || $row->owner || $row->group) { ?>
							<p class="ticket-details">
		<?php if ($tags) { ?>
								<span class="ticket-tags">
									<?php echo $tags; ?>
								</span>
		<?php } ?>
		<?php if ($row->group) { 
			if ($this->acl->check('read', 'tickets'))
			{
				$queryid = $this->queries['common'][0]->id;
			}
			else
			{
				$queryid = $this->queries['mine'][0]->id;
			}
			$group = '<a href="' . JRoute::_('index.php?option='.$this->option.'&controller='.$this->controller.'&task=display&show='.$queryid.'&find='.urlencode('group:'.$this->escape(stripslashes($row->group)))).'">' . $this->escape(stripslashes($row->group)) . '</a>';
		?>
								<span class="ticket-group">
									<?php echo $group; ?>
								</span>
		<?php } ?>
		<?php if ($row->owner) { 
					$owner = Hubzero_User_Profile::getInstance($row->owner);
					$picture = Hubzero_User_Profile_Helper::getMemberPhoto($owner, 0);
		?>
								<span class="ticket-owner hasTip" title="<?php echo JText::_('Assigned to'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $picture; ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($owner->get('username'))); ?><br /><?php echo ($owner->get('organization')) ? $this->escape(stripslashes($owner->get('organization'))) : '[organization unknown]'; ?>">
									<?php echo $this->escape(stripslashes($owner->get('name'))); ?>
								</span>
		<?php } ?>
							</p>
		<?php } ?>
						</td>
						<td class="tkt-severity">
							<span class="ticket-severity <?php echo $this->escape($row->severity); ?> hasTip" title="<strong><?php echo JText::_('Priority'); ?>:</strong>&nbsp;<?php echo $this->escape($row->severity); ?>">
								<span><?php echo $this->escape($row->severity); ?></span>
							</span>
		<?php if ($this->acl->check('delete', 'tickets')) { ?>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&id=' . $row->id); ?>" title="<?php echo JText::_('SUPPORT_DELETE'); ?>">
								<?php echo JText::_('SUPPORT_DELETE'); ?>
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
							<?php echo JText::_('No results found.'); ?>
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
</div><!-- /.main section -->
