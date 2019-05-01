<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->import->get('id')) { ?>
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_MAPPING'); ?></span></legend>

		<table class="field-map">
			<thead>
				<tr>
					<th><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_COLUMN'); ?></th>
					<th><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_MEMBER'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->import->fields() as $mapping) { ?>
				<tr<?php if (!$mapping['field']) { echo ' class="field-unknown"'; } ?>>
					<td>
						<label for="mapping-<?php echo $mapping['name']; ?>">
							<?php echo $mapping['label']; ?>
						</label>
					</td>
					<td>
						<input type="hidden" name="mapping[<?php echo $mapping['name']; ?>][name]" value="<?php echo $this->escape($mapping['name']); ?>" />
						<input type="hidden" name="mapping[<?php echo $mapping['name']; ?>][label]" value="<?php echo $this->escape($mapping['label']); ?>" />
						<select name="mapping[<?php echo $mapping['name']; ?>][field]" id="mapping-<?php echo $mapping['name']; ?>">
							<option value=""><?php echo Lang::txt('COM_GROUPS_UNKNOWN'); ?></option>
							<?php
							$columns = array(
								'gidNumber',
								'cn',
								'description',
								'published',
								'approved',
								//'public_desc',
								//'private_desc',
								'restrict_msg',
								'join_policy',
								'discoverability',
								'discussion_email_autosubscribe',
								'plugins',
								'created',
								'created_by',
								'members',
								'managers',
								//'projects',
								'tags'
							);
							?>
							<optgroup label="<?php echo Lang::txt('COM_GROUPS_IMPORT_FIELDS_DETAILS'); ?>">
								<?php foreach ($columns as $column): ?>
									<option value="<?php echo $column; ?>" <?php if ($mapping['field'] == $column) { echo 'selected="selected"'; } ?>><?php echo $column; ?></option>
								<?php endforeach; ?>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_GROUPS_IMPORT_FIELDS_DESCRIPTION'); ?>">
								<?php
								include_once Component::path('com_groups') . DS . 'models' . DS . 'orm' . DS . 'field.php';

								$fields = Components\Groups\Models\Orm\Field::all()
									->ordered()
									->rows();

								foreach ($fields as $field)
								{
									?>
									<option value="<?php echo $this->escape($field->get('name')); ?>" <?php if ($mapping['field'] == $field->get('name')) { echo 'selected="selected"'; } ?>><?php echo $this->escape($field->get('name')); ?></option>
									<?php
								}
								?>
							</optgroup>
						</select>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</fieldset>
<?php }
