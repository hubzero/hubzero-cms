<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_STUDENTS'), 'courses');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span4">
				<label for="filter_search"><?php echo Lang::txt('COM_COURSES_SEARCH'); ?>:</label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COURSES_STUDENTS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_COURSES_GO'); ?>" />
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span8">
				<label for="filter_offering"><?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:</label>
				<select name="offering" id="filter_offering" class="filter filter-submit">
					<option value="0"><?php echo Lang::txt('COM_COURSES_OFFERING_SELECT'); ?></option>
					<?php
					$offerings = array();
					require_once Component::path('com_courses') . DS . 'models' . DS . 'courses.php';
					$model = \Components\Courses\Models\Courses::getInstance();
					if ($model->courses()->total() > 0)
					{
						foreach ($model->courses() as $course)
						{
						?>
						<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
						<?php
						foreach ($course->offerings() as $offering)
						{
							$offerings[$offering->get('id')] = $course->get('alias') . ' : ' . $offering->get('alias');
							?>
							<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->offering->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
							<?php
						}
						?>
						</optgroup>
						<?php
						}
					}
					?>
				</select>

				<?php if ($this->filters['offering']) { ?>
					<label for="filter_section"><?php echo Lang::txt('COM_COURSES_SECTION'); ?>:</label>
					<select name="section" id="filter_section" class="filter filter-submit">
						<option value="0"><?php echo Lang::txt('COM_COURSES_SECTION_SELECT'); ?></option>
						<?php
						if ($this->offering->sections()->total() > 0)
						{
							foreach ($this->offering->sections() as $section)
							{
						?>
								<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->filters['section_id']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
						<?php
							}
						}
						?>
					</select>
				<?php } else { ?>
					<input type="hidden" name="section" id="filter_section" value="0" />
				<?php } ?>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<?php if ($this->filters['offering']) { ?>
			<caption>
				(<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=courses'); ?>">
					<?php echo $this->escape(stripslashes($this->course->get('alias'))); ?>
				</a>)
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=courses'); ?>">
					<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
				</a>:
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=offerings&course=' . $this->course->get('id')); ?>">
					<?php echo $this->escape(stripslashes($this->offering->get('title'))); ?>
				</a>
			</caption>
		<?php } ?>
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_COURSES_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_NAME'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_COURSES_COL_EMAIL'); ?></th>
				<?php if (!$this->filters['offering']) { ?>
					<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_COURSE_OFFERING'); ?></th>
				<?php } ?>
				<th scope="col"><?php echo Lang::txt('COM_COURSES_COL_SECTION'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_COURSES_COL_CERTIFICATE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_COURSES_COL_ENROLLED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo (!$this->filters['offering']) ? '8' : '7'; ?>">
					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$k = 0;
		$n = count($this->rows);
		foreach ($this->rows as $row)
		{
			$section = \Components\Courses\Models\Section::getInstance($row->get('section_id'));
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->get('user_id')); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&offering=' . $row->get('offering_id') . '&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('name'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('name'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&offering=' . $row->get('offering_id') . '&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('email'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('email'))); ?>
						</span>
					<?php } ?>
				</td>
				<?php if (!$this->filters['offering']) { ?>
					<td>
						<?php echo (isset($offerings[$row->get('offering_id')])) ? $offerings[$row->get('offering_id')] : Lang::txt('COM_COURSES_UNKNOWN'); ?>
					</td>
				<?php } ?>
				<td>
					<?php echo ($section->exists()) ? $this->escape(stripslashes($section->get('title'))) : Lang::txt('COM_COURSES_NONE'); ?>
				</td>
				<td class="priority-3">
					<span class="state <?php echo ($row->get('token')) ? 'publish' : 'unpublish'; ?>">
						<span> <?php echo ($row->get('token')) ? 'redeemed' : ''; ?></span>
					</span>
				</td>
				<td class="priority-4">
					<?php if ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00') { ?>
						<time datetime="<?php echo $row->get('enrolled'); ?>"><?php echo Date::of($row->get('enrolled'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
					<?php } else { ?>
						<?php echo Lang::txt('COM_COURSES_UNKNOWN'); ?>
					<?php } ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>