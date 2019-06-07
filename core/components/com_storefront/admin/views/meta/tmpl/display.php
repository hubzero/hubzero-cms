<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

Toolbar::title(Lang::txt('COM_STOREFRONT'), 'storefront');
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
Toolbar::help('categories');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_PRODUCT_TYPE', 'ptName', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STOREFRONT_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
//for ($i=0, $n=count($this->rows); $i < $n; $i++)
$i = 0;

foreach ($this->rows as $row)
{
	//print_r($row); die;
	//$row =& $this->rows[$i];
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
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->pId; ?>" class="checkbox-toggle" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="-glyph -product" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->pId); ?>" title="<?php echo Lang::txt('COM_STOREFRONT_EDIT_PRODUCT'); ?>">
						<span><?php echo $this->escape(stripslashes($row->pName)); ?></span>
					</a>
				<?php } else { ?>
					<span class="-glyph -product">
						<span><?php echo $this->escape(stripslashes($row->pName)); ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo $row->ptName; ?>
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
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="access <?php echo $color_access; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->pId); ?>" title="<?php echo Lang::txt('COM_STOREFRONT_CHANGE_ACCESS'); ?>">
							<?php echo $row->access; ?>
						</a>
					<?php } else { ?>
						<span class="access <?php echo $color_access; ?>">
						<?php echo $row->access; ?>
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
	<input type="hidden" name="cid" value="<?php echo $this->filters['section']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>