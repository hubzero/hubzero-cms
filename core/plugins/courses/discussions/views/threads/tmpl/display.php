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

$base = $this->offering->link() . '&active=forum';

$this->category->set('section_alias', $this->filters['section']);

$this->thread->set('section', $this->filters['section']);
$this->thread->set('category', $this->category->get('alias'));
?>
<div class="filters">
	<div class="filters-inner">
		<p>
			<a class="comments btn" href="<?php echo Route::url($base . '&unit=' . $this->category->get('alias')); ?>">
				<?php echo Lang::txt('All discussions'); ?>
			</a>
		</p>
		<h3 class="thread-title">
			<?php echo $this->escape(stripslashes($this->post->get('title'))); ?>
		</h3>
	</div>
</div>

<section class="main section">
	<div class="subject">
		<h3 class="thread-title<?php echo ($this->thread->get('closed')) ? ' closed' : ''; ?>">
			<?php echo $this->escape(stripslashes($this->thread->get('title'))); ?>
		</h3>

		<?php
		$threading = $this->config->get('threading', 'list');

		$posts = $this->thread->thread()
			->whereIn('state', $this->filters['state'])
			->whereIn('access', $this->filters['access'])
			->order(($threading == 'tree' ? 'lft' : 'created'), 'asc')
			->paginated()
			->rows();

		if ($posts->count() > 0)
		{
			if ($threading == 'tree')
			{
				$posts = $this->thread->toTree($posts);
			}

			$this->view('_list')
			     ->set('option', $this->option)
			     ->set('group', $this->group)
			     ->set('comments', $posts)
			     ->set('thread', $this->thread)
			     ->set('parent', 0)
			     ->set('config', $this->config)
			     ->set('depth', 0)
			     ->set('cls', 'odd')
			     ->set('filters', $this->filters)
			     ->set('category', $this->category)
			     ->display();
		}
		else
		{
			?>
			<ol class="comments">
				<li>
					<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NO_REPLIES_FOUND'); ?></p>
				</li>
			</ol>
			<?php
		}
		?>

		<form action="<?php echo Route::url($base . '&unit=' . $this->category->get('alias') . '&b=' . $this->post->get('id')); ?>" method="get">
			<?php
			// Initiate paging
			$pageNav = $posts->pagination;
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'discussions');
			$pageNav->setAdditionalUrlParam('unit', $this->category->get('alias'));
			$pageNav->setAdditionalUrlParam('b', $this->post->get('id'));

			echo $pageNav;
			?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ALL_TAGS'); ?></h4>
			<?php if ($this->thread->tags('cloud')) { ?>
				<?php echo $this->thread->tags('cloud'); ?>
			<?php } else { ?>
				<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
			<?php } ?>
		</div><!-- / .container -->

		<?php
		$participants = $this->thread->participants()
			->whereIn('state', $this->filters['state'])
			->whereIn('access', $this->filters['access'])
			->rows();

		if ($participants->count() > 0) { ?>
			<div class="container">
				<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_PARTICIPANTS'); ?></h4>
				<ul>
					<?php
					$anon = false;
					foreach ($participants as $participant)
					{
						if (!$participant->get('anonymous'))
						{
							?>
							<li>
								<a class="member" href="<?php echo Route::url('index.php?option=com_members&id=' . $participant->get('created_by')); ?>">
									<?php echo $this->escape(stripslashes($participant->get('name'))); ?>
								</a>
							</li>
							<?php
						}
						// Only display "anonymous" once
						elseif (!$anon)
						{
							$anon = true;
							?>
							<li>
								<span class="member">
									<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ANONYMOUS'); ?>
								</span>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div><!-- / .container -->
		<?php } ?>

		<?php
		$attachments = Components\Forum\Models\Attachment::all()
			->whereEquals('parent', $this->thread->get('thread'))
			->whereIn('state', $this->filters['state'])
			->rows();

		if ($attachments->count() > 0) { ?>
			<div class="container">
				<h4><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ATTACHMENTS'); ?></h4>
				<ul class="attachments">
					<?php
					foreach ($attachments as $attachment)
					{
						if ($attachment->get('status') != $attachment::STATE_DELETED)
						{
							$cls = 'file';
							$title = trim($attachment->get('description', $attachment->get('filename')));
							$title = ($title ? $title : $attachment->get('filename'));

							// trims long titles
							$title = (strlen($title) > 25) ? substr($title,0,22) . '...' : $title;

							if ($attachment->isImage())
							{
								$cls = 'img';
							}
							?>
							<li>
								<a class="<?php echo $cls; ?> attachment" href="<?php echo Route::url($base . '&unit=' . $this->category->get('alias') . '&b=' . $attachment->get('parent') . '&c=' . $attachment->get('post_id') . '/' . $attachment->get('filename')); ?>">
									<?php echo $this->escape($title); ?>
								</a>
							</li>
							<?php
						} //end status check
					}
					?>
				</ul>
			</div><!-- / .container -->
		<?php } ?>
	</aside><!-- / .aside  -->
</section><!-- / .main section -->

<section class="below section">
	<div class="subject">
		<h3 class="post-comment-title">
			<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ADD_COMMENT'); ?>
		</h3>
		<form action="<?php echo Route::url($base . '&unit=' . $this->category->get('alias') . '&b=' . $this->post->get('id')); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<?php
				$anon = 1;
				$user = \Components\Members\Models\Member::oneOrNew(User::get('id'));
				if (!User::isGuest())
				{
					$anon = 0;
				}
				?>
				<img src="<?php echo $user->picture($anon); ?>" alt="" />
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
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->get('category_id'); ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
			<input type="hidden" name="active" value="discussions" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />

			<?php echo Html::input('token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT_HINT'); ?></p>
	</aside><!-- /.aside -->
</section><!-- / .below section -->