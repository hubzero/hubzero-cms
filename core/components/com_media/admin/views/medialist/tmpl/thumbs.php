<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$cls = '';
if (!empty($this->active)):
	$cls = ' active';
endif;
?>
<div class="media-files media-thumbs<?php echo $cls; ?>" id="media-thumbs">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&folder=' . $this->folder); ?>" method="post" id="media-form-thumbs" name="media-form-thumbs">
		<div class="manager">
			<?php
			$folders = array();
			$files = array();

			// Group files and folders
			foreach ($this->children as $child):
				if ($child['type'] == 'dir'):
					$folders[] = $child;
				elseif ($child['type'] == 'file' || $child['type'] == 'img'):
					$files[] = $child;
				endif;
			endforeach;

			// Display folders first
			foreach ($folders as $child):
				$this->currentFolder = $child;
				echo $this->loadTemplate('folder');
			endforeach;

			// Display files
			foreach ($files as $child):
				if ($child['type'] == 'file'):
					$this->currentDoc = $child;
					echo $this->loadTemplate('doc');
				elseif ($child['type'] == 'img'):
					$this->currentImg = $child;
					echo $this->loadTemplate('img');
				endif;
			endforeach;
			/*foreach ($this->children as $child):
				if ($child['type'] == 'dir'):
					$this->currentFolder = $child;
					echo $this->loadTemplate('folder');
				elseif ($child['type'] == 'file'):
					$this->currentDoc = $child;
					echo $this->loadTemplate('doc');
				elseif ($child['type'] == 'img'):
					$this->currentImg = $child;
					echo $this->loadTemplate('img');
				endif;
			endforeach;*/
			?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="username" value="" />
			<input type="hidden" name="password" value="" />
			<?php echo Html::input('token'); ?>
			<input type="hidden" name="folder" value="<?php echo $this->escape($this->folder); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		</div>
	</form>
<?php /*<div class="uploadForms">
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
</div>*/ ?>
</div>