<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->css('jquery.ui.css', 'system')
     ->js('jquery.timepicker.js', 'system')
     ->js();

$status = $this->row->status->get('title');

$unknown  = 1;
//$name     = Lang::txt('COM_SUPPORT_UNKNOWN');
$usertype = Lang::txt('COM_SUPPORT_UNKNOWN');

if ($this->row->get('login'))
{
	$submitter = $this->row->submitter;
	if ($submitter->get('id'))
	{
		$gids = array();
		foreach (User::getInstance($this->row->submitter->get('id'))->accessgroups() as $g)
		{
			$gids[] = $g->group_id;
		}
		$usertype = implode(', ', $gids);

		$name = '<a rel="profile" href="' . Route::url('index.php?option=com_members&id=' . $submitter->get('id')) . '">' . $this->escape(stripslashes($this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->get('login'))) . ')</a>';
		$unknown = 0;
	}
	else
	{
		$name  = '<a rel="email" href="mailto:' . $this->row->get('email') . '">';
		$name .= ($this->row->get('login')) ? $this->escape(stripslashes($this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->get('login'))) . ')' : $this->escape(stripslashes($this->row->get('name')));
		$name .= '</a>';
	}
}
else
{
	$name  = '<a rel="email" href="mailto:' . $this->row->get('email') . '">' . $this->escape(stripslashes($this->row->get('name'))) . '</a>';
}

$prev = null;
$next = null;
/*
$sq = \Components\Support\Models\Query::oneOrNew($this->filters['show']);
if ($sq->conditions)
{
	$this->filters['sort']    = $sq->sort;
	$this->filters['sortdir'] = $sq->sort_dir;
	if ($rows = \Components\Support\Models\Ticket::allWithQuery($sq, $this->filters))
	{
		foreach ($rows as $key => $row)
		{
			if ($row->id == $this->row->get('id'))
			{
				if (isset($rows[$key - 1]))
				{
					$next = $rows[$key - 1];
				}
				if (isset($rows[$key + 1]))
				{
					$prev = $rows[$key + 1];
				}
				break;
			}
		}
		unset($rows);
	}
}
*/
$cc = array();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($prev) { ?>
			<li>
				<a class="icon-prev prev btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=ticket&id=' . $prev->id . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo Lang::txt('COM_SUPPORT_PREV'); ?>
				</a>
			</li>
		<?php } ?>
			<li>
				<a class="browse btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=tickets&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo Lang::txt('COM_SUPPORT_TICKETS'); ?>
				</a>
			</li>
		<?php if ($next) { ?>
			<li>
				<a class="icon-next next opposite btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=ticket&id=' . $next->id . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo Lang::txt('COM_SUPPORT_NEXT'); ?>
				</a>
			</li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<section class="main section">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<div class="ticket entry" id="t<?php echo $this->row->get('id'); ?>">
				<p class="entry-member-photo">
					<span class="entry-anchor"></span>
					<img src="<?php echo $this->row->submitter->picture($unknown); ?>" alt="" />
				</p><!-- / .entry-member-photo -->
				<div class="entry-content">
					<p class="entry-title">
						<strong><?php echo $name; ?></strong>
						<a class="permalink" href="<?php echo Route::url($this->row->link()); ?>" title="<?php echo Lang::txt('COM_SUPPORT_PERMALINK'); ?>">
							<span class="entry-date-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('date'); ?></time></span>
						</a>
					</p><!-- / .entry-title -->
					<div class="entry-body">
						<p><?php echo $this->row->content; ?></p>
						<?php if ($this->row->attachments->count()) { ?>
							<div class="comment-attachments">
								<?php
								foreach ($this->row->attachments as $attachment)
								{
									if (!trim($attachment->get('description')))
									{
										$attachment->set('description', $attachment->get('filename'));
									}

									if ($attachment->isImage())
									{
										if ($attachment->width() > 400)
										{
											$img = '<p><a href="' . Route::url($attachment->link()) . '" rel="lightbox"><img src="' . Route::url($attachment->link()) . '" alt="' . $this->escape($attachment->get('description')) . '" width="400" /></a></p>';
										}
										else
										{
											$img = '<p><img src="' . Route::url($attachment->link()) . '" alt="' . $this->escape($attachment->get('description')) . '" /></p>';
										}
										echo $img;
									}
									else
									{
										?>
										<a class="attachment <?php echo Filesystem::extension($attachment->get('filename')); ?>" href="<?php echo Route::url($attachment->link()); ?>" title="<?php echo $this->escape($attachment->get('description')); ?>">
											<p class="attachment-description"><?php echo $attachment->get('description'); ?></p>
											<p class="attachment-meta">
												<span class="attachment-size"><?php echo Hubzero\Utility\Number::formatBytes($attachment->size()); ?></span>
												<span class="attachment-action"><?php echo Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD'); ?></span>
											</p>
										</a>
										<?php
									}
								}
								?>
							</div><!-- / .comment-body -->
						<?php } ?>
					</div><!-- / .entry-body -->
				</div><!-- / .entry-content -->
				<?php if ($this->row->access('update', 'tickets') > 0) { ?>
					<div class="entry-details">
						<table>
							<tbody>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_EMAIL'); ?>:</th>
									<td><a href="mailto:<?php echo $this->row->get('email'); ?>"><?php echo $this->escape($this->row->get('email')); ?></a></td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE'); ?>:</th>
									<td><?php echo $this->escape($usertype); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_OS'); ?>:</th>
									<td><?php echo $this->escape($this->row->get('os')); ?> / <?php echo $this->escape($this->row->get('browser')); ?> (<?php echo ($this->row->get('cookies')) ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED') : Lang::txt('COM_SUPPORT_COOKIES_DISABLED'); ?>)</td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_IP'); ?>:</th>
									<td><?php echo $this->escape($this->row->get('ip')); ?> (<?php echo $this->escape($this->row->get('hostname')); ?>)</td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_REFERRER'); ?>:</th>
									<td><?php echo $this->escape($this->row->get('referrer', ' ')); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_INSTANCES'); ?>:</th>
									<td><?php echo $this->escape($this->row->get('instances')); ?></td>
								</tr>
								<?php if ($uas = $this->row->get('uas')) { ?>
									<tr>
										<td colspan="2"><?php echo $this->escape($uas); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div><!-- / .entry-details -->
				<?php } ?>
			</div><!-- / .ticket -->
		</div><!-- / .subject -->
		<aside class="aside">
		<div class="ticket-status">
			<p class="<?php echo (!$this->row->isOpen()) ? 'closed' : 'open'; ?>">
				<strong><?php echo (!$this->row->isOpen()) ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED_TICKET') : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN_TICKET'); ?></strong>
			</p>
			<?php if (!$this->row->isOpen()) { ?>
				<p><?php echo Lang::txt('COM_SUPPORT_NOTE_TO_REOPEN'); ?></p>
			<?php } ?>
		</div><!-- / .entry-status -->

		<div class="ticket-watch">
			<?php if ($this->row->isWatching()) { ?>
				<div id="watching">
					<p><?php echo Lang::txt('COM_SUPPORT_CURRENTLY_WATCHING'); ?></p>
					<p><a class="stop-watching btn" href="<?php echo Route::url($this->row->link('stopWatching') . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>"><?php echo Lang::txt('COM_SUPPORT_STOP_WATCHING'); ?></a></p>
				</div>
			<?php } else { ?>
				<p><a class="start-watching btn" href="<?php echo Route::url($this->row->link('startWatching') . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>"><?php echo Lang::txt('COM_SUPPORT_WATCHING_TICKET'); ?></a></p>
			<?php } ?>
			<p><?php echo Lang::txt('COM_SUPPORT_WATCHING_EXPLANATION'); ?></p>
		</div>
	</aside><!-- / .aside -->
	</div>
</section><!-- / .main section -->

<?php if ($this->row->access('read', 'comments')) { ?>
<section class="below section">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<h3><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENTS'); ?></h3>

		<?php if ($this->row->comments->count() > 0) { ?>
			<ol class="comments">
			<?php
			$o = 'even';
			$i = 0;
			foreach ($this->row->comments as $comment)
			{
				if ($comment->changelog()->format() != 'html')
				{
					$cc = $comment->changelog()->get('cc');
				}
				// Is the comment private?
				// If so, does the user have access to read private comments?
				//   If not, skip it
				if (!$this->row->access('read', 'private_comments') && $comment->isPrivate())
				{
					continue;
				}
				$i++;

				// Set the CSS class
				if ($comment->isPrivate())
				{
					$access = 'private';
				}
				else
				{
					$access = 'public';
				}
				if ($comment->get('created_by') == $this->row->submitter->get('username') && !$comment->isPrivate())
				{
					$access = 'submitter';
				}

				$name = Lang::txt('COM_SUPPORT_UNKNOWN');
				$cite = $name;

				if ($comment->creator->get('id'))
				{
					$cite = $this->escape(stripslashes($comment->creator->get('name')));
					$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $comment->creator->get('id')) . '">' . $cite . ' (' . $this->escape(stripslashes($comment->creator->get('username'))) . ')</a>';
				}

				$o = ($o == 'odd') ? 'even' : 'odd';
				?>
				<li class="comment <?php echo $access . ' ' . $o; ?>" id="c<?php echo $comment->get('id'); ?>">
					<p class="comment-member-photo">
						<img src="<?php echo $comment->creator->picture(); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-head">
							<strong>
								<?php echo $name; ?>
							</strong>
							<a class="permalink" href="<?php echo Route::url($comment->link()); ?>" title="<?php echo Lang::txt('COM_SUPPORT_PERMALINK'); ?>">
								<span class="comment-date-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('time'); ?></time></span>
								<span class="comment-date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('date'); ?></time></span>
							</a>
						</p><!-- / .comment-head -->
					<?php if ($content = $comment->comment) { ?>
						<div class="comment-body">
							<p><?php echo $content; ?></p>
						</div><!-- / .comment-body -->
					<?php } ?>
					<?php if ($comment->attachments->count()) { ?>
						<div class="comment-attachments">
							<?php
							foreach ($comment->attachments as $attachment)
							{
								if (!trim($attachment->get('description')))
								{
									$attachment->set('description', $attachment->get('filename'));
								}

								if ($attachment->hasFile())
								{
									if ($attachment->isImage())
									{
										if ($attachment->width() > 400)
										{
											$img = '<p><a href="' . Route::url($attachment->link()) . '" rel="lightbox"><img src="' . Route::url($attachment->link()) . '" alt="' . $this->escape($attachment->get('description')) . '" width="400" /></a></p>';
										}
										else
										{
											$img = '<p><img src="' . Route::url($attachment->link()) . '" alt="' . $this->escape($attachment->get('description')) . '" /></p>';
										}
										echo $img;
									}
									else
									{
										?>
										<a class="attachment <?php echo Filesystem::extension($attachment->get('filename')); ?>" href="<?php echo Route::url($attachment->link()); ?>" title="<?php echo $this->escape($attachment->get('description')); ?>">
											<p class="attachment-description"><?php echo $this->escape($attachment->get('description')); ?></p>
											<p class="attachment-meta">
												<span class="attachment-size"><?php echo Hubzero\Utility\Number::formatBytes($attachment->size()); ?></span>
												<span class="attachment-action"><?php echo Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD'); ?></span>
											</p>
										</a>
										<?php
									}
								}
								else
								{
									?>
									<div class="attachment <?php echo Filesystem::extension($attachment->get('filename')); ?>" title="<?php echo $this->escape($attachment->get('description')); ?>">
										<p class="attachment-description"><?php echo $this->escape($attachment->get('description')); ?></p>
										<p class="attachment-meta">
											<span class="attachment-size"><?php echo $this->escape($attachment->get('filename')); ?></span>
											<span class="attachment-action"><?php echo Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND'); ?></span>
										</p>
									</div>
									<?php
								}
							}
							?>
						</div><!-- / .comment-body -->
					<?php } ?>
					</div><!-- / .comment-content -->
					<?php if ($this->row->access('update', 'tickets') > 0) { ?>
						<div class="comment-changelog">
							<?php echo $comment->changelog()->render(); ?>
						</div><!-- / .changelog -->
					<?php } ?>
				</li>
			<?php } ?>
			</ol>
		<?php } else { ?>
			<p class="no-comments"><?php echo Lang::txt('COM_SUPPORT_NO_COMMENTS_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .subject -->
		<aside class="aside">
		<?php if ($this->row->access('create', 'comments')) { ?>
			<p>
				<a class="icon-add add btn" href="#commentform"><?php echo Lang::txt('COM_SUPPORT_ADD_COMMENT'); ?></a>
			</p>
		<?php } ?>
	</aside><!-- / .aside -->
	</div>
</section><!-- / .below section -->
<?php } // ACL can read comments ?>

<?php if ($this->row->access('create', 'comments') || $this->row->access('update', 'tickets')) { ?>
<section class="below section">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<h3>
				<?php echo Lang::txt('COM_SUPPORT_COMMENT_FORM'); ?>
			</h3>
			<form action="<?php echo Route::url($this->row->link('update')); ?>" method="post" id="commentform" enctype="multipart/form-data">
				<p class="comment-member-photo">
					<span class="comment-anchor"></span>
					<?php
						$anon = 1;
						if (!User::isGuest())
						{
							$anon = 0;
						}
					?>
					<img src="<?php echo User::picture($anon); ?>" alt="" />
				</p>
				<fieldset>
					<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="ticket[id]" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="username" value="<?php echo User::get('username'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="update" />

					<input type="hidden" name="started" value="<?php echo Date::toSql(); ?>" />

					<input type="hidden" name="search" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="show" value="<?php echo $this->escape($this->filters['show']); ?>" />
					<input type="hidden" name="limit" value="<?php echo $this->escape($this->filters['limit']); ?>" />
					<input type="hidden" name="limistart" value="<?php echo $this->escape($this->filters['start']); ?>" />
					<?php if (!$this->row->access('create', 'private_comments')) { ?>
						<input type="hidden" name="access" value="0" />
					<?php } ?>

				<?php if ($this->row->access('update', 'tickets')) { ?>
					<fieldset>
					<?php if ($this->row->access('update', 'tickets') > 0) { ?>
						<legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS'); ?></span></legend>

						<div class="form-group">
							<label for="tags">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_TAGS'); ?>:<br />
								<?php
								$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->row->tags('string', null))));

								if (count($tf) > 0) {
									echo $tf[0];
								} else { ?>
									<input type="text" name="tags" id="tags" class="form-control" value="<?php echo $this->escape($this->row->tags('string')); ?>" />
								<?php } ?>
							</label>
						</div>

						<div class="grid">
							<div class="col span6">
								<div class="form-group">
									<label for="acgroup">
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_GROUP'); ?>:
										<?php
										$group = '';
										if ($this->row->get('group_id'))
										{
											if ($g = \Hubzero\User\Group::getInstance($this->row->get('group_id')))
											{
												$group = $g->get('cn');
											}
										}

										$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'ticket[group_id]', 'acgroup', '', $group, '', 'ticketowner')));
										if (count($gc) > 0) {
											echo $gc[0];
										} else { ?>
											<input type="text" name="ticket[group_id]" value="<?php echo $this->row->get('group_id'); ?>" class="form-control" id="acgroup" autocomplete="off" />
										<?php } ?>
									</label>
								</div>
							</div>
							<div class="col span6 omega">
								<div class="form-group">
									<label>
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_OWNER'); ?>:
										<?php echo $this->lists['owner']; ?>
									</label>
								</div>
							</div>
						</div>

						<div class="grid">
							<div class="col span6">
								<label for="ticket-field-severity">
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEVERITY'); ?>: <a class="icon-help tooltips popup" href="<?php echo Route::url('index.php?option=com_help&component=support&page=ticket#severity'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_HELP'); ?>"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_HELP'); ?></a>
									<select name="ticket[severity]" id="ticket-field-severity" class="form-control">
										<?php foreach ($this->lists['severities'] as $severity) { ?>
											<option value="<?php echo $severity; ?>"<?php if ($severity == $this->row->get('severity')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)); ?></option>
										<?php } ?>
									</select>
								</label>
							</div>
							<div class="col span6 omega">
								<div class="form-group">
									<label for="status">
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_STATUS'); ?>:
										<select name="ticket[status]" id="status" class="form-control">
											<optgroup label="<?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_OPEN'); ?>">
												<?php foreach (\Components\Support\Models\Status::allOpen()->rows() as $status) { ?>
													<option value="<?php echo $status->get('id'); ?>"<?php if ($this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
												<?php } ?>
											</optgroup>
											<optgroup label="<?php echo Lang::txt('COM_SUPPORT_CLOSED'); ?>">
												<option value="0"<?php if (!$this->row->isOpen() && $this->row->get('status') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED'); ?></option>
												<?php foreach (\Components\Support\Models\Status::allClosed()->rows() as $status) { ?>
													<option value="<?php echo $status->get('id'); ?>"<?php if (!$this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
												<?php } ?>
											</optgroup>
										</select>
									</label>
								</div>
					<?php } else { ?>
							<input type="hidden" name="tags" value="<?php echo $this->escape($this->row->tags('string')); ?>" />

						<?php if ($this->row->isSubmitter()) { ?>
							<?php if (!$this->row->isOpen()) { ?>
								<div class="form-group form-check">
									<label class="option form-check-label" for="field-status">
										<input class="option form-check-input" type="checkbox" name="ticket[status]" id="field-status" value="1" />
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_REOPEN'); ?>
									</label>
								</div>
							<?php } else { ?>
								<div class="form-group form-check">
									<label class="option form-check-label" for="field-status">
										<input class="option form-check-input" type="checkbox" name="ticket[status]" id="field-status" value="0" />
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_CLOSE'); ?>
									</label>
								</div>
							<?php } ?>
						<?php } ?>
					<?php } // ACL can update ticket (admin) ?>

					<?php if ($this->row->access('update', 'tickets') > 0) { ?>
							</div>
						</div>

						<div class="form-group">
							<label for="field-target_date">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_TARGET_DATE'); ?>:
								<input type="text" name="ticket[target_date]" class="datetime-field form-control" id="field-target_date" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::getRoot()) / 60); ?>" placeholder="YYYY-MM-DD hh:mm:ss" value="<?php echo ($this->row->get('target_date') && $this->row->get('target_date') != '0000-00-00 00:00:00') ? $this->escape(Date::of($this->row->get('target_date'))->toLocal('Y-m-d H:i:s')) : ''; ?>" />
							</label>
						</div>

						<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
							<div class="form-group">
								<label for="ticket-field-category">
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_CATEGORY'); ?>:
									<select name="ticket[category]" id="ticket-field-category" class="form-control">
										<option value=""><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
										<?php
										foreach ($this->lists['categories'] as $category)
										{
											?>
											<option value="<?php echo $this->escape($category->alias); ?>"<?php if ($this->row->get('category') == $category->alias) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->title)); ?></option>
											<?php
										}
										?>
									</select>
								</label>
							</div>
						<?php } ?>
					<?php } ?>
					</fieldset>
				<?php } else { ?>
					<input type="hidden" name="tags" value="<?php echo $this->escape($this->row->tags('string')); ?>" />
				<?php } // ACL can update tickets ?>

				<?php if ($this->row->access('create', 'comments') || $this->row->access('create', 'private_comments')) { ?>
					<?php
						$results = Event::trigger('support.onTicketComment', array($this->row));
						echo implode("\n", $results);
					?>
					<fieldset>
						<legend><?php echo Lang::txt('COM_SUPPORT_COMMENT_LEGEND_COMMENTS'); ?>:</legend>

					<?php if ($this->row->access('create', 'comments') > 0 || $this->row->access('create', 'private_comments')) { ?>
						<div class="top grouping">
					<?php } ?>
						<?php if ($this->row->access('create', 'comments') > 0) { ?>
							<div class="form-group">
								<label for="messages">
									<select name="messages" id="messages" class="form-control">
										<option value="mc"><?php echo Lang::txt('COM_SUPPORT_COMMENT_CUSTOM'); ?></option>
										<?php
										$hi = array();
										foreach ($this->lists['messages'] as $message)
										{
											?>
											<option value="m<?php echo $message->id; ?>"><?php echo $this->escape(stripslashes($message->title)); ?></option>
											<?php
											$hi[] = '<input type="hidden" name="m' . $message->id . '" id="m' . $message->id . '" value="' . $this->escape($message->transformMessage($this->row->get('id'))) . '" />' . "\n";
										}
										?>
									</select>
								</label>
								<?php echo implode("\n", $hi); ?>
							</div>
						<?php } // ACL can create comment (admin) ?>
						<?php if ($this->row->access('create', 'private_comments')) { ?>
							<div class="form-group form-check">
								<label for="make-private" class="form-check-label">
									<input class="option form-check-input" type="checkbox" name="access" id="make-private" value="1" />
									<?php echo Lang::txt('COM_SUPPORT_COMMENT_PRIVATE'); ?>
								</label>
							</div>
						<?php } // ACL can create private comments ?>
					<?php if ($this->row->access('create', 'comments') > 0 || $this->row->access('create', 'private_comments')) { ?>
						</div>
						<div class="clear"></div>
					<?php } // ACL can create comments (admin) or private comments ?>
						<div class="form-group">
							<textarea name="comment" id="comment" class="form-control" rows="13" cols="35"><?php echo $this->comment->get('comment'); ?></textarea>
						</div>

						<?php if ($this->row->access('create', 'comments') > 0) { ?>
							<?php if ($this->config->get('email_terse')) { ?>
								<div class="form-group form-check">
									<label for="email_terse" class="form-check-label">
										<input class="option form-check-input" type="checkbox" name="email_terse" id="email_terse" value="1" checked="checked" />
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_TERSE'); ?>
									</label>
								</div>
							<?php } ?>
						<?php } ?>
					</fieldset>

					<fieldset>
						<legend><?php echo Lang::txt('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
						<?php
						$tmp = Request::getString('tmp_dir', ('-' . time()), 'post');
						$this->js('jquery.fileuploader.js', 'system');
						$jbase = rtrim(Request::base(true), '/');
						?>
						<div id="ajax-uploader" data-action="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=upload&amp;ticket=<?php echo $this->row->get('id'); ?>&amp;comment=<?php echo $tmp; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=list&amp;ticket=<?php echo $this->row->get('id'); ?>&amp;comment=<?php echo $tmp; ?>">
							<noscript>
								<div class="form-group">
									<label for="upload">
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE'); ?>:
										<input type="file" name="upload[]" id="upload" class="form-control-file" multiple="multiple" />
									</label>
								</div>
								<div class="form-group">
									<label for="field-description">
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_FILE_DESCRIPTION'); ?>:
										<input type="text" name="description" id="field-description" class="form-control" value="" />
									</label>
								</div>
							</noscript>
						</div>
						<div class="field-wrap file-list" id="ajax-uploader-list">
							<?php
							$this->view('list', 'media')
								->set('model', $this->comment)
								->set('comment', $tmp)
								->set('ticket', $this->row->get('id'))
								->display();
							?>
						</div>
						<input type="hidden" name="tmp_dir" id="comment-tmp_dir" value="<?php echo $tmp; ?>" />
					</fieldset>
				<?php } //if ($this->row->access('create', 'comments') || $this->row->access('create', 'private_comments')) { ?>

				<?php if ($this->row->access('create', 'comments') > 0) { ?>
					<fieldset>
						<legend><?php echo Lang::txt('COM_SUPPORT_COMMENT_LEGEND_EMAIL'); ?>:</legend>
						<div class="grid">
							<div class="col span6">
								<div class="form-group">
									<label for="email_submitter" class="form-check-label">
										<input class="option form-check-input" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
									</label>
								</div>
							</div>
							<div class="col span6 omega">
								<div class="form-group">
									<label for="email_owner" class="form-check-label">
										<input class="option form-check-input" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
										<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'); ?>
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="acmembers">
								<?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'); ?>: <?php
								$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', implode(', ', $cc))));
								if (count($mc) > 0) {
									echo '<span class="hint">' . Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
								} else { ?> <span class="hint"><?php echo Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
								<input type="text" name="cc" id="acmembers" class="form-control" value="<?php echo implode(', ', $cc); ?>" size="35" />
								<?php } ?>
							</label>
						</div>
					</fieldset>
				<?php } else { ?>
					<input type="hidden" name="email_submitter" id="email_submitter" value="1" />
					<input type="hidden" name="email_owner" id="email_owner" value="1" />
					<input type="hidden" name="cc" id="acmembers" value="<?php echo implode(', ', $cc); ?>" />
				<?php } // ACL can create comments (admin) ?>

					<?php echo Html::input('token'); ?>

					<p class="submit">
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_SUPPORT_SUBMIT_COMMENT'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .subject -->
		<aside class="aside">
		<p><?php echo Lang::txt('COM_SUPPORT_COMMENT_FORM_EXPLANATION'); ?></p>
	</aside><!-- / .aside -->
	</div>
</section><!-- / .section -->
<?php } // ACL can create comments
