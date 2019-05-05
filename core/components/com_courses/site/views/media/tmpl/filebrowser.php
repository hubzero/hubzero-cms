<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework', true);

$this->css('media.css')
     ->js('jquery.fileuploader.js', 'system')
     ->js('media.js');
?>
<div id="file_browser">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div id="themanager" class="manager">
				<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . 'tmpl=component&task=listfiles&listdir=' . $this->listdir); ?>" name="imgManager" id="imgManager" width="99%" height="180"></iframe>
			</div>
		</fieldset>
		<fieldset>
			<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ajaxupload&listdir=' . $this->listdir . '&no_html=1'); ?>">
				<noscript>
					<p><input type="file" name="upload" id="upload" /></p>
					<p><input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" /></p>
				</noscript>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="no_html" value="1" />
			<?php echo Html::input('token'); ?>
		</fieldset>
	</form>
</div>
