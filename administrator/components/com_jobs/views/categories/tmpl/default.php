<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>: <small><small>[ Categories ]</small></small>', 'addedit.png' );
JToolBarHelper::addNew( 'newcat', 'New Category' );
JToolBarHelper::editList( 'editcat' );
JToolBarHelper::save( 'saveorder', 'Save Order' );
JToolBarHelper::deleteList( '', 'deletecat', 'Delete' );

?>
<h3><?php echo JText::_('Job Categories'); ?></h3>
<form action="index.php" method="post" name="adminForm">
	<table class="adminlist" summary="<?php echo JText::_('A list of job categories'); ?>">
		<thead>
			<tr>
				<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
                <th width="8%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', JText::_('Order'), 'ordernum', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
                </th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row =& $this->rows[$i];
	
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td class="order"><?php echo $row->id; ?></td>
                <td class="order" nowrap="nowrap">
					<input type="text" name="order[<?php echo $row->id; ?>]" size="5" value="<?php echo $row->ordernum; ?>"  class="text_area" style="text-align: center" />
                </td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=editcat&amp;id[]=<?php echo $row->id; ?>"><?php echo $row->category; ?></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="categories" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>