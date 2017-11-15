<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Products', 'storefront.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
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
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('products');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_STOREFRONT_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');$('#filter-type').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span7">
				<label for="filter-type"><?php echo Lang::txt('COM_STOREFRONT_TYPE'); ?>:</label>
				<select id="filter-type" name="type" onchange="this.form.submit()">
					<option value="-1"<?php if ($this->filters['type'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STOREFRONT_FILTER_TYPE'); ?></option>
					<?php foreach ($this->types as $type) { ?>
						<option value="<?php echo $type->ptId; ?>"<?php if ($this->filters['type'] == $type->ptId) { echo ' selected="selected"'; } ?>><?php echo $type->ptName; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'Alias', 'pAlias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_PRODUCT_TYPE', 'ptName', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">SKUs (published)</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php if ($this->config->get('productAccess')) { ?>
					<th scope="col"><?php echo Lang::txt('COM_STOREFRONT_ACCESS'); ?></th>
				<?php } else { ?>
					<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="7"><?php
				// Initiate paging
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
$k = 0;
$i = 0;
$db = \App::get('db');

foreach ($this->rows as $row)
{
	switch ($row->pActive)
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = Lang::txt('COM_STOREFRONT_PUBLISHED');
			break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = Lang::txt('COM_STOREFRONT_TRASHED');
			break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
			break;
	}

	if (!$row->access)
	{
		$color_access = 'public';
		$task_access  = 'accessregistered';
	}
	elseif ($row->access == 1)
	{
		$color_access = 'registered';
		$task_access  = 'accessspecial';
	}
	else
	{
		$color_access = 'special';
		$task_access  = 'accesspublic';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->pId; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->pId); ?>" title="<?php echo Lang::txt('COM_STOREFRONT_EDIT_PRODUCT'); ?>">
						<span><?php echo $this->escape(stripslashes($row->pName)); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $this->escape(stripslashes($row->pName)); ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $row->pAlias; ?>
				</td>
				<td>
					<?php echo $row->ptName; ?>
				</td>
				<td>

					<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=skus&task=display&id=' . $row->pId); ?>" title="View SKUs">
						<span><?php
						$key = $row->pId;
						$skuCountInfo = $this->skus->$key;
						echo $skuCountInfo->active + $skuCountInfo->inactive;
						if ($skuCountInfo->active + $skuCountInfo->inactive > 0)
						{
							echo ' (' . $skuCountInfo->active . ')';
						}
						?></span>
					</a>
					<?php if ($canDo->get('core.edit.create')) { ?>
						&nbsp;
						<a class="state add" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=skus&task=add&pId=' . $row->pId); ?>">
							<span>[ + ]</span>
						</a>
					<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->pId); ?>" title="<?php echo Lang::txt('COM_STOREFRONT_SET_TASK', $task);?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php
					if ($this->config->get('productAccess'))
					{
						$db->setQuery("SELECT `agId` FROM `#__storefront_product_access_groups` WHERE `exclude`=0 AND `pId`=" . $db->quote($row->pId));
						$accessgroups = $db->loadColumn();
						$ag = array();
						foreach ($accessgroups as $access)
						{
							if (array_key_exists($access, $this->ag))
							{
								$ag[] = $this->ag[$access];
							}
						}
						echo Lang::txt('User is:') . ' ' . (!empty($ag) ? implode(', ', $ag) : Lang::txt('(none)'));

						$db->setQuery("SELECT `agId` FROM `#__storefront_product_access_groups` WHERE `exclude`=1 AND `pId`=" . $db->quote($row->pId));
						$accessgroups = $db->loadColumn();
						$ag = array();
						foreach ($accessgroups as $access)
						{
							if (array_key_exists($access, $this->ag))
							{
								$ag[] = $this->ag[$access];
							}
						}
						echo '<br />' . Lang::txt('User is not:') . ' ' . (!empty($ag) ? implode(', ', $ag) : Lang::txt('(none)'));
					}
					else
					{
						if (array_key_exists($row->access, $this->ag))
						{
							echo $this->ag[$row->access];
						}
						else
						{
							echo $this->ag[0];
						}
					}
					?>
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
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>