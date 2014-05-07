<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Tools' ), 'tools.png' );
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('hosttypes');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Name', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Bit#', 'value', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Description', 'description', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('References'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows) 
{
	$i = 0;
	foreach ($this->rows as $row)
	{
		if ($row->value > 0) 
		{
			$bit = log($row->value)/log(2);
		} 
		else 
		{
			$bit = '';
		}
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->name; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;item=<?php echo $row->name; ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
				</td>
				<td>
					<?php echo $bit; ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;item=<?php echo $row->name; ?>">
						<span><?php echo $this->escape($row->description); ?></span>
					</a>
				</td>
				<td>
					<?php echo $row->refs; ?>
				</td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>