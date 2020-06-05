<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Filesystem\Entity;

$f          = 1;
$i          = 1;
$skipped    = 0;
$maxlevel   = 100;
$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
?>

<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_PROJECT_FILES'); ?>
	</h3>

	<?php if ($this->getError()) : ?>
		<?php echo '<p class="witherror">' . $this->getError() . '</p>'; ?>
	<?php else : ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="action" value="moveit" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="active" value="files" />
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

				<p><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_FILES_CONFIRM'); ?></p>

				<ul class="sample">
					<?php foreach ($this->items as $file) : ?>
						<li>
						<li>
							<?php echo \Components\Projects\Models\File::drawIcon($file->getExtension()); ?>
							<?php echo $file->getName(); ?>
							<?php echo $file->isDir()
								? '<input type="hidden" name="folder[]" value="' . urlencode($file->getPath()) . '" />'
								: '<input type="hidden" name="asset[]"  value="' . urlencode($file->getPath()) . '" />'; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<div id="dirs" class="dirs">
					<h4>
						<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_WHERE'); ?>
					</h4>
					<?php if (count($this->list) > 0) : ?>
						<ul class="dirtree">
							<li>
								<input type="radio" name="newpath" value="" <?php if (!$this->subdir) { echo 'disabled="disabled" '; } ?> checked="checked" /> <span><?php echo Lang::txt('PLG_PROJECTS_FILES_HOME_DIRECTORY'); ?></span>
							</li>
							<?php
								foreach ($this->list as $dir)
								{
									echo \Components\Projects\Helpers\Html::listDirHtml($dir, $this->subdir);
								}
								?>

						</ul>
					<?php endif; ?>
					<?php if ($maxlevel <= 100) : ?>
						<?php if (count($this->list) > 0) : ?>
							<div class="or"><?php echo Lang::txt('COM_PROJECTS_OR'); ?></div>
						<?php endif; ?>
						<label><span class="block"><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TO_NEW_DIRECTORY'); ?></span>
							<span class="mini prominent"><?php echo $this->subdir ? \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->model->link('files') . '&action=browse&connection=' . $this->connection->id, $parent, false, $this->connection->adapter(), '/') : ''; ?></span>
							<input type="text" name="newdir" maxlength="50" value="" />
						</label>
					<?php endif; ?>
				</div>
				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?>" />
					<?php if ($this->ajax) : ?>
						<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
					<?php else : ?>
						<span>
							<a id="cancel-action"  class="btn btn-cancel"  href="<?php echo Route::url($this->url . '&a=1' . $subdirlink); ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
						</span>
					<?php endif; ?>
				</p>
			</fieldset>
		</form>
	<?php endif; ?>
</div>
