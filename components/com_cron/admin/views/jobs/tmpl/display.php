<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Cron\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_CRON'), 'cron.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
Toolbar::custom('run', 'purge', '', 'COM_CRON_RUN', false);
Toolbar::spacer();
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('jobs');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col" class="priority-5"><?php echo $this->grid('sort', 'COM_CRON_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_CRON_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_CRON_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_CRON_COL_STARTS', 'publish_up', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo $this->grid('sort', 'COM_CRON_COL_ENDS', 'publish_down', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_CRON_COL_ACTIVE', 'active', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo $this->grid('sort', 'COM_CRON_COL_LAST_RUN', 'last_run', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo $this->grid('sort', 'COM_CRON_COL_NEXT_RUN', 'next_run', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<!-- <th scope="col"><?php echo Lang::txt('COM_CRON_COL_RECURRENCE'); ?></th> -->
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php
				// initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->results)
{
	$k = 0;
	for ($i=0, $n=count( $this->results ); $i < $n; $i++)
	{
		$row =& $this->results[$i];

		switch ($row->get('state'))
		{
			case '2': // Deleted
				$task = 'publish';
				$alt  = Lang::txt('JTRASHED');
				$cls  = 'trash';
			break;
			case '1': // Published
				$task = 'unpublish';
				$alt  = Lang::txt('JPUBLISHED');
				$cls  = 'publish';
			break;
			case '0': // Unpublished
			default:
				$task = 'publish';
				$alt  = Lang::txt('JUNPUBLISHED');
				$cls  = 'unpublish';
			break;
		}

		switch ($row->get('active'))
		{
			case '1': // Published
				$alt2 = Lang::txt('COM_CRON_ACTIVE');
				$cls2 = 'publish';
			break;
			case '0': // Unpublished
			default:
				$alt2 = Lang::txt('COM_CRON_INACTIVE');
				$cls2 = 'unpublish';
			break;
		}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('title'))); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_CRON_SET_THIS_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="datetime">
						<?php if ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00') { ?>
							<time datetime="<?php echo $row->get('publish_up'); ?>"><?php echo Date::of($row->get('publish_up'))->format(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
						<?php } else { ?>
							<?php echo Lang::txt('COM_CRON_NO_DATE_SET'); ?>
						<?php } ?>
					</span>
				</td>
				<td class="priority-3">
					<span class="datetime">
						<?php if ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00') { ?>
							<time datetime="<?php echo $row->get('publish_down'); ?>"><?php echo Date::of($row->get('publish_down'))->format(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
						<?php } else { ?>
							<?php echo Lang::txt('COM_CRON_NONE'); ?>
						<?php } ?>
					</span>
				</td>
				<td class="priority-2">
					<span class="state <?php echo $cls2; ?>">
						<span><?php echo $alt2; ?></span>
					</span>
				</td>
				<td class="priority-4">
					<span class="datetime">
						<?php if ($row->get('last_run') && $row->get('last_run') != '0000-00-00 00:00:00') { ?>
							<time datetime="<?php echo $this->escape($row->get('last_run')); ?>"><?php echo $this->escape($row->get('last_run')); ?></time>
						<?php } else { ?>
							<?php echo $this->escape($row->get('last_run')); ?>
						<?php } ?>
					</span>
				</td>
				<td class="priority-2">
					<span class="datetime">
						<?php $nxt = ($row->started() ? $row->get('next_run') : $row->get('publish_up')); ?>
						<?php if ($nxt && $nxt != '0000-00-00 00:00:00') { ?>
							<time datetime="<?php echo $this->escape($nxt); ?>"><?php echo $this->escape($nxt); ?></time>
						<?php } else { ?>
							<?php echo $this->escape($nxt); ?>
						<?php } ?>
					</span>
				</td>
				<!-- <td>
					<span class="recurrence">
						<span><?php echo $row->get('recurrence'); ?></span>
					</span>
				</td> -->
			</tr>
<?php
		$k = 1 - $k;
	}
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>