<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();

if ($this->category->get('section_id') == 0)
{
	$this->category->set('section_id', Request::getInt('section_id'));
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

			<div class="form-group">
				<label for="field-section_id">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION'); ?>
					<span class="required"><?php echo Lang::txt('PLG_GROUPS_FORUM_REQUIRED'); ?></span>
					<select name="fields[section_id]" id="field-section_id" class="form-control">
						<option value="0"><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION_SELECT'); ?></option>
						<?php foreach ($this->forum->sections(array('state' => 1))->rows() as $section) { ?>
							<option value="<?php echo $section->get('id'); ?>"<?php if ($this->category->get('section_id') == $section->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
						<?php } ?>
					</select>
				</label>
			</div>

			<div class="form-group">
				<label for="field-title">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE'); ?>
					<input type="text" name="fields[title]" id="field-title" class="form-control" value="<?php echo $this->escape(stripslashes($this->category->get('title'))); ?>" />
				</label>
			</div>

			<div class="form-group">
				<label for="field-description">
					<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION'); ?>
					<textarea name="fields[description]" id="field-description" class="form-control" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->category->get('description'))); ?></textarea>
				</label>
			</div>

			<div class="grid">
				<div class="col span6">
					<div class="form-group">
						<div class="form-check">
							<label for="field-closed" id="comment-anonymous-label" class="form-check-label">
								<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_LOCKED'); ?><br />
								<input class="option form-check-input" type="checkbox" name="fields[closed]" id="field-closed" value="3"<?php if ($this->category->get('closed')) { echo ' checked="checked"'; } ?> />
								<?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_CLOSED'); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="col span6 omega">
					<div class="form-group">
						<label for="field-access">
							<?php echo Lang::txt('PLG_GROUPS_FORUM_ACCESS_DESCRIPTION'); ?>:
							<select name="fields[access]" id="field-access" class="form-control">
								<option value="1"<?php if ($this->category->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'); ?></option>
								<option value="2"<?php if ($this->category->get('access') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'); ?></option>
								<option value="5"<?php if ($this->category->get('access') == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE'); ?></option>
							</select>
						</label>
					</div>
				</div>
			</div>
		</fieldset>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
				<?php echo Lang::txt('JCANCEL'); ?>
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
