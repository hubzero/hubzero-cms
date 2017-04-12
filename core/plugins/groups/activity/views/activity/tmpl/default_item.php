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

defined('_HZEXEC_') or die();

$status = '';
if (!$this->row->wasViewed())
{
	$status = 'new';

	$this->row->markAsViewed();
}

$creator = User::getInstance($this->row->log->get('created_by'));

$name = Lang::txt('PLG_GROUPS_ACTIVITY_ANONYMOUS');

$online = false;

// Is it not anonymous?
if (!$this->row->log->get('anonymous'))
{
	// Get their full name
	$name = $this->escape(stripslashes($creator->get('name', Lang::txt('PLG_GROUPS_ACTIVITY_UNKNOWN'))));

	// Can we see their profile?
	if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
	{
		$name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
	}

	if (isset($this->online) && in_array($this->row->log->get('created_by'), $this->online))
	{
		$online = true;
	}
}

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=activity';
?>
<li
	data-time="<?php echo $this->row->get('created'); ?>"
	data-id="<?php echo $this->row->get('id'); ?>"
	data-log_id="<?php echo $this->row->get('log_id'); ?>"
	data-context="<?php echo $this->row->log->get('scope'); ?>"
	data-action="<?php echo $this->row->log->get('action'); ?>"
	id="activity<?php echo $this->row->get('id'); ?>"
	class="activity <?php echo $this->row->get('scope'); ?> <?php echo $status . ($this->row->get('starred') ? ' starred' : ''); ?>">

	<div class="activity-actor-picture<?php if ($online) { echo ' tooltips" title="' . Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE'); } ?>">
		<?php if ($creator->get('public')) { ?>
			<a class="user-img-wrap" href="<?php echo Route::url($creator->link()); ?>" title="<?php echo $name; ?>">
				<img src="<?php echo $creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE'); ?></span>
				<?php } ?>
			</a>
		<?php } else { ?>
			<span class="user-img-wrap">
				<img src="<?php echo $creator->picture(); ?>" alt="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_PROFILE_PICTURE', $name); ?>" />
				<?php if ($online) { ?>
					<span class="online"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_ONLINE'); ?></span>
				<?php } ?>
			</span>
		<?php } ?>
	</div><!-- / .activity-actor-picture -->

	<div class="activity-content <?php echo $this->escape($this->row->log->get('action')); ?> <?php echo $this->escape(str_replace('.', '-', $this->row->log->get('scope'))); ?>">
		<div class="activity-body">
			<div class="activity-details">
				<span class="activity-actor"><?php echo $name; ?></span>
				<span class="activity-action"><?php echo $this->escape($this->row->log->get('action')); ?></span>
				<span class="activity-channel"><?php echo ($this->row->get('scope') == 'group_managers' ? Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_MANAGERS') : Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_RECIPIENTS_ALL')); //$this->escape($this->row->get('scope') . '.' . $this->row->get('scope_id')); ?></span>
				<span class="activity-context"><?php
					$scope = explode('.', $this->row->log->get('scope'));
					echo $this->escape($scope[0]);
				?></span>
				<span class="activity-time"><time datetime="<?php echo Date::of($this->row->get('created'))->format('Y-m-d\TH:i:s\Z'); ?>"><?php
					$dt = Date::of($this->row->get('created'));
					$ct = Date::of('now');

					$lapsed = $ct->toUnix() - $dt->toUnix();

					if ($lapsed < 30)
					{
						echo Lang::txt('PLG_GROUPS_ACTIVITY_JUST_NOW');
					}
					elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y'))
					{
						echo $dt->toLocal('M j, Y');
					}
					elseif ($lapsed > 86400)
					{
						echo $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
					}
					else
					{
						echo $dt->relative();
					}
				?></time></span>
			</div><!-- / .activity-details -->

			<div class="activity-event">
				<?php
				$content = $this->row->log->get('description');
				$short = null;

				$attachments = $this->row->log->details->get('attachments');
				$attachments = $attachments ?: array();

				$attached = count($attachments);

				if (strlen(strip_tags($content)) > 150)
				{
					$short = Hubzero\Utility\String::truncate($content, 150, array('html' => true));
					?>
					<div class="activity-event-preview">
						<?php echo $short; ?>
						<p>
							<a class="more-content" href="#activity-event-content<?php echo $this->row->get('id'); ?>">
								<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_MORE'); ?>
							</a>
						</p>
					</div>
					<?php
				}
				?>
				<div class="activity-event-content<?php echo ($short ? ' hide' : ''); ?>" id="activity-event-content<?php echo $this->row->get('id'); ?>">
					<?php echo $content; ?>
				</div>

				<?php if ($attached) { ?>
					<div class="activity-attachments">
						<?php
						foreach ($attachments as $attachment)
						{
							$attachment = new Plugins\Groups\Activity\Models\Attachment($attachment);
							$attachment->setUploadDir('/site/groups/' . $this->group->get('gidNumber') . '/uploads');

							if (!$attachment->exists())
							{
								continue;
							}

							if (!trim($attachment->get('description')))
							{
								$attachment->set('description', $attachment->get('filename'));
							}

							$link = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=File:/uploads/' . ($attachment->get('subdir') ? $attachment->get('subdir') . '/' : '') . $attachment->get('filename');

							if ($attachment->isImage())
							{
								?>
								<a class="attachment img" rel="lightbox" href="<?php echo Route::url($link); ?>">
									<img src="<?php echo Route::url($link); ?>" alt="<?php echo $this->escape($attachment->get('description')); ?>" width="<?php echo ($attachment->width() > 400 ? 400 : $attachment->width()); ?>" />
									<p class="attachment-meta">
										<span class="attachment-size"><?php echo Hubzero\Utility\Number::formatBytes($attachment->size()); ?></span>
										<span class="attachment-action"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILE_DOWNLOAD'); ?></span>
									</p>
								</a>
								<?php
							}
							else
							{
								?>
								<a class="attachment <?php echo Filesystem::extension($attachment->get('filename')); ?>" href="<?php echo Route::url($link); ?>" title="<?php echo $this->escape($attachment->get('description')); ?>">
									<p class="attachment-description"><?php echo $attachment->get('description'); ?></p>
									<p class="attachment-meta">
										<span class="attachment-size"><?php echo Hubzero\Utility\Number::formatBytes($attachment->size()); ?></span>
										<span class="attachment-action"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FILE_DOWNLOAD'); ?></span>
									</p>
								</a>
								<?php
							}
						}
						?>
					</div><!-- / .activity-attachments -->
				<?php } ?>
			</div><!-- / .activity-event -->

			<div class="activity-options">
				<?php if ($this->group->published == 1) { ?>
				<ul class="activity-options-main">
					<?php if ($this->row->log->get('scope') == 'activity.comment' && !$this->row->log->get('parent')) { ?>
						<li>
							<?php if (Request::getInt('reply', 0) == $this->row->get('id')) { ?>
								<a
									class="icon-reply reply tooltips active"
									data-txt-active="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_CANCEL'); ?>"
									data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
									title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_CANCEL'); ?>"
									href="<?php echo Route::url($base); ?>"
									rel="comment-form<?php echo $this->row->get('id'); ?>"><!--
									--><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_CANCEL'); ?><!--
								--></a>
							<?php } else { ?>
								<a
									class="icon-reply reply tooltips"
									data-txt-active="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_CANCEL'); ?>"
									data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
									title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?>"
									href="<?php echo Route::url($base . '&action=reply&activity=' . $this->row->get('id')); ?>"
									rel="comment-form<?php echo $this->row->get('id'); ?>"><!--
									--><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLY'); ?><!--
								--></a>
							<?php } ?>
						</li>
					<?php } ?>
					<?php if (!$this->row->log->get('parent')) { ?>
						<li>
							<a
								data-id="activity<?php echo $this->row->get('id'); ?>"
								class="icon-starred tooltips"
								href="<?php echo Route::url($base . '&action=' . ($this->row->get('starred') ? 'un' : '') . 'star&activity=' . $this->row->get('id')); ?>"
								data-hrf-active="<?php echo Route::url($base . '&action=unstar&activity=' . $this->row->get('id')); ?>"
								data-hrf-inactive="<?php echo Route::url($base . '&action=star&activity=' . $this->row->get('id')); ?>"
								data-txt-active="<?php echo Lang::txt('Unstar this'); ?>"
								data-txt-inactive="<?php echo Lang::txt('Star this'); ?>"
								title="<?php echo ($this->row->get('starred') ? Lang::txt('Unstar this') : Lang::txt('Star this')); ?>"><!--
								--><?php echo ($this->row->get('starred') ? Lang::txt('Unstar this') : Lang::txt('Star this')); ?><!--
							--></a>
						</li>
					<?php } ?>
					<li>
						<a
							data-id="activity<?php echo $this->row->get('id'); ?>"
							class="icon-delete tooltips"
							href="<?php echo Route::url($base . '&action=remove&activity=' . $this->row->get('id') . '&' . Session::getFormToken() . '=1'); ?>"
							title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_DELETE'); ?>"
							data-txt-confirm="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_CONFIRM_DELETE'); ?>"><!--
							--><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_DELETE'); ?><!--
						--></a>
					</li>
					<?php /*<li>
						<a data-id="activity<?php echo $this->row->get('id'); ?>" class="icon-options tooltips" href="#moreoptions<?php echo $this->row->get('id'); ?>" title="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_OPTIONS'); ?>"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_OPTIONS'); ?></a>
						<ul class="activity-options-more" id="moreoptions<?php echo $this->row->get('id'); ?>">
							<li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($base . '&action=unsubscribe&scope=' . $this->row->get('scope')); ?>">Hide all like this</a></li>
							<li><a data-id="activity<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($base . '&action=share&activity=' . $this->row->get('id')); ?>">Share</a></li>
						</ul>
					</li>*/ ?>
				</ul>
				<?php } ?>
			</div><!-- / .activity-options -->
			
		</div><!-- / .activity-body -->

		<?php if ($this->group->published == 1 && $this->row->log->get('scope') == 'activity.comment') { ?>
			<div class="comment-add<?php if (Request::getInt('reply', 0) != $this->row->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->row->get('id'); ?>">
				<form id="cform<?php echo $this->row->get('id'); ?>" action="<?php echo Route::url($base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_REPLYING_TO', (!$this->row->log->get('anonymous') ? $name : Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="activity[id]" value="0" />
						<input type="hidden" name="activity[action]" value="created" />
						<input type="hidden" name="activity[scope]" value="<?php echo $this->row->log->get('scope'); ?>" />
						<input type="hidden" name="activity[scope_id]" value="<?php echo $this->row->log->get('scope_id'); ?>" />
						<input type="hidden" name="activity[parent]" value="<?php echo $this->row->log->get('id'); ?>" />
						<input type="hidden" name="activity[created]" value="" />
						<input type="hidden" name="activity[created_by]" value="<?php echo User::get('id'); ?>" />

						<input type="hidden" name="option" value="com_groups" />
						<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
						<input type="hidden" name="active" value="activity" />
						<input type="hidden" name="action" value="post" />

						<?php echo Html::input('token'); ?>

						<div class="input-wrap">
							<label for="comment-<?php echo $this->row->get('id'); ?>-content">
								<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_COMMENTS'); ?></span>
								<?php echo $this->editor('activity[description]', '', 35, 4, 'field_' . $this->row->get('id') . '_comment', array('class' => 'minimal no-footer')); ?>
							</label>
						</div>

						<div class="input-wrap">
							<label class="upload-label" for="activity-<?php echo $this->row->get('id'); ?>-file">
								<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_ACTIVITY_FIELD_FILE'); ?></span>
								<input type="file" class="inputfile" name="activity_file" id="activity-<?php echo $this->row->get('id'); ?>-file" data-multiple-caption="<?php echo Lang::txt('{count} files selected'); ?>" multiple="multiple" />
							</label>
						</div>

						<p class="submit">
							<input type="submit" class="btn btn-secondary" value="<?php echo Lang::txt('PLG_GROUPS_ACTIVITY_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .comment-add -->
		<?php } ?>

		<div class="activity-processor">
			<div class="spinner"><div></div></div>
			<div class="msg"></div>
		</div><!-- / .activity-processor -->
	</div><!-- / .activity-content -->

	<?php
	if ($this->row->log->get('scope') == 'activity.comment')
	{
		$recipient = Hubzero\Activity\Recipient::all();

		$r = $recipient->getTableName();
		$l = Hubzero\Activity\Log::blank()->getTableName();

		$rows = $recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereEquals($r . '.scope', 'group')
			->whereEquals($r . '.scope_id', $this->group->get('gidNumber'))
			->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
			->whereEquals($l . '.parent', $this->row->log->get('id'))
			->order('id', 'asc')
			->rows();

		if ($rows->count())
		{
			?>
			<ul class="activity-comments">
				<?php
				foreach ($rows as $row)
				{
					$this->view('default_item')
						->set('group', $this->group)
						->set('online', $this->online)
						->set('row', $row)
						->display();
				}
				?>
			</ul>
			<?php
		}
	}
	?>

</li>