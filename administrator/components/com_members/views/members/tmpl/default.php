<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$juser = & JFactory::getUser();

JToolBarHelper::title( JText::_( 'MEMBERS' ), 'user.png' );
JToolBarHelper::preferences('com_members', '550');
if ($juser->authorize( 'com_members', 'admin' )) {
	JToolBarHelper::addNew();
}
JToolBarHelper::editList();
if ($juser->authorize( 'com_members', 'admin' )) {
	JToolBarHelper::deleteList();
}

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
			<?php echo JText::_('SEARCH'); ?>
			<select name="search_field">
				<option value="uidNumber"<?php if ($this->filters['search_field'] == 'uidNumber') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
				<option value="email"<?php if ($this->filters['search_field'] == 'email') { echo ' selected="selected"'; } ?>><?php echo JText::_('EMAIL'); ?></option>
				<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
				<option value="surname"<?php if ($this->filters['search_field'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
				<option value="givenName"<?php if ($this->filters['search_field'] == 'giveName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
				<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo JText::_('FULL_NAME'); ?></option>
			</select>
			for 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		
	
		<label>
			<?php echo JText::_('SORT_BY'); ?>:
			<select name="sortby">
				<option value="uidNumber DESC"<?php if ($this->filters['sortby'] == 'uidNumber DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID DESC'); ?></option>
				<option value="uidNumber ASC"<?php if ($this->filters['sortby'] == 'uidNumber ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID ASC'); ?></option>
				<option value="username ASC"<?php if ($this->filters['sortby'] == 'username ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('USERNAME'); ?></option>
				<option value="surname"<?php if ($this->filters['sortby'] == 'surname') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_NAME'); ?></option>
				<option value="givenName"<?php if ($this->filters['sortby'] == 'givenName') { echo ' selected="selected"'; } ?>><?php echo JText::_('FIRST_NAME'); ?></option>
				<option value="org"<?php if ($this->filters['sortby'] == 'org') { echo ' selected="selected"'; } ?>><?php echo JText::_('ORGANIZATION'); ?></option>
				<option value="total DESC"<?php if ($this->filters['sortby'] == 'total DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('NUMBER_OF_CONTRIBUTIONS'); ?></option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('NAME'); ?></th>
				<th><?php echo JText::_('USERNAME'); ?></th>
				<th><?php echo JText::_('ORGANIZATION'); ?></th>
				<th><?php echo JText::_('VIP'); ?></th>
				<th><?php echo JText::_('NUMBER_OF_CONTRIBUTIONS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
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
	
	if (!$row->surname && !$row->givenName) {
		$bits = explode(' ', $row->name);
		$row->surname = array_pop($bits);
		if (count($bits) >= 1) {
			$row->givenName = array_shift($bits);
		}
		if (count($bits) >= 1) {
			$row->middleName = implode(' ',$bits);
		}
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->uidNumber ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->uidNumber; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<? echo $row->uidNumber; ?>"><?php echo stripslashes($row->surname).', '.stripslashes($row->givenName).' '.stripslashes($row->middleName); ?></a></td>
				<td><?php echo $row->username; ?></td>
				<td><?php echo ($row->organization) ? stripslashes($row->organization) : '&nbsp;';?></td>
				<td><?php echo ($row->vip == 1) ? '<span class="check">'.JText::_('YES').'</span>' : '&nbsp;'; ?></td>
				<td><?php echo $row->rcount; ?></td>
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