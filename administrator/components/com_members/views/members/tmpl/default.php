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

include_once(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'html'.DS.'grid.php');
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
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', 'ID', 'uidNumber', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Name', 'surname', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Username', 'username', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'Organization', 'org', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'E-Mail', 'email', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', '# of contributions', 'rcount', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<!-- <th><?php echo JHTML::_('grid.sort', 'Last Visit', 'lastvisitDate', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th> -->
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
	
	if (!$row->lastvisitDate || $row->lastvisitDate == "0000-00-00 00:00:00") {
		$lvisit = JText::_( 'Never' );
	} else {
		$lvisit	= $row->lastvisitDate; //= JHTML::_('date',  $row->lastvisitDate, JText::_('DATE_FORMAT_LC4'));
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->uidNumber ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->uidNumber; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<? echo $row->uidNumber; ?>"><?php echo stripslashes($row->surname).', '.stripslashes($row->givenName).' '.stripslashes($row->middleName); ?></a></td>
				<td><?php echo $row->username; ?></td>
				<td><?php echo ($row->organization) ? stripslashes($row->organization) : '&nbsp;';?></td>
				<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
				<td><?php echo $row->rcount; ?></td>
				<!-- <td><?php echo $lvisit; ?></td> -->
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
	
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>