<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('framework');

$this->js('jquery.datetimepicker.js', 'system');
$this->js('zones.js');
$this->css('jquery.datetimepicker.css', 'system');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$mwdb  = \Components\Tools\Helpers\Utils::getMWDBO();
$zones = with(new \Components\Tools\Tables\Zones($mwdb))->find('all');
?>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" id="btn-save"><?php echo Lang::txt('COM_TOOLS_SAVE'); ?></button>
				<button type="button" id="btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></button>
			</div>
			<?php echo $text; ?>
		</div>
	</fieldset>
	<div class="width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->id); ?>" />
			<input type="hidden" name="fields[tool_version_id]" value="<?php echo $this->escape($this->row->tool_version_id); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="saveZone" />

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-zone-id"><?php echo Lang::txt('COM_TOOLS_FIELD_ZONE'); ?>:</label></th>
						<td>
							<select name="fields[zone_id]" id="field-zone-id">
								<?php foreach ($zones as $zone) : ?>
									<option value="<?php echo $zone->id; ?>" <?php echo ($zone->id == $this->row->zone_id) ? 'selected="selected"' : '';?>>
										<?php echo $zone->title; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-publish-up"><?php echo Lang::txt('COM_TOOLS_FIELD_PUBLISH_UP'); ?>:</label></th>
						<td><input class="datetime" type="text" name="fields[publish_up]" id="field-publish-up" value="<?php echo $this->escape(stripslashes($this->row->publish_up)); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-publish-down"><?php echo Lang::txt('COM_TOOLS_FIELD_PUBLISH_DOWN'); ?>:</label></th>
						<td><input class="datetime" type="text" name="fields[publish_down]" id="field-publish-down" value="<?php echo $this->escape(stripslashes($this->row->publish_down)); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>