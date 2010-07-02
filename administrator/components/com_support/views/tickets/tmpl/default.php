<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'Tickets' ).' ]</small></small>', 'addedit.png' );
JToolBarHelper::preferences('com_support', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();


if ($this->filters['_show'] != '') {
	$fstring = urlencode(trim($this->filters['_show']));
} else {
	$fstring = urlencode(trim($this->filters['_find']));
}

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=<?php echo $this->option; ?>" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('SUPPORT_FIND'); ?>:
			<input type="text" name="find" id="find" value="<?php echo ($this->filters['_show'] == '') ? htmlentities($this->filters['_find']) : ''; ?>" />
		</label>
		
		<a title="<?php echo JText::_('SUPPORT_KEYWORD_GUIDE'); ?>::<table id='keyword-guide' summary='<?php echo JText::_('SUPPORT_KEYWORD_TBL_SUMMARY'); ?>'>
			<tbody>
				<tr>
					<th>q:</th>
					<td>&quot;search term&quot;</td>
				</tr>
				<tr>
					<th>status:</th>
					<td>new, open, waiting, closed, all</td>
				</tr>
				<tr>
					<th>reportedby:</th>
					<td>me, [username]</td>
				</tr>
				<tr>
					<th>owner:</th>
					<td>me, none, [username]</td>
				</tr>
				<tr>
					<th>severity:</th>
					<td>critical, major, normal, minor, trivial</td>
				</tr>
				<tr>
					<th>type:</th>
					<td>automatic, submitted, tool</td>
				</tr>
				<tr>
					<th>tag:</th>
					<td>[tag]</td>
				</tr>
				<tr>
					<th>group:</th>
					<td>[group]</td>
				</tr>
			</tbody>
		</table>" class="editlinktip hasTip" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=tickets#help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
		
		<span><?php echo JText::_('OR'); ?></span>
		
		<label>
			<?php echo JText::_('SHOW'); ?>:
			<select name="show">
				<option value=""<?php if ($this->filters['_show'] == '') { echo ' selected="selected"'; } ?>>--</option>
				<option value="status:new"<?php if ($this->filters['_show'] == 'status:new') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_NEW'); ?></option>
				<option value="status:open"<?php if ($this->filters['_show'] == 'status:open') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_OPEN'); ?></option>
				<option value="owner:none"<?php if ($this->filters['_show'] == 'owner:none') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_UNASSIGNED'); ?></option>
				<option value="status:waiting"<?php if ($this->filters['_show'] == 'status:waiting') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_WAITING'); ?></option>
				<option value="status:closed"<?php if ($this->filters['_show'] == 'status:closed') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_CLOSED'); ?></option>
				<option value="status:all"<?php if ($this->filters['_show'] == 'status:all') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_ALL'); ?></option>
				<option value="reportedby:me"<?php if ($this->filters['_show'] == 'reportedby:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_REPORTED_BY_ME'); ?></option>
				<option value="status:open owner:me"<?php if ($this->filters['_show'] == 'status:open owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_OPEN'); ?></option>
				<option value="status:closed owner:me"<?php if ($this->filters['_show'] == 'status:closed owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_CLOSED'); ?></option>
				<option value="status:all owner:me"<?php if ($this->filters['_show'] == 'status:all owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_ALL'); ?></option>
			</select>
		</label>
		
		<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sortdir']; ?>" />
		
		<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
	</fieldset>

	<table class="adminlist" id="tktlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_NUM'), 'id', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_SUMMARY'), 'summary', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_STATUS'), 'status', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_GROUP'), 'group', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_OWNER'), 'owner', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_AGE'), 'created', $this->filters['sortdir'], $this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('SUPPORT_COL_COMMENTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
ximport('Hubzero_View_Helper_Html');
		
$k = 0;
$database =& JFactory::getDBO();
$sc = new SupportComment( $database );
$st = new SupportTags( $database );

for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	$comments = $sc->countComments(true, $row->id);
	$lastcomment = '0000-00-00 00:00:00';
	if ($comments > 0) {
		$lastcomment = $sc->newestComment(true, $row->id);
	}

	if ($row->status == 2) {
		$status = 'closed';
	} elseif ($comments == 0 && $row->status == 0 && $row->owner == '' && $row->resolved == '') {
		$status = 'new';
	} elseif ($row->status == 1) {
		$status = 'waiting';
	} else {
		if ($row->resolved != '') {
			$status = 'reopened';
		} else {
			$status = 'open';
		}
	}
	
	if ($row->owner == '') {
		$row->owner = '&nbsp';
	}
	
	$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
			<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id; ?></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id=<? echo $row->id; echo ($fstring) ? '&amp;find='.$fstring : ''; ?>"><?php echo stripslashes($row->summary); ?></a>
					<span class="reporter">by <?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]='.$row->login.'">'.$row->login.'</a>)' : ''; ?>, tags: <?php echo $tags; ?></span>
				</td>
				<td><span class="<?php echo $status; ?> status"><?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' ('.$row->resolved.')' : ''; ?></span></td>
				<td><?php echo $row->group; ?></td>
				<td><?php echo $row->owner; ?></td>
				<td><?php echo Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($row->created)); ?></td>
				<td><?php echo $comments; echo ($comments > 0) ? ' ('.Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($lastcomment)).')' : ''; ?></td>
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