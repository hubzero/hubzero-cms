<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die( 'Restricted access' );

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
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<form action="<?php echo Route::url($base); ?>" method="post" id="hubForm" class="full">
		<fieldset>
			<legend class="post-comment-title">
				<?php if ($this->category->exists()) { ?>
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
				<?php foreach ($this->model->sections() as $section) { ?>
					<?php if ($section->get('state') == 1): ?>
						<option value="<?php echo $section->get('id'); ?>"<?php if ($this->category->get('section_id') == $section->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php endif; ?>
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
							<option value="0"<?php if ($this->category->get('access', 0) == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'); ?></option>
							<option value="1"<?php if ($this->category->get('access', 0) == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'); ?></option>
							<?php /*<option value="3"<?php if ($this->category->get('access', 0) == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PROTECTED'); ?></option>*/ ?>
							<option value="4"<?php if ($this->category->get('access', 0) == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE'); ?></option>
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
		<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
		<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
		<input type="hidden" name="active" value="forum" />
		<input type="hidden" name="action" value="savecategory" />

		<?php echo JHTML::_('form.token'); ?>
	</form>
	<div class="clear"></div>
</section><!-- / .main section -->