<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($this->data['type'] != 'folder'):
	$ext = Filesystem::extension($this->data['name']);

	$icon = Html::asset('image', 'assets/filetypes/' . $ext . '.svg', '', null, true, true);
	if (!$icon):
		$icon = Html::asset('image', 'assets/filetypes/file.svg', '', null, true, true);
	endif;
else:
	$icon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);
endif;
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&file=' . urlencode($this->data['path'])); ?>" id="component-form" method="post" name="adminForm" autocomplete="off">
	<fieldset>
		<h2 class="modal-title">
			<?php echo Lang::txt('COM_MEDIA_FILE_INFO'); ?>
		</h2>
	</fieldset>
	<div class="grid">
		<div class="col span5">
			<div class="media-preview">
				<div class="media-preview-inner">
					<?php if ($this->data['type'] == 'img'): ?>
						<div class="media-thumb img-preview <?php echo Filesystem::extension($this->data['name']); ?>" title="<?php echo $this->escape($this->data['name']); ?>" >
							<span class="media-preview-shim"></span><!--
							--><img src="<?php echo COM_MEDIA_BASEURL . $this->data['path']; ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->data['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->data['size']))); ?>" width="<?php echo ($this->data['width'] < 260) ? $this->data['width'] : '260'; ?>" />
						</div>
					<?php else: ?>
						<div class="media-thumb doc-item <?php echo Filesystem::extension($this->data['name']); ?>" title="<?php echo $this->escape($this->data['name']); ?>" >
							<span class="media-preview-shim"></span><!--
							--><img src="<?php echo $icon; ?>" alt="<?php echo $this->escape(Lang::txt('COM_MEDIA_IMAGE_TITLE', $this->data['name'], Components\Media\Admin\Helpers\MediaHelper::parseSize($this->data['size']))); ?>" width="80" />
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="col span7">
			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_NAME'); ?>:</span>
				<span class="media-info-value"><?php echo $this->escape($this->data['name']); ?></span>
			</div>

			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_PATH'); ?>:</span>
				<span class="media-info-value"><?php echo $this->escape($this->data['path']); ?></span>
			</div>

			<?php if ($this->data['type'] != 'folder'): ?>
				<?php if ($this->data['type'] == 'img'): ?>
					<div class="grid">
						<div class="col span4">
				<?php endif; ?>
				<div class="input-wrap">
					<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_SIZE'); ?>:</span>
					<span class="media-info-value"><?php echo Hubzero\Utility\Number::formatBytes($this->data['size']); ?></span>
				</div>
				<?php if ($this->data['type'] == 'img'): ?>
						</div>
						<div class="col span4">
							<div class="input-wrap">
								<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_WIDTH'); ?>:</span>
								<span class="media-info-value"><?php echo $this->data['width']; ?>px</span>
							</div>
						</div>
						<div class="col span4">
							<div class="input-wrap">
								<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_HEIGHT'); ?>:</span>
								<span class="media-info-value"><?php echo $this->data['height']; ?>px</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<div class="input-wrap">
				<span class="media-info-label"><?php echo Lang::txt('COM_MEDIA_LIST_HEADER_MODIFIED'); ?>:</span>
				<span class="media-info-value"><?php echo Date::of($this->data['modified'])->toSql(); ?></span>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<?php echo Html::input('token'); ?>
</form>
