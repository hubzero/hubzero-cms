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

if ($this->entry->get('publish_down') && $this->entry->get('publish_down') == '0000-00-00 00:00:00')
{
	$this->entry->set('publish_down', '');
}

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';

$this->css()
     ->css('jquery.datepicker.css', 'system')
     ->css('jquery.timepicker.css', 'system')
     ->js('jquery.timepicker', 'system')
     ->js();
?>
<ul id="page_options">
	<li>
		<a class="icon-archive archive btn" href="<?php echo Route::url($base); ?>">
			<?php echo Lang::txt('PLG_GROUPS_BLOG_ARCHIVE'); ?>
		</a>
	</li>
</ul>

<?php foreach ($this->getErrors() as $error): ?>
<div class="error"><?php echo $error; ?></div>
<?php endforeach; ?>

<form action="<?php echo Route::url($base); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_BLOG_EDIT_DETAILS'); ?></legend>

		<label<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
			<?php echo Lang::txt('PLG_GROUPS_BLOG_TITLE'); ?> <span class="required"><?php echo Lang::txt('PLG_GROUPS_BLOG_REQUIRED'); ?></span>
			<input type="text" name="entry[title]" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
		</label>
		<?php if ($this->task == 'save' && !$this->entry->get('title')) { ?>
			<p class="error"><?php echo Lang::txt('PLG_GROUPS_BLOG_ERROR_PROVIDE_TITLE'); ?></p>
		<?php } ?>

		<label for="entry_content">
			<?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('PLG_GROUPS_BLOG_REQUIRED'); ?></span>
			<?php echo $this->editor('entry[content]', $this->escape($this->entry->content('raw')), 50, 30, 'entry_content'); ?>
		</label>
		<?php if ($this->task == 'save' && !$this->entry->get('content')) { ?>
			<p class="error"><?php echo Lang::txt('PLG_GROUPS_BLOG_ERROR_PROVIDE_CONTENT'); ?></p>
		<?php } ?>

		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_BLOG_UPLOADED_FILES'); ?></legend>
			<div class="field-wrap">
				<iframe width="100%" height="260" name="filer" id="filer" src="<?php echo 'index.php?option=com_blog&controller=media&id=' . $this->group->get('gidNumber') . '&scope=group&tmpl=component'; ?>"></iframe>
			</div>
		</fieldset>

		<label>
			<?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_TAGS'); ?>
			<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->entry->tags('string')), 'actags'); ?>
			<span class="hint"><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_TAGS_HINT'); ?></span>
		</label>

		<div class="grid">
			<div class="col span6">
				<label for="field-allow_comments">
					<input type="checkbox" class="option" name="entry[allow_comments]" id="field-allow_comments" value="1"<?php if ($this->entry->get('allow_comments') == 1) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_ALLOW_COMMENTS'); ?>
				</label>
			</div>
			<div class="col span6 omega">
				<label for="field-state">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_PRIVACY'); ?>
					<select name="entry[state]" id="field-state">
						<option value="1"<?php if ($this->entry->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_PUBLIC'); ?></option>
						<option value="2"<?php if ($this->entry->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_REGISTERED'); ?></option>
						<option value="0"<?php if ($this->entry->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_PRIVATE'); ?></option>
					</select>
				</label>
			</div>
		</div>

		<div class="grid">
			<div class="col span6">
				<label for="field-publish_up">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_PUBLISH_UP'); ?>
					<input type="text" name="entry[publish_up]" id="field-publish_up" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::of('now')) / 60); ?>" value="<?php echo $this->escape(Date::of($this->entry->get('publish_up'))->toLocal('Y-m-d H:i:s')); ?>" />
					<span class="hint"><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_PUBLISH_HINT'); ?></span>
				</label>
			</div>
			<div class="col span6 omega">
				<label for="field-publish_down">
					<?php echo Lang::txt('PLG_GROUPS_BLOG_PUBLISH_DOWN'); ?>
					<?php
						$down = '';
						if ($this->entry->get('publish_down') != '')
						{
							$down = $this->escape(Date::of($this->entry->get('publish_down'))->toLocal('Y-m-d H:i:s'));
						}
					?>
					<input type="text" name="entry[publish_down]" id="field-publish_down" data-timezone="<?php echo (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::of('now')) / 60); ?>" value="<?php echo $down; ?>" />
					<span class="hint"><?php echo Lang::txt('PLG_GROUPS_BLOG_FIELD_PUBLISH_HINT'); ?></span>
				</label>
			</div>
		</div>
	</fieldset>
	<div class="clear"></div>

	<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="entry[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
	<input type="hidden" name="entry[created]" value="<?php echo $this->escape($this->entry->get('created')); ?>" />
	<input type="hidden" name="entry[created_by]" value="<?php echo $this->escape($this->entry->get('created_by')); ?>" />
	<input type="hidden" name="entry[scope]" value="group" />
	<input type="hidden" name="entry[scope_id]" value="<?php echo $this->escape($this->group->get('gidNumber')); ?>" />
	<input type="hidden" name="entry[access]" value="0" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="blog" />
	<input type="hidden" name="action" value="save" />

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_SAVE'); ?>" />

		<?php if ($this->entry->get('id')) { ?>
			<a class="btn btn-secondary" href="<?php echo Route::url($this->entry->link()); ?>">
				<?php echo Lang::txt('PLG_GROUPS_BLOG_CANCEL'); ?>
			</a>
		<?php } ?>
	</p>
</form>
