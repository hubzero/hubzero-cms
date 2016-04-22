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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$cls = isset($this->cls) ? $this->cls : 'odd';

if ($this->article->get('created_by') == $this->comment->get('created_by'))
{
	$cls .= ' author';
}
$cls .= ($this->comment->isReported()) ? ' abusive' : '';
if ($this->comment->get('state') == 1)
{
	$cls .= ' chosen';
}

$name = Lang::txt('COM_KB_ANONYMOUS');
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
	$comment = '<p class="warning">' . Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
}
else
{
	$comment  = $this->comment->content;
}
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
		<?php if (!$this->comment->isReported() && $this->comment->get('entry_id')) { ?>
			<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
				<?php
				$view = $this->view('_vote')
						     ->set('option', $this->option)
						     ->set('item', $this->comment)
						     ->set('type', 'comment')
						     ->set('vote', '')
						     ->set('id', '');
				if (!User::isGuest())
				{
					$view->set('vote', $this->comment->get('vote'));
					if ($this->comment->get('created_by') == User::get('id'))
					{
						$view->set('id', $this->comment->get('id'));
					}
				}
				$view->display();
				?>
			</p><!-- / .comment-voting -->
		<?php } ?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_KB_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('COM_KB_DATETIME_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_KB_DATETIME_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
			<?php /*if ($this->config->get('access-edit-thread')) { // || User::get('id') == $this->comment->created_by ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($this->base . '&action=delete&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_KB_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=edit&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_KB_EDIT'); ?><!--
					--></a>
				<?php } ?>
			<?php }*/ ?>
			<?php if (!$this->comment->get('reports')) { ?>
				<?php if ($this->depth < $this->article->param('comments_depth', 3) && $this->article->commentsOpen()) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_KB_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_KB_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_KB_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_KB_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_KB_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_KB_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_KB_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('COM_KB_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->article->param('comments_depth', 3) && $this->article->commentsOpen()) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('COM_KB_MUST_LOG_IN', Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->base, false, true)))); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_KB_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_KB_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[parent]" value="<?php echo $this->escape($this->comment->get('id')); ?>" />
						<input type="hidden" name="comment[entry_id]" value="<?php echo $this->escape($this->comment->get('entry_id')); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape(User::get('id')); ?>" />
						<input type="hidden" name="comment[state]" value="1" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="articles" />
						<input type="hidden" name="section" value="<?php echo $this->escape($this->article->get('salias')); ?>" />
						<input type="hidden" name="category" value="<?php echo $this->escape($this->article->get('calias')); ?>" />
						<input type="hidden" name="alias" value="<?php echo $this->escape($this->article->get('alias')); ?>" />
						<input type="hidden" name="task" value="savecomment" />

						<?php echo Html::input('token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_KB_ENTER_COMMENTS'); ?></span>
							<?php echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer')); ?>
						</label>

						<label class="comment-anonymous-label" for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment_<?php echo $this->comment->get('id'); ?>_anonymous" value="1" />
							<?php echo Lang::txt('COM_KB_FIELD_ANONYMOUS'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_KB_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->article->param('comments_depth', 3))
		{
			$comments = $this->comment->replies()
				->whereIn('state', array(
					Components\Kb\Models\Comment::STATE_PUBLISHED,
					Components\Kb\Models\Comment::STATE_FLAGGED
				))
				->rows();

			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('cls', $cls)
			     ->set('depth', $this->depth)
			     ->set('option', $this->option)
			     ->set('article', $this->article)
			     ->set('comments', $comments)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>