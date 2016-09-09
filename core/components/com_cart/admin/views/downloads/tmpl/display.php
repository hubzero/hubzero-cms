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

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('download');

Toolbar::title(Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'), 'cart.png');
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

Toolbar::custom('download', 'download.png', '', 'Download CSV', false);

Toolbar::spacer();
Toolbar::help('downloads');
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

<?php
$this->view('_submenu')
	->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php
	if (!empty($this->filters['skuRequested']))
	{
	?>
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			&nbsp;
		</div>
		<div class="col width-50 fltrt">
			<select name="skuRequested" id="skuRequested" onchange="this.form.submit();">
				<option value="0"<?php if ($this->filters['skuRequested'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('All SKUs'); ?></option>
				<option value="<?php echo $this->filters['skuRequested']; ?>" selected="selected"><?php echo $this->skuRequestedName; ?></option>
			</select>
		</div>
	</fieldset>
	<?php
	}
	?>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_PRODUCT', 'product', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_DOWNLOADED_BY', 'dName', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CART_USER_INFO'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_CART_EULA'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_DOWNLOADED', 'dDownloaded', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">IP</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_CART_STATUS', 'dStatus', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="8"><?php
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

foreach ($this->rows as $row)
{
	switch ($row->dStatus)
	{
		case 1:
			$class = 'publish';
			$task = 'inactive';
			$alt = Lang::txt('COM_CART_ACTIVE');
			break;
		case 0:
			$class = 'unpublish';
			$task = 'active';
			$alt = Lang::txt('COM_CART_INACTIVE');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->dId; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php
					$product = '<a href="' . Route::url('index.php?option=com_storefront&controller=products&task=edit&id=' . $row->pId) . '" target="_blank">' . $this->escape(stripslashes($row->pName)) . '</a>';
					if (!stripslashes($row->pName))
					{
						$product = '<span class="missing">Product n/a</span>';
					}
					if (!stripslashes($row->sSku))
					{
						$product .= ', <span class="missing">SKU n/a</span>';
					}
					else {
						$product .= ', ' . '<a href="' . Route::url('index.php?option=com_storefront&controller=skus&task=edit&id=' . $row->sId) . '" target="_blank">' . $this->escape(stripslashes($row->sSku)) . '</a>';
					}
					?>
					<span><?php echo $product; ?></span>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->dName)) . ' / ' . $this->escape(stripslashes($row->username)); ?></span>
				</td>
				<td>
					<?php
					if ($row->meta)
					{
						if (array_key_exists('userInfo', $row->meta) && $row->meta['userInfo'])
						{
							$meta = unserialize($row->meta['userInfo']['mtValue']);
							$data = array();
							foreach ($meta as $mtK => $mtV)
							{
								if (is_array($mtV))
								{
									$mtV = implode('; ', $mtV);
								}
								$data[] = $mtV;
							}
							echo implode(', ', $data);
						}
					}
					else {
						echo '&nbsp;';
					}
					?>
				</td>
				<td>
					<?php
					if ($row->meta)
					{
						if (array_key_exists('eulaAccepted', $row->meta) && $row->meta['eulaAccepted'])
						{
							if ($row->meta['eulaAccepted']['mtValue'])
							{
								echo 'EULA accepted';
							}
						}
					}
					else {
						echo '&nbsp;';
					}
					?>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->dDownloaded)); ?></span>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->dIp)); ?></span>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->dId) ?>" title="<?php echo Lang::txt('COM_CART_SET_TASK', $task);?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
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

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>