<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Success Story Manager' ), 'addedit.png' );
JToolBarHelper::preferences('com_feedback', '550');
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
		
<?php 
if ($this->type == 'regular') {	
	echo ('<h3>'.JText::_('FEEDBACK_SUBMITTED_QUOTES').'</h3><p class="extranav"><a href="index.php?option='.$this->option.'&amp;type=selected">'.JText::_('FEEDBACK_SELECTED_QUOTES').'</a>.</p>');
} else {
	echo ('<h3>'.JText::_('FEEDBACK_SELECTED_QUOTES').'</h3><p class="extranav"><a href="index.php?option='.$this->option.'">'.JText::_('FEEDBACK_SUBMITTED_QUOTES').'</a>.</p>');
}
?>
	<fieldset id="filter">
		<label>
			<?php echo JText::_('FEEDBACK_SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
	
		<label>
			<?php echo JText::_('FEEDBACK_SORT'); ?>: 
			<select name="sortby" id="sortby">
				<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_DATE'); ?></option>
				<option value="fullname"<?php if ($this->filters['sortby'] == 'fullname') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_NAME'); ?></option>
				<option value="org"<?php if ($this->filters['sortby'] == 'org') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_ORGANIZATION'); ?></option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>#</th>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('FEEDBACK_COL_SUBMITTED'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_AUTHOR'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_ORGANIZATION'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_QUOTE'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_PICTURE'); ?></th>
<?php 		
if ($this->type == 'regular') {	
	echo ('<th>'.JText::_('FEEDBACK_COL_PUBLISH_CONSENT').'</th><th>'.JText::_('FEEDBACK_COL_UID').'</th>');
} else {
	echo ('<th>'.JText::_('FEEDBACK_COL_QUOTES').'</th><th>'.JText::_('FEEDBACK_COL_ROTATION').'</th>');
}
?>   
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];

	//cut quote at 100 characters
	$quotepreview = stripslashes($row->quote);
	$quotepreview = substr($quotepreview, 0, 100);
	if (strlen ($quotepreview)>=99) {
		$quotepreview = $quotepreview.'...';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $i; ?></td>
				<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onClick="isChecked(this.checked);" /></td>
				<td><?php echo JHTML::_('date', $row->date, '%d %b, %Y'); ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>"><?php echo stripslashes($row->fullname); ?></a></td>
				<td><?php echo ($row->org) ? stripslashes($row->org) : '&nbsp;';?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>"><?php echo $quotepreview;?></a></td>
				<td><?php echo ($row->picture != NULL) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '&nbsp;'; ?></td>
				<td><?php if ($this->type == 'regular') {
						echo ($row->publish_ok == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} else {
						echo ($row->notable_quotes == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} ?></td>
				<td><?php if ($this->type == 'regular') {
						echo $row->userid;
					} else {
						echo ($row->flash_rotation == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} ?></td>
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
	<input type="hidden" name="type" value="<?php echo $this->type ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>