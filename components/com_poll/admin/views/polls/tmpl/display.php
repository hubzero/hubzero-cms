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

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$canDo = \Components\Poll\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_POLL'), 'poll.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_poll', '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
Toolbar::spacer();
Toolbar::help('polls');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->lists['search']); ?>" placeholder="<?php echo Lang::txt('COM_POLL_SEARCH_PLACEHOLDER'); ?>" />

			<button onclick="this.form.submit();"><?php echo Lang::txt('COM_POLL_GO'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<?php echo $this->lists['state']; ?>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_POLL_COL_NUM'); ?>
				</th>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th scope="col" class="title">
					<?php echo $this->grid('sort', 'COM_POLL_COL_TITLE', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_PUBLISHED', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_OPEN', 'm.open', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_VOTES', 'm.voters', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_OPTIONS', 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_LAG', 'm.lag', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_POLL_COL_ID', 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php
					$pagination = $this->pagination(
						$this->total,
						$this->limitstart,
						$this->limit
					);
					echo $pagination->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = &$this->items[$i];

			$link = Route::url('index.php?option=' . $this->option . '&view=poll&task=edit&cid='. $row->id);

			$task  = $row->published ? 'unpublish' : 'publish';
			$class = $row->published ? 'published' : 'unpublished';
			$alt   = $row->published ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');

			$task2  = ($row->open == 1) ? 'close' : 'open';
			$class2 = ($row->open == 1) ? 'published' : 'unpublished';
			$alt2   = ($row->open == 1) ? Lang::txt('COM_POLL_OPEN') : Lang::txt('COM_POLL_CLOSED');
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php if (($row->checked_out && $row->checked_out != $this->user->get('id')) || !$canDo->get('core.edit')) { ?>
						<span> </span>
					<?php } else { ?>
						<input type="checkbox" name="cid[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td>
					<?php if (($row->checked_out && $row->checked_out != $this->user->get('id')) || !$canDo->get('core.edit')) {
						echo $row->title;
					} else { ?>
						<span class="editlinktip hasTip" title="<?php echo $this->escape($row->title); ?>">
							<a href="<?php echo $link; ?>">
								<?php echo $this->escape($row->title); ?>
							</a>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class;?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $task . '&cid=' . $row->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_POLL_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class2; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $task2 . '&cid=' . $row->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_POLL_SET_TO', $task2); ?>">
							<span><?php echo $alt2; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class2; ?>">
							<span><?php echo $alt2; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php echo $row->voters; ?>
				</td>
				<td>
					<?php echo $row->numoptions; ?>
				</td>
				<td>
					<?php echo $row->lag; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>