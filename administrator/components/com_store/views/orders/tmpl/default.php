<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';

JToolBarHelper::title( JText::_( 'Store Manager' ).$text, 'addedit.png' );
JToolBarHelper::preferences('com_store', '550');

?>
<script type="text/javascript">
public function submitbutton(pressbutton) 
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

<p class="extranav"><?php echo JText::_('VIEW'); ?>: <strong><?php echo JText::_('ORDERS'); ?></strong> | <a href="index.php?option=<?php echo $this->option; ?>&amp;task=storeitems"><?php echo JText::_('STORE'); ?> <?php echo JText::_('ITEMS'); ?></a></p>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
	    <?php echo count($this->rows); ?> <?php echo JText::_('ORDERS_DISPLAYED'); ?>.
		<label>
			<?php echo JText::_('FILTERBY'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="new"<?php if ($this->filters['filterby'] == 'new') { echo ' selected="selected"'; } ?>><?php echo JText::_('NEW'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
				<option value="processed"<?php if ($this->filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo JText::_('COMPLETED'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
	    		<option value="cancelled"<?php if ($this->filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo JText::_('CANCELLED'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL'); ?> <?php echo ucfirst(JText::_('ORDERS')); ?></option>
			</select>
		</label> 

		<label>
			<?php echo JText::_('SORTBY'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
	    		<option value="m.ordered"<?php if ($this->filters['sortby'] == 'm.ordered') { echo ' selected="selected"'; } ?>><?php echo JText::_('ORDER_DATE'); ?></option>
				<option value="m.status_changed"<?php if ($this->filters['sortby'] == 'm.status_changed') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_STATUS_CHANGE'); ?></option>
				<option value="m.id DESC"<?php if ($this->filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('ORDER')).' '.strtoupper(JText::_('ID')); ?></option>			
			</select>
		</label> 
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo strtoupper(JText::_('ID')); ?></th>
				<th><?php echo JText::_('STATUS'); ?></th>
				<th><?php echo JText::_('ORDERED_ITEMS'); ?></th>
				<th><?php echo JText::_('TOTAL'); ?> (<?php echo JText::_('POINTS'); ?>)</th>
				<th><?php echo JText::_('BY'); ?></th>
				<th><?php echo JText::_('DATE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	$status = '';
	switch ($row->status) 
	{
		case '1':
			$status = strtolower(JText::_('COMPLETED'));
			break;
		case '0':
			$status = '<span class="yes">'.strtolower(JText::_('NEW')).'</span>';
			break;
		case '2':
			$status = '<span style="color:#999;">'.strtolower(JText::_('CANCELLED')).'</span>';
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ORDER'); ?>"><?php echo $row->id; ?></a></td>
				<td><?php echo $status;  ?></td>
				<td><?php echo $row->itemtitles; ?></td>
				<td><?php echo $row->total; ?></td>
				<td><?php echo $row->author;  ?></td>
				<td><?php echo JHTML::_('date', $row->ordered, '%d %b, %Y'); ?></td>	   
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ORDER'); ?>"><?php echo JText::_('DETAILS'); ?></a><?php if ($row->status!=2) { echo '&nbsp;&nbsp;|&nbsp;&nbsp; <a href="index.php?option='.$this->option.'&amp;task=receipt&amp;id='.$row->id.'">'.JText::_('Receipt').'</a>'; } ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<?php echo $this->pageNav->getListFooter(); ?>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>