<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'EVENTS' ).'</a>: <small><small>[ '.JText::_('PAGES').' ]</small></small>', 'addedit.png' );
JToolBarHelper::addNew( 'addpage', JText::_('ADD_PAGE'));
JToolBarHelper::editList( 'editpage' );
JToolBarHelper::deleteList( '', 'removepage', JText::_('REMOVE_PAGE') );

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<h2><?php echo stripslashes($this->event->title); ?></h2>
	
	<fieldset id="filter">
		<label>
			<?php echo JText::_('SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('TITLE'); ?></th>
				<th colspan="3"><?php echo JText::_('REORDER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->id; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=editpage&amp;id[]=<? echo $row->id; ?>&amp;event=<?php echo $this->event->id; ?>"><?php echo stripslashes($row->title).' ('.stripslashes($row->alias).')'; ?></a></td>
				<td><?php echo $this->pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position), 'orderuppage' ); ?></td>
				<td><?php echo $this->pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position), 'orderdownpage' ); ?></td>
				<td><?php echo $row->ordering; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="event" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>