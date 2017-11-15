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

use Hubzero\Html\Builder\Behavior;

defined('_HZEXEC_') or die();


$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': SKU\'s whitelisted users', 'storefront');

Toolbar::appendButton('Popup', 'new', 'Add Users', 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&id=' . $this->sku->getId(), 570, 170);
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

Toolbar::spacer();
Toolbar::cancel();

App::get('document')->addScriptDeclaration(
	'jQuery(document).ready(function($) {
		$(\'a[data-title="Add Users"]\').removeClass(\'modal\').addClass(\'specialPop\');
	});'
);
Behavior::modal('a.specialPop', array('onHide' => '\\function() { refreshMe(); }'));

?>
<script type="text/javascript">
	function refreshMe()
	{
		location.reload();
	}

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

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="5">
					Whitelisted users for: <a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=skus&task=edit&id=' . $this->sku->getId()); ?>" title="<?php echo Lang::txt('Edit SKU'); ?>"><?php echo $this->sku->getName(); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'ID', 'uId', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'Name', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
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
		$i = 0;

		foreach ($this->rows as $row)
		{
			if (!$row->uId)
			{
				$row->uId   = '--';
				$row->name  = '[UNREGISTERED]';
				$row->email = '--';
				$row->username = $row->uName;
			}
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

	<?php echo Html::input('token'); ?>
</form>