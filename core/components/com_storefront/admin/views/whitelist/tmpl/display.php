<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Html\Builder\Behavior;

defined('_HZEXEC_') or die();


$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': SKU\'s whitelisted users', 'storefront');

Toolbar::appendButton('Popup', 'new', 'Add Users', \Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&tmpl=component&task=new&id=' . $this->sku->getId()), 570, 170);
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
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
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
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
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
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>