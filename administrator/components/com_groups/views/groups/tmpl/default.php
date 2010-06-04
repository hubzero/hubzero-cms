<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'GROUPS' ), 'user.png' );
JToolBarHelper::preferences('com_groups', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList('delete','delete');

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
	<fieldset id="filter">
		<label>
			<?php echo JText::_('SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
		<label>
			<?php echo JText::_('TYPE'); ?>:
			<select name="type">
				<option value="all"<?php echo ($this->filters['type'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('ALL'); ?></option>
				<option value="hub"<?php echo ($this->filters['type'] == 'hub') ? ' selected="selected"' : ''; ?>>hub</option>
				<option value="system"<?php echo ($this->filters['type'] == 'system') ? ' selected="selected"' : ''; ?>>system</option>
				<option value="project"<?php echo ($this->filters['type'] == 'project') ? ' selected="selected"' : ''; ?>>project</option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('CN'); ?></th>
				<th><?php echo JText::_('NAME'); ?></th>
				<th><?php echo JText::_('TYPE'); ?></th>
				<th><?php echo JText::_('PUBLISHED'); ?></th>
				<th><?php echo JText::_('APPLICANTS'); ?></th>
				<th><?php echo JText::_('INVITEES'); ?></th>
				<th><?php echo JText::_('MANAGERS'); ?></th>
				<th><?php echo JText::_('TOTAL_MEMBERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	$group = new XGroup();
	$group->gidNumber = $row->gidNumber;
	$group->cn = $row->cn;
	
	$applicants = count($group->get('applicants'));
	$invitees   = count($group->get('invitees'));
	$managers   = count($group->get('managers'));
	$members    = count($group->get('members'));
	
	switch ($row->type) 
	{
		case '0': $type = 'system';  break;
		case '1': $type = 'hub';     break;
		case '2': $type = 'project'; break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->cn ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->gidNumber; ?></td>
				<td><?php echo $row->cn; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<? echo $row->cn; ?>"><?php echo stripslashes($row->description); ?></a></td>
				<td><?php echo $type; ?></td>
				<td><?php echo ($row->published) ? '<span class="check">'.JText::_('YES').'</span>' : '&nbsp;'; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $applicants; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $invitees; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $managers; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $members; ?></a></td>
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