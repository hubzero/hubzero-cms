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
//print_r($this->sku->getId()); die;

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': SKUs\' permitted users', 'storefront.png');

Toolbar::appendButton('Popup', 'new', 'New', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&id=' . $this->sku->getId(), 570, 170);
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList();
}

JToolBarHelper::spacer();
//Toolbar::custom('upload', 'upload.png', '', 'Upload CSV', false);
Toolbar::appendButton('Popup', 'upload', 'Upload CSV', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=upload&id=' . $this->sku->getId(), 570, 170);
JToolBarHelper::spacer();
JToolBarHelper::cancel();

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan=5">
					Users for: <a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=skus&task=edit&id=' . $this->sku->getId()); ?>" title="<?php echo Lang::txt('Edit SKU'); ?>"><?php echo $this->sku->getName(); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo $this->grid('sort', 'ID', 'uId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'Name', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col">Email</th>
			</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="5"><?php
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
//for ($i=0, $n=count($this->rows); $i < $n; $i++)
$i = 0;

foreach ($this->rows as $row)
{
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<span>
						<?php echo $this->escape(stripslashes($row->uId)); ?>
					</span>
				</td>
				<td>
					<span>
						<?php echo $this->escape(stripslashes($row->name)) . ' (' . $this->escape(stripslashes($row->username)) . ')'; ?>
					</span>
				</td>
				<td>
					<span>
						<?php echo $this->escape(stripslashes($row->email)); ?>
					</span>
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
	<input type="hidden" name="sId" value="<?php echo $this->sId; ?>" />
	<input type="hidden" name="pId" value="<?php echo $this->sku->getProductId(); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>