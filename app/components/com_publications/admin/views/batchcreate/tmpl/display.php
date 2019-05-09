<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'), 'publications');

$this->css('batchcreate');
$this->js('batchcreate');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=process'); ?>" method="post" name="adminForm" id="item-form" class="batchupload" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_IMPORT'); ?></span></legend>

		<div class="grid">
			<div class="col span7">
				<div class="input-wrap">
					<label for="projectid">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ADD_IN_PROJECT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					</label>
					<?php 
					// Draw project list
					$this->view('_selectprojects')
					     ->set('projects', $this->projects)
					     ->display(); ?>
				</div>
				<div class="input-wrap file-import" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ATTACH_HINT'); ?>">
					<label for="field-file">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DATA'); ?><span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					</label>
					<input type="file" name="file" id="field-file" />
				</div>
				<div class="input-wrap">
					<input type="submit" name="batch_submit" id="batch_submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_UPLOAD_AND_PREPROCESS'); ?>" />
				</div>
			</div>
			<div class="col span5">
				<p><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_XSD_INSTRUCT'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=xsd'); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_XSD'); ?></a></p>
			</div>
		</div>

		<div class="output-wrap" id="results">
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="process" />
		<input type="hidden" name="base" value="files" />
		<?php echo Html::input('token'); ?>
	</fieldset>
</form>