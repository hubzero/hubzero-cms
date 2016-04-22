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

// No direct access
defined('_HZEXEC_') or die();

$item = $this->post->item();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>
<div class="post full <?php echo $item->type(); ?>" id="b<?php echo $this->post->get('id'); ?>" data-id="<?php echo $this->post->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id')); ?>" data-width="600" data-height="350">
	<div class="content">
		<div class="creator attribution clearfix">
			<?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
				<?php
				$name = $this->escape(stripslashes($item->creator()->get('name')));

				if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
					<a href="<?php echo Route::url($item->creator()->link()); ?>" title="<?php echo $name; ?>" class="img-link">
						<img src="<?php echo $item->creator()->picture(); ?>" alt="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
					</a>
				<?php } else { ?>
					<span class="img-link">
						<img src="<?php echo $item->creator()->picture(); ?>" alt="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
					</span>
				<?php } ?>
				<p>
					<a href="<?php echo Route::url($item->creator()->link()); ?>">
						<?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
					</a> created this post <br />
					<span class="entry-date">
						<span class="entry-date-at">@</span>
						<span class="time"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('time'); ?></time></span>
						<span class="entry-date-on">on</span>
						<span class="date"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('date'); ?></time></span>
					</span>
				</p>
			<?php } else { ?>
				<p class="typeof <?php echo $item->get('type'); ?>">
					<?php echo $this->escape($item->type('title')); ?>
				</p>
			<?php } ?>
		</div><!-- / .attribution -->
		<?php
		$this->view('default_' . $item->type(), 'post')
		     ->set('name', $this->name)
		     ->set('option', $this->option)
		     ->set('group', $this->group)
		     ->set('params', $this->params)
		     ->set('row', $this->post)
		     ->display();
		?>
	<?php if (count($item->tags()) > 0) { ?>
		<div class="tags-wrap">
			<?php echo $item->tags('render'); ?>
		</div>
	<?php } ?>
		<div class="meta">
			<p class="stats">
				<span class="likes">
					<?php echo Lang::txt('%s likes', $item->get('positive', 0)); ?>
				</span>
				<span class="comments">
					<?php echo Lang::txt('%s comments', $item->get('comments', 0)); ?>
				</span>
				<span class="reposts">
					<?php echo Lang::txt('%s reposts', $item->get('reposts', 0)); ?>
				</span>
			</p>
	<?php /*if (!User::isGuest()) { ?>
			<div class="actions">
		<?php if ($item->get('created_by') == User::get('id')) { ?>
				<a class="edit" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/edit'); ?>">
					<span><?php echo Lang::txt('Edit'); ?></span>
				</a>
		<?php } else { ?>
				<a class="vote <?php echo ($item->get('voted')) ? 'unlike' : 'like'; ?>" data-id="<?php echo $this->post->get('id'); ?>" data-text-like="<?php echo Lang::txt('Like'); ?>" data-text-unlike="<?php echo Lang::txt('Unlike'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/vote'); ?>">
					<span><?php echo ($item->get('voted')) ? Lang::txt('Unlike') : Lang::txt('Like'); ?></span>
				</a>
		<?php } ?>
				<a class="comment" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/comment'); ?>">
					<span><?php echo Lang::txt('Comment'); ?></span>
				</a>
				<a class="repost" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/collect'); ?>">
					<span><?php echo Lang::txt('Collect'); ?></span>
				</a>
		<?php if ($this->post->get('original') && ($item->get('created_by') == User::get('id') || $this->params->get('access-delete-item'))) { ?>
				<a class="delete" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/delete'); ?>">
					<span><?php echo Lang::txt('Delete'); ?></span>
				</a>
		<?php } else if ($this->post->get('created_by') == User::get('id') || $this->params->get('access-edit-item')) { ?>
				<a class="unpost" data-id="<?php echo $this->post->get('id'); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/remove'); ?>">
					<span><?php echo Lang::txt('Remove'); ?></span>
				</a>
		<?php } ?>
			</div><!-- / .actions -->
	<?php }*/ ?>
		</div><!-- / .meta -->
<?php //if ($this->post->created_by != $this->post->created_by) { ?>
		<div class="convo attribution clearfix">
			<a href="<?php echo Route::url($this->post->creator()->link()); ?>" title="<?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" class="img-link">
				<img src="<?php echo $this->post->creator()->picture(); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" />
			</a>
			<p>
				<?php
				$who = $this->escape(stripslashes($this->post->creator()->get('name')));
				if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels()))
				{
					$who = '<a href="' . Route::url($this->post->creator()->link()) . '">' . $name . '</a>';
				}

				$where = '<a href="' . Route::url($base . '&task=' . $this->collection->get('alias')) . '">' . $this->escape(stripslashes($this->collection->get('title'))) . '</a>';

				echo Lang::txt('PLG_GROUPS_COLLECTIONS_ONTO', $who, $where);
				?>
				<br />
				<span class="entry-date">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('time'); ?></time></span>
					<span class="entry-date-on">on</span>
					<span class="date"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('date'); ?></time></span>
				</span>
			</p>
		</div><!-- / .attribution -->
	<?php
	if ($item->get('comments'))
	{
		foreach ($item->comments() as $comment)
		{
			$cuser = $comment->creator();
			?>
			<div class="commnts">
				<div class="comment convo clearfix" id="c<?php echo $comment->get('id'); ?>">
					<a href="<?php echo Route::url($cuser->getLink()); ?>" class="img-link">
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($cuser, $comment->get('anonymous')); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($cuser->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo Route::url($cuser->getLink()); ?>"><?php echo $this->escape(stripslashes($cuser->get('name'))); ?></a> said <br />
						<span class="entry-date">
							<span class="entry-date-at">@</span>
							<span class="time"><time datetime="<?php echo $comment->get('created'); ?>"><?php echo $comment->created('time'); ?></time></span>
							<span class="entry-date-on">on</span>
							<span class="date"><time datetime="<?php echo $comment->get('created'); ?>"><?php echo $comment->created('date'); ?></time></span>
						</span>
					</p>
					<blockquote>
						<p><?php echo stripslashes($comment->content); ?></p>
					</blockquote>
				</div>
			</div>
			<?php
		}
	}

	if (!User::isGuest())
	{
		$now = Date::of('now');
		?>
		<div class="commnts">
			<div class="comment convo clearfix">
				<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>" class="img-link">
					<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto(User::getRoot(), 0); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes(User::get('name'))); ?>" />
				</a>
				<p>
					<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>"><?php echo $this->escape(stripslashes(User::get('name'))); ?></a> will say <br />
					<span class="entry-date">
						<span class="entry-date-at">@</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo Date::toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
						<span class="entry-date-on">on</span>
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo Date::toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
					</span>
				</p>
				<form action="<?php echo Route::url($base . '&scope=post/' . $this->post->get('id') . '/savecomment'); ?>" method="post" id="comment-form" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
						<input type="hidden" name="comment[item_type]" value="collection" />
						<input type="hidden" name="comment[state]" value="1" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
						<input type="hidden" name="scope" value="post/<?php echo $this->post->get('id'); ?>/savecomment" />
						<input type="hidden" name="action" value="savecomment" />
						<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

						<?php echo Html::input('token'); ?>

						<textarea name="comment[content]" cols="35" rows="3"></textarea>
						<input type="submit" class="comment-submit" value="<?php echo Lang::txt('Post comment'); ?>" />
					</fieldset>
				</form>
			</div>
		</div>
		<?php
	}
	?>
	</div><!-- / .content -->
</div><!-- / .bulletin -->