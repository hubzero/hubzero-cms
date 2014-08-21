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

JToolBarHelper::title(JText::_('COM_SUPPORT').': '.JText::_('COM_SUPPORT_TICKETS'), 'support.png');
JToolBarHelper::preferences('com_support', '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('tickets');

JHTML::_('behavior.tooltip');

$this->css();
?>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="ticketForm">
	<div class="collft">
		<div class="colrt">
			<div class="col width-30 fltlft">
				<p>
					<a class="modal" id="new-query" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=add&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
						<?php echo JText::_('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>
					</a>
				</p>
				<h3 data-views="common-views"><span><?php echo JText::_('COM_SUPPORT_QUERIES_COMMON'); ?></span></h3>
				<ul id="common-views" class="views">
			<?php if (count($this->queries['common']) > 0) { ?>
				<?php 
				$i = 0;
				foreach ($this->queries['common'] as $query)
				{
					?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id . (intval($this->filters['show']) != $query->id ? '&amp;search=' : ''); ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="modal copy" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('COM_SUPPORT_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('COM_SUPPORT_EDIT'); ?>
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
						<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
					</li>
			<?php } ?>
				</ul>
				<h3 data-views="my-views"><span><?php echo JText::_('COM_SUPPORT_QUERIES_MINE'); ?></span></h3>
				<ul id="my-views" class="views">
					<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
						<a class="my-watchlist" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=-1&amp;limitstart=0<?php echo  (intval($this->filters['show']) != $query->id ? '&amp;search=' : ''); ?>">
							<?php echo $this->escape(JText::_('COM_SUPPORT_QUERIES_WATCHING')); ?> <span><?php echo $this->watchcount; ?></span>
						</a>
					</li>
		<?php if (count($this->queries['mine']) > 0) { ?>
			<?php foreach ($this->queries['mine'] as $query) { ?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id . (intval($this->filters['show']) != $query->id ? '&amp;search=' : ''); ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="modal copy" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('COM_SUPPORT_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('COM_SUPPORT_EDIT'); ?>
						</a>
					</li>
			<?php } ?>
		<?php } else { ?>
					<li>
						<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
					</li>
		<?php } ?>
				</ul>
				<h3 data-views="custom-views"><span><?php echo JText::_('COM_SUPPORT_QUERIES_CUSTOM'); ?></span></h3>
				<ul id="custom-views" class="views">
		<?php if (count($this->queries['custom']) > 0) { ?>
			<?php foreach ($this->queries['custom'] as $query) { ?>
					<li<?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;show=<?php echo $query->id . (intval($this->filters['show']) != $query->id ? '&amp;search=' : ''); ?>">
							<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
						</a>
						<a class="delete" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=remove&amp;id[]=<?php echo $query->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('COM_SUPPORT_DELETE'); ?>">
							<?php echo JText::_('COM_SUPPORT_DELETE'); ?>
						</a>
						<a class="modal edit" href="index.php?option=<?php echo $this->option; ?>&amp;controller=queries&amp;task=edit&amp;tmpl=component&amp;id[]=<?php echo $query->id; ?>" title="<?php echo JText::_('COM_SUPPORT_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
							<?php echo JText::_('COM_SUPPORT_EDIT'); ?>
						</a>
					</li>
			<?php } ?>
		<?php } else { ?>
					<li>
						<span class="none"><?php echo JText::_('COM_SUPPORT_NONE'); ?></span>
					</li>
		<?php } ?>
				</ul>
			</div><!-- / .col width-30 fltlft -->
			<div class="col width-70 fltrt">

				<table id="tktlist">
					<thead>
						<tr>
							<td colspan="2">
								<fieldset id="filter-bar">
									<label for="filter_search"><?php echo JText::_('COM_SUPPORT_FIND'); ?>:</label> 
									<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER'); ?>" />

									<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
									<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
									<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

									<button onclick="this.form.submit();"><?php echo JText::_('COM_SUPPORT_GO'); ?></button>
								</fieldset>
							</td>
							<th>
								<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; //echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_AGE'), 'created', $this->filters['sortdir'], $this->filters['sort']); ?>
								<ul class="sort-options">
									<li>
										<span class="sort-header"><?php echo JText::_('COM_SUPPORT_SORT_RESULTS'); ?></span>
										<ul>
									<li>
										<a<?php if ($this->filters['sort'] == 'created') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('created','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo JText::_('COM_SUPPORT_COL_AGE'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'status') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('status','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo  JText::_('COM_SUPPORT_COL_STATUS'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'severity') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('severity','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo  JText::_('COM_SUPPORT_COL_SEVERITY'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'summary') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('summary','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo  JText::_('COM_SUPPORT_COL_SUMMARY'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'group') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('group','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo  JText::_('COM_SUPPORT_COL_GROUP'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'owner') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('owner','<?php echo $direction; ?>','');" title="<?php echo JText::_('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo  JText::_('COM_SUPPORT_COL_OWNER'); ?>
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
					$k = 0;
					$database = JFactory::getDBO();
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

					$tsformat = JFactory::getDBO()->getDateFormat();

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
						<tr class="<?php echo (!$row->isOpen() ? 'closed' : ''); ?>">
							<th<?php if ($row->get('status')) { echo ($row->status('color') ? ' style="border-left-color: #' . $row->status('color') . ';"' : ''); } ?>>
								<span class="ticket-id">
									<?php echo $row->get('id'); ?>
								</span>
								<span class="<?php echo $row->status('class'); ?> status hasTip" title="<?php echo JText::_('COM_SUPPORT_TICKET_DETAILS'); ?> :: <?php echo '<strong>' . JText::_('COM_SUPPORT_COL_STATUS') . ':</strong> ' . $row->status('text'); echo (!$row->isOpen() ? ' (' . $this->escape($row->get('resolved')) . ')' : ''); ?>">
									<?php echo JText::_('COM_SUPPORT_TICKET_STATUS_' . strtoupper($status)); echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
								</span>
							</th>
							<td>
								<p>
									<span class="ticket-author">
										<?php echo $row->get('name'); echo ($row->get('login')) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]=' . $this->escape($row->get('login')) . '">' . $this->escape($row->get('login')) . '</a>)' : ''; ?>
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
									<a class="ticket-content" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->get('id'); ?>">
										<?php echo $this->escape($row->get('summary', JText::_('COM_SUPPORT_TICKET_NO_CONTENT'))); ?>
									</a>
								</p>
							<?php if ($tags || $row->isOwned()) { ?>
								<p class="ticket-details">
								<?php if ($tags) { ?>
									<span class="ticket-tags">
										<?php echo $tags; ?>
									</span>
								<?php } ?>
								<?php if ($row->get('group')) { ?>
									<span class="ticket-group">
										<?php echo $this->escape(stripslashes($row->get('group'))); ?>
									</span>
								<?php } ?>
								<?php if ($row->isOwned()) { ?>
									<span class="ticket-owner hasTip" title="<?php echo JText::_('COM_SUPPORT_TICKET_ASSIGNED_TO'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $row->owner()->getPicture(); ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($row->owner('username'))); ?><br /><?php echo $this->escape(stripslashes($row->owner('organization', JText::_('COM_SUPPORT_USER_ORG_UNKNOWN')))); ?>">
										<?php echo $this->escape(stripslashes($row->owner('name'))); ?>
									</span>
								<?php } ?>
								</p>
							<?php } ?>
							</td>
							<td class="tkt-severity">
								<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo '<strong>' . JText::_('COM_SUPPORT_TICKET_PRIORITY') . ':</strong>&nbsp;' . $this->escape($row->get('severity', 'normal')); ?>">
									<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
								</span>
								<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
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
	jQuery(document).ready(function($){
		addDeleteQueryEvent();
	});
	function addDeleteQueryEvent()
	{
		$('.views').on('click', '.delete', function (e){
			e.preventDefault();

			var res = confirm('<?php echo JText::_('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>');
			if (!res) {
				return false;
			}

			var href = $(this).href;
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}

			$.get(href, {}, function(response){
				$('#custom-views').html(response);
			});
			
			return false;
		});
		$('.fltlft h3').on('click', function (e){
			e.preventDefault();

			if (!$(this).hasClass('closed')) {
				$(this).addClass('closed');
				$($(this).attr('data-views')).addClass('closed');
			} else {
				$(this).removeClass('closed');
				$($(this).attr('data-views')).removeClass('closed');
			}
		});

		var clear = $('#clear-search');
		// Create the clear button if it doesn't already exist
		if (!clear.length) {
			var close = $('<span>')
							.attr('id', 'clear-search')
							.css('display', 'none')
							.on('click', function(event) {
								$('#filter_search').value = '';
								$('#ticketForm').submit();
							})
							.appendTo($('#filter-bar'));
		}

		if ($('#filter_search').val() != '') {
			$('#clear-search').css('display', 'block');
		}

		$('#filter_search').on('keyup', function (e) {
			var clear = $('#clear-search');
			// Show the button
			if ($(this).val() != '') {
				if (clear.css('display') != 'block') {
					clear.css('display', 'block');
				}
			} else {
				clear.css('display', 'none');
			}
		});
	}
</script>