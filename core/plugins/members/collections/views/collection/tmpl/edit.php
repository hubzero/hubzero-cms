<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

if (!$this->entry->exists())
{
	$legend = 'PLG_MEMBERS_COLLECTIONS_LEGEND_NEW_COLLECTION';
}
else
{
	$legend = 'PLG_MEMBERS_COLLECTIONS_LEGEND_EDIT_COLLECTION';
}

$this->css();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url($base . '&task=save'); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo Lang::txt($legend); ?></legend>

		<div class="form-group">
			<label for="field-access">
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY'); ?>
				<select name="fields[access]" id="field-access" class="form-control">
					<option value="0"<?php if ($this->entry->get('access') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_PUBLIC'); ?></option>
					<option value="1"<?php if ($this->entry->get('access') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_REGISTERED'); ?></option>
					<option value="4"<?php if ($this->entry->get('access') == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_PRIVATE'); ?></option>
				</select>
			</label>
		</div>

		<div class="form-group">
			<label for="field-title"<?php if ($this->task == 'save' && !$this->entry->get('title')) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" name="fields[title]" id="field-title" class="form-control" size="35" value="<?php echo $this->escape(stripslashes($this->entry->get('title'))); ?>" />
			</label>
		</div>

		<div class="form-group">
			<label for="field-description">
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_DESCRIPTION'); ?>
				<?php echo $this->editor('fields[description]', $this->escape(stripslashes($this->entry->description('raw'))), 35, 5, 'field-description', array('class' => 'form-control minimal no-footer')); ?>
			</label>
		</div>

		<div class="form-group">
			<label for="actags">
				<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TAGS'); ?>
				<?php
				$tags = ($this->entry->get('id') ? $this->entry->item()->tags('string') : '');
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $tags)));
				$tf = implode('', $tf);
				if ($tf) {
					echo $tf;
				} else { ?>
					<input type="text" name="tags" id="actags" class="form-control" value="<?php echo $this->escape($tags); ?>" />
				<?php } ?>
				<span class="hint"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TAGS_HINT'); ?></span>
			</label>
		</div>

		<div class="grid">
			<div class="col span6">
				<div class="form-group">
					<label for="field-layout" class="form-control<?php if ($this->task == 'save' && !$this->entry->get('layout')) { echo ' fieldWithErrors'; } ?>">
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT'); ?>
						<select name="fields[layout]" id="field-layout" class="form-control">
							<option value="grid"<?php if ($this->entry->get('layout') == 'grid') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT_GRID'); ?></option>
							<option value="list"<?php if ($this->entry->get('layout') == 'list') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT_LIST'); ?></option>
						</select>
					</label>
				</div>
			</div>
			<div class="col span6 omega">
				<div class="form-group">
					<label for="field-sort" class="form-control<?php if ($this->task == 'save' && !$this->entry->get('sort')) { echo ' fieldWithErrors'; } ?>">
						<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT'); ?>
						<select name="fields[sort]" id="field-sort">
							<option value="created"<?php if ($this->entry->get('sort') == 'created') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_CREATED'); ?></option>
							<option value="ordering"<?php if ($this->entry->get('sort') == 'ordering') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_ORDERING'); ?></option>
						</select>
					</label>
				</div>
			</div>
		</div>
		<p class="hint"><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_DETAILS'); ?></p>
	</fieldset>

	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
	<input type="hidden" name="fields[object_id]" value="<?php echo $this->escape($this->member->get('id')); ?>" />
	<input type="hidden" name="fields[object_type]" value="member" />
	<input type="hidden" name="fields[created]" value="<?php echo $this->escape($this->entry->get('created')); ?>" />
	<input type="hidden" name="fields[created_by]" value="<?php echo $this->escape($this->entry->get('created_by')); ?>" />
	<input type="hidden" name="fields[state]" value="<?php echo $this->escape($this->entry->get('state')); ?>" />

	<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="action" value="savecollection" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_SAVE'); ?>" />
		<a class="btn btn-secondary" href="<?php echo Route::url($base . '&task=all'); ?>">
			<?php echo Lang::txt('JCANCEL'); ?>
		</a>
	</p>
</form>
