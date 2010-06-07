<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Tag/Group' ).' ]</small></small>', 'addedit.png' );
JToolBarHelper::addNew('newtg');
JToolBarHelper::editList('edittg');
JToolBarHelper::deleteList('','deletetg');

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist" id="tktlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('SUPPORT_COL_TAG'); ?></th>
				<th><?php echo JText::_('SUPPORT_COL_GROUP'); ?></th>
				<th colspan="3"><?php echo JText::_('SUPPORT_COL_PRIORITY'); ?></th>
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
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
	{
		$row = &$this->rows[$i];
		$row->position = null;
?>
			<tr>
				<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edittg&amp;id=<? echo $row->id; ?>"><?php echo $row->tag; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edittg&amp;id=<? echo $row->id; ?>"><?php echo $row->description.' ('.$row->cn.')'; ?></a></td>
				<td><?php echo $row->priority; ?></td>
				<td><?php echo $this->pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?></td>
				<td><?php echo $this->pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?></td>
			</tr>
<?php
		$k = 1 - $k;
	}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>