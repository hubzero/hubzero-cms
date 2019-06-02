<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS'), 'support');
Toolbar::preferences('com_support', '550');
Toolbar::spacer();
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('tickets');

Html::behavior('tooltip');

$this->css()
	->js('tickets.js');
?>

<div class="panel" id="panes">
	<div class="panel-row">

		<div class="pane pane-queries" id="queries" data-update="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1'); ?>">
			<div class="pane-inner">

				<ul id="watch-list">
					<li id="folder_watching" class="open">
						<span class="icon-watch folder"><?php echo Lang::txt('COM_SUPPORT_QUERIES_WATCHING'); ?></span>
						<ul id="queries_watching" class="wqueries">
							<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
								<a class="query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&show=-1&limitstart=0' . (intval($this->filters['show']) != -1 ? '&search=' : '')); ?>">
									<?php echo $this->escape(Lang::txt('COM_SUPPORT_QUERIES_WATCHING_OPEN')); ?> <span><?php echo $this->watch['open']; ?></span>
								</a>
							</li>
							<li<?php if (intval($this->filters['show']) == -2) { echo ' class="active"'; }?>>
								<a class="query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&show=-2&limitstart=0' . (intval($this->filters['show']) != -2 ? '&search=' : '')); ?>">
									<?php echo $this->escape(Lang::txt('COM_SUPPORT_QUERIES_WATCHING_CLOSED')); ?> <span><?php echo $this->watch['closed']; ?></span>
								</a>
							</li>
						</ul>
					</li>
				</ul>

				<ul id="query-list">
					<?php if (count($this->folders) > 0) { ?>
						<?php foreach ($this->folders as $folder) { ?>
							<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
								<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
								<span class="folder-options">
									<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
										<?php echo Lang::txt('JACTION_DELETE'); ?>
									</a>
									<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1&fields[id]=' . $folder->id); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
										<?php echo Lang::txt('JACTION_EDIT'); ?>
									</a>
								</span>
								<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
									<?php foreach ($folder->queries as $query) { ?>
										<li id="query_<?php echo $this->escape($query->id); ?>" <?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
											<a class="query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&show=' . $query->id . (intval($this->filters['show']) != $query->id ? '&search=&limitstart=0' : '')); ?>">
												<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
											</a>
											<span class="query-options">
												<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>" data-confirm="<?php echo Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>">
													<?php echo Lang::txt('JACTION_DELETE'); ?>
												</a>
												<a class="modal edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
													<?php echo Lang::txt('JACTION_EDIT'); ?>
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
						<a class="icon-list modal" id="new-query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=add&tmpl=component&' . Session::getFormToken() . '=1'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}" title="<?php echo Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>">
							<?php echo Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY'); ?>
						</a>
					</li>
					<li>
						<a class="icon-folder" id="new-folder" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=addfolder&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>" data-ptompt="<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>">
							<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>
						</a>
					</li>
					<?php /* <li>
						<a class="icon-batch" id="new-batch" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tickets&task=batch&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>">
							<?php echo Lang::txt('COM_SUPPORT_BATCH_PROCESS'); ?>
						</a>
					</li> */ ?>
				</ul>

			</div>
		</div><!-- / .pane -->
		<div class="pane pane-list">
			<div class="pane-inner" id="tickets">

				<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="ticketForm">
					<div class="list-options">
						<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
						<ul class="sort-options">
							<li>
								<span class="sort-header"><?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?></span>
								<ul>
									<li>
										<a<?php if ($this->filters['sort'] == 'created') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('created','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'status') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('status','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'severity') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('severity','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'summary') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('summary','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'group') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('group','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
										</a>
									</li>
									<li>
										<a<?php if ($this->filters['sort'] == 'owner') { echo ' class="active ' . strtolower($this->filters['sortdir']) . '"'; } ?> href="javascript:tableOrdering('owner','<?php echo $direction; ?>','');" title="<?php echo Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
										</a>
									</li>
								</ul>
							</li>
						</ul>
						<fieldset id="filter-bar">
							<label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
							<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER'); ?>" />

							<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
							<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
							<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

							<button type="submit"><?php echo Lang::txt('COM_SUPPORT_GO'); ?></button>
						</fieldset>
					</div>

					<ul id="tktlist">
					<?php
				$k = 0;
				$database = App::get('db');

				$st = new \Components\Support\Models\Tags();

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
					$alltags = $st->checkTags($ids);
				}

				$tid = Request::getInt('ticket', 0);

				$tsformat = $database->getDateFormat();
				$statuses = array();

				if (count($this->rows) > 0)
				{
					$i = 0;
					foreach ($this->rows as $row)
					{
						if ($tid && $row->get('id') != $tid)
						{
							continue;
						}

						$lastcomment = $row->comments()
							->order('created', 'desc')
							->row()
							->get('created');

						$tags = '';
						if (isset($alltags[$row->get('id')]))
						{
							$tags = $row->tags('linkedlist');
						}

						if (!in_array($row->status->get('id'), $statuses))
						{
							$statuses[] = $row->status->get('id');
							$this->css('#tktlist li .status-' . $row->status->get('id') . ' { border-left-color: #' . $row->status->get('color') . '; }');
						}
						?>
						<li class="<?php echo !$row->isOpen() ? 'closed' : 'open'; ?>" data-id="<?php echo $row->get('id'); ?>" id="ticket-<?php echo $row->get('id'); ?>">
							<div class="ticket-wrap status-<?php echo $row->status->get('id'); ?>">
								<p>
									<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
									<span class="ticket-id">
										# <?php echo $row->get('id'); ?>
									</span>
									<span class="<?php echo $row->status->get('alias'); ?> status hasTip" title="<?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS'); ?> :: <?php echo '<strong>' . Lang::txt('COM_SUPPORT_COL_STATUS') . ':</strong> ' . $row->status->get('title');
echo !$row->isOpen() ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>">
										<?php echo $row->status->get('title');
echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
									</span>
									<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
										<span class="ticket-activity">
											<time datetime="<?php echo $lastcomment; ?>"><?php echo Date::of($lastcomment)->relative(); ?></time>
										</span>
									<?php } ?>
									<?php if ($row->get('target_date') && $row->get('target_date') != '0000-00-00 00:00:00') { ?>
										<span class="ticket-target_date hasTip" title="<?php echo Lang::txt('Target date: %s', Date::of($row->get('target_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))); ?>">
											<time datetime="<?php echo Date::of($row->get('target_date'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($row->get('target_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
										</span>
									<?php } ?>
								</p>
								<p>
									<span class="ticket-author">
										<?php echo $row->get('name');
echo ($row->get('login')) ? ' (<a href="' . Route::url('index.php?option=com_members&task=edit&id=' . $this->escape($row->get('login'))) . '">' . $this->escape($row->get('login')) . '</a>)' : ''; ?>
									</span>
									<span class="ticket-datetime">
										@ <time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
									</span>
								</p>
								<p>
									<a class="ticket-content" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
										<?php echo $this->escape($row->get('summary', Lang::txt('COM_SUPPORT_TICKET_NO_CONTENT'))); ?>
									</a>
								</p>
								<?php if ($tags || $row->isOwned() || $row->get('group_id')) { ?>
									<p class="ticket-details">
										<?php if ($tags) { ?>
											<span class="ticket-tags">
												<?php echo $tags; ?>
											</span>
										<?php } ?>
										<?php if ($row->get('group_id')) { ?>
											<span class="ticket-group">
												<?php
												$gname = Lang::txt('COM_SUPPORT_UNKNOWN');
												if ($group = \Hubzero\User\Group::getInstance($row->get('group_id')))
												{
													$gname = $group->get('cn');
												}
												echo $this->escape($gname);
												?>
											</span>
										<?php } ?>
										<?php if ($row->isOwned()) { ?>
											<span class="ticket-owner hasTip" title="<?php echo Lang::txt('COM_SUPPORT_TICKET_ASSIGNED_TO'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $row->assignee->picture(); ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($row->assignee->get('username'))); ?><br /><?php echo $this->escape(stripslashes($row->assignee->get('organization', Lang::txt('COM_SUPPORT_USER_ORG_UNKNOWN')))); ?>">
												<?php echo $this->escape(stripslashes($row->assignee->get('name'))); ?>
											</span>
										<?php } ?>
									</p>
								<?php } ?>
								<p class="tkt-severity">
									<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo '<strong>' . Lang::txt('COM_SUPPORT_TICKET_PRIORITY') . ':</strong>&nbsp;' . $this->escape($row->get('severity', 'normal')); ?>">
										<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
									</span>
								</p>
							</div>
						</li>
						<?php
						$i++;
						$k = 1 - $k;
					}
				}
				else
				{
					?>
						<li>
							<p class="no-records"><?php echo Lang::txt('No tickets found.'); ?></p>
						</li>
					<?php
				}
					?>
					</ul>

					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>

					<input type="hidden" name="option" value="<?php echo $this->option ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />

					<?php echo Html::input('token'); ?>
				</form>

			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->

		<div class="pane pane-item">
			<div class="pane-inner" id="ticket">
				<p class="instructions"><?php echo Lang::txt('Select a ticket from the list to view details.'); ?></p>
			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->

	</div><!-- / .panel-row -->
</div><!-- / .panel -->
