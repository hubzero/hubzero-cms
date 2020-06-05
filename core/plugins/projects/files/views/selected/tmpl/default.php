<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$multi = isset($this->multi) && $this->multi ? '[]' : '';
?>
<li>
	<img class="file-type file-type-<?php echo $this->file->get('ext'); ?>" src="<?php echo $this->file->getIcon(); ?>" alt="<?php echo $this->escape($this->file->get('name')); ?>" />

	<?php echo $this->escape($this->file->get('name')); ?>

	<?php if ($this->file->get('converted')): ?>
		<span class="remote-file"><?php echo $this->file->get('type') == 'folder' ? Lang::txt('PLG_PROJECTS_FILES_REMOTE_FOLDER') : Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE'); ?></span>
	<?php endif; ?>

	<?php if ($this->file->get('converted') && $this->file->get('originalPath')): ?>
		<span class="remote-file faded">
			<?php echo Lang::txt('PLG_PROJECTS_FILES_CONVERTED_FROM_ORIGINAL'). ' ' . basename($this->file->get('originalPath')); ?>
			<?php if ($this->file->get('originalFormat')): ?>
				(<?php echo $this->file->get('originalPath'); ?>)
			<?php endif; ?>
		</span>
	<?php endif; ?>

	<?php if (isset($this->skip) && $this->skip == true): ?>
		<span class="file-skipped"><?php echo Lang::txt('PLG_PROJECTS_FILES_SKIPPED'); ?></span>
	<?php endif; ?>

	<input type="hidden" name="<?php echo ($this->file->get('type') == 'folder' ? 'folder' : 'asset') . $multi; ?>" value="<?php echo $this->escape(urlencode($this->file->get('name'))); ?>" />

	<?php
	if (isset($this->extras)):
		echo $this->extras;
	endif;
	?>
</li>