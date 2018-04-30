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

// No direct access.
defined('_HZEXEC_') or die();

?>
<script type="text/javascript">
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	submitform(pressbutton);
}
</script>
<form target="_parent" action="<?php echo Route::url('index.php?option=com_media&folder=' . $this->folder); ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="manager">
		<?php
			if ($this->folder != '') {
				echo $this->loadTemplate('up');
			}
			foreach ($this->children as $child) {
				if ($child['type'] == 'dir') {
					$this->currentFolder = $child;
					echo $this->loadTemplate('folder');
				}
				else if ($child['type'] == 'file') {
					$this->currentDoc = $child;
					echo $this->loadTemplate('doc');
				}
				else if ($child['type'] == 'img') {
					$this->currentImg = $child;
					echo $this->loadTemplate('img');
				}
			}
		?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<input type="hidden" name="token" value="<?php echo Session::getFormToken(); ?>" />
		<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
	</div>
</form>
<div class="uploadForms">
	<form action="<?php echo Route::url('index.php?option=com_media&controller=media&task=new');?>" name="folderForm" id="folderForm" method="post">
		<legend><?php echo Lang::txt('COM_MEDIA_CREATE_FOLDER'); ?></legend>
			<div class="path">
			<?php if (User::authorise('core.create', 'com_media')) { ?>
			<input class="inputbox" type="text" id="foldername" name="foldername" placeholder="<?php echo Lang::txt('COM_MEDIA_FOLDER_NAME'); ?>" />
			<input class="update-folder" type="hidden" name="parent" id="parent" value="<?php echo $this->folder; ?>" />
			<button type="submit"><?php echo Lang::txt('COM_MEDIA_CREATE'); ?></button>
			<?php } ?>
		</div>
		<?php echo Html::input('token'); ?>
	</form>
	<form action="<?php echo Route::url('index.php?option=com_media&controller=media&task=upload&' . Session::getFormToken() . '=1', true, true); ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
		<legend>
			<?php //echo $this->config->get('upload_maxsize')=='0' ? Lang::txt('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : Lang::txt('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?>
			<?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?>
		</legend>
		<fieldset id="uploadform">
			<fieldset id="upload-noflash" class="actions">
				<label for="upload-file" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata[]" multiple="multiple" />
				<label for="upload-submit" class="hidelabeltxt"><?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?></label>
				<input type="submit" id="upload-submit" value="<?php echo Lang::txt('COM_MEDIA_START_UPLOAD'); ?>"/>
			</fieldset>
			<input class="hidden" type="hidden" name="folder" id="folder" value="<?php echo $this->folder; ?>" />

			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_media'); ?>" />
		</fieldset>
	</form>
</div>
