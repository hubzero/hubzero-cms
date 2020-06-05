<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Directory path breadcrumbs
$bc    = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent, false, $this->connection->adapter());
$bcEnd = $this->item->isDir() ? '<span class="folder">' . $this->item->getName() . '</span>' : '<span class="file">' . $this->item->getName() . '</span>';
$lang  = $this->item->isDir() ? 'folder' : 'file';
?>

<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME') . ' ' . $lang . ' ' . $bc . ' ' . $bcEnd; ?>
	</h3>
	<?php if ($this->getError()) : ?>
		<p class="witherror"><?php $this->getError(); ?></p>
	<?php else : ?>
		<form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
			<fieldset>
				<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
				<input type="hidden" name="action" value="renameit" />
				<input type="hidden" name="type" value="<?php echo $lang; ?>" />
				<input type="hidden" name="oldname" value="<?php echo $this->item->getPath(); ?>" />
				<h5><?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_NAME'); ?></h5>
				<label>
					<input type="text" name="newname" maxlength="250" value="<?php echo $this->item->getFilename(); ?>" />
				</label>
				<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
				<input type="reset" class="btn btn-cancel" id="cancel-action" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			</fieldset>
		</form>
	<?php endif; ?>
</div>
