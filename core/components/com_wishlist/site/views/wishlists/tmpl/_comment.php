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

	if ($this->wish->get('proposed_by') == $this->comment->get('created_by'))
	{
		$cls .= ' author';
	}

	$name = Lang::txt('COM_WISHLIST_ANONYMOUS');
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

			<?php if ($attachment = $this->comment->get('attachment')) { ?>
			<p class="comment-attachment icon-attachment">
				<?php echo $attachment; ?>
			</p>
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
					<a class="icon-reply reply active" data-txt-active="<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WISHLIST_REPLY'); ?>" href="<?php echo Route::url($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>" data-txt-inactive="<?php echo Lang::txt('COM_WISHLIST_REPLY'); ?>" href="<?php echo Route::url($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
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
						<legend><span><?php echo Lang::txt('COM_WISHLIST_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : Lang::txt('COM_WISHLIST_ANONYMOUS'))); ?></span></legend>

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

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo Lang::txt('COM_WISHLIST_ENTER_COMMENTS'); ?></span>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
							?>
						</label>

						<label class="comment-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
							<?php echo Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_SUBMIT'); ?>" />
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
			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('cls', $cls)
			     ->set('depth', $this->depth)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies(array('state' => array(1, 3))))
			     ->set('wishlist', $this->wishlist)
			     ->set('wish', $this->wish)
			     ->display();
		}
		?>
	</li>