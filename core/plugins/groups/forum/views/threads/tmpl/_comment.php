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

	$this->comment->set('section', $this->filters['section']);
	$this->comment->set('category', $this->category->get('alias'));

	$this->config->set('access-edit-post', false);
	if (User::get('id') == $this->comment->get('created_by'))
	{
		$this->config->set('access-edit-post', true);
	}

	$name = Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS');
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
		$comment = '<p class="warning">' . Lang::txt('PLG_GROUPS_FORUM_COMMENT_REPORTED') . '</p>';
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
				<a class="permalink" href="<?php echo Route::url($this->comment->link('anchor')); ?>" title="<?php echo Lang::txt('PLG_GROUPS_FORUM_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_FORUM_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_FORUM_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php if ($this->comment->wasModified()) { ?>
						&mdash; <?php echo Lang::txt('PLG_GROUPS_FORUM_EDITED'); ?>
						<span class="comment-date-at"><?php echo Lang::txt('PLG_GROUPS_FORUM_AT'); ?></span>
						<span class="time"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_GROUPS_FORUM_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->modified(); ?>"><?php echo $this->comment->modified('date'); ?></time></span>
					<?php } ?>
				</a>
			</p>
			<div class="comment-body">
				<?php echo $comment; ?>
			</div>
			<div class="comment-attachments">
				<?php
				foreach ($this->comment->attachments()->whereEquals('state', Components\Forum\Models\Attachment::STATE_PUBLISHED)->rows() as $attachment)
				{
					if (!trim($attachment->get('description')))
					{
						$attachment->set('description', $attachment->get('filename'));
					}

					$link = $this->comment->link() . '/' . $attachment->get('post_id') . '/' . $attachment->get('filename');

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
						$html = '<p class="attachment"><a href="' . Route::url($link) . '" title="' . $this->escape($attachment->get('description')) . '">' . $attachment->get('description') . '</a></p>';
					}

					echo $html;
				}
				?>
			</div><!-- / .comment-attachments -->
			<p class="comment-options">
			<?php if (
						$this->config->get('access-manage-thread')
					 || $this->config->get('access-delete-thread')
					 || $this->config->get('access-edit-thread')
					 || $this->config->get('access-delete-post')
					 || $this->config->get('access-edit-post')
					) { ?>
				<?php if ($this->comment->get('parent') && $this->config->get('access-delete-post')) { ?>
					<a class="icon-delete delete" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('delete')); ?>"><!--
						--><?php echo Lang::txt('PLG_GROUPS_FORUM_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if (($this->config->get('access-edit-thread') || $this->config->get('access-edit-post')) && $this->comment->get('created_by') == User::get('id')) { ?>
					<a class="icon-edit edit" data-id="c<?php echo $this->comment->get('id'); ?>" href="<?php echo Route::url($this->comment->link('edit')); ?>"><!--
						--><?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT'); ?><!--
					--></a>
				<?php } ?>
			<?php } ?>

			<?php if (!$this->comment->isReported()) { ?>
				<?php if (!$this->thread->get('closed') && $this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_FORUM_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_GROUPS_FORUM_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_FORUM_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
				<a class="icon-abuse abuse" href="<?php echo Route::url($this->comment->link('abuse')); ?>" rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_GROUPS_FORUM_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

			<?php if (!$this->thread->get('closed') && $this->config->get('threading') == 'tree' && $this->depth < $this->config->get('threading_depth', 3)) { ?>
				<div class="comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
					<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->thread->link()); ?>" method="post" enctype="multipart/form-data">
						<fieldset>
							<legend><span><?php echo Lang::txt('PLG_GROUPS_FORUM_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS'))); ?></span></legend>

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
							<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
							<input type="hidden" name="active" value="forum" />
							<input type="hidden" name="action" value="savethread" />

							<?php echo Html::input('token'); ?>

							<label for="comment-<?php echo $this->comment->get('id'); ?>-content">
								<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS'); ?></span>
								<?php
								echo $this->editor('fields[comment]', '', 35, 4, 'field_' . $this->comment->get('id') . '_comment', array('class' => 'minimal no-footer'));
								?>
							</label>

							<label class="upload-label" for="comment-<?php echo $this->comment->get('id'); ?>-file">
								<span class="label-text"><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE'); ?>:</span>
								<input type="file" name="upload" id="comment-<?php echo $this->comment->get('id'); ?>-file" />
							</label>

							<label class="reply-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
								<input class="option" type="checkbox" name="fields[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
								<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
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
			     ->set('group', $this->group)
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
