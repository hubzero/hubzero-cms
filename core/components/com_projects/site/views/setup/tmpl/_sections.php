<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<ul id="panelist">
	<?php foreach ($this->sections as $section): ?>
		<?php if ($section != 'info'): ?>
			<li <?php if ($section == $this->section) { echo 'class="activepane"'; } ?>>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=' . strtolower($section)); ?>">
					<?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($section)); ?>
				</a>
			</li>
		<?php else: ?>
			<li <?php if ($this->section == 'info' || $this->section == 'info_custom') { echo 'class="activepane"'; } ?>>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=' . strtolower($section)); ?>">
					<?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($section)); ?>
				</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
