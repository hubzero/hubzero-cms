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

$base = $this->offering->link() . '&active=forum';
?>
<div class="filters">
	<div class="filters-inner">
		<p>
			<a class="comments btn" href="<?php echo Route::url($base . '&unit=' . $this->category->alias); ?>">
				<?php echo Lang::txt('All discussions'); ?>
			</a>
		</p>
		<h3 class="thread-title">
			<?php echo $this->escape(stripslashes($this->post->title)); ?>
		</h3>
	</div>
</div>

<section class="main section">
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo Route::url($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="get">
			<?php
			if ($this->rows)
			{
				$last = '0000-00-00 00:00:00';
				foreach ($this->rows as $row)
				{
					if ($row->created > $last)
					{
						$last = $row->created;
					}
				}
				echo '<input type="hidden" name="lastchange" id="lastchange" value="' . $last . '" />';
				$this->view('list')
				     ->set('option', $this->option)
				     ->set('comments', $this->rows)
				     ->set('post', $this->post)
				     ->set('unit', $this->category->alias)
				     ->set('lecture', $this->post->id)
				     ->set('config', $this->config)
				     ->set('depth', 0)
				     ->set('cls', 'odd')
				     ->set('base', $base)
				     ->set('attach', $this->attach)
				     ->set('course', $this->course)
				     ->display();
			}
			else
			{
				?>
				<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NO_REPLIES_FOUND'); ?></p>
				<?php
			}

			// Initiate paging
			$pageNav = $this->pagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'forum');
			$pageNav->setAdditionalUrlParam('unit', $this->category->alias);
			$pageNav->setAdditionalUrlParam('b', $this->post->id);

			echo $pageNav->render();
			?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ALL_TAGS'); ?></h4>
			<?php if ($this->tags) { ?>
				<?php echo $this->tags; ?>
			<?php } else { ?>
				<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
			<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_PARTICIPANTS'); ?></h4>
			<?php if ($this->participants) { ?>
				<ul>
				<?php
					$anon = false;
					foreach ($this->participants as $participant)
					{
						if (!$participant->anonymous) {
						?>
						<li><a href="<?php echo Route::url('index.php?option=com_members&id=' . $participant->created_by); ?>"><?php echo $this->escape(stripslashes($participant->name)); ?></a></li>
						<?php
						} else if (!$anon) {
							$anon = true;
						?>
						<li><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS'); ?></li>
						<?php
						}
					}
				?>
				</ul>
			<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ATTACHMENTS'); ?></h4>
			<?php if ($this->attachments) { ?>
				<ul class="attachments">
				<?php
				foreach ($this->attachments as $attachment)
				{
					$title = ($attachment->description) ? $attachment->description : $attachment->filename;
					?>
					<li><a href="<?php echo Route::url($base . '&unit=' . $this->category->alias . '&b=' . $attachment->parent . '&c=' . $attachment->post_id . '/' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
				<?php } ?>
				</ul>
			<?php } else { ?>
				<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
			<?php } ?>
		</div><!-- / .container -->
	</aside><!-- / .aside  -->
</section><!-- / .main section -->

<section class="below section">
	<div class="subject">
		<h3 class="post-comment-title">
			<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ADD_COMMENT'); ?>
		</h3>
		<form action="<?php echo Route::url($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<?php
				$anon = 1;
				$jxuser = \Hubzero\User\Profile::getInstance(User::get('id'));
				if (!User::isGuest())
				{
					$anon = 0;
				}
				?>
				<img src="<?php echo $jxuser->getPicture($anon); ?>" alt="" />
			</p>

			<fieldset>
			<?php if (User::isGuest()) { ?>
				<p class="warning"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id')); ?>"><?php echo $this->escape(User::get('name')); ?></a>
					</strong>
					<span class="permalink">
						<span class="comment-date-at"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_AT'); ?><</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo Date::of('now')->toLocal(Lang::txt('TIME_FORMAt_HZ1')); ?></time></span>
						<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo Date::of('now')->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?></time></span>
					</span>
				</p>

				<label for="field_comment">
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?>
					<?php
					echo $this->editor('fields[comment]', '', 35, 15, 'field_comment', array('class' => 'minimal no-footer'));
					?>
				</label>

				<fieldset>
					<legend><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
					<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
				</p>
			<?php } ?>

			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->category_id; ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />

			<?php echo Html::input('token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT_HINT'); ?></p>
	</aside><!-- /.aside -->
</section><!-- / .below section -->