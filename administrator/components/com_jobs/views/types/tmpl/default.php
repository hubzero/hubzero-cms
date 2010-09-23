<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_jobs">'.JText::_( 'Jobs Manager' ).'</a>: <small><small>[ Types ]</small></small>', 'addedit.png' );
JToolBarHelper::addNew( 'newtype', 'New Type' );
JToolBarHelper::editList( 'edittype' );
JToolBarHelper::deleteList( '', 'deletetype', 'Delete' );

?>
<h3><?php echo JText::_('Job Types'); ?></h3>
<form action="index.php" method="post" name="adminForm">
	<table class="adminlist" summary="<?php echo JText::_('A list of job types'); ?>">
		<thead>
			<tr>
				<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
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
$i = 0;
foreach ($this->rows as $avalue => $alabel) 
{
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $avalue ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $avalue; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edittype&amp;id[]=<?php echo $avalue; ?>"><?php echo $alabel; ?></a></td>
			</tr>
<?php
	$k = 1 - $k;
	$i++;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="types" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>