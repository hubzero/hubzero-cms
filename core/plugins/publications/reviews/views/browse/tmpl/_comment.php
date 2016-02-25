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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	$name = Lang::txt('PLG_PUBLICATIONS_REVIEWS_ANONYMOUS');
	$huser = new \Hubzero\User\Profile;

	if (!$this->comment->get('anonymous'))
	{
		$huser = \Hubzero\User\Profile::getInstance($this->comment->get('created_by'));
		if (is_object($huser) && $huser->get('name'))
		{
			$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $huser->get('uidNumber')) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	$this->comment->set('item_type', 'pubreview');

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_NOTICE_POSTING_REPORTED') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}

	if ($this->comment->get('publication_id'))
	{
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
			<img src="<?php echo $huser->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
		<?php if (!$this->comment->isReported() && $this->comment->get('publication_id')) { ?>
			<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
				<?php
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'publications',
						'element' => 'reviews',
						'name'    => 'browse',
						'layout'  => '_rateitem'
					)
				);
				$view->option = $this->option;
				$view->item   = $this->comment;
				$view->type   = 'review';
				$view->vote   = '';
				$view->id     = '';
				if (!User::isGuest())
				{
					if ($this->comment->get('created_by') == User::get('username'))
					{
						$view->vote = $this->comment->get('vote');
						$view->id   = $this->comment->get('id');
					}
				}
				$view->display();
				?>
			</p><!-- / .comment-voting -->
		<?php } ?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo Route::url($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_PERMALINK'); ?>">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>
			<?php if ($this->comment->get('publication_id')) { ?>
			<p>
				<span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_OUT_OF_5_STARS', $this->comment->get('rating', 0)); ?></span></span>
			</p>
			<?php } ?>

	<?php if (Request::getWord('action') == 'edit' && Request::getInt('comment') == $this->comment->get('id')) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
					<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
					<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created]" value="<?php echo $this->comment->get('created'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->comment->get('created_by'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->publication->id; ?>" />
					<input type="hidden" name="active" value="reviews" />
					<input type="hidden" name="action" value="savereply" />

					<?php echo Html::input('token'); ?>

					<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
						<span class="label-text"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_ENTER_COMMENTS'); ?></span>
						<?php
						echo $this->editor('comment[content]', $this->comment->content('raw'), 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
						?>
					</label>

					<label id="comment-anonymous-label" for="comment-anonymous">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" <?php if ($this->comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
					</label>

					<p class="submitarea">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>
			<?php echo $comment; ?>

			<p class="comment-options">
		<?php if (!$this->comment->isReported() && !stristr($comment, 'class="warning"')) { ?>
			<?php if (User::get('id') == $this->comment->get('created_by')) { ?>
					<a class="icon-delete delete" data-txt-confirm="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_CONFIRM_DELETE'); ?>" href="<?php echo Route::url($this->base . '&action=delete' . ($this->comment->get('publication_id') ? 'review' : 'reply') . '&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_DELETE'); ?><!--
					--></a>
					<a class="icon-edit edit" href="<?php echo Route::url($this->base . '&action=edit' . ($this->comment->get('publication_id') ? 'review' : '') . '&comment=' . $this->comment->get('id') . ($this->comment->get('publication_id') ? '#commentform' : '#c' . $this->comment->get('id'))); ?>"><!--
						--><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_EDIT'); ?><!--
					--></a>
			<?php } ?>
			<?php if (!$this->comment->get('reports')) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (Request::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id') . '-' . $this->depth; ?>"><!--
					--><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id') . '-' . $this->depth; ?>"><!--
					--><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_NOTICE_POSTING_REPORTED'); ?>" href="<?php echo Route::url($this->comment->link('report')); ?>"><!--
					--><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
		<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (Request::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id') . '-' . $this->depth; ?>">
				<?php if (User::isGuest()) { ?>
				<p class="warning">
					<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->base, false, true))) . '">' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo Route::url($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('PLG_PUBLICATIONS_REVIEWS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo ($this->comment->get('publication_id') ? 0 : $this->comment->get('id')); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo User::get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->publication->id; ?>" />
						<input type="hidden" name="active" value="reviews" />
						<input type="hidden" name="action" value="savereply" />

						<?php echo Html::input('token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_ENTER_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submitarea">
							<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_SUBMIT'); ?>" />
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
			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('publication', $this->publication)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies(array('state' => array(1, 3))))
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>