<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Story\Models\Submission;

$canDo = \Components\Story\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_STORY_SUBMISSIONS'), 'story.png');
if ($canDo->get('story.publish'))
{
	Toolbar::custom('accept', 'featured', '', 'COM_STORY_ACCEPT', true);
	Toolbar::custom('attention', 'star', '', 'COM_STORY_FLAG_ATTENTION', true);
	Toolbar::custom('hold', 'pause', '', 'COM_STORY_HOLD', true);
	Toolbar::custom('reject', 'unpublish', '', 'COM_STORY_REJECT', true);
	Toolbar::custom('spam', 'trash', '', 'COM_STORY_MARK_SPAM', true);
}

Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_STORIES'), Route::url('index.php?option=' . $this->option . '&controller=stories'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SECTIONS'), Route::url('index.php?option=' . $this->option . '&controller=sections'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_TOPICS'), Route::url('index.php?option=' . $this->option . '&controller=topics'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_DISCUSSIONS'), Route::url('index.php?option=' . $this->option . '&controller=comments'));
Submenu::addEntry(Lang::txt('COM_STORY_SUBMENU_SUBMISSIONS'), Route::url('index.php?option=' . $this->option . '&controller=submissions'), true);

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=submissions'); ?>" method="post" name="adminForm" id="adminForm">

	<p class="info"><?php echo Lang::txt('COM_STORY_TRIAGE_INTRO'); ?></p>

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" />

		<label for="filter_state"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?>:</label>
		<select name="state" id="filter_state" onchange="this.form.submit();">
			<option value=""><?php echo Lang::txt('COM_STORY_FILTER_STATE_OPEN'); ?></option>
<?php foreach (array('pending', 'hold', 'accepted', 'rejected', 'spam') as $state) { ?>
			<option value="<?php echo $state; ?>"<?php echo ($this->filters['state'] == $state) ? ' selected="selected"' : ''; ?>><?php
				echo Lang::txt('COM_STORY_STATE_' . strtoupper($state));
			?></option>
<?php } ?>
		</select>

		<input type="submit" value="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_ID'); ?></th>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_ATTENTION'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_SUBJECT'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_SUBMITTER'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_EDITOR_POPULARITY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_POPULARITY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORY_COL_STATE'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php if (!count($this->rows)) { ?>
			<tr><td colspan="8"><?php echo Lang::txt('COM_STORY_NONE_FOUND'); ?></td></tr>
<?php } else {
	$i = 0;
	foreach ($this->rows as $row) {
		$submitter = $row->get('created_by') ? User::getInstance($row->get('created_by')) : null;
?>
			<tr class="<?php echo ($i++ % 2 == 0) ? 'even' : 'odd'; ?><?php echo $row->get('attention_needed') ? ' needs-attention' : ''; ?>">
				<td><?php echo (int) $row->get('id'); ?></td>
				<td><?php echo Html::grid('id', $i, $row->get('id')); ?></td>
				<td><?php echo $row->get('attention_needed') ? '&#9733;' : ''; ?></td>
				<td>
					<strong><?php echo $this->escape($row->get('subject')); ?></strong>
<?php if ($row->get('url')) { ?>
					<br /><small><a href="<?php echo $this->escape($row->get('url')); ?>" rel="nofollow noopener"><?php echo $this->escape($row->get('url')); ?></a></small>
<?php } ?>
<?php if ($row->get('body')) { ?>
					<br /><small><?php echo $this->escape(substr($row->get('body'), 0, 160)); ?></small>
<?php } ?>
				</td>
				<td><?php echo $submitter && $submitter->get('id') ? $this->escape($submitter->get('name')) : '&mdash;'; ?></td>
				<td><?php echo round((float) $row->get('editor_popularity'), 1); ?></td>
				<td class="band-<?php echo (int) $row->band($this->config); ?>"><?php echo round((float) $row->get('popularity'), 1); ?></td>
				<td><?php echo Lang::txt('COM_STORY_STATE_' . strtoupper($row->get('state'))); ?></td>
			</tr>
<?php }
} ?>
		</tbody>
	</table>

	<?php echo $this->rows->pagination; ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="submissions" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>
