<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$live_site = rtrim(Request::base(), '/');

$this->css()
     ->css('conditions.css')
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
				<a class="icon-stats stats btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_STATS'); ?>
				</a>
			</li>
		<?php } ?>
			<li class="last">
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>">
					<?php echo Lang::txt('COM_SUPPORT_NEW_TICKET'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="panel tickets">
	<div class="panel-row">

		<div class="pane pane-queries" id="queries" data-update="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1'); ?>">
			<div class="pane-inner">

				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<ul id="watch-list">
						<li id="folder_watching" class="open">
							<span class="icon-watch folder"><?php echo Lang::txt('COM_SUPPORT_WATCH_LIST'); ?></span>
							<ul id="queries_watching" class="wqueries">
								<li<?php if (intval($this->filters['show']) == -1) { echo ' class="active"'; }?>>
									<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-1&limitstart=0' . (intval($this->filters['show']) != -1 ? '&search=' : '')); ?>">
										<?php echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_OPEN')); ?> <span><?php echo $this->watch['open']; ?></span>
									</a>
								</li>
								<li<?php if (intval($this->filters['show']) == -2) { echo ' class="active"'; }?>>
									<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=-2&limitstart=0' . (intval($this->filters['show']) != -2 ? '&search=' : '')); ?>">
										<?php echo $this->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_CLOSED')); ?> <span><?php echo $this->watch['closed']; ?></span>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				<?php } ?>

				<ul id="query-list">
					<?php if (count($this->folders) > 0) { ?>
						<?php foreach ($this->folders as $folder) { ?>
							<li id="folder_<?php echo $this->escape($folder->id); ?>" class="open">
								<span class="icon-folder folder" id="<?php echo $this->escape($folder->id); ?>-title" data-id="<?php echo $this->escape($folder->id); ?>"><?php echo $this->escape($folder->title); ?></span>
								<?php if ($this->acl->check('read', 'tickets')) { ?>
									<span class="folder-options">
										<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
											<?php echo Lang::txt('JACTION_DELETE'); ?>
										</a>
										<a class="edit editfolder" data-id="<?php echo $this->escape($folder->id); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1&fields[id]=' . $folder->id); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>">
											<?php echo Lang::txt('JACTION_EDIT'); ?>
										</a>
									</span>
								<?php } ?>
								<?php if (isset($folder->queries)) : ?>
								<ul id="queries_<?php echo $this->escape($folder->id); ?>" class="queries">
									<?php foreach ($folder->queries as $query) { ?>
										<li id="query_<?php echo $this->escape($query->id); ?>" <?php if (intval($this->filters['show']) == $query->id) { echo ' class="active"'; }?>>
											<a class="aquery" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $query->id . (intval($this->filters['show']) != $query->id ? '&search=&limitstart=0' : '')); ?>">
												<?php echo $this->escape(stripslashes($query->title)); ?> <span><?php echo $query->count; ?></span>
											</a>
											<?php if ($this->acl->check('read', 'tickets')) { ?>
												<span class="query-options">
													<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=remove&id=' . $query->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
														<?php echo Lang::txt('JACTION_DELETE'); ?>
													</a>
													<a class="modal edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=edit&id=' . $query->id . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('JACTION_EDIT'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}">
														<?php echo Lang::txt('JACTION_EDIT'); ?>
													</a>
												</span>
											<?php } ?>
										</li>
									<?php } ?>
								</ul>
							<?php endif; ?>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>
				<?php if ($this->acl->check('read', 'tickets')) { ?>
					<ul class="controls">
						<li>
							<a class="icon-list modal" id="new-query" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=add&' . Session::getFormToken() . '=1'); ?>" rel="{handler: 'iframe', size: {x: 570, y: 550}}" title="<?php echo Lang::txt('COM_SUPPORT_ADD_QUERY'); ?>">
								<?php echo Lang::txt('COM_SUPPORT_ADD_QUERY'); ?>
							</a>
						</li>
						<li>
							<a class="icon-folder" id="new-folder" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=addfolder&' . Session::getFormToken() . '=1'); ?>" data-href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>">
								<?php echo Lang::txt('COM_SUPPORT_ADD_FOLDER'); ?>
							</a>
						</li>
					</ul>
				<?php } ?>

			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->
		<div class="pane pane-list">
			<div class="pane-inner" id="tickets">
				<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>" method="post" id="ticketForm">
					<div class="list-options">
						<?php $direction = (strtolower($this->filters['sortdir']) == 'desc') ? 'asc' : 'desc'; ?>
						<ul class="sort-options">
							<li>
								<span class="sort-header"><?php echo Lang::txt('COM_SUPPORT_SORT_RESULTS'); ?></span>
								<ul>
									<li>
										<a class="sort-age<?php if ($this->filters['sort'] == 'created') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=created&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_AGE'); ?>
										</a>
									</li>
									<li>
										<a class="sort-status<?php if ($this->filters['sort'] == 'status') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=status&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?>
										</a>
									</li>
									<li>
										<a class="sort-severity<?php if ($this->filters['sort'] == 'severity') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=severity&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SEVERITY'); ?>
										</a>
									</li>
									<li>
										<a class="sort-summary<?php if ($this->filters['sort'] == 'summary') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=summary&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_SUMMARY'); ?>
										</a>
									</li>
									<li>
										<a class="sort-group<?php if ($this->filters['sort'] == 'group') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=group&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_GROUP'); ?>
										</a>
									</li>
									<li>
										<a class="sort-owner<?php if ($this->filters['sort'] == 'owner') { echo ' active ' . strtolower($this->filters['sortdir']); } ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&sort=owner&sortdir=' . $direction . '&limit=' . $this->filters['limit'] . '&limitstart=0'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_CLICK_TO_SORT'); ?>">
											<?php echo Lang::txt('COM_SUPPORT_COL_OWNER'); ?>
										</a>
									</li>
								</ul>
							</li>
						</ul>
						<fieldset id="filter-bar">
							<label for="filter_search"><?php echo Lang::txt('COM_SUPPORT_FIND'); ?>:</label>
							<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_SUPPORT_SEARCH_THIS_QUERY'); ?>" />

							<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
							<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
							<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />

							<input type="submit" class="submit" value="<?php echo Lang::txt('COM_SUPPORT_GO'); ?>" />
						</fieldset>
					</div>
					<table id="tktlist" style="clear: none;">
						<tfoot>
							<tr>
								<td colspan="8">
									<?php
									$pageNav = $this->pagination(
										$this->total,
										$this->filters['start'],
										$this->filters['limit']
									);
									$pageNav->setAdditionalUrlParam('show', $this->filters['show']);
									$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
									echo $pageNav->render();
									?>
								</td>
							</tr>
						</tfoot>
						<tbody>
					<?php
					$k = 0;
					$sc = new \Components\Support\Tables\Comment($this->database);
					$st = new \Components\Support\Models\Tags();

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

						$cls = 'even';

						for ($i=0, $n=count($this->rows); $i < $n; $i++)
						{
							$row = &$this->rows[$i];

							if (!($row instanceof \Components\Support\Models\Ticket))
							{
								$row = new \Components\Support\Models\Ticket($row);
							}

							// Was there any activity on this item?
							$lastcomment = '0000-00-00 00:00:00';
							if (isset($lastactivities[$row->get('id')]))
							{
								$lastcomment = $lastactivities[$row->get('id')]['lastactivity'];
							}

							$tags = '';
							if (isset($alltags[$row->get('id')]))
							{
								$tags = $row->tags('linkedlist');
							}
							?>
							<tr class="<?php echo $cls == 'odd' ? 'even' : 'odd'; ?>">
								<td<?php if ($row->get('status')) { echo ($row->status('color') ? ' style="border-left-color: #' . $row->status('color') . ';"' : ''); } ?>>
									<span class="hasTip" title="<?php echo Lang::txt('COM_SUPPORT_DETAILS'); ?> :: <?php echo Lang::txt('COM_SUPPORT_COL_STATUS') . ': ' . $row->status('text'); ?>">
										<span class="ticket-id">
											<?php echo $row->get('id'); ?>
										</span>
										<span class="<?php echo ($row->isOpen() ? 'open' : 'closed') . ' ' . $row->status('class'); ?> status">
											<?php echo $row->status('text'); echo (!$row->isOpen()) ? ' (' . $this->escape($row->get('resolved')) . ')' : ''; ?>
										</span>
									</span>
								</td>
								<td colspan="6">
									<p>
										<span class="ticket-author">
											<?php echo $this->escape($row->get('name')); echo ($row->submitter()->get('id')) ? ' (<a href="' . Route::url('index.php?option=com_members&id=' . $row->submitter()->get('id')) . '">' . $this->escape($row->get('login')) . '</a>)' : ($row->get('login') ? ' (' . $this->escape($row->get('login')) . ')' : ''); ?>
										</span>
										<span class="ticket-datetime">
											@ <time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('local'); ?></time>
										</span>
										<?php if ($lastcomment && $lastcomment != '0000-00-00 00:00:00') { ?>
											<span class="ticket-activity">
												<time datetime="<?php echo $lastcomment; ?>"><?php echo Date::of($lastcomment)->relative(); ?></time>
											</span>
										<?php } ?>
									</p>
									<p>
										<a class="ticket-content" title="<?php echo $this->escape($row->content('parsed')); ?>" href="<?php echo Route::url($row->link() . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit=' . $this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
											<?php echo ($row->content('clean') ? $row->content('clean', 200) : Lang::txt('COM_SUPPORT_NO_CONTENT_FOUND')); ?>
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
												/*if ($this->acl->check('read', 'tickets'))
												{
													$queryid = $this->queries['common'][0]->id;
												}
												else
												{
													$queryid = $this->queries['mine'][0]->id;
												}*/
												echo $this->escape(stripslashes($row->get('group'))); //'<a href="' . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&show=' . $queryid . '&find=' . urlencode('group:' . $this->escape(stripslashes($row->get('group'))))) . '">' . $this->escape(stripslashes($row->get('group'))) . '</a>';
												?>
											</span>
										<?php } ?>
										<?php if ($row->isOwned()) { ?>
											<span class="ticket-owner hasTip" title="<?php echo Lang::txt('COM_SUPPORT_ASSIGNED_TO'); ?>::<img border=&quot;1&quot; src=&quot;<?php echo $row->owner()->picture(); ?>&quot; name=&quot;imagelib&quot; alt=&quot;User photo&quot; width=&quot;40&quot; height=&quot;40&quot; style=&quot;float: left; margin-right: 0.5em;&quot; /><?php echo $this->escape(stripslashes($row->owner()->get('username'))); ?><br /><?php echo $this->escape(stripslashes($row->owner()->get('organization', Lang::txt('COM_SUPPORT_UNKNOWN')))); ?>">
												<?php echo $this->escape(stripslashes($row->owner()->get('name'))); ?>
											</span>
										<?php } ?>
										</p>
									<?php } ?>
								</td>
								<td class="tkt-severity">
									<span class="ticket-severity <?php echo $this->escape($row->get('severity', 'normal')); ?> hasTip" title="<?php echo Lang::txt('COM_SUPPORT_PRIORITY'); ?>:&nbsp;<?php echo $this->escape($row->get('severity', 'normal')); ?>">
										<span><?php echo $this->escape($row->get('severity', 'normal')); ?></span>
									</span>
									<?php if ($this->acl->check('delete', 'tickets')) { ?>
										<a class="delete" href="<?php echo Route::url($row->link('delete')); ?>" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
											<?php echo Lang::txt('JACTION_DELETE'); ?>
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
									<?php echo Lang::txt('COM_SUPPORT_NO_RESULTS_FOUND'); ?>
								</td>
							</tr>
					<?php
					}
					?>
						</tbody>
					</table>

					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="display" />
				</form>
			</div><!-- / .pane-inner -->
		</div><!-- / .pane -->
	</div><!-- / .panel-row -->
</section><!-- / .panel -->
<script type="text/javascript">
<?php if ($this->acl->check('read', 'tickets')) { ?>
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

			var res = confirm('<?php echo Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE'); ?>');
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

			var title = prompt('<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>', folder.text());
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

		var title = prompt('<?php echo Lang::txt('COM_SUPPORT_FOLDER_NAME'); ?>');
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

	var sinput = $('#filter_search');

	if (sinput.length) {
		var clear = $('#clear-search');

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
	}

	$('a.modal').fancybox({
		type: 'ajax',
		width: 600,
		height: 550,
		autoSize: false,
		fitToView: false,
		titleShow: false,
		arrows: false,
		closeBtn: true,
		/*tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div><a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a></div>'
		},*/
		beforeLoad: function() {
			href = $(this).attr('href');
			if (href.indexOf('?') == -1) {
				href += '?no_html=1';
			} else {
				href += '&no_html=1';
			}
			$(this).attr('href', href);
		},
		afterShow: function() {
			Conditions.addqueryroot('.query', true);

			if ($('#queryForm').length > 0) {
				$('#queryForm').on('submit', function(e) {
					e.preventDefault();

					if (!$('#field-title').val()) {
						alert('<?php echo Lang::txt('COM_SUPPORT_QUERY_ERROR_MISSING_TITLE'); ?>');
						return false;
					}

					query = Conditions.getCondition('.query > fieldset');
					$('#field-conditions').val(JSON.stringify(query));

					$.post($(this).attr('action'), $(this).serialize(), function(data) {
						$('#query-list').html(data);
						$.fancybox.close();
					});
				});
			}
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
<?php } else { ?>
jQuery(document).ready(function($){
	var sinput = $('#filter_search');

	if (sinput.length) {
		var clear = $('#clear-search');

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
	}
});
<?php } ?>
</script>
