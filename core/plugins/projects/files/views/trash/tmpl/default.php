<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED_FILES'); ?></h3>

<form id="hubForm-ajax" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id')); ?>">
	<fieldset >
<?php if (empty($this->files)) { ?>
	<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_FILES_TRASH_EMPTY'); ?></p>
<?php } else { ?>
	<div class="wrapper">
		<table id="filelist" class="listing">
			<thead>
				<tr>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_FILE'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_OPTIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->files as $filename => $file) {

					$dirname = dirname($filename);
				?>
				<tr class="mini">
					<td>
						<span class="icon-image"></span>
						<span><?php echo basename($filename); ?></span>
						<?php if ($dirname != '.') { ?>
						<span class="faded block ipadded">
							<span class="icon-folder"></span>
							<?php echo $dirname; ?></span>
						<?php } ?>
					</td>
					<td class="faded">
						<?php echo \Components\Projects\Helpers\Html::formatTime($file['date'], true, true); ?>
						<span class="block"><?php echo $file['author']; ?></span>
					</td>
					<td><a href="<?php echo Route::url($this->url . '&action=restore&asset=' . urlencode($filename) . '&hash=' . $file['hash']);  ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_RESTORE'); ?></a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
		<p class="submitarea">
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CLOSE'); ?>" />
			<?php } else {  ?>
			<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_GO_BACK'); ?></a>
			<?php } ?>
		</p>

<?php } ?>
	</fieldset>
</form>
</div>