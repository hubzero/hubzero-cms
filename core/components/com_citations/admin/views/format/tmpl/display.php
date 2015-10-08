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
	<div class="grid">
		<div class="col span7">
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
					<textarea name="citationFormat[format]" rows="10" id="format-string"><?php echo trim(preg_replace('/\r|\n/', '', $this->currentFormat->format)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
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
	</div>

	<input type="hidden" name="citationFormat[current]" value="<?php echo $this->currentFormat->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
