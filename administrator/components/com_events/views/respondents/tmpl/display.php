<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b. %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M. Y';
	$tz = false;
}

JToolBarHelper::title( '<a href="index.php?option=com_events">'.JText::_( 'COM_EVENTS' ).'</a>: <small><small>[ '.JText::_('COM_EVENTS_RESPONDANTS').' ]</small></small>', 'user.png' );
JToolBarHelper::custom('download', 'upload', JText::_('COM_EVENTS_DOWNLOAD_CSV'), JText::_('COM_EVENTS_DOWNLOAD_CSV'), false, false);
JToolBarHelper::deleteList( '', 'remove', JText::_('Delete') );
JToolBarHelper::cancel();

$rows = $this->resp->getRecords();
$pageNav = $this->resp->getPaginator();
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

<form action="" method="post" name="adminForm" id="adminForm">
	<h2><?php echo stripslashes($this->event->title); ?></h2>
	
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->resp->getSearchTerms(); ?>" />

		<label for="filter_sortby"><?php echo JText::_('SORT'); ?>:</label>
		<select name="sortby" id="filter_sortby">
			<option value="id DESC"<?php if ($this->resp->getOrdering() == 'id DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_ID_DESC'); ?></option>
			<option value="id ASC"<?php if ($this->resp->getOrdering() == 'id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_ID_ASC'); ?></option>
			<option value="name DESC"<?php if ($this->resp->getOrdering() == 'name DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_NAME_DESC'); ?></option>
			<option value="name ASC"<?php if ($this->resp->getOrdering() == 'name ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_NAME_ASC'); ?></option>
			<option value="special DESC"<?php if ($this->resp->getOrdering() == 'special DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_SPECIAL_DESC'); ?></option>
			<option value="special ASC"<?php if ($this->resp->getOrdering() == 'special ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_SPECIAL_ASC'); ?></option>
			<option value="registered DESC"<?php if ($this->resp->getOrdering() == 'registered DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_REGISTERED_DESC'); ?></option>
			<option value="registered ASC"<?php if ($this->resp->getOrdering() == 'registered ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_EVENTS_REGISTERED_ASC'); ?></option>
		</select>

		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
 			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
				<th scope="col"><?php echo JText::_('COM_EVENTS_NAME'); ?></th>
				<th scope="col"><?php echo JText::_('COM_EVENTS_EMAIL'); ?></th>
				<th scope="col"><?php echo JText::_('COM_EVENTS_REGISTERED'); ?></th>
				<th scope="col"><?php echo JText::_('COM_EVENTS_SPECIAL_NEEDS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_EVENTS_COMMENT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
/*$k = 0;
foreach ($rows as $idx=>&$row)
{
	if (!@$row->id) continue;*/
$k = 0;
for ($i=0, $n=count( $rows ); $i < $n; $i++) 
{
	$row = &$rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="rid[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=respondent&amp;id=<?php echo $row->id; ?>&amp;event_id=<?php echo $this->event->id; ?>"><?php echo $this->escape(stripslashes($row->last_name . ', ' . $row->first_name)); ?></a></td>
				<td><a href="mailto:<?php echo $row->email ?>"><?php echo $this->escape($row->email); ?></a></td>
				<td><?php echo JHTML::_('date', $row->registered, $dateFormat, $tz); ?></td>
				<td><?php 
				if (!empty($row->dietary_needs)) {
					echo 'Dietary needs: '.$this->escape($row->dietary_needs).'<br />';
				}
				if ($row->disability_needs) {
					echo 'Disability consideration requested';
				}
				?></td>
				<td><?php echo $this->escape($row->comment); ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="workshop" value="<?php $id = JRequest::getVar('id', array()); echo is_array($id) ? implode(',', $id) : $id; ?>" />
	<input type="hidden" name="id[]" value="<?php $id = JRequest::getVar('id', array()); echo is_array($id) ? implode(',', $id) : $id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
