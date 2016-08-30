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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$user = User::getInstance();

$unknown  = true;
$name     = '';
$usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
$notify   = array();

if ($this->row->get('login'))
{
	if ($this->row->get('name'))
	{
		jimport('joomla.user.helper');
		$usertype = implode(', ', JUserHelper::getUserGroups($this->row->submitter()->get('id')));

		$name = '<a rel="profile" href="' . Route::url('index.php?option=com_members&task=edit&id=' . $this->row->submitter()->get('id')) . '">' . $this->escape(stripslashes($this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->get('login'))) . ')</a>';
		$unknown = false;

		$notify[] = $this->escape(stripslashes($this->row->get('name'))) . ' (' . $this->escape(stripslashes($this->row->get('login'))) . ')';
	}
}

if (!$name)
{
	if ($this->row->get('name'))
	{
		$name = $this->escape($this->row->get('name')) . ' (' . $this->escape($this->row->get('email')) . ')';
	}
	else
	{
		$name = $this->escape($this->row->get('email'));
	}
	$notify[] = $name;
}

if ($this->row->isOwned())
{
	if ($this->row->owner()->get('name'))
	{
		$notify[] = $this->escape(stripslashes($this->row->owner()->get('name'))) . ' (' . $this->escape(stripslashes($this->row->owner()->get('username'))) . ')';
	}
}

$lastactivity = Lang::txt('COM_SUPPORT_NOT_APPLICAPABLE');
if ($this->row->comments()->total() > 0)
{
	$last = $this->row->comments()->last();
	$lastactivity = '<time datetime="' . $last->created() . '">' . Date::of($last->created())->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . '</time>';
	$this->row->comments()->rewind();
}

$cc = array();

$no_html = Request::getInt('no_html', 0);
if (!$no_html)
{
	$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

	Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . $text, 'support.png');
	Toolbar::save();
	Toolbar::apply();
	Toolbar::cancel();
	Toolbar::spacer();
	Toolbar::help('ticket');

	Html::behavior('tooltip');
	$this->css();
}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" <?php echo (!$no_html ? 'name="adminForm" id="item-form"' : 'name="ajaxForm" id="ajax-form"'); ?> enctype="multipart/form-data">
	<?php if (!$no_html) { ?>
	<div class="grid">
	<div class="col span8">
		<fieldset>
			<legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET'); echo ($this->row->get('id')) ? ' #' . $this->row->get('id') : ''; ?></span></legend>
	<?php } else { ?>
				<dl class="ticket-info <?php echo $this->row->get('severity'); ?>">
					<dt>#</dt>
					<dd><?php echo $this->row->get('id'); ?></dd>
					<dt>Type:</dt>
					<dd>Issue</dd>
					<dt><?php echo Lang::txt('COM_SUPPORT_TICKET_STATUS'); ?>:</dt>
					<dd class="ticket-status <?php if (!$this->row->isOpen()) { echo 'closed'; } else { echo 'open'; } ?>"><?php echo (!$this->row->isOpen()) ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED') : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN'); ?></dd>
					<dt><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY'); ?>:</dt>
					<dd class="ticket-severity <?php echo $this->row->get('severity'); ?>"><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($this->row->get('severity'))); ?></dd>
				</dl>
			<!-- <dl class="ticket-status <?php if (!$this->row->isOpen()) { echo 'closed'; } else { echo 'open'; } ?>">
				<dt><?php echo Lang::txt('COM_SUPPORT_TICKET_STATUS'); ?></dt>
				<dd><?php echo (!$this->row->isOpen()) ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED') : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN'); ?></dd>
			</dl> -->
	<?php } ?>
			<div class="ticket<?php echo ($no_html ? '-body' : ''); ?>" id="t<?php echo $this->row->get('id'); ?>">
				<p class="ticket-member-photo">
					<img src="<?php echo $this->row->submitter()->picture($unknown); ?>" alt="" />
				</p>
				<div class="ticket-head">
					<strong>
						<?php echo $name; ?>
					</strong>
					<a class="permalink" href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=edit&id=' . $this->row->get('id')); ?>" title="<?php echo Lang::txt('COM_SUPPORT_PERMALINK'); ?>">
						<span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->row->created(); ?>"><?php echo $this->row->created('time'); ?></time></span>
						<span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
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
										$img = '<p><a href="' . Route::url($attachment->link()) . '"><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" width="400" /></a></p>';
									}
									else
									{
										$img = '<p><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" /></p>';
									}
									echo $img;
								}
								else
								{
									echo '<p class="attachment"><a href="' . Route::url($attachment->link()) . '" title="' . $attachment->get('description') . '">' . $attachment->get('description') . '</a></p>';
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
								<td colspan="2"><?php echo $this->escape($this->row->get('uas')); ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div><!-- / .ticket-details -->
			</div><!-- / .ticket -->
	<?php if (!$no_html) { ?>
		</fieldset>
	</div>
	<div class="col span4">
		<dl class="ticket-status <?php if (!$this->row->isOpen()) { echo 'closed'; } else { echo 'open'; } ?>">
			<dt><?php echo Lang::txt('COM_SUPPORT_TICKET_STATUS'); ?></dt>
			<dd><?php echo (!$this->row->isOpen()) ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED') : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN'); ?></dd>
		</dl>

		<table class="meta">
			<tbody>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_SEVERITY'); ?></th>
					<td><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($this->row->get('severity'))); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_OWNER'); ?></th>
					<td><?php
					if ($this->row->isOwned())
					{
						if ($this->row->owner('id'))
						{
							echo '<a rel="profile" href="' . Route::url('index.php?option=com_members&task=edit&id=' . $this->row->owner('id')) . '">' . $this->escape(stripslashes($this->row->owner('name'))) . '</a>';
						}
						else
						{
							echo $this->escape($this->row->get('owner'));
						}
					}
					else
					{
						echo Lang::txt('COM_SUPPORT_NONE');
					}
					?></td>
				</tr>
				<tr>
					<th scope="row"><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS_LAST_ACTIVITY'); ?></th>
					<td><?php echo $lastactivity; ?></td>
				</tr>
			</tbody>
		</table>

		<div class="ticket-watch">
			<?php if ($this->row->isWatching()) { ?>
				<div id="watching">
					<p><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_IN_LIST'); ?></p>
					<p><a class="stop-watching btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->row->get('id') . '&watch=stop'); ?>"><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_STOP_WATCHING'); ?></a></p>
				</div>
			<?php } else { ?>
				<p><a class="start-watching btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->row->get('id') . '&watch=start'); ?>"><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_START_WATCHING'); ?></a></p>
			<?php } ?>
			<p><?php echo Lang::txt('COM_SUPPORT_WATCH_TICKET_ABOUT'); ?></p>
		</div>
	</div>
	</div>
	<?php } ?>

	<?php if ($no_html) { ?>
	<div class="ticket-comments">
	<?php } ?>

	<?php if ($this->row->comments()->total() > 0) { ?>
		<?php if (!$no_html) { ?>
		<div class="grid">
		<div class="col span8">
		<?php } ?>
			<fieldset>
				<legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENTS'); ?></span></legend>

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

					$name = Lang::txt('COM_SUPPORT_UNKNOWN');
					$cite = $name;

					if ($comment->creator()->get('id'))
					{
						$cite = $this->escape(stripslashes($comment->creator()->get('name')));
						$name = '<a href="' . Route::url('index.php?option=com_members&task=edit&id[]=' . $comment->creator()->get('id')) . '">' . $cite . ' (' . $this->escape($comment->creator()->get('username')) . ')</a>';
					}

					if ($comment->changelog()->format() != 'html')
					{
						$cc = $comment->changelog()->get('cc');
					}
					?>
					<li class="<?php echo $access .' comment'; ?>" id="c<?php echo $comment->get('id'); ?>">
						<p class="comment-member-photo">
							<span class="comment-anchor"></span>
							<img src="<?php echo $comment->creator()->picture(); ?>" alt="<?php echo Lang::txt('COM_SUPPORT_PROFILE_IMAGE'); ?>" />
						</p>
						<p class="comment-head">
							<strong>
								<?php echo $name; ?>
							</strong>
							<a class="permalink" href="<?php echo 'index.php?option=com_support&amp;controller=tickets&amp;task=edit&amp;id=' . $this->row->get('id') . '#c' . $comment->get('id'); ?>" title="<?php echo Lang::txt('COM_SUPPORT_PERMALINK'); ?>">
								<span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('time'); ?></time></span>
								<span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->escape($comment->created()); ?>"><?php echo $comment->created('date'); ?></time></span>
							</a>
						</p>
						<blockquote class="comment-content" cite="<?php echo $cite; ?>">
						<?php if ($content = $comment->content('parsed')) { ?>
							<p><?php echo $content; ?></p>
						<?php } else { ?>
							<p class="comment-none"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_NO_CONTENT'); ?></p>
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
											$img = '<p><a href="' . Route::url($attachment->link()) . '"><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" width="400" /></a></p>';
										}
										else
										{
											$img = '<p><img src="' . Route::url($attachment->link()) . '" alt="' . $attachment->get('description') . '" /></p>';
										}
										echo $img;
									}
									else
									{
										echo '<p class="attachment"><a href="' . Route::url($attachment->link()) . '" title="' . $attachment->get('description') . '">' . $attachment->get('description') . '</a></p>';
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
		<?php if (!$no_html) { ?>
		</div>
		<div class="col span4">
			<p>
				<a class="new button" href="#commentform"><?php echo Lang::txt('COM_SUPPORT_TICKET_ADD_COMMENT'); ?></a>
			</p>
		</div>
		</div>
		<?php } ?>
	<?php } // end if (count($comments) > 0) ?>

	<?php if (!$no_html) { ?>
	<div class="grid">
	<div class="col span8">
	<?php } ?>
		<fieldset id="commentform">
			<legend><span><?php echo Lang::txt('COM_SUPPORT_TICKET_DETAILS'); ?></span></legend>

			<div class="new ticket">
				<p class="ticket-member-photo">
					<span class="ticket-anchor"></span>
					<img src="<?php echo $user->picture(0); ?>" alt="<?php echo Lang::txt('COM_SUPPORT_PROFILE_IMAGE'); ?>" />
				</p>

				<fieldset class="ticket-head">
					<strong>
						<a rel="profile" href="<?php echo Route::url('index.php?option=com_members&task=edit&id=' . $this->escape($user->get('id'))); ?>">
							<?php echo $this->escape($user->get('name')); ?> (<?php echo $this->escape($user->get('username')); ?>)
						</a>
					</strong>
					<span class="permalink">
						<span class="time-at"><?php echo Lang::txt('COM_SUPPORT_AT'); ?></span>
						<span class="time"><time><?php echo Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
						<span class="date-on"><?php echo Lang::txt('COM_SUPPORT_ON'); ?></span>
						<span class="date"><time><?php echo Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
					</span>

					<label for="comment-field-access" class="private hasTip" title="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?>">
						<input type="checkbox" name="access" id="comment-field-access" value="1" />
						<span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FIELD_ACCESS'); ?></span>
					</label>
				</fieldset><!-- / .ticket-head -->

				<div class="ticket-content">
					<?php
						$results = Event::trigger('support.onTicketComment', array($this->row));
						echo implode("\n", $results);
					?>
					<fieldset>
						<div class="input-wrap">
							<label for="comment-field-template">
								<select name="messages" id="comment-field-template">
									<option value="custom"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_CUSTOM'); ?></option>
									<?php
									$hi = array();
									foreach ($this->lists['messages'] as $message)
									{
										$message->message = str_replace('"','&quot;', stripslashes($message->message));
										$message->message = str_replace('&quote;', '&quot;', $message->message);
										$message->message = str_replace('#XXX', '#' . $this->row->get('id'), $message->message);
										$message->message = str_replace('{ticket#}', $this->row->get('id'), $message->message);
										$message->message = str_replace('{sitename}', Config::get('sitename'), $message->message);
										$message->message = str_replace('{siteemail}', Config::get('mailfrom'), $message->message);
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
								<span class="label"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_COMMENTS'); ?></span>
								<textarea name="comment" id="comment-field-comment" cols="75" rows="15"><?php echo $this->comment->get('comment'); ?></textarea>
							</label>

							<?php if ($this->config->get('email_terse')) { ?>
								<label for="email_terse">
									<input class="option" type="checkbox" name="email_terse" id="email_terse" value="1" checked="checked" />
									<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_TERSE'); ?>
								</label>
							<?php } ?>
						</div>

						<!--
						<div class="grid">
							<div class="col span6">
								<div class="input-wrap">
									<label for="comment-field-upload">
										<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>
										<input type="file" name="upload" id="comment-field-upload" />
									</label>
								</div>
							</div>
							<div class="col span6">
								<div class="input-wrap">
									<label for="comment-field-description">
										<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION'); ?>
										<input type="text" name="description" id="comment-field-description" value="" />
									</label>
								</div>
							</div>
						</div>
						-->
						<fieldset>
							<legend><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
							<?php
							$tmp = Request::getVar('tmp_dir', ('-' . time()), 'post');
							if (!$no_html) {
								$this->js('jquery.fileuploader.js', 'system');
							}
							?>
							<div id="ajax-uploader"
								data-action="<?php echo Route::url('index.php?option=com_support&controller=media&task=upload&no_html=1&ticket=' . $this->row->get('id') . '&comment=' . $tmp); ?>"
								data-list="<?php echo Route::url('index.php?option=com_support&controller=media&task=list&no_html=1&ticket=' . $this->row->get('id') . '&comment=' . $tmp); ?>">
								<noscript>
									<div class="input-wrap">
										<label for="upload">
											<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE'); ?>:
											<input type="file" name="upload" id="upload" />
										</label>
									</div>

									<div class="input-wrap">
										<label for="field-description">
											<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_DESCRIPTION'); ?>:
											<input type="text" name="description" id="field-description" value="" />
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

						<div class="input-wrap">
							<label for="comment-field-message">
								<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC'); ?> <?php
								$mc = Event::trigger('hubzero.onGetMultiEntry', array(
									array(
										'members',   // The component to call
										'cc',        // Name of the input field
										'comment-field-message', // ID of the input field
										'',          // CSS class(es) for the input field
										implode(', ', $cc) // The value of the input field
									)
								));
								if (count($mc) > 0) {
									echo '<span class="hint">' . Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE') . '</span>' . $mc[0];
								} else { ?> <span class="hint"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
									<input type="text" name="cc" id="comment-field-message" value="<?php echo implode(', ', $cc); ?>" />
								<?php } ?>
							</label>
						</div>

						<div class="grid">
							<div class="col span6">
								<div class="input-wrap">
									<label for="email_submitter">
										<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" />
										<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_SUBMITTER'); ?>
									</label>
								</div>
							</div>
							<div class="col span6">
								<div class="input-wrap">
									<label for="email_owner">
										<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" />
										<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_OWNER'); ?>
									</label>
								</div>
							</div>
						</div>
					</fieldset>
				</div><!-- / .ticket-content -->

				<fieldset class="ticket-details">
					<div class="input-wrap">
						<label for="tags">
							<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_TAGS'); ?>
							<?php
							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->row->tags('string'))));

							if (count($tf) > 0) {
								echo $tf[0];
							} else { ?>
								<input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->row->tags('string', null)); ?>" />
							<?php } ?>
						</label>
					</div>

					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<label for="ticket-field-group">
									<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP'); ?>:<br />
									<?php
									$gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', array(array('groups', 'group', 'acgroup','', $this->row->get('group'),'','owner')));
									if (count($gc) > 0) {
										echo $gc[0];
									} else { ?>
									<input type="text" name="group" value="<?php echo $this->escape($this->row->get('group')); ?>" id="acgroup" value="" size="30" autocomplete="off" />
									<?php } ?>
								</label>
							</div>
						</div>
						<div class="col span6">
							<div class="input-wrap">
								<label for="owner">
									<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER'); ?>
									<?php echo $this->lists['owner']; ?>
								</label>
							</div>
						</div>
					</div>

				<?php if (isset($this->lists['categories']) && $this->lists['categories']) { ?>
					<div class="input-wrap">
						<label for="ticket-field-category">
							<?php echo Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY'); ?>
							<select name="category" id="ticket-field-category">
								<option value=""><?php echo Lang::txt('COM_SUPPORT_NONE'); ?></option>
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

					<div class="input-wrap">
						<label for="target_date"><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_TARGET_DATE'); ?></label>
						<?php echo Html::input('calendar', 'target_date', ($this->row->get('target_date') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->get('target_date'))->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-target_date')); ?>
					</div>

					<div class="grid">
						<div class="col span6">
							<div class="input-wrap">
								<label for="ticket-field-severity">
									<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEVERITY'); ?>
									<select name="severity" id="ticket-field-severity">
										<?php foreach (\Components\Support\Helpers\Utilities::getSeverities() as $severity) { ?>
											<option value="<?php echo $severity; ?>"<?php if ($severity == $this->row->get('severity')) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)); ?></option>
										<?php } ?>
									</select>
								</label>
							</div>
						</div>
						<div class="col span6">
							<div class="input-wrap">
								<label for="ticket-field-status">
									<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS'); ?>:
									<select name="status" id="ticket-field-status">
										<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN'); ?>">
											<?php foreach ($this->row->statuses('open') as $status) { ?>
												<option value="<?php echo $status->get('id'); ?>"<?php if ($this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
											<?php } ?>
										</optgroup>
										<optgroup label="<?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED'); ?>">
											<option value="0"<?php if (!$this->row->isOpen() && $this->row->get('status') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED'); ?></option>
											<?php foreach ($this->row->statuses('closed') as $status) { ?>
												<option value="<?php echo $status->get('id'); ?>"<?php if (!$this->row->isOpen() && $this->row->get('status') == $status->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($status->get('title')); ?></option>
											<?php } ?>
										</optgroup>
									</select>
								</label>
							</div>
						</div>
					</div>
				</fieldset><!-- / .ticket-details -->
			</div>
		</fieldset>
	<?php if (!$no_html) { ?>
	</div>
	<div class="col spn4">
		<p><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION'); ?></p>
	</div>
	</div>
	<?php } ?>

	<input type="hidden" name="started" value="<?php echo Date::toSql(); ?>" />

	<input type="hidden" name="id" id="ticketid" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="username" value="<?php echo User::get('username'); ?>" />

	<?php if ($no_html) { ?>
		<p class="submit"><input type="submit" value="<?php echo Lang::txt('Save'); ?>" /></p>
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="task" value="apply" />
	</div>
	<?php } else { ?>
		<input type="hidden" name="task" value="save" />
	<?php } ?>

	<?php echo Html::input('token'); ?>
</form>
<?php if (!$no_html) { ?>
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

jQuery(document).ready(function($){
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

	var attach = $("#ajax-uploader");
	if (attach.length) {
		$('#ajax-uploader-list')
			.on('click', 'a.delete', function (e){
				e.preventDefault();
				if ($(this).attr('data-id')) {
					$.get($(this).attr('href'), {}, function(data) {});
				}
				$(this).parent().parent().remove();
			});
		var running = 0;

		var uploader = new qq.FileUploader({
			element: attach[0],
			action: attach.attr("data-action"),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_INSTRUCTIONS'); ?></span></div>' + 
						'<div class="qq-upload-drop-area"><span><?php echo Lang::txt('COM_SUPPORT_TICKET_COMMENT_FILE_INSTRUCTIONS'); ?></span></div>' +
						'<ul class="qq-upload-list"></ul>' + 
					'</div>',
			onSubmit: function(id, file) {
				running++;
			},
			onComplete: function(id, file, response) {
				running--;

				// HTML entities had to be encoded for the JSON or IE 8 went nuts. So, now we have to decode it.
				response.html = response.html.replace(/&gt;/g, '>');
				response.html = response.html.replace(/&lt;/g, '<');
				$('#ajax-uploader-list').append(response.html);

				if (running == 0) {
					$('ul.qq-upload-list').empty();
				}
			}
		});
	}
});
</script>
<?php } ?>
