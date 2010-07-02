<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'EVENTS_MANAGER' ), 'addedit.png' );
JToolBarHelper::addNew( 'addpage', 'Add Page');
JToolBarHelper::custom('viewList', 'assign', JText::_( 'VIEW_RESPONDENTS' ), JText::_( 'VIEW_RESPONDENTS' ), true, false);
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

$juser =& JFactory::getUser();

JHTML::_('behavior.tooltip');
?>

<form action="index.php?option=<?php echo $this->option; ?>" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('EVENTS_SEARCH'); ?>:
			<input type="text" name="search" value="<?php echo htmlentities($this->filters['search'], ENT_COMPAT, 'UTF-8'); ?>" />
		</label>
	
		<?php echo $this->clist; ?>
		
		<input type="submit" name="submitsearch" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ID'); ?></th>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TITLE'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEAT'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ANNOUNCEMENT'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TIMESHEET'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CHECKEDOUT'); ?></th>
				<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
				<th><?php echo JText::_('Pages'); ?></th>
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
$database =& JFactory::getDBO();
$p = new EventsPage( $database );
for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $row->id; ?></td>
				<td><?php 
				if ($row->checked_out && $row->checked_out != $juser->get('id')) { 
					echo '&nbsp;'; 
				} else { 
					?><input type="checkbox" id="cb<?php echo $i;?>" name="id[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /><?php 
				} ?></td>
				<td><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td><?php echo $row->category; ?></td>

<td><?php
	if ($row->reccurtype > 0) {
		switch ($row->reccurtype) 
		{
			case "1": $reccur = JText::_('EVENTS_CAL_LANG_REP_WEEK');  break;
			case "2": $reccur = JText::_('EVENTS_CAL_LANG_REP_WEEK');  break;
			case "3": $reccur = JText::_('EVENTS_CAL_LANG_REP_MONTH'); break;
			case "4": $reccur = JText::_('EVENTS_CAL_LANG_REP_MONTH'); break;
			case "5": $reccur = JText::_('EVENTS_CAL_LANG_REP_YEAR');  break;
		}
		if ($row->reccurday >= 0) {
			$dayname = EventsHtml::getLongDayName($row->reccurday);
			
			if (($row->reccurtype == 1) || ($row->reccurtype == 2)) {
				//$pairorimpair = $row->reccurweeks == "pair" ? _CAL_LANG_REP_WEEKPAIR : ($row->reccurweeks == "impair" ? _CAL_LANG_REP_WEEKIMPAIR : _CAL_LANG_REP_WEEK);
				
				if (trim($row->reccurweeks) == 'pair') {
					$pairorimpair = JText::_('EVENTS_CAL_LANG_REP_WEEKPAIR');
				} else if ($row->reccurweeks == 'impair') {
					$pairorimpair = JText::_('EVENTS_CAL_LANG_REP_WEEKIMPAIR'); 
				} else {
					$pairorimpair = JText::_('EVENTS_CAL_LANG_REP_WEEK');
				}
				echo JText::_('EVENTS_CAL_LANG_EACH').'&nbsp;'.$dayname.'&nbsp;'.$pairorimpair;
			//} elseif ($row->reccurtype == 1) {
			//	echo $dayname."&nbsp;"._CAL_LANG_EACHOF."&nbsp;".$reccur;
			} else {
				echo JText::_('EVENTS_CAL_LANG_EACH').'&nbsp;'.$reccur;
			}
		} else {
			echo JText::_('EVENTS_CAL_LANG_EACH').'&nbsp;'.$reccur;
		}
	} else {
		$bits_up = explode('-',$row->publish_up);
		$bup = explode(' ', end($bits_up));
		$bits_dn = explode('-',$row->publish_down);
		$bdn = explode(' ', end($bits_dn));
		if ($bup[0] != $bdn[0]) {
			echo JText::_('EVENTS_CAL_LANG_ALLDAYS');
		} else {
			echo '&nbsp;';
		}
	}
?></td>

				<td><?php
	$now = date( "Y-m-d h:i:s" );
	if ($now <= $row->publish_up && $row->state == "1") {
		$img = 'publish_y.png';
		$alt = JText::_( 'Pending' );
	} else if (($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->state == "1") {
		$img = 'publish_g.png';
		$alt = JText::_( 'Published' );
	} else if ($now > $row->publish_down && $row->state == "1") {
		$img = 'publish_r.png';
		$alt = JText::_( 'Expired' );
	} elseif ($row->state == "0") {
		$img = 'publish_x.png';
		$alt = JText::_( 'Unpublished' );
	}

	$times = '';
	if (isset($row->publish_up)) {
		if ($row->publish_up == '0000-00-00 00:00:00') {
			$times .= JText::_('EVENTS_CAL_LANG_FROM').' : '.JText::_('EVENTS_CAL_LANG_ALWAYS').'<br />';
		} else {
			$times .= JText::_('EVENTS_CAL_LANG_FROM').' : '.$row->publish_up.'<br />';
		}
	}
	if (isset($row->publish_down)) {
		if ($row->publish_down == '0000-00-00 00:00:00') {
			$times .= JText::_('EVENTS_CAL_LANG_TO').' : '.JText::_('EVENTS_CAL_LANG_NEVER').'<br />';
		} else {
			$times .= JText::_('EVENTS_CAL_LANG_TO').' : '.$row->publish_down.'<br />';
		}
	}
	
	$pages = $p->getCount(array('event_id'=>$row->id));
	
	if ($times) {
        ?><span class="editlinktip hasTip" title="<?php echo JText::_( 'Publish Information' );?>::<?php echo $times; ?>">
			<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? 'unpublish' : 'publish' ?>')">
				<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
			</a>
		</span><?php
	}
?></td>
				<td><?php 
				if ($row->announcement == 0) {
					$class = 'unpublished'; 
					$tsk = 'announcement';
					$alt = 'event';
				} else {
					$class = 'published';
					$tsk = 'event';
					$alt = 'announcement';
					}
				?><a class="<?php echo $class;?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','make_<?php echo $tsk;?>')" title="Click to make into an <?php echo $tsk;?>"><span><?php echo $alt; ?></span></a></td>
				<td><?php echo $times; ?></td>
				<td><?php
				if ($row->checked_out) { 
					echo $row->editor;
				} else { 
					echo '&nbsp;';
				} ?></td>
				<td><?php echo $row->groupname;?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=pages&amp;id[]=<? echo $row->id; ?>"><?php echo $pages; ?> <?php echo JText::_('Pages'); ?></a></td>
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