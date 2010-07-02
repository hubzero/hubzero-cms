<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'TAGS' ), 'addedit.png' );
JToolBarHelper::preferences('com_tags', '550');
JToolBarHelper::spacer();
JToolBarHelper::custom( 'pierce', 'copy', '', JText::_('PIERCE'), false );
JToolBarHelper::custom( 'merge', 'forward', '', JText::_('MERGE'), false );
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();

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
			<?php echo JText::_('SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>

		<label>
			<?php echo JText::_('FILTER'); ?>:
			<select name="filterby" onchange="document.adminForm.submit();">
				<option value="all"<?php if ($this->filters['by'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_ALL_TAGS'); ?></option>
				<option value="user"<?php if ($this->filters['by'] == 'user') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_USER_TAGS'); ?></option>
				<option value="admin"<?php if ($this->filters['by'] == 'admin') { echo ' selected="selected"'; } ?>><?php echo JText::_('FILTER_ADMIN_TAGS'); ?></option>
			</select>
		</label>

		<input type="submit" name="filter_submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('RAW_TAG'); ?></th>
				<th><?php echo JText::_('TAG'); ?></th>
				<th><?php echo JText::_('ALIAS'); ?></th>
				<th><?php echo JText::_('ADMIN'); ?></th>
				<th><?php echo JText::_('NUMBER_TAGGED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
//JPluginHelper::importPlugin('tags');
//$dispatcher =& JDispatcher::getInstance();
$database =& JFactory::getDBO();
$to = new TagsObject( $database );

$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	$now = date( "Y-m-d H:i:s" );
	$check = '';
	if ($row->admin == 1) {
		$check = '<span class="check">'.strToLower( JText::_('ADMIN') ).'</span>';
	}
	
	/*$totals = $dispatcher->trigger( 'onTagCount', array($row->id) );
	$total = 0;
	foreach ($totals as $t) 
	{
		$total = $total + $t;
	}*/
	$total = $to->getCount( $row->id );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->raw_tag); ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->tag); ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id=<?php echo $row->id;?>"><?php echo stripslashes($row->alias); ?></a></td>
				<td><?php echo $check; ?></td>
				<td><?php echo $total; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>