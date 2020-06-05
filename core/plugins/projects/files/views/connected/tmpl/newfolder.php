<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_ADD_NEW_FOLDER'); ?> <?php if ($this->subdir) { ?> <?php echo Lang::txt('PLG_PROJECTS_FILES_IN'); ?> <?php echo \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->model->link('files') . '&action=browse&connection=' . $this->connection->id, $parent, false, $this->connection->adapter()); ?></span> <?php } ?></h3>
			<?php  ?>
	<?php if ($this->getError()) : ?>
		<p class="witherror"><?php echo $this->getError(); ?></p>
	<?php else : ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
			<fieldset>
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="action" value="savedir" />
				<label>
					<img src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/img/folder.gif" alt="" />
					<input type="text" name="newdir" maxlength="100" value="untitled" />
				</label>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
				<input type="reset" class="btn btn-cancel" id="cancel-action" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			</fieldset>
		</form>
	<?php endif; ?>
</div>
