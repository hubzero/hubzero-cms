<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

//add title and save button to toolbar
Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_FORMAT'), 'citation.png');
Toolbar::save();
Toolbar::spacer();
Toolbar::help('format');

// include citations format class
// new citations format object
$cf = new \Components\Citations\Helpers\Format();
?>

<script type="text/javascript">
var $jQ = jQuery.noConflict();

function submitbutton(pressbutton)
{
	var form = $jQ('adminForm');
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}

$jQ(document).ready(function(e) {
	var formatSelector = $jQ('#format-selector'),
		formatBox = $jQ('#format-string');

	//when we change format box
	formatSelector.on('change', function(event) {
		var value  = $jQ(this).val(),
			format = $jQ(this).find(':selected').attr('data-format');
		formatBox.val(format);
	});

	//when we customize the format
	formatBox.on('keyup', function(event) {
		var customOption = formatSelector.find('option[value=custom]');
		customOption.attr('data-format', formatBox.val());
	});

	$jQ(function($) {
		$('tr').click(function() {
			$('#format-string').val($('#format-string').val() + $(this).attr('id'));
			$('#format-string').focus();
		});
	}); 
});
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('CITATION_FORMAT'); ?></span></legend>

			<div class="input-wrap">
				<label for="format-selector"><?php echo Lang::txt('CITATION_FORMAT_STYLE'); ?>:</label><br />
				<select name="citationFormat[id]" id="format-selector">
					<?php foreach ($this->formats as $format): ?>
					<?php ($this->currentFormat->id == $format->id ? $selected = 'selected' : $selected = ''); ?> 
					<option value="<?php echo $format->id; ?>" <?php echo $selected; ?> data-format="<?php echo str_replace('"', '\"', $format->format); ?>" > 
						<?php echo $format->style; ?>
					</option>
					<?php endforeach; ?>
					<option value="custom"><?php echo Lang::txt('CITATION_CUSTOM_FORMAT'); ?></option>
				</select>
			</div>

			<div class="input-wrap">
				<label for="format-string"><?php echo Lang::txt('CITATION_FORMAT_STRING'); ?>:</label><br />
				<textarea name="citationFormat[format]" rows="10" id="format-string"><?php echo trim( preg_replace('/\r|\n/', '', $this->currentFormat->format)); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<div class="data-wrap">
			<table class="admintable">
				<thead>
					<tr>
						<th><?php echo Lang::txt('CITATION_FORMAT_PLACEHOLDER'); ?></th>
						<th><?php echo Lang::txt('CITATION_FORMAT_VALUE'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						// get the keys
						$keys = $cf->getTemplateKeys();

						foreach ($keys as $k => $v)
						{
							echo "<tr id='{$v}'><td>{$v}</td><td>{$k}</td></tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>	
	<div class="clr"></div>

	<input type="hidden" name="citationFormat[current]" value="<?php echo $this->currentFormat->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>
