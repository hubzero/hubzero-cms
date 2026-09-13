<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Comment;

$canDo = \Components\Story\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_STORY_DISCUSSIONS'), 'story.png');
if ($canDo->get('core.edit'))
{
	Toolbar::custom('rebuild', 'refresh', '', 'COM_STORY_REBUILD_TREES', false);
}

Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_STORIES'), Route::url('index.php?option=' . $this->option . '&controller=stories'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SECTIONS'), Route::url('index.php?option=' . $this->option . '&controller=sections'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_TOPICS'), Route::url('index.php?option=' . $this->option . '&controller=topics'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_DISCUSSIONS'), Route::url('index.php?option=' . $this->option . '&controller=comments'), true);

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=comments'); ?>" method="post" name="adminForm" id="adminForm">

	<p class="info"><?php echo Lang::txt('COM_STORY_REBUILD_EXPLAINER'); ?></p>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_ID'); ?></th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_TITLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_STATUS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_STORED_COUNT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_ACTUAL_COUNT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_ACTIVITY'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php if (!count($this->rows)) { ?>
			<tr>
				<td colspan="7"><?php echo Lang::txt('JGLOBAL_NO_MATCHING_RESULTS'); ?></td>
			</tr>
<?php } else {
	$i = 0;
	foreach ($this->rows as $row)
	{
		$actual = Comment::all()
			->whereEquals('discussion_id', $row->get('id'))
			->whereEquals('state', Comment::STATE_PUBLISHED)
			->count();
?>
			<tr class="<?php echo ($i++ % 2 == 0) ? 'even' : 'odd'; ?>">
				<td><?php echo (int) $row->get('id'); ?></td>
				<td><?php echo Html::grid('id', $i, $row->get('id')); ?></td>
				<td><?php echo $this->escape($row->get('title')); ?></td>
				<td><?php echo $this->escape($row->get('comment_status')); ?></td>
				<td><?php echo (int) $row->get('comment_count'); ?></td>
				<td<?php echo ((int) $row->get('comment_count') != (int) $actual) ? ' class="drifted"' : ''; ?>><?php echo (int) $actual; ?></td>
				<td><?php echo $row->get('last_activity') ? Date::of($row->get('last_activity'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : '&mdash;'; ?></td>
			</tr>
<?php }
} ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="comments" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
