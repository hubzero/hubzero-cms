<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
				<?php echo Lang::txt('COM_COURSES_CANCEL'); ?>
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