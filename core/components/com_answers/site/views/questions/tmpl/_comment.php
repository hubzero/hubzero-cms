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
if ($this->question->get('created_by') == $this->comment->get('created_by'))
{
	$cls .= ' author';
}
$cls .= ($this->comment->isReported()) ? ' abusive' : '';
if ($this->comment->get('state') == 1)
{
	$cls .= ' chosen';
}

if (!$this->comment->get('item_type'))
{
	$this->comment->set('item_type', 'response');
}
if (!$this->comment->get('item_id'))
{
	$this->comment->set('item_id', ($this->depth == 1 ? $this->comment->get('id') : $this->item_id));
}
?>
	<li class="comment <?php echo $cls; ?>" id="<?php echo ($this->depth == 1 ? 'a' : 'c') . $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<?php if (!$this->comment->isReported() && $this->comment->get('qid')) { ?>
				<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
					<?php
					$this->view('_vote')
					     ->set('option', $this->option)
					     ->set('item', $this->comment)
					     ->set('vote', $this->comment->ballot())
					     ->display();
					?>
				</p><!-- / .comment-voting -->
			<?php } ?>

			<p class="comment-title">
				<strong>
					<?php
					$name = Lang::txt('COM_ANSWERS_ANONYMOUS');
					if (!$this->comment->get('anonymous'))
					{
						$name = $this->escape(stripslashes($this->comment->creator()->get('name', $name)));
						if ($this->comment->creator()->get('public'))
						{
							$name = '<a href="' . Route::url($this->comment->creator()->getLink()) . '">' . $name . '</a>';
						}
					}
					echo $name;
					?>
				</strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#' . ($this->depth == 1 ? 'a' : 'c') . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('COM_ANSWERS_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo Lang::txt('COM_ANSWERS_DATETIME_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('COM_ANSWERS_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php
				$comment  = $this->comment->content;

				if ($this->comment->isReported())
				{
					$comment = '<p class="warning">' . Lang::txt('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
				}

				echo $comment;
				?>
			</div>

			<p class="comment-options">
			<?php /*if ($this->config->get('access-edit-thread')) { // || User::get('id') == $this->comment->created_by ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo Route::url($this->base . '&action=delete&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_ANSWERS_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=edit&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('COM_ANSWERS_EDIT'); ?><!--
					--></a>
				<?php } ?>
			<?php }*/ ?>
			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_ANSWERS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_ANSWERS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->depth . $this->comment->get('item_type') . $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_ANSWERS_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_ANSWERS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_ANSWERS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->depth . $this->comment->get('item_type') . $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_ANSWERS_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('COM_ANSWERS_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('COM_ANSWERS_REPORT_ABUSE'); ?><!--
				--></a>
				<?php if (User::get('id') == $this->question->get('created_by') && $this->question->isOpen() && $this->comment->get('qid') && $this->depth <= 1) { ?>
					<a class="accept" href="<?php echo Route::url($this->comment->link('accept')); ?>"><?php echo Lang::txt('COM_ANSWERS_ACCEPT_ANSWER'); ?></a>
				<?php } ?>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->depth . $this->comment->get('item_type') . $this->comment->get('id'); ?>">
				<?php if (User::get('guest')) { ?>
				<p class="warning">
					<?php echo Lang::txt('COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->base, false, true))) . '">' . Lang::txt('COM_ANSWERS_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->depth . $this->comment->get('item_type') . $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('COM_ANSWERS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_ANSWERS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type', 'response'); ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo ($this->depth == 1 ? 0 : $this->comment->get('id')); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="comment[state]" value="1" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="questions" />
						<input type="hidden" name="rid" value="<?php echo $this->question->get('id'); ?>" />
						<input type="hidden" name="task" value="savereply" />

						<?php echo Html::input('token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_ANSWERS_ENTER_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label class="comment-anonymous-label" for="comment_<?php echo $this->comment->get('id'); ?>_anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment_<?php echo $this->comment->get('id'); ?>_anonymous" value="1" />
							<?php echo Lang::txt('COM_ANSWERS_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_ANSWERS_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$this->view('_list')
			     ->set('item_id', $this->comment->get('item_id'))
			     ->set('parent', $this->comment->get('id'))
			     ->set('question', $this->question)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies()->where('state', '!=', 2)->rows())
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>