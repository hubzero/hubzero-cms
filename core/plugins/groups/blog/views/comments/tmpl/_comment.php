<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


	$cls = isset($this->cls) ? $this->cls : 'odd';

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
		$comment = '<p class="warning">' . Lang::txt('PLG_GROUPS_BLOG_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content();
	}
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<a class="comment-anchor" name="c<?php echo $this->comment->get('id'); ?>"></a>
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_BLOG_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_BLOG_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_BLOG_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php if ($this->comment->wasModified()) { ?>
						&mdash; <?php echo Lang::txt('PLG_GROUPS_BLOG_EDITED'); ?>
						<span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_BLOG_AT'); ?></span> 
						<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span> 
						<span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_BLOG_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
					<?php } ?>
				</a>
			</p>

	<?php if (Request::getWord('action') == 'editcomment'
			&& Request::getInt('comment') == $this->comment->get('id')
			&& ($this->config->get('access-edit-comment') || User::get('id') == $this->comment->get('created_by'))) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo Lang::txt('PLG_GROUPS_BLOG_COMMENT_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->comment->get('entry_id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created]" value="<?php echo $this->comment->get('created'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->comment->get('created_by'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="action" value="savecomment" />

					<?php echo Html::input('token'); ?>

					<div class="form-group">
						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', $this->comment->get('content'), 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'form-control minimal no-footer'));
							?>
						</label>
					</div>

					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label comment-anonymous-label" for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
								<input class="option form-check-input" type="checkbox" name="comment[anonymous]" id="comment_<?php echo $this->comment->get('id'); ?>_anonymous" value="1" <?php if ($this->comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
								<?php echo Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS'); ?>
							</label>
						</div>
					</div>

					<p class="submit">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>
			<?php echo $comment; ?>

			<p class="comment-options">
			<?php if ($this->config->get('access-delete-comment')) { ?>
				<a class="icon-delete delete" data-confirm="<?php echo Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->base . '&action=deletecomment&comment=' . $this->comment->get('id')); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?><!--
				--></a>
			<?php } ?>
			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->config->get('access-edit-comment') || User::get('id') == $this->comment->get('created_by')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=editcomment&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('JCANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_BLOG_REPLY'); ?>" href="<?php echo Route::url($this->base . '&reply=' . $this->comment->get('id')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_BLOG_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" href="<?php echo Route::url('index.php?option=com_support&task=reportabuse&category=blog&id=' . $this->comment->get('id') . '&parent=' . $this->comment->get('entry_id')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_BLOG_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset id="commentform<?php echo $this->comment->get('id'); ?>">
						<legend><span><?php echo Lang::txt('PLG_GROUPS_BLOG_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[entry_id]" value="<?php echo $this->comment->get('entry_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('id'); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="comment[state]" value="1" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
						<input type="hidden" name="active" value="blog" />
						<input type="hidden" name="task" value="view" />
						<input type="hidden" name="action" value="savecomment" />

						<?php echo Html::input('token'); ?>

						<div class="form-group">
							<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
								<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_COMMENTS'); ?></span>
								<?php
								echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'form-control minimal no-footer'));
								?>
							</label>
						</div>

						<div class="form-group">
							<div class="form-check">
								<label for="comment-anonymous" class="form-check-label comment-anonymous-label">
									<input class="option form-check-input" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
									<?php echo Lang::txt('PLG_GROUPS_BLOG_POST_ANONYMOUS'); ?>
								</label>
							</div>
						</div>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
			</div><!-- / .addcomment -->
		<?php } ?>
	<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$replies = $this->comment->replies()
				->whereIn('state', array(
					Components\Blog\Models\Comment::STATE_PUBLISHED,
					Components\Blog\Models\Comment::STATE_FLAGGED
				))
				->ordered()
				->rows();

			$this->view('_list', 'comments')
				->set('group', $this->group)
				->set('parent', $this->comment->get('id'))
				->set('cls', $cls)
				->set('depth', $this->depth)
				->set('option', $this->option)
				->set('comments', $replies)
				->set('config', $this->config)
				->set('base', $this->base)
				->display();
		}
		?>
	</li>