<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Story;

$canDo = \Components\Story\Helpers\Permissions::getActions('component');

$states = array(
	Story::STATE_TRASHED   => Lang::txt('COM_STORY_STATE_TRASHED'),
	Story::STATE_DRAFT     => Lang::txt('COM_STORY_STATE_DRAFT'),
	Story::STATE_QUEUED    => Lang::txt('COM_STORY_STATE_QUEUED'),
	Story::STATE_PUBLISHED => Lang::txt('COM_STORY_STATE_PUBLISHED'),
	Story::STATE_ARCHIVED  => Lang::txt('COM_STORY_STATE_ARCHIVED')
);

Toolbar::title(Lang::txt('COM_STORY_STORIES'), 'story.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::archiveList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_STORIES'), Route::url('index.php?option=' . $this->option . '&controller=stories'), true);
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SECTIONS'), Route::url('index.php?option=' . $this->option . '&controller=sections'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_TOPICS'), Route::url('index.php?option=' . $this->option . '&controller=topics'));

$sectionTitles = array();
foreach ($this->sections as $section)
{
	$sectionTitles[$section->get('id')] = $section->get('title');
}

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=stories'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" />

		<label for="filter_state"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?>:</label>
		<select name="state" id="filter_state" class="filter filter-submit">
			<option value="-1"><?php echo Lang::txt('COM_STORY_FILTER_STATE_ALL'); ?></option>
<?php foreach ($states as $value => $label) { ?>
			<option value="<?php echo $value; ?>"<?php if ((string) $this->filters['state'] === (string) $value) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
<?php } ?>
		</select>

		<label for="filter_section"><?php echo Lang::txt('COM_STORY_COL_SECTION'); ?>:</label>
		<select name="section" id="filter_section" class="filter filter-submit">
			<option value="0"><?php echo Lang::txt('COM_STORY_FILTER_SECTION_ALL'); ?></option>
<?php foreach ($this->sections as $section) { ?>
			<option value="<?php echo $section->get('id'); ?>"<?php if ($this->filters['section'] == $section->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($section->get('title')); ?></option>
<?php } ?>
		</select>

		<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
		<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_STORY_COL_KICKER'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_STORY_COL_SECTION'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_PUBLISHED', 'publish_up', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_STORY_COL_HITS'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORY_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
foreach ($this->rows as $row)
{
	$state = (int) $row->get('state');
	?>
			<tr class="<?php echo 'row' . ($i % 2); ?>">
				<td><?php echo $this->escape($row->get('id')); ?></td>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $this->escape($row->get('id')); ?>" class="checkbox-toggle" />
					<label for="cb<?php echo $i; ?>" class="sr-only visually-hidden"><?php echo Lang::txt('JSELECT'); ?></label>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=stories&task=edit&id[]=' . $row->get('id')); ?>"><?php echo $this->escape($row->get('title')); ?></a>
<?php } else { ?>
					<?php echo $this->escape($row->get('title')); ?>
<?php } ?>
					<span class="hint"><code>/<?php echo implode('/', $row->publishedOn()); ?>/<?php echo $this->escape($row->get('alias')); ?></code></span>
				</td>
				<td class="priority-4"><?php echo $this->escape($row->get('kicker')); ?></td>
				<td class="priority-3"><?php echo $this->escape(isset($sectionTitles[$row->get('section_id')]) ? $sectionTitles[$row->get('section_id')] : '—'); ?></td>
				<td>
<?php if ($row->get('publish_up')) { ?>
					<time datetime="<?php echo $this->escape($row->get('publish_up')); ?>"><?php echo Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
<?php } else { ?>
					<span class="hint"><?php echo Lang::txt('COM_STORY_NOT_SCHEDULED'); ?></span>
<?php } ?>
				</td>
				<td class="priority-5"><?php echo (int) $row->get('hits'); ?></td>
				<td><?php echo $this->escape(isset($states[$state]) ? $states[$state] : $state); ?></td>
			</tr>
	<?php
	$i++;
}
?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="stories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
