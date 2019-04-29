<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="sidebox<?php if (count($this->files) == 0) { echo ' suggestions'; } ?>">
		<h4><a href="<?php echo Route::url($this->model->link('files')); ?>" class="hlink"><?php echo (count($this->files) == 0) ? Lang::txt('COM_PROJECTS_FILES') : Lang::txt('PLG_PROJECTS_FILES_RECENTLY_ADDED'); ?></a>
</h4>
<?php if (count($this->files) == 0) { ?>
	<p class="s-files">
		<a href="<?php echo Route::url($this->model->link('files')); ?>"><?php echo Lang::txt('COM_PROJECTS_WELCOME_UPLOAD_FILES'); ?></a>
	</p>
<?php } else { ?>
	<ul>
		<?php foreach ($this->files as $file) {
			$ext = 'folder';
			$url = $this->model->link('files') . '&action=browse&subdir=' . urlencode($file->get('localPath'));
			if ($file->get('type') == 'file')
			{
				$url = $this->model->link('files') . '&action=download&asset=' . urlencode($file->get('localPath'));
				$ext = $file->get('ext');
			}
		?>
			<li>
				<a href="<?php echo Route::url($url); ?>" title="<?php echo $this->escape($file->get('name')); ?>"><?php echo $file->drawIcon($ext); ?> <?php echo \Components\Projects\Helpers\Html::shortenFileName($file->get('name')); ?></a>
				<span class="block faded mini">
					<?php echo $file->getSize('formatted'); ?> &middot; <?php echo Date::of($file->get('date'))->toLocal('M d, Y'); ?> &middot; <?php echo \Components\Projects\Helpers\Html::shortenName($file->get('author')); ?>
				</span>
			</li>
		<?php } ?>
	</ul><?php } ?>
</div>
