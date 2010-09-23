<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>: <small><small>[ Services ]</small></small>', 'addedit.png' );
//JToolBarHelper::addNew('newservice','New Service');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<h3><?php echo JText::_('Services'); ?></h3>
	<form action="index.php" method="post" name="adminForm">
		
		<table class="adminlist" summary="<?php echo JText::_('A list of paid/subscription-based HUB services'); ?>">
			<thead>
				<tr>
					<th width="2%" nowrap="nowrap"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
					<th width="5%" nowrap="nowrap"><?php echo JText::_('ID'); ?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('Title'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
                    <th><?php echo JHTML::_('grid.sort', JText::_('Category'), 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
                    <th><?php echo JHTML::_('grid.sort', JText::_('Status'), 'status', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
<?php
	$k = 0;
	$i = 0;
	foreach ($this->rows as $row) 
	{
?>
				<tr class="<?php echo "row$k"; ?>">
					<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
					<td><?php echo $row->id; ?></td>
                    <td><?php echo $row->title; ?></td>
					<td><?php echo $row->category; ?></td>
                    <td class="<?php echo $row->status==1 ? JText::_('active') : JText::_('inactive') ; ?>"><?php echo $row->status==1 ? JText::_('active') : JText::_('inactive') ; ?></td>
				</tr>
<?php
		$k = 1 - $k;
		$i++;
	}
?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="services" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>