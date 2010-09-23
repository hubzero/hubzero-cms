<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_resources">'.JText::_( 'Resources' ).'</a>: <small><small>[ Types ]</small></small>', 'addedit.png' );
JToolBarHelper::addNew( 'newtype', 'New Type' );
JToolBarHelper::editList( 'edittype' );
JToolBarHelper::deleteList( '', 'deletetype', 'Delete' );

?>
<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label for="Category">
			<?php echo JText::_('Category'); ?>:
			<?php echo ResourcesHtml::selectType($this->cats, 'category', $this->filters['category'], 'Select...', '', '', ''); ?>
		</label>
	
		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>
	
	<table class="adminlist" summary="<?php echo JText::_('A list of resource types and their grouping'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
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
	$row = &$this->rows[$i];
	
	$cat_title = '';
	
	foreach ($this->cats as $cat)
	{
		if ($row->category == $cat->id) {
			$cat_title = $cat->type;
		}
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->id; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edittype&amp;id[]=<?php echo $row->id; ?>"><?php echo $row->type; ?></a></td>
				<td><?php echo $cat_title; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="viewtypes" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>