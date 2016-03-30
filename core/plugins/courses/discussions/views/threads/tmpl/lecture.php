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

$this->css()
     ->js('discussions.lecture.js');

$base = $this->course->offering()->link();
?>
<div id="comments-container" data-action="<?php echo Route::url($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>">

	<div class="comments-wrap">

		<div class="comments-views">
			<div class="comments-feed">
				<div class="comments-toolbar cf">
					<p class="comment-sort-options">
						<?php echo Lang::txt('%s Discussions', count($this->threads)); ?>
					</p>
					<p class="comments-controls">
						<a class="add<?php if (!$this->thread) { echo ' active'; } ?>" href="<?php echo Route::url($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" title="<?php echo Lang::txt('Start a new discussion'); ?>"><?php echo Lang::txt('New'); ?></a>
					</p>
				</div><!-- / .comments-toolbar -->

				<div class="comments-options-bar">
					<form class="comments-search" action="<?php echo Route::url($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="get">
						<fieldset>
							<input type="text" name="search" class="search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('search ...'); ?>" />
							<input type="submit" class="submit" value="<?php echo Lang::txt('Go'); ?>" />
							<input type="hidden" name="action" value="search" />
						</fieldset>
					</form>
				</div><!-- / .comments-options-bar -->

				<div class="comment-threads">
					<div class="category search-results hide">
						<div class="category-header">
							<span class="category-title"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SEARCH'); ?></span>
						</div>
						<div class="category-content">
						</div>
					</div>
					<div class="category category-results">
						<div class="category-content">
							<?php
							$threads_lastchange = '0000-00-00 00:00:00';
							if ($this->threads)
							{
								$threads_lastchange = $this->threads[0]->get('created');
								$category = $this->threads[0]->get('category_id');
							}

							$instructors = array();
							$inst = $this->course->instructors();
							if (count($inst) > 0)
							{
								foreach ($inst as $i)
								{
									$instructors[] = $i->get('user_id');
								}
							}
							$this->view('_threads')
								->set('category', 'category' . $this->post->get('category_id'))
								->set('option', $this->option)
								->set('threads', $this->threads)
								->set('unit', $this->unit->get('alias'))
								->set('lecture', $this->lecture->get('alias'))
								->set('config', $this->config)
								->set('cls', 'odd')
								->set('instructors', $instructors)
								->set('base', $base . '&active=outline')
								->set('course', $this->course)
								->set('search', $this->filters['search'])
								->set('active', $this->thread)
								->display();
							?>
							<input type="hidden" name="threads_lastchange" id="threads_lastchange" value="<?php echo $threads_lastchange; ?>" />
						</div>
					</div>
				</div><!-- / .comment-threads -->
			</div><!-- / .comments-feed -->

			<div class="comments-panel">
				<div class="comments-toolbar">
					<p><span class="comments" data-comments="<?php echo Lang::txt('%s comments'); ?>" data-add="<?php echo Lang::txt('Start a discussion'); ?>"><?php echo Lang::txt('Start a discussion'); ?></span></p>
				</div>
				<div class="comments-frame">

					<form action="<?php echo Route::url($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="post" id="commentform"<?php if ($this->data) { echo ' class="hide"'; } ?> enctype="multipart/form-data">

						<p class="comment-member-photo">
							<a class="comment-anchor" name="commentform"></a>
							<?php
							$anone = 1;
							if (!User::isGuest())
							{
								$anon = 0;
							}
							?>
							<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto(User::getRoot(), $anon); ?>" alt="<?php echo Lang::txt('User photo'); ?>" />
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
									<span class="comment-date-at">@</span>
									<span class="time"><time datetime="<?php echo Date::of('now')->toSql(); ?>"><?php echo Date::of('now')->toLocal(Lang::txt('TIME_FORMAt_HZ1')); ?></time></span>
									<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo Date::of('now')->toSql(); ?>"><?php echo Date::of('now')->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?></time></span>
								</span>
							</p>

							<label for="field_comment">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
								<?php
								echo $this->editor('fields[comment]', '', 35, 5, 'field_comment', array('class' => 'minimal no-footer'));
								?>
							</label>

							<label for="field-upload" id="comment-upload">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:</span>
								<input type="file" name="upload" id="field-upload" />
							</label>

							<label for="field-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
								<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
							</p>
						<?php } ?>
						</fieldset>
						<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
						<input type="hidden" name="fields[parent]" id="field-parent" value="0" />
						<input type="hidden" name="fields[state]" id="field-state" value="1" />
						<input type="hidden" name="fields[scope]" id="field-scope" value="<?php echo $this->post->get('scope'); ?>" />
						<input type="hidden" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->post->get('scope_id'); ?>" />
						<input type="hidden" name="fields[scope_sub_id]" id="field-scope_sub_id" value="<?php echo $this->post->get('scope_sub_id'); ?>" />
						<input type="hidden" name="fields[id]" id="field-id" value="" />
						<input type="hidden" name="fields[object_id]" id="field-object_id" value="<?php echo $this->post->get('object_id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="action" value="savethread" />
						<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />
						<input type="hidden" name="return" value="<?php echo base64_encode(Route::url($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias'))); ?>" />

						<?php echo Html::input('token'); ?>

						<p class="instructions">
							<?php echo Lang::txt('Click on a comment on the left to view a discussion or start your own above.'); ?>
						</p>
					</form>

					<div class="comment-thread"><?php if ($this->data) { echo $this->data->html; } ?></div>

				</div><!-- / .comments-frame -->
			</div><!-- / .comments-panel -->
		</div><!-- / .comments-views -->

 	</div><!-- / .comments-wrap -->
</div><!-- / #comments-container -->