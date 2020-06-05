<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_EVENTS_MANAGER'), 'event');
Toolbar::preferences('com_events', '550');
Toolbar::spacer();
Toolbar::custom('addpage', 'new', 'COM_EVENTS_PAGES_ADD', 'COM_EVENTS_PAGES_ADD', true, false);
Toolbar::custom('respondents', 'user', 'COM_EVENTS_VIEW_RESPONDENTS', 'COM_EVENTS_VIEW_RESPONDENTS', true, false);
Toolbar::spacer();
Toolbar::publishList();
Toolbar::unpublishList();
Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();

Toolbar::spacer();
Toolbar::help('events');

Html::behavior('tooltip');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('COM_EVENTS_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER'); ?>" />

		<?php echo $this->clist; ?>
		<?php echo $this->glist; ?>

		<input type="submit" name="submitsearch" value="<?php echo Lang::txt('COM_EVENTS_SEARCH_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ID'); ?></th>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TIMESHEET'); ?></th>
				<th scope="col" class="priority-5"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_PAGES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php
					// Initiate paging
					$pageNav = $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					echo $pageNav->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$database = App::get('db');
$p = new \Components\Events\Tables\Page($database);
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
						&nbsp;
					<?php } else { ?>
						<input type="checkbox" id="cb<?php echo $i; ?>" name="id[]" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
					<?php } ?>
				</td>
				<td>
					<?php if ($row->checked_out && $row->checked_out != User::get('id')) { ?>
						<span class="checkedout hasTip" title="Checked out::<?php echo $this->escape(stripslashes($row->editor)); ?>">
							<?php echo $this->escape(html_entity_decode(stripslashes($row->title))); ?>
						</span>
					<?php } else { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<?php echo $this->escape(html_entity_decode(stripslashes($row->title))); ?>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span>
						<?php echo $this->escape($row->category); ?>
					</span>
				</td>
				<td class="priority-3">
					<?php
					$now = Date::toSql();
					$alt = Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
					if ($now <= $row->publish_up && $row->state == "1") {
						$alt = Lang::txt('COM_EVENTS_EVENT_PENDING');
					} else if (($now <= $row->publish_down || !$row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->state == "1") {
						$alt = Lang::txt('COM_EVENTS_EVENT_PUBLISHED');
					} else if ($now > $row->publish_down && $row->state == "1") {
						$alt = Lang::txt('COM_EVENTS_EVENT_EXPIRED');
					} elseif ($row->state == "0") {
						$alt = Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
					}

					$times = '';
					if (isset($row->publish_up)) {
						if (!$row->publish_up || $row->publish_up == '0000-00-00 00:00:00') {
							$times .= Lang::txt('COM_EVENTS_CAL_LANG_FROM') . ' : ' . Lang::txt('COM_EVENTS_CAL_LANG_ALWAYS').'<br />';
						} else {
							$times .= Lang::txt('COM_EVENTS_CAL_LANG_FROM') . ' : ' . date('Y-m-d H:i:s', strtotime($row->publish_up)) . '<br />';
						}
					}
					if (isset($row->publish_down)) {
						if (!$row->publish_down || $row->publish_down == '0000-00-00 00:00:00') {
							$times .= Lang::txt('COM_EVENTS_CAL_LANG_TO') . ' : ' . Lang::txt('COM_EVENTS_CAL_LANG_NEVER').'<br />';
						} else {
							$times .= Lang::txt('COM_EVENTS_CAL_LANG_FROM') . ' : ' . date('Y-m-d H:i:s', strtotime($row->publish_down)) . '<br />';
						}
					}

					$pages = $p->getCount(array('event_id'=>$row->id));

					if ($times) {
					?>
						<a class="state <?php echo $row->state ? 'publish' : 'unpublish' ?> hasTip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')" title="<?php echo Lang::txt('COM_EVENTS_EVENT_PUBLISH_INFO');?>::<?php echo $times; ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $times; ?>
				</td>
				<td class="priority-5">
					<?php if ($row->scope == 'group') : ?>
						<?php
							$group = \Hubzero\User\Group::getInstance($row->scope_id);
							if (is_object($group))
							{
								echo Lang::txt('COM_EVENTS_EVENT_GROUP', Route::url('index.php?option=com_events&group_id=' . $group->get('gidNumber')), $group->get('description'));
							}
							else
							{
								echo Lang::txt('COM_EVENTS_EVENT_GROUP_NOT_FOUND', $row->scope_id);
							}
						?>
					<?php else : ?>
						<span>
							<?php echo $this->escape(stripslashes($row->groupname)); ?>
						</span>
					<?php endif; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pages&event_id=' . $row->id); ?>">
						<?php echo Lang::txt('COM_EVENTS_EVENT_NUMBER_OF_PAGES', $pages); ?>
					</a>
				</td>
			</tr>
<?php
$k = 1 - $k;

}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>