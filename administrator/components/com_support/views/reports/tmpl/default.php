<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'addedit.png' );

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

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('SHOW'); ?>:
			<select name="state" onchange="document.adminForm.submit( );">
				<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('OUTSTANDING'); ?></option>
				<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('RELEASED'); ?></option>
			</select>
		</label> 

		<label>
			<?php echo JText::_('SORT_BY'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
				<option value="a.category"<?php if ($this->filters['sortby'] == 'a.category') { echo ' selected="selected"'; } ?>><?php echo JText::_('CATEGORY'); ?></option>
				<option value="a.created DESC"<?php if ($this->filters['sortby'] == 'a.created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('MOST_RECENT'); ?></option>
			</select>
		</label> 
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('STATUS'); ?></th>
				<th><?php echo JText::_('REPORTED_ITEM'); ?></th>
				<th><?php echo JText::_('REASON'); ?></th>
				<th><?php echo JText::_('BY'); ?></th>
				<th><?php echo JText::_('DATE'); ?></th>
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	$status = '';
	switch ($row->state) 
	{
		case '1':
			$status = JText::_('RELEASED');
			break;
		case '0':
			$status = '<span class="yes">'.JText::_('NEW').'</span>';
			break;
	}

	$juser =& JUser::getInstance($row->created_by);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $status;  ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=abusereport&amp;id=<?php echo $row->id; ?>&amp;cat=<?php echo $row->category; ?>"><?php echo ($row->category.' #'.$row->referenceid); ?></a></td>
				<td><?php echo $row->subject; ?></td>
				<td><?php echo $juser->get('username');  ?></td>
				<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>	   
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="abusereports" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>