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
     ->js();

$database = App::get('db');

$base = $this->course->offering()->link();

$instructors = array();

$inst = $this->course->instructors();
if (count($inst) > 0)
{
	foreach ($inst as $i)
	{
		$instructors[] = $i->get('user_id');
	}
}
?>
<div id="comments-container" data-action="<?php echo Route::url($base . '&active=discussions'); ?>">

	<div class="comments-wrap">
		<div class="comments-views">

			<div class="comments-feed">
				<div class="comments-toolbar cf">
					<p class="comment-sort-options">
						<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS'); ?>
					</p>
					<p class="comments-controls">
						<a class="add active" href="<?php echo Route::url($base . '&active=discussions'); ?>" title="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW_TOPIC'); ?>"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW'); ?></a>
					</p>
				</div><!-- / .comments-toolbar -->

				<div class="comments-options-bar">
					<form class="comments-search" action="<?php echo Route::url($base . '&active=discussions'); ?>" method="get">
						<fieldset>
							<input type="text" name="search" class="search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SEARCH_PLACEHOLDER'); ?>" />
							<input type="submit" class="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_GO'); ?>" />

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
							<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
							<input type="hidden" name="active" value="discussions" />
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

					<div class="category category-results" id="ctmyreplies">
						<?php
						$threads = \Components\Forum\Models\Post::all()
							->whereEquals('scope', $this->filters['scope'])
							->whereEquals('scope_id', $this->filters['scope_id'])
							->whereEquals('scope_sub_id', $this->filters['scope_sub_id'])
							->whereEquals('state', \Components\Forum\Models\Post::STATE_PUBLISHED)
							->whereEquals('created_by', User::get('id'))
							->order('created', 'desc')
							->limit(100)
							->rows();
						?>
						<div class="category-header">
							<span class="category-title"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_REPLIES_TO_MY_COMMENTS'); ?></span>
							<span class="category-discussions count"><?php echo $threads->count(); ?></span>
						</div><!-- / .category-header -->
						<div class="category-content">
							<?php
							$this->view('_threads')
								->set('category', 'categoryreplies')
								->set('option', $this->option)
								->set('threads', $threads)
								->set('unit', '')
								->set('lecture', 0)
								->set('config', $this->config)
								->set('instructors', $instructors)
								->set('cls', 'odd')
								->set('base', $base)
								->set('course', $this->course)
								->set('prfx', 'mine')
								->set('active', $this->thread)
								->display();
							?>
						</div><!-- / .category-content -->
					</div><!-- / .category -->

					<div class="category category-results closed" id="newcomments">
						<?php
						$threads = \Components\Forum\Models\Post::all()
							->whereEquals('scope', $this->filters['scope'])
							->whereEquals('scope_id', $this->filters['scope_id'])
							->whereEquals('scope_sub_id', $this->filters['scope_sub_id'])
							->whereEquals('state', \Components\Forum\Models\Post::STATE_PUBLISHED)
							->order('created', 'desc')
							->limit(100)
							->rows();
						?>
						<div class="category-header">
							<span class="category-title"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LATEST_COMMENTS'); ?></span>
							<span class="category-discussions count"><?php echo $threads->count(); ?></span>
						</div><!-- / .category-header -->
						<div class="category-content">
							<?php
							$this->view('_threads')
								->set('category', 'categorynew')
								->set('option', $this->option)
								->set('threads', $threads)
								->set('unit', '')
								->set('lecture', 0)
								->set('config', $this->config)
								->set('instructors', $instructors)
								->set('cls', 'odd')
								->set('base', $base)
								->set('course', $this->course)
								->set('prfx', 'new')
								->set('active', $this->thread)
								->display();
							?>
						</div><!-- / .category -->
					</div><!-- / .category -->

				</div><!-- / .comment-threads -->

			</div><!-- / .comments-feed -->

			<div class="comments-panel">
				<div class="comments-toolbar">
					<p><span class="comments" data-comments="%s comments" data-add="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW_TOPIC'); ?>"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW_TOPIC'); ?></span><!--  <span class="instructor-comments">0 instructor comments</span> --></p>
				</div><!-- / .comments-toolbar -->
				<div class="comments-frame">

					<?php
					$c = 0;
					foreach ($this->sections as $section)
					{
						if ($section->get('categories'))
						{
							$c++;
						}
					}
					if ($c) {
					?>

					<form action="<?php echo Route::url($base . '&active=discussions'); ?>" method="post" id="commentform"<?php if ($this->data) { echo ' class="hide"'; } ?> enctype="multipart/form-data">
						<p class="comment-member-photo">
							<?php
							$anon = 1;
							if (!User::isGuest())
							{
								$anon = 0;
							}
							$now = Date::getRoot();
							?>
							<img src="<?php echo User::picture($anon); ?>" alt="<?php echo $this->escape(User::get('name')); ?>" />
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
									<span class="time"><time datetime="<?php echo $now; ?>"><?php echo Date::of($now)->toLocal(Lang::txt('TIME_FORMAt_HZ1')); ?></time></span>
									<span class="comment-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $now; ?>"><?php echo Date::of($now)->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?></time></span>
								</span>
							</p>

							<label for="field_comment">
								<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
								<?php
								echo $this->editor('fields[comment]', '', 35, 5, 'field_comment', array('class' => 'minimal no-footer'));
								?>
							</label>

							<div class="grid">
								<div class="col span-half">
									<label for="field-upload" id="comment-upload">
										<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:</span>
										<input type="file" name="upload" id="field-upload" />
									</label>
								</div>
								<div class="col span-half omega">
									<label for="field-category_id">
									<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY'); ?></span>
									<select name="fields[category_id]" id="field-category_id">
										<option value="0"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY_SELECT'); ?></option>
										<?php
										foreach ($this->sections as $section)
										{
											if ($section->get('categories'))
											{
												?>
												<optgroup label="<?php echo $this->escape(stripslashes($section->get('title'))); ?>">
												<?php
												foreach ($section->get('categories') as $category)
												{
													if ($category->get('closed'))
													{
														continue;
													}
													?>
													<option value="<?php echo $category->get('id'); ?>"><?php echo $this->escape(stripslashes($category->get('title'))); ?></option>
													<?php
												}
												?>
												</optgroup>
												<?php
											}
										}
										?>
									</select>
								</label>
								</div>
							</div>

							<label for="field-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
								<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
							</p>
						<?php } ?>
						</fieldset>
						<input type="hidden" name="fields[parent]" id="field-parent" value="0" />
						<input type="hidden" name="fields[state]" id="field-state" value="1" />
						<input type="hidden" name="fields[scope]" id="field-scope" value="course" />
						<input type="hidden" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->filters['scope_id']; ?>" />
						<input type="hidden" name="fields[scope_sub_id]" id="field-scope_id" value="<?php echo $this->filters['scope_sub_id']; ?>" />
						<input type="hidden" name="fields[id]" id="field-id" value="" />
						<input type="hidden" name="fields[object_id]" id="field-object_id" value="" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="action" value="savethread" />

						<?php echo Html::input('token'); ?>

						<p class="instructions">
							<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_INSTRUCTIONS'); ?>
						</p>
					</form>
					<?php } else { ?>
						<p class="instructions">
							<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EMPTY_FORUM'); ?>
						</p>
					<?php } ?>
					<div class="comment-thread"><?php if ($this->data) { echo $this->data->html; } ?></div>
				</div><!-- / .comments-frame -->
			</div><!-- / .comments-panel -->

		</div><!-- / .comments-views -->
	</div><!-- / .comments-wrap -->
</div><!-- / #comments-container -->