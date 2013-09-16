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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('Support').': <small><small>[ '.JText::_('Tickets').' ]</small></small>', 'support.png');
JToolBarHelper::preferences('com_support', '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
//JToolBarHelper::editList();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('tickets.html', true);

ximport('Hubzero_User_Profile');

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="ticketForm">
	<div class="collft">
		<div class="colrt">
			<div class="col width-30 fltlft">
				<p>
					<a class="modal" id="new-query" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=add&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
						<?php echo JText::_('Add custom query'); ?>
					</a>
				</p>
				<h3 data-views="common-views"><span><?php echo JText::_('Common'); ?></span></h3>
				<ul id="common-views" class="views">
		<?php if (count($this->queries['common']) > 0) { ?>
			<?php 
			$i = 0;
			foreach ($this->queries['common'] as $query) 
			{ 
				?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id; ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="modal copy" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('Edit'); ?>
						</a>
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
						<span class="none"><?php echo JText::_('(none)'); ?></span>
					</li>
		<?php } ?>
				</ul>
				<h3 data-views="my-views"><span><?php echo JText::_('Mine'); ?></span></h3>
				<ul id="my-views" class="views">
					<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
						<a class="my-watchlist" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=-1&amp;limitstart=0'); ?>">
							<?php echo $this->escape(JText::_('Watch list')); ?> <span><?php echo $this->watchcount; ?></span>
						</a>
					</li>
		<?php if (count($this->queries['mine']) > 0) { ?>
			<?php foreach ($this->queries['mine'] as $query) { ?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id; ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="modal copy" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('Edit'); ?>
						</a>
					</li>
			<?php } ?>
		<?php } else { ?>
					<li>
						<span class="none"><?php echo JText::_('(none)'); ?></span>
					</li>
		<?php } ?>
				</ul>
				<h3 data-views="custom-views"><span><?php echo JText::_('Custom'); ?></span></h3>
				<ul id="custom-views" class="views">
		<?php if (count($this->queries['custom']) > 0) { ?>
			<?php foreach ($this->queries['custom'] as $query) { ?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id; ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=remove&amp;id[]=<?php echo $query->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Delete'); ?>">
							<?php echo JText::_('Delete'); ?>
						</a>
						<a class="modal edit" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('Edit'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('Edit'); ?>
						</a>
					</li>
			<?php } ?>
		<?php } else { ?>
					<li>
						<span class="none"><?php echo JText::_('(none)'); ?></span>
					</li>
		<?php } ?>
				</ul>
			</div><!-- / .col width-30 fltlft -->
			<div class="col width-70 fltrt">

				<table id="tktlist">
					<thead>
						<tr>
							<!-- <th scope"col"><?php echo JText::_('SUPPORT_COL_NUM'); ?></th> -->
							<td colspan="2">
								<fieldset id="filter-bar">
									<label for="filter_search"><?php echo JText::_('SUPPORT_FIND'); ?>:</label> 
									<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="Search this query..." />

									<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
									<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
									<input type="hidden" name="show" value="<?php echo $this->filters['show']; ?>" />

									<button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
								</fieldset>
							</td>
							<th>
								<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; //echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_AGE'), 'created', $this->filters['sortdir'], $this->filters['sort']); ?>
								<ul class="sort-options">
									<li>
										<span class="sort-header">Sort results</span>
										<ul>
									<li>
										<a<?php if ($this->filters['sort'] == 'created') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('created','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo JText::_('SUPPORT_COL_AGE'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'status') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('status','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo  JText::_('SUPPORT_COL_STATUS'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'severity') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('severity','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo  JText::_('Severity'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'summary') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('summary','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo  JText::_('SUPPORT_COL_SUMMARY'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'group') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('group','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo  JText::_('SUPPORT_COL_GROUP'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'owner') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('owner','<?php echo $direction; ?>','');" title="Click to sort by this column">
											<?php echo  JText::_('SUPPORT_COL_OWNER'); ?>
										</a>
									</li>
								</ul>
								</li>
								</ul>
							</th>
							<th class="tkt-severity"> </th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">
								<?php echo $this->pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>
					<tbody>
			<?php
			ximport('Hubzero_View_Helper_Html');

			$k = 0;
			$database =& JFactory::getDBO();
			$sc = new SupportComment($database);
			$st = new SupportTags($database);

			// Collect all the IDs
			$ids = array();
			if ($this->rows)
			{
				foreach ($this->rows as $row)
				{
					$ids[] = $row->id;
				}
			}

			// Pull out the last activity date for all the IDs
			$lastactivities = array();
			if (count($ids))
			{
				$lastactivities = $sc->newestCommentsForTickets(true, $ids);
				$alltags = $st->checkTags($ids);
			}

			for ($i=0, $n=count($this->rows); $i < $n; $i++)
			{
				$row = &$this->rows[$i];

				$comments = 0;

				$lastcomment = '0000-00-00 00:00:00';
				if (isset($lastactivities[$row->id]))
				{
					$lastcomment = $lastactivities[$row->id]['lastactivity']; //
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
						<tr class="<?php echo ($row->status == 2) ? 'closed' : ''; ?>">
							<!-- <td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td> -->
							<th>
								<span class="ticket-id">
									<?php echo $row->id; ?>
								</span>
								<span class="<?php echo $status; ?> status hasTip" title="<?php echo JText::_('Details'); ?> :: <?php echo '<strong>' . JText::_('SUPPORT_COL_STATUS') . ':</strong> ' . $status; echo ($row->open == 0) ? ' (' . $this->escape($row->resolved) . ')' : ''; ?>">
									<?php echo $status; echo ($row->open == 0) ? ' (' . $this->escape($row->resolved) . ')' : ''; ?>
								</span>
							</th>
							<td>
								<p>
									<span class="ticket-author">
										<?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]=' . $this->escape($row->login) . '">' . $this->escape($row->login) . '</a>)' : ''; ?>
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
									<a class="ticket-content" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
										<?php echo $this->escape($row->summary); ?>
									</a>
								</p>
			<?php if ($tags || $row->owner) { ?>
								<p class="ticket-details">
			<?php if ($tags) { ?>
									<span class="ticket-tags">
										<?php echo $tags; ?>
									</span>
			<?php } ?>
			<?php if ($row->group) { ?>
									<span class="ticket-group">
										<?php echo $this->escape(stripslashes($row->group)); ?>
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
								<span class="ticket-severity <?php echo $this->escape($row->severity); ?> hasTip" title="<?php echo '<strong>' . JText::_('Priority') . ':</strong>&nbsp;' . $this->escape($row->severity); ?>">
									<span><?php echo $this->escape($row->severity); ?></span>
								</span>
								<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
							</td>
						</tr>
			<?php
				$k = 1 - $k;
			}
			?>
					</tbody>
				</table>

				<input type="hidden" name="option" value="<?php echo $this->option ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />

				<?php echo JHTML::_('form.token'); ?>
			</div><!-- / .col width-70 fltrt -->
		</div><!-- / .colrt -->
	</div><!-- / .collft -->
	<div class="clr"></div>
</form>

<script type="text/javascript">
	window.addEvent('domready', function(){
		addDeleteQueryEvent();
	});
	function addDeleteQueryEvent()
	{
		$$('.views .delete').each(function (el) {
			$(el).addEvent('click', function (e){
				new Event(e).stop();

				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					return false;
				}

				var href = $(this).href;
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}

				var myAjax = new Ajax(href, {
					method: 'get',
					update: $('custom-views'),
					evalScripts: false,
					onSuccess: function() {
						addDeleteQueryEvent();
					}
				}).request();
				
				return false;
			});
		});
		$$('.fltlft h3').each(function(el) {
			$(el).addEvent('click', function (e){
				e.preventDefault();
				if (!$(this).hasClass('closed')) {
					$(this).addClass('closed');
					$($(this).getProperty('data-views')).addClass('closed');
				} else {
					$(this).removeClass('closed');
					$($(this).getProperty('data-views')).removeClass('closed');
				}
			});
		});
		var clear = $('clear-search');
						// Create the clear button if it doesn't already exist
			if (!clear) {
				var close = new Element('span', {
					id: 'clear-search',
					styles: {
						'display': 'none'
					},
					events: {
							'click': function(event) {
								$('filter_search').value = '';
								$('ticketForm').submit();
							}
						}
				}).injectInside($('filter-bar'));
			}
		if ($('filter_search').value != '') {
			$('clear-search').setStyles({
				display: 'block'
			});
		}

		$('filter_search').addEvent('keyup', function (e) {
			var clear = $('clear-search');
			// Show the button
			if (this.value != '') {
				if (clear.getStyle('display') != 'block') {
					clear.setStyles({
						display: 'block'
					});
				}
			} else {
				clear.setStyles({
						display: 'none'
					});
			}
		});
	}
</script>