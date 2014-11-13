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

$this->css()
     ->js();

$juser = JFactory::getUser();

JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

$status = $this->row->status('text');

$unknown  = 1;
//$name     = JText::_('COM_SUPPORT_UNKNOWN');
$usertype = JText::_('COM_SUPPORT_UNKNOWN');

if ($this->row->get('login'))
{
	$submitter = $this->row->submitter();
	if ($submitter->get('uidNumber'))
	{
		jimport( 'joomla.user.helper' );
		$usertype = implode(', ', JUserHelper::getUserGroups($submitter->get('uidNumber')));

		$name = '<a rel="profile" href="' . JRoute::_('index.php?option=com_members&id=' . $submitter->get('uidNumber')) . '">' . $this->escape(stripslashes($submitter->get('name'))) . ' (' . $this->escape(stripslashes($this->row->get('login'))) . ')</a>';
		$unknown = 0;
	}
	else
	{
		$name  = '<a rel="email" href="mailto:'. $this->row->get('email') .'">';
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

$sq = new SupportQuery($this->database);
$sq->load($this->filters['show']);
if ($sq->conditions)
{
	$tbl = new SupportTicket($this->database);

	$sq->query = $sq->getQuery($sq->conditions);

	$this->filters['sort']    = $sq->sort;
	$this->filters['sortdir'] = $sq->sort_dir;
	if ($rows = $tbl->getRecords($sq->query, $this->filters))
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

$cc = array();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($prev) { ?>
			<li>
				<a class="icon-prev prev btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=ticket&id=' . $prev->id . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo JText::_('COM_SUPPORT_PREV'); ?>
				</a>
			</li>
		<?php } ?>
			<li>
				<a class="browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=tickets&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo JText::_('COM_SUPPORT_TICKETS'); ?>
				</a>
			</li>
		<?php if ($next) { ?>
			<li>
				<a class="icon-next next opposite btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=ticket&id=' . $next->id . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>">
					<?php echo JText::_('COM_SUPPORT_NEXT'); ?>
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

	<div class="subject">
		<div class="ticket entry" id="t<?php echo $this->row->get('id'); ?>">
			<p class="entry-member-photo">
				<span class="entry-anchor"></span>
				<img src="<?php echo $this->row->submitter()->getPicture($unknown); ?>" alt="" />
			</p><!-- / .entry-member-photo -->
			<div class="entry-content">
				<p class="entry-title">
					<strong><?php echo $name; ?></strong>
					<a class="permalink" href="<?php echo JRoute::_($this->row->link()); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">
						<span class="entry-date-at"><?php echo JText::_('COM_SUPPORT_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('time'); ?></time></span>
						<span class="entry-date-on"><?php echo JText::_('COM_SUPPORT_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('date'); ?></time></span>
					</a>
				</p><!-- / .entry-title -->
				<div class="entry-body">
					<p><?php echo $this->row->content('parsed'); ?></p>
					<?php if ($this->row->attachments()->total()) { ?>
						<div class="comment-attachments">
							<?php
							foreach ($this->row->attachments() as $attachment)
							{
								if (!trim($attachment->get('description')))
								{
									$attachment->set('description', $attachment->get('filename'));
								}

								if ($attachment->isImage())
								{
									if ($attachment->width() > 400)
									{
										$img = '<p><a href="' . JRoute::_($attachment->link()) . '"><img src="' . JRoute::_($attachment->link()) . '" alt="' . $attachment->get('description') . '" width="400" /></a></p>';
									}
									else
									{
										$img = '<p><img src="' . JRoute::_($attachment->link()) . '" alt="' . $attachment->get('description') . '" /></p>';
									}
									echo $img;
								}
								else
								{
									echo '<p class="attachment"><a href="' . JRoute::_($attachment->link()) . '" title="' . $attachment->get('description') . '">' . $attachment->get('description') . '</a></p>';
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
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_EMAIL'); ?>:</th>
								<td><a href="mailto:<?php echo $this->row->get('email'); ?>"><?php echo $this->escape($this->row->get('email')); ?></a></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_USERTYPE'); ?>:</th>
								<td><?php echo $this->escape($usertype); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_OS'); ?>:</th>
								<td><?php echo $this->escape($this->row->get('os')); ?> / <?php echo $this->escape($this->row->get('browser')); ?> (<?php echo ($this->row->get('cookies')) ? JText::_('COM_SUPPORT_COOKIES_ENABLED') : JText::_('COM_SUPPORT_COOKIES_DISABLED'); ?>)</td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_IP'); ?>:</th>
								<td><?php echo $this->escape($this->row->get('ip')); ?> (<?php echo  $this->escape($this->row->get('hostname')); ?>)</td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_REFERRER'); ?>:</th>
								<td><?php echo $this->escape($this->row->get('referrer', ' ')); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_INSTANCES'); ?>:</th>
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
				<strong><?php echo (!$this->row->isOpen()) ? JText::_('COM_SUPPORT_TICKET_STATUS_CLOSED_TICKET') : JText::_('COM_SUPPORT_TICKET_STATUS_OPEN_TICKET'); ?></strong>
			</p>
			<?php if (!$this->row->isOpen()) { ?>
				<p><?php echo JText::_('COM_SUPPORT_NOTE_TO_REOPEN'); ?></p>
			<?php } ?>
		</div><!-- / .entry-status -->

		<div class="ticket-watch">
			<?php if ($this->row->isWatching()) { ?>
				<div id="watching">
					<p><?php echo JText::_('COM_SUPPORT_CURRENTLY_WATCHING'); ?></p>
					<p><a class="stop-watching btn" href="<?php echo JRoute::_($this->row->link('stopWatching') . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>"><?php echo JText::_('COM_SUPPORT_STOP_WATCHING'); ?></a></p>
				</div>
			<?php } else { ?>
				<p><a class="start-watching btn" href="<?php echo JRoute::_($this->row->link('startWatching') . '&show=' . $this->filters['show'] . '&search=' . $this->filters['search'] . '&limit='.$this->filters['limit'] . '&limitstart=' . $this->filters['start']); ?>"><?php echo JText::_('COM_SUPPORT_WATCHING_TICKET'); ?></a></p>
			<?php } ?>
			<p><?php echo JText::_('COM_SUPPORT_WATCHING_EXPLANATION'); ?></p>
		</div>
	</aside><!-- / .aside -->
</section><!-- / .main section -->

<?php if ($this->row->access('read', 'comments')) { ?>
<section class="below section">
	<div class="subject">
		<h3><?php echo JText::_('COM_SUPPORT_TICKET_COMMENTS'); ?></h3>

	<?php if ($this->row->comments()->total() > 0) { ?>
		<ol class="comments">
		<?php
		$o = 'even';
		$i = 0;
		foreach ($this->row->comments() as $comment)
		{
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
			if ($comment->get('created_by') == $this->row->submitter('username') && !$comment->isPrivate())
			{
				$access = 'submitter';
			}

			$name = JText::_('COM_SUPPORT_UNKNOWN');
			$cite = $name;

			if ($comment->creator())
			{
				$cite = $this->escape(stripslashes($comment->creator('name')));
				$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $comment->creator('id')) . '">' . $cite . ' (' . $this->escape(stripslashes($comment->creator('username'))) . ')</a>';
			}

			$o = ($o == 'odd') ? 'even' : 'odd';

			if ($comment->changelog()->format() != 'html')
			{
				$cc = $comment->changelog()->get('cc');
			}
			?>
			<li class="comment <?php echo $access . ' ' . $o; ?>" id="c<?php echo $comment->get('id'); ?>">
				<p class="comment-member-photo">
					<img src="<?php echo $comment->creator('picture'); ?>" alt="" />
				</p>
				<div class="comment-content">
					<p class="comment-head">
						<strong>
							<?php echo $name; ?>
						</strong>
						<a class="permalink" href="<?php echo JRoute::_($comment->link()); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">
							<span class="comment-date-at"><?php echo JText::_('COM_SUPPORT_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo JText::_('COM_SUPPORT_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('date'); ?></time></span>
						</a>
					</p><!-- / .comment-head -->
				<?php if ($content = $comment->content('parsed')) { ?>
					<div class="comment-body">
						<p><?php echo $content; ?></p>
					</div><!-- / .comment-body -->
				<?php } ?>
				<?php if ($comment->attachments()->total()) { ?>
					<div class="comment-attachments">
						<?php
						foreach ($comment->attachments() as $attachment)
						{
							if (!trim($attachment->get('description')))
							{
								$attachment->set('description', $attachment->get('filename'));
							}

							if ($attachment->isImage())
							{
								if ($attachment->width() > 400)
								{
									$img = '<p><a href="' . JRoute::_($attachment->link()) . '"><img src="' . JRoute::_($attachment->link()) . '" alt="' . $attachment->get('description') . '" width="400" /></a></p>';
								}
								else
								{
									$img = '<p><img src="' . JRoute::_($attachment->link()) . '" alt="' . $attachment->get('description') . '" /></p>';
								}
								echo $img;
							}
							else
							{
								echo '<p class="attachment"><a href="' . JRoute::_($attachment->link()) . '" title="' . $attachment->get('description') . '">' . $attachment->get('description') . '</a></p>';
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
		<p class="no-comments"><?php echo JText::_('COM_SUPPORT_NO_COMMENTS_FOUND'); ?></p>
	<?php } ?>
	</div><!-- / .subject -->
	<aside class="aside">
		<?php if ($this->row->access('create', 'comments')) { ?>
			<p>
				<a class="icon-add add btn" href="#commentform"><?php echo JText::_('COM_SUPPORT_ADD_COMMENT'); ?></a>
			</p>
		<?php } ?>
	</aside><!-- / .aside -->
</section><!-- / .below section -->
<?php } // ACL can read comments ?>

<?php if ($this->row->access('create', 'comments') || $this->row->access('update', 'tickets')) { ?>
<section class="below section">
	<div class="subject">
		<h3>
			<?php echo JText::_('COM_SUPPORT_COMMENT_FORM'); ?>
		</h3>
		<form action="<?php echo JRoute::_($this->row->link('update')); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<span class="comment-anchor"></span>
				<?php
					$jxuser = new \Hubzero\User\Profile();

					$anon = 1;
					if (!$juser->get('guest'))
					{
						$jxuser->load($juser->get('id'));
						$anon = 0;
					}
				?>
				<img src="<?php echo $jxuser->getPicture($anon); ?>" alt="" />
			</p>
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="ticket[id]" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="update" />

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
					<legend><span><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS'); ?></span></legend>

					<label>
						<?php echo JText::_('COM_SUPPORT_COMMENT_TAGS'); ?>:<br />
						<?php
						$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->row->tags('string', null))));

						if (count($tf) > 0) {
							echo $tf[0];
						} else { ?>
							<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->row->tags('string')); ?>" />
						<?php } ?>
					</label>

					<div class="grid">
						<div class="col span6">
							<label>
								<?php echo JText::_('COM_SUPPORT_COMMENT_GROUP'); ?>:
								<?php
								$gc = $dispatcher->trigger('onGetSingleEntryWithSelect', array(array('groups', 'ticket[group]', 'acgroup', '', $this->row->get('group'), '', 'ticketowner')));
								if (count($gc) > 0) {
									echo $gc[0];
								} else { ?>
									<input type="text" name="ticket[group]" value="<?php echo $this->row->get('group'); ?>" id="acgroup" value="" autocomplete="off" />
								<?php } ?>
							</label>
						</div>
						<div class="col span6 omega">
							<label>
								<?php echo JText::_('COM_SUPPORT_COMMENT_OWNER'); ?>:
								<?php echo $this->lists['owner']; ?>
							</label>
						</div>
					</div>

					<div class="grid">
						<div class="col span6">
							<label>
								<?php echo JText::_('COM_SUPPORT_COMMENT_SEVERITY'); ?>:
								<?php echo SupportHtml::selectArray('ticket[severity]', $this->lists['severities'], $this->row->get('severity')); ?>
							</label>
						</div>
						<div class="col span6 omega">
							<label for="status">
								<?php echo JText::_('COM_SUPPORT_COMMENT_STATUS'); ?>:
								<select name="ticket[status]" id="status">
									<optgroup label="<?php echo JText::_('COM_SUPPORT_COMMENT_OPT_OPEN'); ?>">
										<?php foreach ($this->row->statuses('open') as $status) { ?>
											<option value="<?php echo $status->get('id'); ?>"<?php if ($this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
										<?php } ?>
									</optgroup>
									<optgroup label="<?php echo JText::_('COM_SUPPORT_CLOSED'); ?>">
										<option value="0"<?php if (!$this->row->isOpen() && $this->row->get('status') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_COMMENT_OPT_CLOSED'); ?></option>
										<?php foreach ($this->row->statuses('closed') as $status) { ?>
											<option value="<?php echo $status->get('id'); ?>"<?php if (!$this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
										<?php } ?>
									</optgroup>
								</select>
							</label>
				<?php } else { ?>
						<input type="hidden" name="tags" value="<?php echo $this->escape($this->row->tags('string')); ?>" />

					<?php if ($this->row->isSubmitter()) { ?>
						<?php if (!$this->row->isOpen()) { ?>
							<label class="option" for="field-status">
								<input class="option" type="checkbox" name="ticket[status]" id="field-status" value="1" />
								<?php echo JText::_('COM_SUPPORT_COMMENT_REOPEN'); ?>
							</label>
						<?php } else { ?>
							<label class="option" for="field-status">
								<input class="option" type="checkbox" name="ticket[status]" id="field-status" value="0" />
								<?php echo JText::_('COM_SUPPORT_COMMENT_CLOSE'); ?>
							</label>
						<?php } ?>
					<?php } ?>
				<?php } // ACL can update ticket (admin) ?>
						
				<?php if ($this->row->access('update', 'tickets') > 0) { ?>
						</div>
					</div>

					<?php if (isset($this->lists['categories']) && $this->lists['categories'])  { ?>
					<label for="ticket-field-category">
						<?php echo JText::_('COM_SUPPORT_COMMENT_CATEGORY'); ?>
						<select name="ticket[category]" id="ticket-field-category">
							<option value=""><?php echo JText::_('COM_SUPPORT_NONE'); ?></option>
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
					<?php } ?>
				<?php } ?>
				</fieldset>
			<?php } else { ?>
				<input type="hidden" name="tags" value="<?php echo $this->escape($this->row->tags('string')); ?>" />
			<?php } // ACL can update tickets ?>

			<?php if ($this->row->access('create', 'comments') || $this->row->access('create', 'private_comments')) { ?>
				<?php
					JPluginHelper::importPlugin('support');
					$results = $dispatcher->trigger('onTicketComment', array($this->row));
					echo implode("\n", $results);
				?>
				<fieldset>
					<legend><?php echo JText::_('COM_SUPPORT_COMMENT_LEGEND_COMMENTS'); ?>:</legend>

				<?php if ($this->row->access('create', 'comments') > 0 || $this->row->access('create', 'private_comments')) { ?>
					<div class="top grouping">
				<?php } ?>
					<?php if ($this->row->access('create', 'comments') > 0) { ?>
						<label for="messages">
							<?php
							$hi = array();
							$o  = '<select name="messages" id="messages">' . "\n";
							$o .= "\t" . '<option value="mc">' . JText::_('COM_SUPPORT_COMMENT_CUSTOM') . '</option>' . "\n";
							$jconfig = JFactory::getConfig();
							foreach ($this->lists['messages'] as $message)
							{
								$message->message = str_replace('"', '&quot;', $message->message);
								$message->message = str_replace('&quote;', '&quot;', $message->message);
								$message->message = str_replace('#XXX', '#' . $this->row->get('id'), $message->message);
								$message->message = str_replace('{ticket#}', $this->row->get('id'), $message->message);
								$message->message = str_replace('{sitename}', $jconfig->getValue('config.sitename'), $message->message);
								$message->message = str_replace('{siteemail}', $jconfig->getValue('config.mailfrom'), $message->message);

								$o .= "\t".'<option value="m' . $message->id . '">' . $this->escape(stripslashes($message->title)) . '</option>' . "\n";

								$hi[] = '<input type="hidden" name="m' . $message->id . '" id="m' . $message->id . '" value="' . $this->escape(stripslashes($message->message)) . '" />' . "\n";
							}
							$o .= '</select>' . "\n";
							echo $o;
							?>
						</label>
						<?php echo implode("\n", $hi); ?>
					<?php } // ACL can create comment (admin) ?>
					<?php if ($this->row->access('create', 'private_comments')) { ?>
						<label>
							<input class="option" type="checkbox" name="access" id="make-private" value="1" />
							<?php echo JText::_('COM_SUPPORT_COMMENT_PRIVATE'); ?>
						</label>
					<?php } // ACL can create private comments ?>
				<?php if ($this->row->access('create', 'comments') > 0 || $this->row->access('create', 'private_comments')) { ?>
					</div>
					<div class="clear"></div>
				<?php } // ACL can create comments (admin) or private comments ?>
					<textarea name="comment" id="comment" rows="13" cols="35"></textarea>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
					<?php
					$tmp = ('-' . time());
					$this->js('jquery.fileuploader.js', 'system');
					$jbase = rtrim(JURI::getInstance()->base(true), '/');
					?>
					<div id="ajax-uploader" data-action="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=upload&amp;ticket=<?php echo $this->row->get('id'); ?>&amp;comment=<?php echo $tmp; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_support&amp;no_html=1&amp;controller=media&amp;task=list&amp;ticket=<?php echo $this->row->get('id'); ?>&amp;comment=<?php echo $tmp; ?>">
						<noscript>
							<label for="upload">
								<?php echo JText::_('COM_SUPPORT_COMMENT_FILE'); ?>:
								<input type="file" name="upload" id="upload" />
							</label>

							<label for="field-description">
								<?php echo JText::_('COM_SUPPORT_COMMENT_FILE_DESCRIPTION'); ?>:
								<input type="text" name="description" id="field-description" value="" />
							</label>
						</noscript>
					</div>
					<div class="field-wrap file-list" id="ajax-uploader-list">
					</div>
					<input type="hidden" name="tmp_dir" id="comment-tmp_dir" value="<?php echo $tmp; ?>" />
				</fieldset>
			<?php } //if ($this->row->access('create', 'comments') || $this->row->access('create', 'private_comments')) { ?>

			<?php if ($this->row->access('create', 'comments') > 0) { ?>
				<fieldset>
					<legend><?php echo JText::_('COM_SUPPORT_COMMENT_LEGEND_EMAIL'); ?>:</legend>
					<div class="grid">
						<div class="col span6">
							<label for="email_submitter">
								<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
								<?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
							</label>
						</div>
						<div class="col span6 omega">
							<label for="email_owner">
								<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
								<?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'); ?>
							</label>
						</div>
					</div>

					<label>
						<?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'); ?>: <?php
						$mc = $dispatcher->trigger('onGetMultiEntry', array(array('members', 'cc', 'acmembers', '', implode(', ', $cc))));
						if (count($mc) > 0) {
							echo '<span class="hint">' . JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
						} else { ?> <span class="hint"><?php echo JText::_('COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
						<input type="text" name="cc" id="acmembers" value="<?php echo implode(', ', $cc); ?>" size="35" />
						<?php } ?>
					</label>
				</fieldset>
			<?php } else { ?>
				<input type="hidden" name="email_submitter" id="email_submitter" value="1" />
				<input type="hidden" name="email_owner" id="email_owner" value="1" />
			<?php } // ACL can create comments (admin) ?>

				<?php echo JHTML::_('form.token'); ?>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('COM_SUPPORT_SUBMIT_COMMENT'); ?>" />
				</p>
			</fieldset>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo JText::_('COM_SUPPORT_COMMENT_FORM_EXPLANATION'); ?></p>
	</aside><!-- / .aside -->
</section><!-- / .section -->
<?php } // ACL can create comments ?>
