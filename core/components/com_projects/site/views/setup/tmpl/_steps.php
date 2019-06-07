<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ol id="steps" class="steps">
	<li class="setup-step<?php
		if ($this->step == 0):
			echo ' active';
		elseif ($this->model->get('setup_stage') >= 1):
			echo ' completed';
		endif;
		?>">
		<?php if ($this->model->get('setup_stage') > 0 && $this->step != 0): ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=setup&section=describe'); ?>">
				<?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?>
			</a>
		<?php else: ?>
			<?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?>
		<?php endif; ?>
	</li>
	<li class="setup-step<?php
		if ($this->step == 1):
			echo ' active';
		elseif ($this->model->get('setup_stage') >= 2):
			echo ' completed';
		else:
			echo ' coming';
		endif;
		?>">
		<?php if ($this->model->get('setup_stage') >= 1 && $this->step != 1): ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=setup&section=team'); ?>">
				<?php echo Lang::txt('COM_PROJECTS_ADD_TEAM'); ?>
			</a>
		<?php else: ?>
			<?php echo Lang::txt('COM_PROJECTS_ADD_TEAM'); ?>
		<?php endif; ?>
	</li>
	<?php if ($this->step == 2): ?>
		<li class="setup-step active">
			<?php echo Lang::txt('COM_PROJECTS_SETUP_ONE_LAST_THING'); ?>
		</li>
	<?php endif; ?>
	<li class="setup-step coming">
		<?php echo Lang::txt('COM_PROJECTS_READY_TO_GO'); ?>
	</li>
</ol>
