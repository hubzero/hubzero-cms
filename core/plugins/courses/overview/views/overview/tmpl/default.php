<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = strtolower(Request::getWord('field', ''));
$task  = strtolower(Request::getWord('task', ''));

if ($this->course->access('edit', 'course') && $field == 'description')
{
	?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="form-inplace" method="post">
		<label for="field_description">
			<?php
				echo $this->editor('course[description]', $this->escape($this->course->description('raw')), 35, 50, 'field_description');
			?>
		</label>

		<p class="submit">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_COURSES_SAVE'); ?>" />
			<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=course&gid=' . $this->course->get('alias')); ?>">
				<?php echo Lang::txt('JCANCEL'); ?>
			</a>
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="course" />
		<input type="hidden" name="task" value="save" />

		<?php echo Html::input('token'); ?>

		<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
		<input type="hidden" name="course[id]" value="<?php echo $this->escape($this->course->get('id')); ?>" />
		<input type="hidden" name="course[alias]" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
	</form>
	<?php
}
else
{
	if ($this->course->access('edit', 'course'))
	{
		?>
		<div class="manager-options">
			<a class="icon-edit btn btn-secondary" href="<?php echo Route::url($this->course->link() . '&task=edit&field=description'); ?>">
				<?php echo Lang::txt('COM_COURSES_EDIT'); ?>
			</a>
			<span><strong><?php echo Lang::txt('COM_COURSES_LONG_DESCRIPTION'); ?></strong></span>
		</div>
		<?php
	}

	if (!$this->course->get('description'))
	{
		echo '<p><em>' . Lang::txt('COM_COURSES_LONG_DESCRIPTION_NONE') . '</em></p>';
	}
	else
	{
		echo $this->course->description('parsed');
	}
}