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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();

if ($this->category->get('section_id') == 0)
{
	$this->category->set('section_id', Request::getVar('section_id'));
}

?>
<ul id="page_options">
	<li>
		<a class="icon-folder categories btn" href="<?php echo Route::url($base); ?>">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES'); ?>
		</a>
	</li>
</ul>

<section class="main section">
	<form action="<?php echo Route::url($base); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend class="post-comment-title">
				<?php if ($this->category->get('id')) { ?>
					<?php echo Lang::txt('PLG_GROUPS_FORUM_EDIT_CATEGORY'); ?>
				<?php } else { ?>
					<?php echo Lang::txt('PLG_GROUPS_FORUM_NEW_CATEGORY'); ?>
				<?php } ?>
			</legend>

			<label for="field-section_id">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION'); ?>
				<span class="required"><?php echo Lang::txt('PLG_GROUPS_FORUM_REQUIRED'); ?>
				<select name="fields[section_id]" id="field-section_id">
					<option value="0"><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION_SELECT'); ?></option>
					<?php foreach ($this->forum->sections(array('state' => 1))->rows() as $section) { ?>
						<option value="<?php echo $section->get('id'); ?>"<?php if ($this->category->get('section_id') == $section->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php } ?>
				</select>
			</label>

			<label for="field-title">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE'); ?>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->category->get('title'))); ?>" />
			</label>

			<label for="field-description">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>
				<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->category->get('description'))); ?></textarea>
			</label>

			<div class="grid">
				<div class="col span6">
					<label for="field-closed" id="comment-anonymous-label">
						<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_LOCKED'); ?><br />
						<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->category->get('closed')) { echo ' checked="checked"'; } ?> />
						<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_CLOSED'); ?>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="field-access">
						<?php echo Lang::txt('PLG_GROUPS_FORUM_ACCESS_DESCRIPTION'); ?>:
						<select name="fields[access]" id="field-access">
							<option value="1"<?php if ($this->category->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'); ?></option>
							<option value="2"<?php if ($this->category->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'); ?></option>
							<option value="5"<?php if ($this->category->get('access') == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE'); ?></option>
						</select>
					</label>
				</div>
			</div>
		</fieldset>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
				<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>
			</a>
		</p>

		<input type="hidden" name="fields[alias]" value="<?php echo $this->escape($this->category->get('alias')); ?>" />
		<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->category->get('id')); ?>" />
		<input type="hidden" name="fields[state]" value="1" />
		<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->forum->get('scope')); ?>" />
		<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->forum->get('scope_id')); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
		<input type="hidden" name="active" value="forum" />
		<input type="hidden" name="action" value="savecategory" />

		<?php echo Html::input('token'); ?>
	</form>
	<div class="clear"></div>
</section><!-- / .main section -->