<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_PROJECT_FILES'); ?>
	</h3>

	<?php if ($this->getError()) : ?>
		<p class="witherror"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<?php if (!$this->getError()) : ?>
		<form id="hubForm-ajax" method="post" class="" action="<?php echo Route::url($this->url); ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="action" value="removeit" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="active" value="files" />
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

				<p><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE_FILES_CONFIRM'); ?></p>

				<ul class="sample">
					<?php foreach ($this->items as $file) : ?>
						<li>
							<?php echo \Components\Projects\Models\File::drawIcon($file->getExtension()); ?>
							<?php echo $file->getName(); ?>
							<?php echo $file->isDir()
								? '<input type="hidden" name="folder[]" value="' . urlencode($file->getPath()) . '" />'
								: '<input type="hidden" name="asset[]"  value="' . urlencode($file->getPath()) . '" />'; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?>" id="submit-ajaxform" />
					<?php if ($this->ajax) : ?>
						<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
					<?php else :  ?>
						<a id="cancel-action" href="<?php echo Route::url($this->url . '&a=1' . $subdirlink); ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
					<?php endif; ?>
				</p>
			</fieldset>
		</form>
	<?php endif; ?>
</div>
