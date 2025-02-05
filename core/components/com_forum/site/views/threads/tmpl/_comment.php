<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

	$this->css('like.css')
		->js('like.js');

	$likeArray = $this->like;
	$countLike = count($likeArray);
	$currentUserId = User::get('id');

	$userLikesComment = false;
	$userNameLikesArray = "";

	foreach ( $likeArray as $likeObj ) 
	{
		if ( $currentUserId == $likeObj->userId ) 
		{
			$userLikesComment = true;
		}

		$userNameLikesArray .= "/" . ($likeObj->userName) . "#" . ($likeObj->userId);
	}

	$userNameLikesArray = substr($userNameLikesArray,1);

	$this->comment->set('section', $this->filters['section']);
	$this->comment->set('category', $this->category->get('alias'));

	$name = Lang::txt('JANONYMOUS');
	if (!$this->comment->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
		if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels()))
		{
			$name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
		}
	}

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . Lang::txt('COM_FORUM_CONTENT_FLAGGED') . '</p>';
	}
	else
	{
		$comment = $this->comment->comment;
	}
?>
	<li class="comment <?php echo $cls; ?><?php if (!$this->comment->get('parent')) { echo ' start'; } ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->comment->link('anchor')); ?>" title="<?php echo Lang::txt('COM_FORUM_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php if ($this->comment->wasModified()) { ?>
						&mdash; <?php echo Lang::txt('COM_FORUM_EDITED'); ?>
						<span class="comment-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
					<?php } ?>
				</a>
			</p>
			<div class="comment-body">
				<?php echo $comment; ?>

                <!-- Heart and "Like" link Button -->
                <!-- Only show if people is logged in -->
				<?php if (!User::isGuest()) { ?>
					<div class="elementInline likeContainer">
						<a class="icon-heart like <?php if ($userLikesComment) { echo "userLiked"; } ?>" href="#"
						data-thread="<?php echo $this->thread->get('id'); ?>"
						data-post="<?php echo $this->comment->get('id'); ?>"
						data-user="<?php echo User::get('id'); ?>"
						data-user-name="<?php echo User::get('name'); ?>"
						data-likes-list="<?php echo $userNameLikesArray; ?>"
						data-count="<?php echo $countLike; ?>"
						></a>
						<span class="likesStat <?php if ($countLike==0) { echo "noLikes"; } ?>">
							<?php echo ($countLike>0) ? "View Likes (" . $countLike . ")" : "No Likes"; ?>
						</span>
					</div>
					<div class="clear"></div>

					<div class="whoLikedPost">
						<?php if (strlen($userNameLikesArray) > 0) { ?>
							<div class="names">
								<?php
									$nameArray = preg_split("#/#", $userNameLikesArray);
									$links = array();
									foreach ($nameArray as $nameString) 
									{
										$nameArray = explode("#", $nameString);
										$userName = $nameArray[0];
										$userId =  $nameArray[1];
										$userProfileUrl = "/members/$userId/profile";

										$links[] = "<a href=$userProfileUrl target='_blank'>$userName</a>";
									}
									echo join(", ", $links) . " liked this";
								?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

			</div>

			<div class="comment-attachments">
				<?php
				foreach ($this->comment->attachments()->whereEquals('state', Components\Forum\Models\Attachment::STATE_PUBLISHED)->rows() as $attachment)
				{
					if (!trim($attachment->get('description')))
					{
						$attachment->set('description', $attachment->get('filename'));
					}

					if ($attachment->exists())
					{
						$link = $attachment->link(); //$this->comment->link() . '&post=' . $attachment->get('post_id') . '&file=' . $attachment->get('filename');

						if ($attachment->isImage())
						{
							if ($attachment->width() > 400)
							{
								$html = '<p><a href="' . Route::url($link) . '" rel="external"><img src="' . Route::url($link) . '" alt="' . $this->escape($attachment->get('description')) . '" width="400" /></a></p>';
							}
							else
							{
								$html = '<p><img src="' . Route::url($link) . '" alt="' . $this->escape($attachment->get('description')) . '" /></p>';
							}
						}
						else
						{
							$html  = '<a class="attachment ' . Filesystem::extension($attachment->get('filename')) . '" href="' . Route::url($link) . '" title="' . $this->escape($attachment->get('description')) . '">';
							$html .= '<p class="attachment-description">' . $attachment->get('description') . '</p>';
							$html .= '<p class="attachment-meta">';
							$html .= '<span class="attachment-size">' . Hubzero\Utility\Number::formatBytes($attachment->size()) . '</span>';
							$html .= '<span class="attachment-action">' . Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') . '</span>';
							$html .= '</p>';
							$html .= '</a>';
						}
					}
					else
					{
						$html  = '<div class="attachment ' . Filesystem::extension($attachment->get('filename')) . '" title="' . $this->escape($attachment->get('description')) . '">';
						$html .= '<p class="attachment-description">' . $attachment->get('description') . '</p>';
						$html .= '<p class="attachment-meta">';
						$html .= '<span class="attachment-size">' . $attachment->get('filename') . '</span>';
						$html .= '<span class="attachment-action">' . Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') . '</span>';
						$html .= '</p>';
						$html .= '</div>';
					}

					echo $html;
				}
				?>
			</div><!-- / .comment-attachments -->
			<?php if ($this->config->get('access-manage-thread')
						||
						(!$this->comment->get('parent') && $this->comment->get('created_by') == User::get('id') &&
							(
								$this->config->get('access-delete-thread') ||
								$this->config->get('access-edit-thread')
							)
						)
						||
						($this->comment->get('parent') && $this->comment->get('created_by') == User::get('id') &&
							(
								$this->config->get('access-delete-post') ||
								$this->config->get('access-edit-post')
							)
						)
					) { ?>
				<p class="comment-options">
					<?php if ((!$this->comment->get('parent') && $this->config->get('access-delete-thread')) || ($this->comment->get('parent') && $this->config->get('access-delete-post'))) { ?>
						<a class="icon-delete delete" data-txt-confirm="<?php echo Lang::txt('COM_FORUM_CONFIRM_DELETE'); ?>" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('delete')); ?>"><!--
							--><?php echo Lang::txt('JACTION_DELETE'); ?><!--
						--></a>
					<?php } ?>
					<?php if ((!$this->comment->get('parent') && $this->config->get('access-edit-thread')) || ($this->comment->get('parent') && $this->config->get('access-edit-post'))) { ?>
						<a class="icon-edit edit" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
							--><?php echo Lang::txt('JACTION_EDIT'); ?><!--
						--></a>
					<?php } ?>
					<?php if (!$this->comment->isReported()) { ?>
						<?php if (!$this->thread->get('closed') && $this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3)) { ?>
							<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
							<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_FORUM_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
							--><?php echo Lang::txt('JCANCEL'); ?><!--
						--></a>
							<?php } else { ?>
							<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_FORUM_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
							--><?php echo Lang::txt('COM_FORUM_REPLY'); ?><!--
						--></a>
							<?php } ?>
						<?php } ?>
						<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_FORUM_CONTENT_FLAGGED'); ?>" href="<?php echo Route::url($this->comment->link('abuse')); ?>"><!--
							--><?php echo Lang::txt('COM_FORUM_REPORT_ABUSE'); ?><!--
						--></a>
					<?php } ?>
				</p>
			<?php } ?>

			<?php if (!User::isGuest() && !$this->thread->get('closed') && $this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3)) { ?>
				<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
					<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->thread->link()); ?>" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend><span><?php echo Lang::txt('COM_FORUM_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS'))); ?></span></legend>

							<input type="hidden" name="fields[id]" value="0" />
							<input type="hidden" name="fields[state]" value="1" />
							<input type="hidden" name="fields[access]" value="<?php echo $this->thread->get('access', 0); ?>" />
							<input type="hidden" name="fields[scope]" value="<?php echo $this->thread->get('scope'); ?>" />
							<input type="hidden" name="fields[category_id]" value="<?php echo $this->thread->get('category_id'); ?>" />
							<input type="hidden" name="fields[scope_id]" value="<?php echo $this->thread->get('scope_id'); ?>" />
							<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->thread->get('scope_sub_id'); ?>" />
							<input type="hidden" name="fields[object_id]" value="<?php echo $this->thread->get('object_id'); ?>" />
							<input type="hidden" name="fields[parent]" value="<?php echo $this->comment->get('id'); ?>" />
							<input type="hidden" name="fields[thread]" value="<?php echo $this->comment->get('thread'); ?>" />
							<input type="hidden" name="fields[created]" value="" />
							<input type="hidden" name="fields[created_by]" value="<?php echo User::get('id'); ?>" />

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="save" />

							<?php echo Html::input('token'); ?>

							<div class="form-group">
								<label for="field_<?php echo $this->comment->get('id'); ?>_comment">
									<span class="label-text"><?php echo Lang::txt('COM_FORUM_FIELD_COMMENTS'); ?></span>
									<?php
									echo $this->editor('fields[comment]', '', 35, 4, 'field_' . $this->comment->get('id') . '_comment', array('class' => 'form-control minimal no-footer'));
									?>
								</label>
							</div>

							<div class="form-group">
								<label class="upload-label" for="comment-<?php echo $this->comment->get('id'); ?>-file">
									<span class="label-text"><?php echo Lang::txt('COM_FORUM_ATTACH_FILE'); ?>:</span>
									<input type="file" class="form-control-file" name="upload" id="comment-<?php echo $this->comment->get('id'); ?>-file" />
								</label>
							</div>

							<?php if ($this->config->get('allow_anonymous')) { ?>
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label reply-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
											<input class="option form-check-input" type="checkbox" name="fields[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
											<?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?>
										</label>
									</div>
								</div>
							<?php } ?>

							<p class="submit">
								<input type="submit" class="btn" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .addcomment -->
			<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3))
		{
			$this->view('_list')
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('comments', $this->comment->get('replies'))
			     ->set('thread', $this->thread)
			     ->set('parent', $this->comment->get('id'))
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('filters', $this->filters)
			     ->set('category', $this->category)
			     ->display();
		}
		?>
	</li>

