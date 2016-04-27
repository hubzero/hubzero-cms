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

$no_html = Request::getInt('no_html', 0);

$base = $this->offering->link() . '&active=' . $this->name;
if ($this->post->id) {
	$action = $base . '&unit=' . $this->category->alias . '&b=' . $this->post->id;
} else {
	$action = $base . '&unit=' . $this->category->alias;
}

if (!($this->post instanceof \Components\Forum\Models\Post))
{
	$this->post = new \Components\Forum\Models\Post($this->post);
}
?>
	<form action="<?php echo Route::url($base); ?>" method="post" id="commentform" class="comment-edit" enctype="multipart/form-data" data-thread="<?php echo $this->post->get('thread'); ?>">
	<?php if (!$no_html) { ?>
		<p class="comment-member-photo">
			<a class="comment-anchor" name="commentform"></a>
			<?php
			$anone = 1;
			if (!User::isGuest())
			{
				$anon = 0;
			}
			$now = Date::of('now');
			?>
			<img src="<?php echo User::picture($anon); ?>" alt="<?php echo Lang::txt('User photo'); ?>" />
		</p>

		<fieldset>
	<?php } ?>
	<?php if (User::isGuest()) { ?>
			<p class="warning"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?></p>
	<?php } else { ?>
			<?php if (!$no_html) { ?>
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
			<?php } ?>

			<label for="field_<?php echo $this->post->get('id'); ?>_comment">
				<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
				<?php
				echo $this->editor('fields[comment]', $this->escape($this->post->get('content')), 35, 5, 'field_' . $this->post->get('id') . '_comment', array('class' => 'minimal no-footer'));
				?>
			</label>
		<?php if (!$this->post->get('parent')) { ?>
			<div class="grid">
				<div class="col span-half">
		<?php } ?>
			<label for="field-upload" id="comment-upload">
				<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:</span>
				<input type="file" name="upload" id="field-upload" />
			</label>
		<?php if (!$this->post->get('parent')) { ?>
				</div>
				<div class="col span-half omega">
					<label for="field-category_id">
						<span class="label-text"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY'); ?></span>
						<select name="fields[category_id]" id="field-category_id">
							<option value="0"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY_SELECT'); ?></option>
						<?php
						foreach ($this->sections as $section)
						{
							if ($section->categories)
							{
						?>
							<optgroup label="<?php echo $this->escape(stripslashes($section->title)); ?>">
							<?php foreach ($section->categories as $category) { ?>
								<option value="<?php echo $category->id; ?>"<?php if ($category->id == $this->post->get('category_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->title)); ?></option>
							<?php } ?>
							</optgroup>
						<?php
							}
						}
						?>
					</select>
				</label>
				</div>
			</div>
		<?php } else { ?>
			<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
		<?php } ?>

			<label for="field-anonymous" id="comment-anonymous-label">
				<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous"<?php if ($this->post->get('anonymous') == 1) { echo ' checked="checked"'; } ?> value="1" />
				<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
			</label>

			<p class="submit">
				<input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
			</p>
	<?php } ?>
	<?php if (!$no_html) { ?>
		</fieldset>
	<?php } ?>
		<input type="hidden" name="fields[parent]" id="field-parent" value="<?php echo $this->post->get('parent'); ?>" />
		<input type="hidden" name="fields[state]" id="field-state" value="<?php echo $this->post->get('state'); ?>" />
		<input type="hidden" name="fields[scope]" id="field-scope" value="<?php echo $this->post->get('scope', 'course'); ?>" />
		<input type="hidden" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->post->get('scope_id'); ?>" />
		<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->post->get('id'); ?>" />
		<input type="hidden" name="fields[object_id]" id="field-object_id" value="<?php echo $this->post->get('object_id'); ?>" />
		<input type="hidden" name="fields[thread]" id="field-thread" value="<?php echo $this->post->get('thread'); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
		<input type="hidden" name="active" value="discussions" />
		<input type="hidden" name="action" value="savethread" />

		<input type="hidden" name="section" value="<?php //echo $this->filters['section']; ?>" />
		<input type="hidden" name="return" value="<?php //echo base64_encode(Route::url($base . '&active=outline&unit=' . $this->filters['section'] . '&b=' . $this->category->alias)); ?>" />

		<?php echo Html::input('token'); ?>
	<?php if (!$no_html) { ?>
		<p class="instructions">
			Click on a comment on the left to view a discussion or start your own above.
		</p>
	<?php } ?>
	</form>