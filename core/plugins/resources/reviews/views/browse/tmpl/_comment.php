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

$name = Lang::txt('PLG_RESOURCES_REVIEWS_ANONYMOUS');

if (!$this->comment->get('anonymous'))
{
	$name = $this->escape(stripslashes($this->comment->creator->get('name')));
	if (in_array($this->comment->creator->get('access'), User::getAuthorisedViewLevels()))
	{
		$name = '<a href="' . Route::url($this->comment->creator->link()) . '">' . $name . '</a>';
	}
}

$this->comment->set('item_type', 'review');

if ($this->comment->isReported())
{
	$comment = '<p class="warning">' . Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED') . '</p>';
}
else
{
	$comment  = $this->comment->content;
}

if ($this->comment->get('resource_id'))
{
	$this->comment->set('created_by', $this->comment->get('user_id'));
	$this->comment->set('item_id', $this->comment->get('id'));
	$this->comment->set('parent', 0);

	switch ($this->comment->get('rating', 0))
	{
		case 0.5: $class = ' half-stars';      break;
		case 1:   $class = ' one-stars';       break;
		case 1.5: $class = ' onehalf-stars';   break;
		case 2:   $class = ' two-stars';       break;
		case 2.5: $class = ' twohalf-stars';   break;
		case 3:   $class = ' three-stars';     break;
		case 3.5: $class = ' threehalf-stars'; break;
		case 4:   $class = ' four-stars';      break;
		case 4.5: $class = ' fourhalf-stars';  break;
		case 5:   $class = ' five-stars';      break;
		case 0:
		default:  $class = ' no-stars';      break;
	}
}

?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator->picture($this->comment->get('anonymous', 0)); ?>" alt="" />
		</p>
		<div class="comment-content">
		<?php if (!$this->comment->isReported() && $this->comment->get('resource_id') && $this->config->get('voting')) { ?>
			<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
				<?php
				$this->comment->set('helpful', $this->comment->votes()->whereEquals('vote', 1)->total());
				$this->comment->set('nothelpful', $this->comment->votes()->whereEquals('vote', -1)->total());

				if (!User::isGuest() && $this->comment->get('created_by') == User::get('id'))
				{
					$this->comment->set('vote', $this->comment->ballot(User::get('id'), Request::ip())->get('vote'));
				}

				$this->view('_rateitem')
					->set('option', $this->option)
					->set('item', $this->comment)
					->set('type', 'review')
					->display();
				?>
			</p><!-- / .comment-voting -->
		<?php } ?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_PERMALINK'); ?>">
					<?php if (!$this->comment->created() || $this->comment->created() == '0000-00-00 00:00:00') { ?>
						<span class="comment-date-unknown"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_UNKNOWN'); ?></span>
					<?php } else { ?>
						<span class="comment-date-at">@</span>
						<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
					<?php } ?>
				</a>
			</p>
			<?php if ($this->comment->get('resource_id')) { ?>
			<p>
				<span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_OUT_OF_5_STARS', $this->comment->get('rating', 0)); ?></span></span>
			</p>
			<?php } ?>

	<?php if (Request::getWord('action') == 'edit' && Request::getInt('comment') == $this->comment->get('id')) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
					<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
					<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created]" value="<?php echo $this->comment->get('created'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->comment->get('created_by'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
					<input type="hidden" name="active" value="reviews" />
					<input type="hidden" name="action" value="savereply" />

					<?php echo Html::input('token'); ?>

					<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
						<span class="label-text"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS'); ?></span>
						<?php
						echo $this->editor('comment[content]', $this->comment->get('content'), 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
						?>
					</label>

					<label id="comment-anonymous-label" for="comment-anonymous">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" <?php if ($this->comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>
			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
		<?php if (!$this->comment->isReported() && !stristr($comment, 'class="warning"')) { ?>
			<?php if (User::get('id') == $this->comment->get('created_by') || User::authorise('core.manage', 'com_resources')) { ?>
					<a class="icon-delete delete" data-txt-confirm="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->base . '&action=delete' . ($this->comment->get('resource_id') ? 'review' : 'reply') . '&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_DELETE'); ?><!--
					--></a>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=edit' . ($this->comment->get('resource_id') ? 'review' : '') . '&comment=' . $this->comment->get('id') . ($this->comment->get('resource_id') ? '#commentform' : '')); ?>"><!--
						--><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_EDIT'); ?><!--
					--></a>
			<?php } ?>
			<?php if (!$this->comment->get('reports')) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
		<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->base, false, true))) . '">' . Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('PLG_RESOURCES_REVIEWS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo ($this->comment->get('resource_id') ? 0 : $this->comment->get('id')); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
						<input type="hidden" name="active" value="reviews" />
						<input type="hidden" name="action" value="savereply" />

						<?php echo Html::input('token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
	<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$replies = $this->comment->replies()
				->whereIn('state', array(
					Components\Resources\Reviews\Models\Comment::STATE_PUBLISHED,
					Components\Resources\Reviews\Models\Comment::STATE_FLAGGED
				))
				->ordered()
				->rows();

			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('resource', $this->resource)
			     ->set('option', $this->option)
			     ->set('comments', $replies)
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>
