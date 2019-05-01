<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework');

$this->css();
$this->js('jquery.fileuploader.js', 'system');

if ($this->getError())
{
	echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>';
}

$this->js('media.js');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend class="upload-path">
			<span>
				<?php echo Lang::txt('Path') . ': ' . DS . trim($this->config->get('uploadpath', '/site/courses'), DS) . DS . ($this->course_id ? $this->course_id . DS : '') . 'pagefiles' . ($this->listdir ? DS . $this->listdir : ''); ?>
			</span>
		</legend>
		<div id="ajax-uploader-before">&nbsp;</div>
		<div id="ajax-uploader" data-action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=upload&course=' . $this->course_id . '&listdir=' . $this->listdir . '&no_html=1&' . Session::getFormToken() . '=1'); ?>" data-instructions="<?php echo Lang::txt('COM_COURSES_UPLOAD_CLICK_OR_DROP'); ?>">
			<table>
				<tbody>
					<tr>
						<td>
							<input type="file" name="upload" id="upload" />
						</td>
						<td>
							<input type="submit" value="<?php echo Lang::txt('Upload'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="themanager" class="manager">
			<iframe src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&listdir=' . $this->listdir . '&course=' . $this->course_id); ?>" name="imgManager" id="imgManager" width="98%" height="150"
				data-dir="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=list&tmpl=component&course=' . $this->course_id); ?>"></iframe>
		</div>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
		<input type="hidden" name="course" value="<?php echo $this->course_id; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>
	<?php echo Html::input('token'); ?>
</form>
