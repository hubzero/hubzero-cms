<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'MEETINGS' ), 'user.png' );
JToolBarHelper::preferences('com_meetings', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

$today = date('Y-m-d');

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
			<?php echo JText::_('SORT'); ?>:
			<select name="sortby" id="sortby">
				<option value="date_begin"<?php if ($this->filters['sortby'] == 'date_begin') { echo ' selected="selected"'; } ?>>Begins (desc)</option>
				<option value="date_end"<?php if ($this->filters['sortby'] == 'date_end') { echo ' selected="selected"'; } ?>>Ends (desc)</option>
				<option value="title"<?php if ($this->filters['sortby'] == 'title') { echo ' selected="selected"'; } ?>>Title</option>
				<option value="owner"<?php if ($this->filters['sortby'] == 'owner') { echo ' selected="selected"'; } ?>>Owner</option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('MEETINGS_COL_TITLE'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_BEGINS'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_ENDS'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_OWNER'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_STATUS'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_ACCESS'); ?></th>
				<th><?php echo JText::_('MEETINGS_COL_ID'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	if ($row->deleted != 'true') {
		if ($today <= $row->date_begin) {
			$status = 'pending';
			$statustext = JText::_('MEETING_STATUS_PENDING');
		} else {
			if ($today <= $row->date_end) {
				$status = 'published';
				$statustext = JText::_('MEETING_STATUS_ACTIVE');
			} else {
				$status = 'expired';
				$statustext = JText::_('MEETING_STATUS_EXPIRED');
			}
		}
	} else {
		$status = 'unpublished';
		$statustext = JText::_('MEETING_STATUS_DELETED');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id=<? echo $row->id; ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td><?php echo $row->date_begin;?></td>
				<td><?php echo $row->date_end;?></td>
				<td><?php echo $row->owner;?></td>
				<td><a class="<?php echo $status;?>" href="#"><span><?php echo $statustext;?></span></a></td>
				<td><?php echo $row->access;?></td>
				<td><?php echo $row->id; ?></td>
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