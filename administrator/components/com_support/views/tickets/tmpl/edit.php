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
defined('_JEXEC') or die( 'Restricted access' );

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));

JToolBarHelper::title(JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_TICKET') . ': ' . $text, 'support.png');
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('ticket');

$juser = JFactory::getUser();

$user = new \Hubzero\User\Profile();
$user->load($juser->get('id'));

$unknown  = true;
$name     = '';
$usertype = JText::_('COM_SUPPORT_UNKNOWN');
$notify   = array();

if ($this->row->get('login'))
{
	if ($this->row->submitter('name'))
	{
		jimport('joomla.user.helper');
		$usertype = implode(', ', JUserHelper::getUserGroups($this->row->submitter('id')));

		$name = '<a rel="profile" href="' . JRoute::_('index.php?option=com_members&amp;task=edit&amp;id[]=' . $this->row->submitter('id')) . '">' . $this->escape($this->row->submitter('name', $this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->submitter('username'))) . ')</a>';
		$unknown = false;

		$notify[] = $this->escape($this->row->submitter('name', $this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->submitter('username'))) . ')';
	}
}

if (!$name)
{
	if ($this->row->get('name'))
	{
		$name  = $this->escape($this->row->get('name')) . ' (' . $this->escape($this->row->get('email')) . ')';
	}
	else
	{
		$name  = $this->escape($this->row->get('email'));
	}
	$notify[] = $name;
}

$owner = new \Hubzero\User\Profile();
if ($this->row->isOwned())
{
	if ($this->row->owner('name'))
	{
		$notify[] = $this->escape(stripslashes($this->row->owner('name'))) . ' (' . $this->escape(stripslashes($this->row->owner('username'))) . ')';
	}
}

$lastactivity = JText::_('COM_SUPPORT_NOT_APPLICAPABLE');
if ($this->row->comments()->total() > 0)
{
	$last = $this->row->comments()->last();
	$lastactivity = '<time datetime="' . $last->created() . '">' . JHTML::_('date', $last->created(), JText::_('TIME_FORMAT_HZ1')) . '</time>';
	$this->row->comments()->rewind();
}

JHTML::_('behavior.tooltip');
$this->css();

JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

$cc = array();
?>
<form action="index.php" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="col width-70 fltlft">
		<fieldset>
			<legend><span><?php echo JText::_('COM_SUPPORT_TICKET'); echo ($this->row->get('id')) ? ' #' . $this->row->get('id') : ''; ?></span></legend>

			<div class="ticket" id="t<?php echo $this->row->get('id'); ?>">
				<p class="ticket-member-photo">
					<span class="ticket-anchor"></span>
					<img src="<?php echo $this->row->submitter()->getPicture($unknown); ?>" alt="" />
				</p>
				<div class="ticket-head">
					<strong>
						<?php echo $name; ?>
					</strong>
					<a class="permalink" href="index.php?option=com_support&amp;controller=tickets&amp;task=edit&amp;id=<?php echo $this->row->get('id'); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">
						<span class="time-at"><?php echo JText::_('COM_SUPPORT_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('time'); ?></time></span>
						<span class="date-on"><?php echo JText::_('COM_SUPPORT_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('date'); ?></time></span>
					</a>
				</div>
				<blockquote class="ticket-content" cite="<?php echo $this->escape($this->row->get('login', $this->row->get('name'))); ?>">
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
				</blockquote><!-- / .ticket-content -->
				<div class="ticket-details">
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
								<td><?php echo $this->escape($this->row->get('ip')); ?> (<?php echo $this->escape($this->row->get('hostname')); ?>)</td>
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
								<td colspan="2"><?php echo $this->escape($this->row->get('uas')); ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div><!-- / .ticket-details -->
			</div><!-- / .ticket -->
		</fieldset>
	</div><!-- / .col width-70 fltlft -->
	<div class="col width-30 fltrt">
		<dl class="ticket-status <?php if (!$this->row->isOpen()) { echo 'closed'; } else { echo 'open'; } ?>">
			<dt><?php echo JText::_('COM_SUPPORT_TICKET_STATUS'); ?></dt>
			<dd><?php echo (!$this->row->isOpen()) ? JText::_('COM_SUPPORT_TICKET_STATUS_CLOSED') : JText::_('COM_SUPPORT_TICKET_STATUS_OPEN'); ?></dd>
		</dl>

		<table class="meta">
			<tbody>
				<tr>
					<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_SEVERITY'); ?></th>
					<td><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($this->row->get('severity'))); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_OWNER'); ?></th>
					<td><?php
					if ($this->row->isOwned())
					{
						if ($this->row->owner('id'))
						{
							echo '<a rel="profile" href="index.php?option=com_members&amp;task=edit&amp;id[]=' . $this->row->owner('id') . '">' . $this->escape(stripslashes($this->row->owner('name'))) . '</a>';
						}
						else
						{
							echo $this->escape($this->row->get('owner'));
						}
					}
					else
					{
						echo JText::_('COM_SUPPORT_NONE');
					}
					?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS_LAST_ACTIVITY'); ?></th>
					<td><?php echo $lastactivity; ?></td>
				</tr>
			</tbody>
		</table>

		<div class="ticket-watch">
		<?php if ($this->row->isWatching()) { ?>
			<div id="watching">
				<p><?php echo JText::_('COM_SUPPORT_WATCH_TICKET_IN_LIST'); ?></p>
				<p><a class="stop-watching btn" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $this->row->get('id'); ?>&amp;watch=stop"><?php echo JText::_('COM_SUPPORT_WATCH_TICKET_STOP_WATCHING'); ?></a></p>
			</div>
		<?php } else { ?>
			<p><a class="start-watching btn" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $this->row->get('id'); ?>&amp;watch=start"><?php echo JText::_('COM_SUPPORT_WATCH_TICKET_START_WATCHING'); ?></a></p>
		<?php } ?>
			<p><?php echo JText::_('COM_SUPPORT_WATCH_TICKET_ABOUT'); ?></p>
		</div>
	</div><!-- / .col width-30 fltlft -->
	<div class="clr"></div>

	<?php if ($this->row->comments()->total() > 0) { ?>
		<div class="col width-70 fltlft">
			<fieldset>
				<legend><span><?php echo JText::_('COM_SUPPORT_TICKET_COMMENTS'); ?></span></legend>

				<ol class="comments">
				<?php
				foreach ($this->row->comments() as $comment)
				{
					$access = 'public';
					if ($comment->isPrivate())
					{
						$access = 'private';
					}

					if ($comment->get('created_by') == $this->row->get('login') && !$comment->isPrivate())
					{
						$access = 'submitter';
					}

					$name = JText::_('COM_SUPPORT_UNKNOWN');
					$cite = $name;

					if ($comment->creator())
					{
						$cite = $this->escape(stripslashes($comment->creator('name')));
						$name = '<a href="' . JRoute::_('index.php?option=com_members&task=edit&id[]=' . $comment->creator('id')) . '">' . $cite . ' (' . $this->escape($comment->creator('username')) . ')</a>';
					}

					if ($comment->changelog()->format() != 'html')
					{
						$cc = $comment->changelog()->get('cc');
					}
					?>
					<li class="<?php echo $access .' comment'; ?>" id="c<?php echo $comment->get('id'); ?>">
						<p class="comment-member-photo">
							<span class="comment-anchor"></span>
							<img src="<?php echo $comment->creator('picture'); ?>" alt="<?php echo JText::_('COM_SUPPORT_PROFILE_IMAGE'); ?>" />
						</p>
						<p class="comment-head">
							<strong>
								<?php echo $name; ?>
							</strong>
							<a class="permalink" href="<?php echo 'index.php?option=com_support&amp;controller=tickets&amp;task=edit&amp;id=' . $this->row->get('id') . '#c' . $comment->get('id'); ?>" title="<?php echo JText::_('COM_SUPPORT_PERMALINK'); ?>">
								<span class="time-at"><?php echo JText::_('COM_SUPPORT_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('time'); ?></time></span>
								<span class="date-on"><?php echo JText::_('COM_SUPPORT_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('date'); ?></time></span>
							</a>
						</p>
						<blockquote class="comment-content" cite="<?php echo $cite; ?>">
						<?php if ($content = $comment->content('parsed')) { ?>
							<p><?php echo $content; ?></p>
						<?php } else { ?>
							<p class="comment-none"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_NO_CONTENT'); ?></p>
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
						</blockquote><!-- / .comment-content -->
						<div class="comment-changelog">
							<?php echo $comment->changelog()->render(); ?>
						</div><!-- / .changelog -->
					</li>
					<?php
				}
				?>
				</ol>
			</fieldset>
		</div><!-- / .col width-70 -->
		<div class="col width-30 fltrt">
			<p>
				<a class="new button" href="#commentform"><?php echo JText::_('COM_SUPPORT_TICKET_ADD_COMMENT'); ?></a>
			</p>
		</div><!-- / .col width-30 -->
		<div class="clr"></div>
	<?php } // end if (count($comments) > 0) ?>

	<div class="col width-70 fltlft">
		<fieldset id="commentform">
			<legend><span><?php echo JText::_('COM_SUPPORT_TICKET_DETAILS'); ?></span></legend>

			<div class="new ticket">
				<p class="ticket-member-photo">
					<span class="ticket-anchor"></span>
					<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($user, 0); ?>" alt="<?php echo JText::_('COM_SUPPORT_PROFILE_IMAGE'); ?>" />
				</p>

				<fieldset class="ticket-head">
					<strong>
						<a rel="profile" href="index.php?option=com_members&amp;task=edit&amp;id[]=<?php echo $this->escape($user->get('id')); ?>">
							<?php echo $this->escape($user->get('name')); ?> (<?php echo $this->escape($user->get('username')); ?>)
						</a>
					</strong>
					<span class="permalink">
						<span class="time-at"><?php echo JText::_('COM_SUPPORT_AT'); ?></span>
						<span class="time"><time><?php echo JHTML::_('date', JFactory::getDate()->toSql(), JText::_('TIME_FORMAT_HZ1')); ?></time></span>
						<span class="date-on"><?php echo JText::_('COM_SUPPORT_ON'); ?></span>
						<span class="date"><time><?php echo JHTML::_('date', JFactory::getDate()->toSql(), JText::_('DATE_FORMAT_HZ1')); ?></time></span>
					</span>

					<label for="comment-field-access" class="private hasTip" title="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?>">
						<input type="checkbox" name="access" id="comment-field-access" value="1" />
						<span><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS'); ?></span>
					</label>
				</fieldset><!-- / .ticket-head -->

				<div class="ticket-content">
					<?php
						JPluginHelper::importPlugin('support');
						$results = $dispatcher->trigger('onTicketComment', array($this->row));
						echo implode("\n", $results);
					?>
					<fieldset>
						<div class="input-wrap">
							<label for="comment-field-template">
								<select name="messages" id="comment-field-template">
									<option value="custom"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_CUSTOM'); ?></option>
									<?php
									$hi = array();
									$jconfig = JFactory::getConfig();
									foreach ($this->lists['messages'] as $message)
									{
										$message->message = str_replace('"','&quot;', stripslashes($message->message));
										$message->message = str_replace('&quote;', '&quot;', $message->message);
										$message->message = str_replace('#XXX', '#' . $this->row->get('id'), $message->message);
										$message->message = str_replace('{ticket#}', $this->row->get('id'), $message->message);
										$message->message = str_replace('{sitename}', $jconfig->getValue('config.sitename'), $message->message);
										$message->message = str_replace('{siteemail}', $jconfig->getValue('config.mailfrom'), $message->message);
										?>
											<option value="m<?php echo $message->id; ?>"><?php echo $this->escape(stripslashes($message->title)); ?></option>
										<?php
										$hi[] = '<input type="hidden" name="m' . $message->id . '" id="m' . $message->id . '" value="' . $this->escape(stripslashes($message->message)) . '" />';
									}
									?>
								</select>
								<?php echo implode("\n", $hi); ?>
							</label>

							<label for="comment-field-content">
								<span class="label"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS'); ?></span>
								<textarea name="comment" id="comment-field-comment" cols="75" rows="15"></textarea>
							</label>
						</div>

						<div class="col width-50 fltlft">
							<div class="input-wrap">
								<label for="comment-field-upload">
									<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>
									<input type="file" name="upload" id="comment-field-upload" />
								</label>
							</div>
						</div>
						<div class="col width-50 fltrt">
							<div class="input-wrap">
								<label for="comment-field-description">
									<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION'); ?>
									<input type="text" name="description" id="comment-field-description" value="" />
								</label>
							</div>
						</div>
						<div class="clr"></div>

						<div class="input-wrap">
							<label for="comment-field-message">
								<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?> <?php
								$mc = $dispatcher->trigger('onGetMultiEntry', array(
									array(
										'members',   // The component to call
										'cc',        // Name of the input field
										'comment-field-message', // ID of the input field
										'',          // CSS class(es) for the input field
										implode(', ', $cc) // The value of the input field
									)
								));
								if (count($mc) > 0) {
									echo '<span class="hint">' . JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
								} else { ?> <span class="hint"><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
									<input type="text" name="cc" id="comment-field-message" value="<?php echo implode(', ', $cc); ?>" />
								<?php } ?>
							</label>
						</div>
						<div class="col width-50 fltlft">
							<div class="input-wrap">
								<label for="email_submitter">
									<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
									<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
								</label>
							</div>
						</div>
						<div class="col width-50 fltrt">
							<div class="input-wrap">
								<label for="email_owner">
									<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
									<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER'); ?>
								</label>
							</div>
						</div>
						<div class="clr"></div>
					</fieldset>
				</div><!-- / .ticket-content -->

				<fieldset class="ticket-details">
					<div class="input-wrap">
						<label for="tags">
							<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_TAGS'); ?>
							<?php
							$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->row->tags('string'))));

							if (count($tf) > 0) {
								echo $tf[0];
							} else { ?>
								<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->row->tags('string', null)); ?>" />
							<?php } ?>
						</label>
					</div>

					<div class="col width-50 fltlft">
						<div class="input-wrap">
							<label for="ticket-field-group">
								<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:<br />
								<?php
								$gc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','', $this->row->get('group'),'','owner')) );
								if (count($gc) > 0) {
									echo $gc[0];
								} else { ?>
								<input type="text" name="group" value="<?php echo $this->escape($this->row->get('group')); ?>" id="acgroup" value="" size="30" autocomplete="off" />
								<?php } ?>
							</label>
						</div>
					</div>
					<div class="col width-50 fltrt">
						<div class="input-wrap">
							<label for="owner">
								<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OWNER'); ?>
								<?php echo $this->lists['owner']; ?>
							</label>
						</div>
					</div>
					<div class="clr"></div>

				<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
					<div class="input-wrap">
						<label for="ticket-field-category">
							<?php echo JText::_('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
							<select name="category" id="ticket-field-category">
								<option value=""><?php echo JText::_('COM_SUPPORT_NONE'); ?></option>
								<?php
								foreach ($this->lists['categories'] as $category)
								{
									?>
								<option value="<?php echo $this->escape($category->alias); ?>"<?php if ($category->alias == $this->row->get('category')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->title)); ?></option>
									<?php
								}
								?>
							</select>
						</label>
					</div>
				<?php } ?>

					<div class="col width-50 fltlft">
						<div class="input-wrap">
							<label for="ticket-field-severity">
								<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_SEVERITY'); ?>
								<select name="severity" id="ticket-field-severity">
									<option value="critical"<?php if ($this->row->get('severity') == 'critical') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_CRITICAL'); ?></option>
									<option value="major"<?php if ($this->row->get('severity') == 'major') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_MAJOR'); ?></option>
									<option value="normal"<?php if ($this->row->get('severity') == 'normal') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_NORMAL'); ?></option>
									<option value="minor"<?php if ($this->row->get('severity') == 'minor') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_MINOR'); ?></option>
									<option value="trivial"<?php if ($this->row->get('severity') == 'trivial') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_SEVERITY_TRIVIAL'); ?></option>
								</select>
							</label>
						</div>
					</div>
					<div class="col width-50 fltrt">
						<div class="input-wrap">
							<label for="ticket-field-status">
								<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?>:
								<select name="status" id="ticket-field-status">
									<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
										<?php foreach ($this->row->statuses('open') as $status) { ?>
											<option value="<?php echo $status->get('id'); ?>"<?php if ($this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
										<?php } ?>
									</optgroup>
									<optgroup label="<?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
										<option value="0"<?php if (!$this->row->isOpen() && $this->row->get('status') == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
										<?php foreach ($this->row->statuses('closed') as $status) { ?>
											<option value="<?php echo $status->get('id'); ?>"<?php if (!$this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
										<?php } ?>
									</optgroup>
								</select>
							</label>
						</div>
					</div>
					<div class="clr"></div>
				</fieldset><!-- / .ticket-details -->
			</div>
		</fieldset>
	</div><!-- / .col width-70 -->
	<div class="col width-30 fltrt">
		<p><?php echo JText::_('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?></p>
	</div><!-- / .col width-30 -->
	<div class="clr"></div>

	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}

if ($('#comment-field-template').length) {
	$('#comment-field-template').on('change', function() {
		var co = $('#comment-field-comment');

		if ($(this).val() != 'mc') {
			var hi = $('#' + $(this).val()).val();
			co.val(hi);
		} else {
			co.val('');
		}
	});
}

if ($('#comment-field-access').length) {
	$('#comment-field-access').on('click', function() {
		var es = $('#email_submitter');

		if ($(this).prop('checked')) {
			if (es.prop('checked') == true) {
				es.prop('checked', false);
				es.prop('disabled', true);
			}
		} else {
			es.prop('disabled', false);
		}
	});
}
</script>
