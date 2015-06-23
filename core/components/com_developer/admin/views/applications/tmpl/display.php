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
 * @author    Chris Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Developer\Helpers\Permissions::getActions('application');

// title & toolbar
Toolbar::title(Lang::txt('COM_DEVELOPER') . ': ' . Lang::txt('COM_DEVELOPER_APPLICATIONS'));
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit'))
{
	Toolbar::custom('resetclientsecret', 'refresh', 'refresh', Lang::txt('COM_DEVELOPER_RESET_CLIENT_SECRET'));
	Toolbar::custom('removetokens', 'cancel', 'cancel', Lang::txt('COM_DEVELOPER_REVOKE_TOKENS'));
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
	Toolbar::deleteList('', 'delete');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}

// This line makes sure we're including the javascript framework
Html::behavior('framework');
?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'resetclientsecret')
	{
		if (confirm("<?php echo Lang::txt('COM_DEVELOPER_RESET_CLIENT_SECRET_CONFIRM'); ?>"))
		{
			Joomla.submitform(pressbutton, document.getElementById('item-form'));
		}
		return;
	}

	if (pressbutton == 'removetokens')
	{
		if (confirm("<?php echo Lang::txt('COM_DEVELOPER_REVOKE_TOKENS_CONFIRM'); ?>"))
		{
			Joomla.submitform(pressbutton, document.getElementById('item-form'));
		}
		return;
	}

	Joomla.submitform(pressbutton, document.getElementById('item-form'));
	return;
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->getErrors()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_DEVELOPER_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_DEVELOPER_COL_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_DEVELOPER_COL_STATE'); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_DEVELOPER_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_DEVELOPER_COL_CREATED_BY', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_DEVELOPER_COL_HUB_ACCOUNT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
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
		$i = 0;

		foreach ($this->rows as $row)
		{
			if ($row->isPublished())
			{
				$alt  = Lang::txt('JPUBLISHED');
				$cls  = 'publish';
				$task = 'unpublish';
			}
			else if ($row->isUnpublished())
			{
				$alt  = Lang::txt('JUNPUBLISHED');
				$task = 'publish';
				$cls  = 'unpublish';
			}
			else if ($row->isDeleted())
			{
				$alt  = Lang::txt('JTRASHED');
				$task = 'publish';
				$cls  = 'trash';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id') ?>" onclick="isChecked(this.checked, this);" />
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('name'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('name'))); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . JUtility::getToken() . '=1'); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-5">
					<?php echo $row->created('relative'); ?>
				</td>
				<td class="priority-4">
					<?php echo $row->creator('name'); ?>
				</td>
				<td class="priority-3">
					<?php echo ($row->isHubAccount()) ? '<span class="state default"><span>'.Lang::txt('COM_DEVELOPER_COL_HUB_ACCOUNT').'</span></span>' : ''; ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
