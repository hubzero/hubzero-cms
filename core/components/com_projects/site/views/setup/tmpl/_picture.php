<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->model->exists())
{
	return;
}
?>
<div class="grid pictureframe js">
	<div class="col span3">
		<div id="project-image-box" class="project-image-box">
			<img id="project-image-content" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&alias=' . $this->model->get('alias') . '&media=master'); ?>" alt="" />
		</div>
		<?php if ($this->model->get('picture')) { ?>
		<p class="actionlink"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=deleteimg&alias=' . $this->model->get('alias') ); ?>" id="deleteimg">[ <?php echo Lang::txt('JACTION_DELETE'); ?> ]</a></p>
		<?php } ?>
	</div>
	<div class="col span9 omega" id="ajax-upload" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=doajaxupload&no_html=1'); ?>">
		<div class="form-group">
			<label for="uploader">
				<?php echo Lang::txt('COM_PROJECTS_UPLOAD_NEW_IMAGE'); ?> <span class="hint"><?php echo Lang::txt('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE'); ?></span>
				<span id="status-box"></span>
				<input name="upload" type="file" class="option uploader form-control-file" id="uploader" />
			</label>
		</div>
		<input type="button" value="<?php echo Lang::txt('COM_PROJECTS_UPLOAD'); ?>" class="btn" id="upload-file" />
	</div>
</div>