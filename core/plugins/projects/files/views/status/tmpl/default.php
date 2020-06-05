<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_GIT_STATUS'); ?></h3>
<form id="hubForm-ajax" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id')); ?>">
	<fieldset >
		<?php echo $this->status; ?>
		<p class="submitarea">
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php } else {  ?>
				<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_GO_BACK'); ?></a>
			<?php } ?>
		</p>
	</fieldset>
</form>
</div>