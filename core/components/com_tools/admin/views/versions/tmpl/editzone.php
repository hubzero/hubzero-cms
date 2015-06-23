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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('framework');

\Hubzero\Document\Assets::addSystemScript('jquery.datetimepicker');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.datetimepicker.css');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$mwdb  = \Components\Tools\Helpers\Utils::getMWDBO();
$zones = with(new \Components\Tools\Tables\Zones($mwdb))->find('all');
?>

<script>
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	submitform(pressbutton);
}
function saveAndUpdate()
{
	submitbutton('saveZone');
	window.parent.setTimeout(function(){
		var src = window.parent.document.getElementById('zoneslist').src;

		window.parent.document.getElementById('zoneslist').src = src + '&';
		window.parent.$.fancybox.close();
	}, 700);
}
jQuery(document).ready(function($){
	$('.datetime').datetimepicker({
		step: 15,
		time24h: true,
		format: 'Y-m-d H:i:s',
		defaultTime: '08:00'
	});
});
</script>

<?php if ($this->getError()) : ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php endif; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="saveAndUpdate();"><?php echo Lang::txt('COM_TOOLS_SAVE'); ?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo Lang::txt('COM_TOOLS_CANCEL'); ?></button>
			</div>
			<?php echo $text; ?>
		</div>
	</fieldset>
	<div class="col width-100">
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
	<div class="clr"></div>

	<?php echo Html::input('token'); ?>
</form>