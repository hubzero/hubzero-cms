<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( '<a href="index.php?option=com_sef">'.JText::_( 'SEF Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::preferences('com_sef', '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
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
			ViewMode:
			<?php echo $this->lists['viewmode'];?> 
		</label>
		
		<label>
			Sort by:
			<?php echo $this->lists['sortby'];?>
		</label>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>#</th>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th>Hits</th>
				<th><?php echo (($this->is404mode == true) ? 'Date Added' : '<acronym title="Search Engine Friendly">SEF</acronym> URL' ); ?></th>
				<th><?php echo (($this->is404mode == true) ? 'URL' : 'Real URL' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{ 
	$row =& $this->rows[$i];
?>
			<tr class="<?php echo 'row'. $k; ?>">
				<td><?php echo $i; ?></td>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->cpt; ?></td>
				<td><?php 
				if ($this->is404mode == true) {
   					echo $row->dateadd;
				} else { 
					?><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>"><?php echo $row->oldurl;?></a><?php 
				} ?></td>
				<td><?php 
   				if ($this->is404mode == true) {
   					?><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id;?>"><?php echo $row->oldurl;?></a><?php 
				} else {
					$row->newurl = str_replace('&','&amp;', $row->newurl);
					echo $row->newurl;
				} ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>