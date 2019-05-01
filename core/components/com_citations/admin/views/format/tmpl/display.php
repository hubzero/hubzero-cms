<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//add title and save button to toolbar
Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_FORMAT'), 'citation');
Toolbar::save();
Toolbar::spacer();
Toolbar::help('format');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

// include citations format class
// new citations format object
$cf = new \Components\Citations\Helpers\Format();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
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
				<table class="admintable" id="preformatted">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('CITATION_FORMAT_PLACEHOLDER'); ?></th>
							<th scope="col"><?php echo Lang::txt('CITATION_FORMAT_VALUE'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							// get the keys
							$keys = $cf->getTemplateKeys();

							foreach ($keys as $k => $v)
							{
								echo '<tr id="' . $v . '"><td>' . $v . '</td><td>' . $k . '</td></tr>';
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
