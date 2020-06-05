<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if ($this->wish->get('proposed_by') == $this->comment->get('created_by'))
	{
		$cls .= ' author';
	}

	$name = Lang::txt('JANONYMOUS');
	if (!$this->comment->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
		if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels()))
		{
			$name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
		}
	}

	if ($this->comment->isReported())
	{
		$cls .= ' abusive';
		$comment = '<p class="warning">' . Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment = $this->comment->content;
	}

	$this->comment->set('listcategory', $this->wishlist->get('category'));
	$this->comment->set('listreference', $this->wishlist->get('referenceid'));
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->wish->link() . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_WISHLIST_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('COM_WISHLIST_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_WISHLIST_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<?php if ($this->comment->attachments->count() > 0) { ?>
				<div class="comment-attachments">
					<?php
					foreach ($this->comment->attachments as $attachment)
					{
						if (!trim($attachment->get('description')))
						{
							$attachment->set('description', $attachment->get('filename'));
						}

						$link = $attachment->link('download');

						if ($attachment->exists())
						{
							if ($attachment->isImage())
							{
								if ($attachment->width() > 400)
								{
									$html = '<p><a href="' . Route::url($link) . '"><img src="' . Route::url($link) . '" alt="' . $this->escape($attachment->get('description')) . '" width="400" /></a></p>';
								}
								else
								{
									$html = '<p><img src="' . Route::url($link) . '" alt="' . $this->escape($attachment->get('description')) . '" /></p>';
								}
							}
							else
							{
								$html  = '<a class="attachment ' . Filesystem::extension($attachment->get('filename')) . '" href="' . Route::url($link) . '" title="' . $this->escape($attachment->get('description')) . '">';
								$html .= '<p class="attachment-description">' . $this->escape($attachment->get('description')) . '</p>';
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
							$html .= '<p class="attachment-description">' . $this->escape($attachment->get('description')) . '</p>';
							$html .= '<p class="attachment-meta">';
							$html .= '<span class="attachment-size">' . $this->escape($attachment->get('filename')) . '</span>';
							$html .= '<span class="attachment-action">' . Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') . '</span>';
							$html .= '</p>';
							$html .= '</div>';
						}

						echo $html;
					}
					?>
				</div><!-- / .comment-attachments -->
			<?php } ?>

			<p class="comment-options">
			<?php /*if ($this->config->get('access-edit-thread')) { // || User::get('id') == $this->comment->get('created_by') ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($this->wish->link() . '&action=delete&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_WISHLIST_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->wish->link() . '&action=edit&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_WISHLIST_EDIT'); ?><!--
					--></a>
				<?php } ?>
			<?php }*/ ?>
			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WISHLIST_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('JCANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WISHLIST_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_WISHLIST_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('COM_WISHLIST_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('COM_WISHLIST_PLEASE_LOGIN_TO_COMMENT', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->wish->link(), false, true))) . '">' . Lang::txt('COM_WISHLIST_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->wish->link()); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_WISHLIST_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type') ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="listid" value="<?php echo $this->escape($this->wish->get('wishlist')); ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />
						<input type="hidden" name="task" value="savereply" />
						<input type="hidden" name="referenceid" value="<?php echo $this->wishlist->get('referenceid'); ?>" />
						<input type="hidden" name="cat" value="wish" />

						<?php echo Html::input('token'); ?>

						<div class="form-group">
							<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
								<span class="label-text"><?php echo Lang::txt('COM_WISHLIST_ENTER_COMMENTS'); ?></span>
								<?php
								echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'form-control minimal no-footer'));
								?>
							</label>
						</div>

						<div class="form-group form-check">
							<label class="comment-anonymous-label form-check-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
								<input class="option form-check-input" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
								<?php echo Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
							</label>
						</div>

						<p class="submit">
							<input type="submit" class="btn" value="<?php echo Lang::txt('COM_WISHLIST_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->wish->config()->get('comments_depth', 3))
		{
			$comments = $this->comment->replies()
				->whereIn('state', array(
					Components\Wishlist\Models\Comment::STATE_PUBLISHED,
					Components\Wishlist\Models\Comment::STATE_FLAGGED
				))
				->rows();

			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('cls', $cls)
			     ->set('depth', $this->depth)
			     ->set('option', $this->option)
			     ->set('comments', $comments)
			     ->set('wishlist', $this->wishlist)
			     ->set('wish', $this->wish)
			     ->display();
		}
		?>
	</li>