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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->category->set('section_alias', $this->section->get('alias'));
$this->post->set('section', $this->section->get('alias'));
$this->post->set('category', $this->category->get('alias'));

if ($this->post->get('id'))
{
	$action = $base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias') . '/' . $this->post->get('thread');
}
else
{
	$action = $base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias');
	$this->post->set('access', 0);
}

$this->css()
     ->js();
?>
<ul id="page_options">
	<li>
		<a class="icon-comments comments btn" href="<?php echo Route::url($base . '&scope=' . $this->section->get('alias') . '/' . $this->category->get('alias')); ?>">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_ALL_DISCUSSIONS'); ?>
		</a>
	</li>
</ul>

<section class="main section">
	<?php if ($this->config->get('access-plugin') == 'anyone' || $this->config->get('access-plugin') == 'registered') { ?>
	<div class="subject">
	<?php } ?>

		<h3 class="post-comment-title">
			<?php if ($this->post->get('id')) { ?>
				<?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT_DISCUSSION'); ?>
			<?php } else { ?>
				<?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_DISCUSSION'); ?>
			<?php } ?>
		</h3>

		<form action="<?php echo Route::url($action); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<img src="<?php echo $this->post->creator->picture(); ?>" alt="" />
			</p>

			<fieldset>
			<?php if ($this->config->get('access-edit-thread') && !$this->post->get('parent')) { ?>
				<div class="grid">
					<div class="col span6">
						<label for="field-sticky">
							<input class="option" type="checkbox" name="fields[sticky]" id="field-sticky" value="1"<?php if ($this->post->get('sticky')) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_STICKY'); ?>
						</label>
					</div>
					<div class="col span6 omega">
						<label for="field-closed">
							<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->post->get('closed')) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_CLOSED_THREAD'); ?>
						</label>
					</div>
				</div>
			<?php } else { ?>
				<input type="hidden" name="fields[sticky]" id="field-sticky" value="<?php echo $this->escape($this->post->get('sticky')); ?>" />
				<input type="hidden" name="fields[closed]" id="field-closed" value="<?php echo $this->escape($this->post->get('closed')); ?>" />
			<?php } ?>

			<?php if (!$this->post->get('parent')) { ?>
				<?php if ($this->config->get('access-plugin') == 'anyone' || $this->config->get('access-plugin') == 'registered') { ?>
				<label for="field-access">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS'); ?>
					<select name="fields[access]" id="field-access">
						<option value="1"<?php if ($this->post->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'); ?></option>
						<option value="2"<?php if ($this->post->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'); ?></option>
						<option value="5"<?php if ($this->post->get('access') == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE'); ?></option>
					</select>
				</label>
				<?php } else { ?>
					<input type="hidden" name="fields[access]" id="field-access" value="<?php echo $this->post->get('access', 0); ?>" />
				<?php } ?>

				<label for="field-category_id">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_CATEGORY'); ?> <span class="required"><?php echo Lang::txt('PLG_GROUPS_FORUM_REQUIRED'); ?></span>
					<select name="fields[category_id]" id="field-category_id">
						<option value="0"><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
						<?php
						$filters = array(
							'state'  => 1,
							'access' => User::getAuthorisedViewLevels()
						);
						if (in_array(User::get('id'), $this->group->get('members')))
						{
							$filters['access'][] = 5;
						}
						foreach ($this->forum->sections($filters)->rows() as $section)
						{
							$categories = $section->categories()
								->whereEquals('state', $filters['state'])
								->whereIn('access', $filters['access'])
								->rows();
							if ($categories->count() > 0) { ?>
								<optgroup label="<?php echo $this->escape(stripslashes($section->get('title'))); ?>">
									<?php foreach ($categories as $category) { ?>
										<option value="<?php echo $category->get('id'); ?>"<?php if ($this->category->get('alias') == $category->get('alias')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($category->get('title'))); ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						<?php } ?>
					</select>
				</label>

				<label for="field-title">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_GROUPS_FORUM_REQUIRED'); ?></span>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->post->get('title'))); ?>" />
				</label>
			<?php } else { ?>
				<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->escape($this->post->get('category_id')); ?>" />
				<input type="hidden" name="fields[access]" id="field-access" value="<?php echo $this->post->get('access', 0); ?>" />
			<?php } ?>

				<label for="field_comment">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_COMMENTS'); ?> <span class="required"><?php echo Lang::txt('PLG_GROUPS_FORUM_REQUIRED'); ?></span>
					<?php
					echo $this->editor('fields[comment]', $this->escape(stripslashes($this->post->get('comment'))), 35, 15, 'field_comment', array('class' => 'minimal no-footer'));
					?>
				</label>

				<label>
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_TAGS'); ?>:
					<?php
						echo $this->autocompleter('tags', 'tags', $this->escape($this->post->tags('string')), 'actags');
					?>
				</label>

				<fieldset>
					<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_LEGEND_ATTACHMENTS'); ?></legend>

					<?php $attachment = $this->post->attachments()->row(); ?>

					<div class="grid">
						<div class="col span-half">
							<label for="upload">
								<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE'); ?>: <?php if ($attachment->get('filename')) { echo '<strong>' . $this->escape(stripslashes($attachment->get('filename'))) . '</strong>'; } ?>
								<input type="file" name="upload" id="upload" />
							</label>
						</div>
						<div class="col span-half omega">
							<label for="field-attach-descritpion">
								<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>:
								<input type="text" name="description" id="field-attach-descritpion" value="<?php echo $this->escape(stripslashes($attachment->get('description'))); ?>" />
							</label>
						</div>
						<input type="hidden" name="attachment" value="<?php echo $this->escape(stripslashes($attachment->get('id'))); ?>" />
					</div>
					<?php if ($attachment->get('id')) { ?>
						<p class="warning">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_FILE_WARNING'); ?>
						</p>
					<?php } ?>
				</fieldset>

				<?php if ($this->post->get('id')) { ?>
					<label for="field-notify" id="comment-notify-label">
						<input class="option" type="checkbox" name="notify" id="field-notify" value="1" checked="checked" />
						<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_NOTIFY'); ?>
					</label>
				<?php } else { ?>
					<input class="option" type="hidden" name="notify" id="field-notify" value="1" />
				<?php } ?>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->post->get('anonymous')) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SUBMIT'); ?>" />
				</p>

				<div class="sidenote">
					<p>
						<strong><?php echo Lang::txt('PLG_GROUPS_FORUM_KEEP_POLITE'); ?></strong>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[parent]" value="<?php echo $this->escape($this->post->get('parent')); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->post->get('id')); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->forum->get('scope')); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->forum->get('scope_id')); ?>" />
			<input type="hidden" name="fields[thread]" value="<?php echo $this->escape($this->post->get('thread')); ?>" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->escape($this->section->get('alias')); ?>" />

			<?php echo Html::input('token'); ?>
		</form>
	<?php if ($this->config->get('access-plugin') == 'anyone' || $this->config->get('access-plugin') == 'registered') { ?>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT_HINT'); ?></p>
	</aside><!-- /.aside -->
	<?php } ?>
</section><!-- / .below section -->