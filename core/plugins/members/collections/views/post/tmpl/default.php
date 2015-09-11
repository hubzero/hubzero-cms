<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->post->item();
$base = $this->member->getLink() . '&active=' . $this->name;

$this->css()
     ->js();
?>

<div class="post full <?php echo $item->type(); ?>" id="b<?php echo $this->post->get('id'); ?>" data-id="<?php echo $this->post->get('id'); ?>" data-closeup-url="<?php echo Route::url($base . '&task=post/' . $this->post->get('id')); ?>" data-width="600" data-height="350">
	<div class="content">
		<div class="creator attribution cf">
			<?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
				<?php
				$name = $this->escape(stripslashes($item->creator('name')));

				if ($item->creator('public')) { ?>
					<a href="<?php echo Route::url($item->creator()->getLink()); ?>" title="<?php echo $name; ?>" class="img-link">
						<img src="<?php echo $item->creator()->getPicture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
					</a>
				<?php } else { ?>
					<span class="img-link">
						<img src="<?php echo $item->creator()->getPicture(); ?>" alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
					</span>
				<?php } ?>
				<p>
					<a href="<?php echo Route::url($item->creator()->getLink()); ?>">
						<?php echo $this->escape(stripslashes($item->creator('name'))); ?>
					</a> created this post
					<br />
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
		     ->set('actual', true)
		     ->set('name', $this->name)
		     ->set('option', $this->option)
		     ->set('member', $this->member)
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
		</div><!-- / .meta -->
		<div class="convo attribution clearfix">
			<a href="<?php echo Route::url($this->post->creator()->getLink()); ?>" title="<?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" class="img-link">
				<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($this->post->creator(), 0); ?>" alt="Profile picture of <?php echo $this->escape(stripslashes($this->post->creator()->get('name'))); ?>" />
			</a>
			<p>
				<?php
				$who = $this->escape(stripslashes($this->post->creator('name')));
				if ($this->post->creator('public'))
				{
					$who = '<a href="' . Route::url($this->post->creator()->getLink()) . '">' . $name . '</a>';
				}

				$where = '<a href="' . Route::url($base . '&task=' . $this->collection->get('alias')) . '">' . $this->escape(stripslashes($this->collection->get('title'))) . '</a>';

				echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where);
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
		?>
		<div class="commnts">
			<?php
			foreach ($item->comments() as $comment)
			{
				$cuser = \Hubzero\User\Profile::getInstance($comment->created_by);
				?>
				<div class="comment convo clearfix" id="c<?php echo $comment->id; ?>">
					<a href="<?php echo Route::url('index.php?option=com_members&id=' . $comment->created_by); ?>" class="img-link">
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($cuser, $comment->anonymous); ?>" class="profile user_image" alt="Profile picture of <?php echo $this->escape(stripslashes($cuser->get('name'))); ?>" />
					</a>
					<p>
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . $comment->created_by); ?>"><?php echo $this->escape(stripslashes($cuser->get('name'))); ?></a> said <br />
						<span class="entry-date">
							<span class="entry-date-at">@</span>
							<span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo Date::of($comment->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
							<span class="entry-date-on">on</span>
							<span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo Date::of($comment->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
						</span>
					</p>
					<blockquote>
						<p><?php echo stripslashes($comment->content); ?></p>
					</blockquote>
				</div>
				<?php
			}
			?>
		</div>
		<?php
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
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo Date::of($now)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
						<span class="entry-date-on">on</span>
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo Date::of($now)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
					</span>
				</p>
				<form action="<?php echo Route::url($base . '&task=post/' . $this->post->get('id') . '/savecomment'); ?>" method="post" id="comment-form" enctype="multipart/form-data">
					<fieldset>
						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
						<input type="hidden" name="comment[item_type]" value="collection" />
						<input type="hidden" name="comment[state]" value="1" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
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