<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="sidebox<?php if (count($this->notes) == 0) { echo ' suggestions'; } ?>">
	<h4>
		<a href="<?php echo Route::url($this->model->link('notes')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('PLG_PROJECTS_NOTES')); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_NOTES')); ?></a>
		<?php if (count($this->notes) > 0) { ?>
			<span><a href="<?php echo Route::url($this->model->link('notes')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
		<?php } ?>
	</h4>
	<?php if (count($this->notes) == 0) { ?>
		<p class="s-notes"><a href="<?php echo Route::url($this->model->link('notes') . '&action=new'); ?>"><?php echo Lang::txt('PLG_PROJECTS_NOTES_ADD_NOTE'); ?></a></p>
	<?php } else { ?>
		<ul>
			<?php foreach ($this->notes as $note) { ?>
				<li>
					<a href="<?php echo Route::url($this->model->link('notes') . '&pagename=' . ($note->path ? $note->path . '/' : '') . $note->pagename); ?>" class="notes"><?php echo $this->escape(\Hubzero\Utility\Str::truncate($note->title, 35)); ?></a>
				</li>
			<?php } ?>
		</ul>
	<?php } ?>
</div>
