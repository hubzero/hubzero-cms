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

JToolBarHelper::title(JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_TICKETS'), 'support.png');
JToolBarHelper::preferences('com_support', '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('tickets');

JHTML::_('behavior.tooltip');

$this->css();
?>

<div class="panel" id="panes">
	<div class="panel-row">

		<div class="pane pane-queries" id="queries" data-update="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . JUtility::getToken() . '=1'); ?>">
			<div class="pane-inner">

				<ul id="watch-list">
					<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
						<a class="icon-watch query" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&show=-1&limitstart=0' . (intval($this->filters['show']) != -1 ? '&search=' : '')); ?>">
							<?php echo $this->escape(JText::_('COM_SUPPORT_QUERIES_WATCHING')); ?> <span><?php echo $this->watchcount; ?></span>
						</a>
					</li>
				</ul>

				<ul id="query-list">
					<?php if (count($this->folders) > 0) { ?>
						<?php foreach ($this->folders as $folder) { ?>
							<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
								<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
								<span class="folder-options">
									<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">
										<?php echo JText::_('JACTION_DELETE'); ?>
									</a>
									<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . JUtility::getToken() . '=1'); ?>" data-href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . JUtility::getToken() . '=1&fields[id]=' . $folder->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo JText::_('JACTION_EDIT'); ?>
									</a>
								</span>
								<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
									<?php foreach ($folder->queries as $query) { ?>
										<li id="query_<?php echo $this->escape($query->id); ?>" <?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
											<a class="query" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&show=' . $query->id . (intval($this->filters['show']) != $query->id ? '&search=' : '')); ?>">
												<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
											</a>
											<span class="query-options">
												<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::_('JACTION_DELETE'); ?>">
													<?php echo JText::_('JACTION_DELETE'); ?>
												</a>
												<a class="modal edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id . '&tmpl=component&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
													<?php echo JText::_('JACTION_EDIT'); ?>
												</a>
											</span>
										</li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>

				<ul class="controls">
					<li>
						<a class="icon-list modal" id="new-query" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=add&tmpl=component&' . JUtility::getToken() . '=1'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}" title="<?php echo JText::_('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>">
							<?php echo JText::_('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>
						</a>
					</li>
					<li>
						<a class="icon-folder" id="new-folder" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=addfolder&' . JUtility::getToken() . '=1'); ?>" data-href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::_('COM_SUPPORT_ADD_FOLDER'); ?>">
							<?php echo JText::_('COM_SUPPORT_ADD_FOLDER'); ?>
						</a>
					</li>
					<?php /* <li>
						<a class="icon-batch" id="new-batch" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=tickets&task=batch&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::_('COM_SUPPORT_BATCH_PROCESS'); ?>">
							<?php echo JText::_('COM_SUPPORT_BATCH_PROCESS'); ?>
						</a>
					</li> */ ?>
				</ul>

			</div>
		</div><!-- / .pane -->
		<div class="pane pane-list">
			<div class="pane-inner" id="tickets">

				<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="ticketForm">
					<div class="list-options">
						<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
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
						<fieldset id="filter-bar">
							<label for="filter_search"><?php echo JText::_('COM_SUPPORT_FIND'); ?>:</label> 
							<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER'); ?>" />

							<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
							<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
							<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

							<button onclick="this.form.submit();"><?php echo JText::_('COM_SUPPORT_GO'); ?></button>
						</fieldset>
					</div>

					<ul id="tktlist">
					<?php
					$k = 0;
					$database = JFactory::getDBO();
					$sc = new SupportComment($database);
					$st = new SupportModelTags();

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

					$tid = JRequest::getInt('ticket', 0);

					$tsformat = JFactory::getDBO()->getDateFormat();

					for ($i=0, $n=count($this->rows); $i < $n; $i++)
					{
						$row = &$this->rows[$i];

						if (!($row instanceof SupportModelTicket))
						{
							$row = new SupportModelTicket($row);
						}

						if ($tid && $row->get('id') != $tid)
						{
							continue;
						}

						//$comments = 0;

						$lastcomment = '0000-00-00 00:00:00';
						if (isset($lastactivities[$row->get('id')]))
						{
							$lastcomment = $lastactivities[$row->get('id')]['lastactivity'];
						}
						// Was there any activity on this item?
						/*if ($lastcomment && $lastcomment != '0000-00-00 00:00:00')
						{
							$comments = 1;
						}*/

						$tags = '';
						if (isset($alltags[$row->get('id')]))
						{
							$tags = $row->tags('linkedlist');
						}
						?>
						<li class="<?php echo (!$row->isOpen() ? 'closed' : 'open'); ?>" data-id="<?php echo $row->get('id'); ?>" id="ticket-<?php echo $row->get('id'); ?>">
							<div class="ticket-wrap"<?php if ($row->get('status')) { echo ($row->status('color') ? ' style="border-left-color: #' . $row->status('color') . ';"' : ''); } ?>>
								<p>
									<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
									<span class="ticket-id">
										# <?php echo $row->get('id'); ?>
									</span>
									<span class="<?php echo $row->status('class'); ?> status hasTip" title="<?php echo JText::_('COM_SUPPORT_TICKET_DETAILS'); ?> :: <?php echo '<strong>' . JText::_('COM_SUPPORT_COL_STATUS') . ':</strong> ' . $row->status('text'); echo (!$row->isOpen() ? ' (' . $this->escape($row->get('resolved')) . ')' : ''); ?>">
										<?php echo $row->status('text'); echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
									</span>
									<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
										<span class="ticket-activity">
											<time datetime="<?php echo $lastcomment; ?>"><?php echo JHTML::_('date.relative', $lastcomment); ?></time>
										</span>
									<?php } ?>
								</p>
								<p>
									<span class="ticket-author">
										<?php echo $row->get('name'); echo ($row->get('login')) ? ' (<a href="' . JRoute::_('index.php?option=com_members&task=edit&id=' . $this->escape($row->get('login'))) . '">' . $this->escape($row->get('login')) . '</a>)' : ''; ?>
									</span>
									<span class="ticket-datetime">
										@ <time datetime="<?php echo $row->created(); ?>"><?php echo $row->created(); ?></time>
									</span>
								</p>
								<p>
									<a class="ticket-content" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
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
								<p class="tkt-severity">
									<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo '<strong>' . JText::_('COM_SUPPORT_TICKET_PRIORITY') . ':</strong>&nbsp;' . $this->escape($row->get('severity', 'normal')); ?>">
										<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
									</span>
								</p>
							</div>
						</li>
						<?php
						$k = 1 - $k;
					}
					?>
					</ul>

					<?php echo $this->pageNav->getListFooter(); ?>

					<input type="hidden" name="option" value="<?php echo $this->option ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />

					<?php echo JHTML::_('form.token'); ?>
				</form>

			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->

	</div><!-- / .panel-row -->
</div><!-- / .panel -->

<script type="text/javascript">
String.prototype.tmpl = function (tmpl) {
	if (typeof(tmpl) == 'undefined' || !tmpl) {
		tmpl = 'component';
	}
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'tmpl=' + tmpl;
};
String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

jQuery(document).ready(function($){
	var panes = $('#panes');

	_DEBUG = $('#system-debug').length;

	$('#queries')
		.on('click', 'span.folder', function(e) {
			var parent = $(this).parent();

			if (parent.hasClass('open')) {
				parent.removeClass('open');
			} else {
				parent.addClass('open');
			}
		})
		.on('click', 'a.delete', function (e){
			e.preventDefault();

			var res = confirm('<?php echo JText::_('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>');
			if (!res) {
				return false;
			}

			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('href').nohtml());
			}

			$.get($(this).attr('href').nohtml(), {}, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$('#query-list').html(response);
			});

			return false;
		})
		.on('click', 'a.editfolder', function(e) {
			e.preventDefault();

			var folder = $('#' + $(this).attr('data-id') + '-title');

			var title = prompt('<?php echo JText::_('COM_SUPPORT_FOLDER_NAME'); ?>', folder.text());
			if (title) {
				$.get($(this).attr('data-href').nohtml() + '&fields[title]=' + title, function(response){
					folder.text(title);
				});
			}
		});

	if (jQuery.ui && jQuery.ui.sortable) {
		$('#query-list').sortable({
			update: function (e, ui) {
				var col = $("#query-list").sortable("serialize");

				if (_DEBUG) {
					window.console && console.log('Calling: ' + $('#queries').attr('data-update').nohtml() + '&' + col);
				}

				$.getJSON($('#queries').attr('data-update').nohtml() + '&' + col, function(response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
			}
		});

		applySortable();
	}

	$('#new-folder').on('click', function(e) {
		e.preventDefault();

		var title = prompt('<?php echo JText::_('Folder name'); ?>');
		if (title) {
			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('data-href').nohtml() + '&fields[title]=' + title);
			}

			$.get($(this).attr('data-href').nohtml() + '&fields[title]=' + title, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$('#query-list').html(response);

				//var template = Handlebars.compile($("#folder-template").html());
				//$('#queries').append(template(response));
			});
		}
	});

	$('#tktlist').find('input').on('change', function(e) {
		var el = $(this),
			parent = el.closest('li');

		if (el.prop('checked')) {
			if (!parent.hasClass('ui-selected')) {
				parent.addClass('ui-selected');
			}
		} else {
			if (parent.hasClass('ui-selected')) {
				parent.removeClass('ui-selected');
			}
		}
	});

	/*$('#new-batch').on('click', function(e) {
		e.preventDefault();

		var ids = new Array();

		$(".ui-selected").each(function() {
			ids.push($(this).attr('data-id'));
		});

		if (ids.length > 1) {
			var url = '<?php echo JRoute::_('index.php?option=' . $this->option); ?>';
			$.fancybox.open($(this).attr('href').tmpl() + '&id[]=' + ids.join('&id[]='), {
				arrows: false,
				type: 'iframe',
				autoSize: false,
				fitToView: false
			});
		} else {
			alert('<?php echo JText::_('Please select two or more items to batch process.'); ?>'); 
		}
	});*/

	var clear = $('#clear-search'),
		sinput = $('#filter_search');

	if (!clear.length) {
		clear = $('<span>')
			.attr('id', 'clear-search')
			.css('display', 'none')
			.on('click', function(event) {
				sinput.val('');
				$('#ticketForm').submit();
			})
			.appendTo($('#filter-bar'));
	}

	if (sinput.val() != '') {
		clear.show();
	}

	sinput.on('keyup', function (e) {
		if ($(this).val() != '') {
			if (clear.css('display') != 'block') {
				clear.show();
			}
		} else {
			clear.hide();
		}
	});
});

function applySortable()
{
	if (jQuery.ui && jQuery.ui.sortable) {
		$('ul.queries').sortable({
			connectWith: 'ul.queries',
			update: function (e, ui) {
				var col = [];

				$('ul.queries').each(function(i, el) {
					var ul = $(el),
						folder = parseInt(ul.attr('id').split('_')[1]);

					ul.find('li').each(function(k, elm) {
						col.push(folder + '_' + $(elm).attr('id').split('_')[1]);
					});
				});

				if (_DEBUG) {
					window.console && console.log('Calling: ' + $('#queries').attr('data-update').nohtml() + '&queries[]=' + col.join('&queries[]='));
				}

				$.getJSON($('#queries').attr('data-update').nohtml() + '&queries[]=' + col.join('&queries[]='), function(response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
			}
		});
	}
}
</script>