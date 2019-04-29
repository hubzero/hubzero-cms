<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// push scripts and styles
$this->css()
     ->js('groups.mediabrowser.js', 'com_groups')
     ->js('jquery.fileuploader', 'system')
     ->js('jquery.contextMenu', 'system')
     ->css('jquery.contextMenu.css', 'system');

//get request vars
$type          = Request::getWord('type', '', 'get');
$ckeditor      = Request::getString('CKEditor', '', 'get');
$ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type=' . $type . '&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
?>
<div class="files-wrap">
	<div class="upload-browser cf">
		<?php
			foreach ($this->notifications as $notification)
			{
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>

		<div class="upload-browser-col left">
			<div class="toolbar cf">
				<div class="title"><?php echo Lang::txt('COM_GROUPS_MEDIA_GROUP_FILES'); ?></div>
				<?php if ($this->authorized && $this->group->published == 1) { ?>
					<div class="buttons">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=addfolder&tmpl=component&protected=true'); ?>" class="icon-add action-addfolder"><?php echo Lang::txt('Add folder'); ?></a>
					</div>
				<?php } ?>
			</div>
			<div class="foldertree" data-activefolder="<?php echo $this->activeFolder; ?>">
				<?php echo $this->folderTree; ?>
			</div>
			<div class="foldertree-list">
				<?php echo $this->folderList; ?>
			</div>
			<?php if ($this->authorized && $this->group->published == 1) { ?>
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" enctype="multipart/form-data" class="upload-browser-uploader">
					<fieldset>
						<div id="ajax-uploader" data-instructions="<?php echo Lang::txt('Click or drop file'); ?>" data-action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=ajaxupload&no_html=1&' . Session::getFormToken() . '=1'); ?>">
							<noscript>
								<p><input type="file" name="upload" id="upload" /></p>
								<p><input type="submit" value="<?php echo Lang::txt('UPLOAD'); ?>" /></p>
							</noscript>
						</div>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="media" />
						<input type="hidden" name="task" value="upload" />
						<input type="hidden" name="listdir" id="listdir" value="<?php echo $this->group->get('gidNumber'); ?>" />
						<input type="hidden" name="tmpl" value="component" />
						<?php echo Html::input('token'); ?>
					</fieldset>
				</form>
			<?php } ?>
		</div>
		<div class="upload-browser-col right">
			<iframe class="upload-browser-filelist-iframe" src="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=listfiles&tmpl=component&type=' . $ckeditorQuery); ?>"></iframe>
		</div>
	</div>
</div>
