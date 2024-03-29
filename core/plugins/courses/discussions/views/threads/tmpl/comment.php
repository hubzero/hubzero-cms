<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

	if ($this->comment->isReported())
	{
		$this->comment->set('anonymous', 1);
		$comment = '<p class="warning">' . Lang::txt('PLG_COURSES_DISCUSSIONS_CONTENT_REPORTED') . '</p>';
	}
	else
	{
		$comment = $this->comment->comment;
		if ($this->search)
		{
			$comment = preg_replace('#' . $this->search . '#i', "<span class=\"highlight\">\\0</span>", $comment);
		}
	}

	$name = Lang::txt('JANONYMOUS');
	if (!$this->comment->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->comment->creator->get('name', $name)));
		if (in_array($this->comment->creator->get('access'), User::getAuthorisedviewLevels()))
		{
			$name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
		}
	}

	$cls = isset($this->cls) ? $this->cls : 'odd';
	if (!$this->comment->get('anonymous') && $this->course->offering()->member($this->comment->get('created_by'))->get('id'))
	{
		if (!$this->course->offering()->member($this->comment->get('created_by'))->get('student'))
		{
			$cls .= ' ' . strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias'));
		}
		else if (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section'))
		{
			$cls .= ' ' . strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias'));
		}
	}
	$cls .= ' ' . $this->comment->get('treename');

	if ($this->unit)
	{
		$this->base .= '&unit=' . $this->unit;
	}
	if ($this->lecture)
	{
		$this->base .= '&b=' . $this->lecture;
	}
?>
	<li class="comment <?php echo $cls; ?><?php if (!$this->comment->get('parent')) { echo ' start'; } ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_PERMALINK'); ?>">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php if ($this->comment->wasModified()) { ?>
						&mdash; <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDITED'); ?>
						<span class="comment-date-at">@</span>
						<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
					<?php } ?>
				</a>
				<?php if (!$this->comment->get('anonymous') && $this->course->offering()->member($this->comment->get('created_by'))->get('id')) { ?>
					<?php if (!$this->course->offering()->member($this->comment->get('created_by'))->get('student')) { ?>
					<span class="role <?php echo strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias')); ?>">
						<?php echo $this->escape(stripslashes($this->course->offering()->member($this->comment->get('created_by'))->get('role_title'))); ?>
					</span>
					<?php } else if (!$this->course->offering()->access('manage') && $this->course->offering()->access('manage', 'section')) { ?>
						<span class="role <?php echo strtolower($this->course->offering()->member($this->comment->get('created_by'))->get('role_alias')); ?>">
							<?php echo $this->escape(stripslashes($this->course->offering()->member($this->comment->get('created_by'))->get('role_title'))); ?>
						</span>
					<?php } ?>
				<?php } ?>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<?php if ($this->config->get('access-delete-thread')) { ?>
						<a class="icon-delete delete" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('delete')); ?>"><!--
							--><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE'); ?><!--
						--></a>
					<?php } ?>
					<?php if ($this->config->get('access-edit-thread')) { ?>
						<a class="icon-edit edit" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
							--><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT'); ?><!--
						--></a>
					<?php } ?>
				<?php } ?>
				<?php if (!$this->comment->isReported()) { ?>
					<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
						<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
						<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('base')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('JCANCEL'); ?><!--
					--></a>
						<?php } else { ?>
						<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('JCANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLY'); ?><!--
					--></a>
						<?php } ?>
					<?php } ?>
					<a class="icon-abuse abuse" href="<?php echo Route::url($this->comment->link('abuse')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
						--><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPORT_ABUSE'); ?><!--
					--></a>
				<?php } ?>
			</p>

			<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
				<div class="comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
					<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->comment->link('base')); ?>" method="post" enctype="multipart/form-data">
						<fieldset id="commentform<?php echo $this->comment->get('id'); ?>">
							<legend><span><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('JANONYMOUS'))); ?></span></legend>

							<input type="hidden" name="fields[id]" value="0" />
							<input type="hidden" name="fields[state]" value="1" />
							<input type="hidden" name="fields[scope]" value="<?php echo $this->post->get('scope'); ?>" />
							<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->get('category_id'); ?>" />
							<input type="hidden" name="fields[scope_id]" value="<?php echo $this->post->get('scope_id'); ?>" />
							<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->post->get('scope_sub_id'); ?>" />
							<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />
							<input type="hidden" name="fields[parent]" value="<?php echo $this->comment->get('id'); ?>" />
							<input type="hidden" name="fields[thread]" value="<?php echo $this->comment->get('thread'); ?>" />
							<input type="hidden" name="fields[created]" value="" />
							<input type="hidden" name="fields[created_by]" value="<?php echo User::get('id'); ?>" />
							<input type="hidden" name="depth" value="<?php echo ($this->depth + 1); ?>" />

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
							<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
							<input type="hidden" name="active" value="discussions" />
							<input type="hidden" name="action" value="savethread" />
							<input type="hidden" name="return" value="<?php echo base64_encode(Route::url($this->base)); ?>" />

							<?php echo Html::input('token'); ?>

							<label for="comment_<?php echo $this->comment->get('id'); ?>_reply">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
								<?php echo $this->editor('fields[comment]', '', 35, 5, 'comment_' . $this->comment->get('id') . '_reply', array('class' => 'minimal no-footer')); ?>
							</label>

							<label class="upload-label" for="comment-<?php echo $this->comment->get('id'); ?>-file">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ATTACH_FILE'); ?>:</span>
								<input type="file" name="upload" id="comment-<?php echo $this->comment->get('id'); ?>-file" />
							</label>

							<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
								<?php if ($this->config->get('comments_anon', 1)) { ?>
									<input class="option" type="checkbox" name="fields[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
									<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
								<?php } else { ?>
									&nbsp; <input class="option" type="hidden" name="fields[anonymous]" value="0" />
								<?php } ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
							</p>
						</fieldset>
					</form>
				</div><!-- / .addcomment -->
			<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$this->view('list')
				->set('parent', $this->comment->get('id'))
				->set('thread', $this->comment->get('thread'))
				->set('option', $this->option)
				->set('comments', $this->comment->get('replies'))
				->set('post', $this->post)
				->set('unit', $this->unit)
				->set('lecture', $this->lecture)
				->set('config', $this->config)
				->set('depth', $this->depth)
				->set('cls', $cls)
				->set('base', $this->base)
				->set('attach', $this->attach)
				->set('course', $this->course)
				->set('search', $this->search)
				->display();
		}
		?>
	</li>