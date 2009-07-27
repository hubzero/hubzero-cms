<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class EventsHtml 
{
	public function error( $msg )
	{
		return '<p class="error">'.$msg.'</p>';
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script> alert('".$msg."'); window.history.go(-1); </script>";
	}
	
	//-----------
	
	public function buildRadioOption( $arr, $tag_name, $tag_attribs, $key, $text, $selected ) 
	{  
		$html = '';
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) 
		{
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			
			$sel = '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) 
				{
					$k2 = $obj->$key;
					if ($k == $k2) {
						$sel = ' checked="checked"';
						break;
					}
				}
			} else {
				$sel = ($k == $selected ? ' checked="checked"' : '');
			}
			$html .= '<label><input name="'.$tag_name.'" id="'.$tag_name.$i.'" type="radio" value="'.$k.'"'.$sel.' '.$tag_attribs.'/>'.$t.'</label>'.n;
		}
		return $html;
	}
	
	//-----------
	
	public function buildCategorySelect($catid, $args, $gid, $option)
	{
		$database =& JFactory::getDBO();

		$catsql = "SELECT id AS value, name AS text FROM #__categories "
				. "WHERE section='$option' AND access<='$gid' AND published='1' ORDER BY ordering";	

		$categories[] = JHTML::_('select.option', '0', JText::_('EVENTS_CAL_LANG_EVENT_CHOOSE_CATEG'), 'value', 'text');

		$database->setQuery($catsql);
		$categories = array_merge( $categories, $database->loadObjectList() );
		$clist = JHTML::_('select.genericlist', $categories, 'catid', $args, 'value', 'text', $catid, false, false );
		
		echo $clist;
	}
	
	//-----------
	
	public function buildReccurDaySelect($reccurday, $tag_name, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));        
		$daynamelist[] = JHTML::_('select.option', '-1', '&nbsp;'.JText::_('EVENTS_CAL_LANG_BYDAYNUMBER').'<br />', 'value', 'text');
		for ($a=0; $a<7; $a++) 
		{
			$name_of_day = '&nbsp;'.$day_name[$a];
			$daynamelist[] = JHTML::_('select.option', $a, $name_of_day, 'value', 'text');
        }
		$tosend = EventsHtml::buildRadioOption( $daynamelist, $tag_name, $args, 'value', 'text', $reccurday );
		echo $tosend;
    }

	//-----------

	public function buildWeekDaysCheck($reccurweekdays, $args) 
	{
		$day_name = array('<span style="color:red;">'.JText::_('EVENTS_CAL_LANG_SUNDAYSHORT').'</span>',
							JText::_('EVENTS_CAL_LANG_MONDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_TUESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_THURSDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_FRIDAYSHORT'),
							JText::_('EVENTS_CAL_LANG_SATURDAYSHORT'));    
		$tosend = '';
		if ($reccurweekdays == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweekdays);
			$countsplit = count($split);
		}
        
		for ($a=0; $a<7; $a++) 
		{
			$checked = '';
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<input type="checkbox" id="cb_wd'.$a.'" name="reccurweekdays" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$day_name[$a].n;
		}
		echo $tosend;
	}

	//-----------

	public function buildWeeksCheck($reccurweeks, $args) 
	{
		$week_name = array('',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 1<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 2<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 3<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 4<br />',
							JText::_('EVENTS_CAL_LANG_REP_WEEK').' 5<br />');        
		$tosend = '';
		$checked = '';
    
		if ($reccurweeks == '') {
			$split = array();
			$countsplit = 0;
		} else {
			$split = explode("|", $reccurweeks);
			$countsplit = count($split);
		}
        
		for ($a=1; $a<6; $a++) 
		{
			$checked = '';
			if ($reccurweeks == '') { 
				$checked = 'checked="checked"';
			}
			for ($x = 0; $x < $countsplit; $x++) 
			{
				if ($split[$x] == $a) {
					$checked = 'checked="checked"';
				}
			}
			$tosend .= '<input type="checkbox" id="cb_wn'.$a.'" name="reccurweeks" value="'.$a.'" '.$args.' '.$checked.'/>&nbsp;'.$week_name[$a].n;     
		}
		echo $tosend;
	}
	
	//-----------

	public function getLongDayName($daynb) 
	{
		$dayname = '';
		switch ($daynb) 
		{
			case '0': $dayname = JText::_('EVENTS_CAL_LANG_SUNDAY');    break;
			case '1': $dayname = JText::_('EVENTS_CAL_LANG_MONDAY');    break;
			case '2': $dayname = JText::_('EVENTS_CAL_LANG_TUESDAY');   break;
			case '3': $dayname = JText::_('EVENTS_CAL_LANG_WEDNESDAY'); break;
			case '4': $dayname = JText::_('EVENTS_CAL_LANG_THURSDAY');  break;
			case '5': $dayname = JText::_('EVENTS_CAL_LANG_FRIDAY');    break;
			case '6': $dayname = JText::_('EVENTS_CAL_LANG_SATURDAY');  break;
		}
		return $dayname;
	}

	//-----------

	public function getColorBar($event_id=NULL,$newcolor)
	{
		$database =& JFactory::getDBO();
		
		if ($event_id != NULL) {
			$database->setQuery( "SELECT color_bar FROM #__events WHERE id = '$event_id'" );
			$rows = $database->loadResultList();
			$row = $rows[0];
			if ($newcolor) {
				if ($newcolor <> $row->color_bar) {
					$database->setQuery( "UPDATE #__events SET color_bar = '$newcolor' WHERE id = '$event_id'" );
					return $newcolor;
				}
			} else {
				return $row->color_bar;
			}
		} else {
			// dmcd May 20/04  check the new config parameter to see what the default
			// color should be
			switch (_CAL_CONF_DEFCOLOR) 
			{
				case 'none':
					return '';
				case 'category':
					// fetch the category color for this event?
					// Note this won't work for a new event since
					// the user can change the category on-the-fly
					// in the event entry form.  We need to dump a
					// javascript array of all the category colors
					// into the event form so the color can track the
					// chosen category.
					return '';
				case 'random':
				default:
					$event_id = rand(1,50);
					// BAR COLOR GENERATION
					//$start_publish = mktime (0, 0, 0, date("m"),date("d"),date("Y"));
	                             
					//$colorgenerate = intval(($start_publish/$event_id));
					//$bg1color = substr($colorgenerate, 5, 1);
					//$bg2color = substr($colorgenerate, 3, 1);
					//$bg3color = substr($colorgenerate, 7, 1);
					$bg1color = rand(0,9);
					$bg2color = rand(0,9);
					$bg3color = rand(0,9);
					$newcolorgen = "#".$bg1color."F".$bg2color."F".$bg3color."F";
       
					return $newcolorgen;
			}
		}
	}
	
	//-----------
	
	public function events( $rows, $clist, $search, $pageNav, $option ) 
	{
	    $juser =& JFactory::getUser();
		
		JHTML::_('behavior.tooltip');
		?>

		<form action="index.php?option=<?php echo $option; ?>" method="post" name="adminForm">
			<fieldset id="filter">
				<label>
					<?php echo JText::_('EVENTS_SEARCH'); ?>:
					<input type="text" name="search" value="<?php echo $search;?>" />
				</label>
			
				<?php echo $clist; ?>
				
				<input type="submit" name="submitsearch" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>

			<table class="adminlist">
				<thead>
					<tr>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ID'); ?></th>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TITLE'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEAT'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ANNOUNCEMENT'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TIMESHEET'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CHECKEDOUT'); ?></th>
						<th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
	$k = 0;
	for ($i=0, $n=count( $rows ); $i < $n; $i++) 
	{
		$row = &$rows[$i];
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $row->id; ?></td>
						<td><?php 
						if ($row->checked_out && $row->checked_out != $juser->get('id')) { 
							echo '&nbsp;'; 
						} else { 
							?><input type="checkbox" id="cb<?php echo $i;?>" name="id[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /><?php 
						} ?></td>
						<td><a href="index.php?option=<?php echo $option; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>"><?php echo stripslashes($row->title); ?></a></td>
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
					</tr>
<?php
   $k = 1 - $k;

   }
?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	//-----------

	public function edit( $row, $config, $fields, $glist, $times, $myid, $option, $tags ) 
	{	
		$editor =& JFactory::getEditor();
		
		ximport('xuser');
		$xuserc =& XUser::getInstance( $row->created_by );
		$xuserm =& XUser::getInstance( $row->modified_by );
		$userm = is_object($xuserm) ? $xuserm->get('name') : '';
		$userc = is_object($xuserc) ? $xuserc->get('name') : '';
		?>
		<script type="text/javascript" src="../components/<?php echo $option; ?>/js/calendar.rc4.js"></script>
		<script type="text/javascript">
		var HUB = {};
		
		/*window.addEvent('domready', function() {
			myCal1 = new Calendar({ publish_up: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
			myCal2 = new Calendar({ publish_down: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
		});*/
		</script>
		
		<script type="text/javascript" src="../components/<?php echo $option; ?>/js/events.js"></script>
		<form action="index.php" method="post" name="adminForm" id="hubForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('EVENT'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TITLE'); ?>: *</td>
								<td><input type="text" name="title" size="45" maxlength="250" value="<?php echo $row->title; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>:</th>
								<td><?php echo EventsHtml::buildCategorySelect($row->catid, '', 0, $option);?></td>
							</tr>
							<tr>
								<td class="key" style="vertical-align:top;"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACTIVITY'); ?>:</td>
								<td><?php echo $editor->display('econtent', $row->content, 'auto', 'auto', '45', '10', false); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>:</td>
								<td><input type="text" name="adresse_info" size="45" maxlength="120" value="<?php echo $row->adresse_info; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CONTACT'); ?>:</td>
								<td><input type="text" name="contact_info" size="45" maxlength="120" value="<?php echo $row->contact_info; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_EXTRA'); ?>:</td>
								<td><input type="text" name="extra_info" size="45" maxlength="240" value="<?php echo $row->extra_info; ?>" /></td>
							</tr>
							<?php
							foreach ($fields as $field) 
							{
							?>
							<tr>
								<td class="key"><?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">*</span>' : ''; ?></td>
								<td><?php
								if ($field[2] == 'checkbox') {
									echo '<input type="checkbox" name="fields['. $field[0] .']" value="1"';
									if (stripslashes(end($field)) == 1) {
										echo ' checked="checked"';
									}
									echo ' />';
								} else {
									echo '<input type="text" name="fields['. $field[0] .']" size="45" maxlength="255" value="'. stripslashes(end($field)) .'" />';
								}
								?></td>
							</tr>
							<?php 
							}
							?>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TAGS'); ?>:</td>
								<td><input type="text" name="tags" size="45" value="<?php echo $tags; ?>" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('PUBLISHING'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STARTDATE'); ?></td>
								<td>
									<input type="text" name="publish_up" id="publish_up" size="12" maxlength="10" value="<?php echo $times['start_publish'];?>" />
									
								</td>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STARTTIME');?></td>
								<td>
									<input type="text" name="start_time" id="start_time" size="8" maxlength="8" value="<?php echo $times['start_time'];?>" />
									<?php if ($config->getCfg('calUseStdTime') =='YES') { ?>
									<input id="start_pm0" name="start_pm" type="radio"  value="0" <?php if (!$times['start_pm']) echo "checked"; ?> />AM
									<input id="start_pm1" name="start_pm" type="radio"  value="1" <?php if ($times['start_pm']) echo "checked"; ?> />PM
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ENDDATE'); ?></td>
								<td>
									<input type="text" name="publish_down" id="publish_down" size="12" maxlength="10" value="<?php echo $times['stop_publish'];?>" />
									
								</td>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ENDTIME');?></td>
								<td>
									<input class="inputbox" type="text" name="end_time" id="end_time" size="8" maxlength="8" value="<?php echo $times['end_time'];?>" />
									<?php if ($config->getCfg('calUseStdTime') =='YES') { ?>
									<input id="end_pm0" name="end_pm" type="radio"  value="0" <?php if (!$times['end_pm']) echo "checked"; ?> />AM
									<input id="end_pm1" name="end_pm" type="radio"  value="1" <?php if ($times['end_pm']) echo "checked"; ?> />PM
									<?php } ?>
								</td>
							</tr>
							<!-- REPEAT -->
							<tr>
								<td class="key" style="vertical-align:top;"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEATTYPE'); ?></td>
								<td colspan="3">
									<table>
										<tr>
											<td style="width:60px;"><span style="text-decoration:underline"><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY');?></span></td>
											<td colspan="2" style="background-color:#FFCCCC;">
												<input id="reccurtype0" name="reccurtype" type="radio"  value="0" <?php if ($row->reccurtype == 0) { echo 'checked="checked"'; } ?> />
												<?php echo JText::_('EVENTS_CAL_LANG_ALLDAYS'); ?>
											</td>
										</tr>
										<tr> 
											<td rowspan="3" style="vertical-align:top;"><span style="text-decoration:underline;"><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></span></td>
											<td style="width:100px;background-color:#FFCC99;">
												<input id="reccurtype1" name="reccurtype" type="radio" value="1" <?php if ($row->reccurtype == 1) { echo 'checked="checked"'; } ?> />
												1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?>
											</td>
											<td style="background-color:#FFCC99">
												<?php 
												if ($row->reccurtype == 1 || $row->reccurtype == 2) {
													$arg = '';
												} else {
													$arg = ' disabled="disabled"';
												}
												echo EventsHtml::buildReccurDaySelect($row->reccurday_week,'reccurday_week',$arg); ?>
											</td>
										</tr>
										<tr> 
											<td style="background-color:#FFCC99">
												<input id="reccurtype2" name="reccurtype" type="radio" value="2" <?php if ($row->reccurtype == 2) { echo 'checked="checked"'; } ?> />
												n * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?>
											</td>
											<td style="background-color:#FFCC99">
												<?php 
												if ($row->reccurtype == 1 || $row->reccurtype == 2) {
													$arg = '';
												} else {
													$arg = ' disabled="disabled"';
												}
												echo EventsHtml::buildWeekDaysCheck($row->reccurweekdays, $arg); ?>
											</td>
										</tr>
										<tr>
											<td style="background-color:#FFCC99; text-align: right;vertical-align:top;">
												<em><?php echo JText::_('EVENTS_CAL_LANG_EVENT_WEEKOPT');?></em>
											</td>
											<td style="background-color:#FFCC99">
												<?php echo EventsHtml::buildWeeksCheck($row->reccurweeks, $arg); ?>
												<input id="cb_wn6" name="reccurweekss" type="radio" value="pair" <?php if ($row->reccurweeks == 'pair') { echo 'checked="checked"'; } else { echo 'disabled="disabled"'; } ?> />
												<?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKPAIR'); ?><br />
												<input id="cb_wn7" name="reccurweekss" type="radio" value="impair" <?php if ($row->reccurweeks == 'impair') { echo 'checked="checked"'; } else { if ($row->reccurtype != 1 && $row->reccurtype != 2) { echo 'disabled="disabled"'; } } ?> />
												<?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKIMPAIR'); ?>
											</td>
										</tr>
										<tr>
											<td rowspan="2" style="vertical-align:top;"><span style="text-decoration:underline"><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></span></td>
											<td style="background-color:#99CC66">
												<input id="reccurtype3" name="reccurtype" type="radio" value="3" <?php if ($row->reccurtype == 3) { echo 'checked="checked"'; } ?> />
												1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?>
											</td>
											<td style="background-color:#99CC66">
												<?php 
												if ($row->reccurtype == 3) {
													$arg = '';
												} else {
													$arg = ' disabled="disabled"';
												}
												echo EventsHtml::buildReccurDaySelect($row->reccurday_month,'reccurday_month',$arg); ?>
											</td>
										</tr>
										<tr>
											<td colspan="2" style="background-color:#99CC66">
												<input id="reccurtype4" name="reccurtype" type="radio"  value="4" <?php if ($row->reccurtype == 4) { echo 'checked="checked"'; } ?> />
												<?php echo JText::_('EVENTS_CAL_LANG_EACH').' '.JText::_('EVENTS_CAL_LANG_ENDMONTH'); ?>
											</td>
										</tr>
										<tr>
											<td rowspan="2" style="vertical-align:top;"><span style="text-decoration:underline"><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></span></td>
											<td style="background-color:#FFCCCC">
												<input id="reccurtype5" name="reccurtype" type="radio" value="5" <?php if ($row->reccurtype == 5) { echo 'checked="checked"'; } ?> />
												1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?>
											</td>
											<td style="background-color:#FFCCCC">
												<?php 
												if ($row->reccurtype == 5) {
													$arg = '';
												} else {
													$arg = ' disabled="disabled"';
												}
												echo EventsHtml::buildReccurDaySelect($row->reccurday_year,'reccurday_year',$arg); ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<!-- END REPEAT -->
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<fieldset class="adminform">
					<legend><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STATUS'); ?></legend>

					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_STATE'); ?></td>
								<td><?php echo $row->state > 0 ? JText::_('Published') : ($row->state < 0 ? JText::_('Archived') : JText::_('Draft Unpublished'));?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_HITS'); ?></td>
								<td><?php echo $row->hits;?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CREATED'); ?></td>
								<td><?php echo $row->created ? $row->created.'</td></tr><tr><td class="key">'.JText::_('EVENTS_CAL_LANG_EVENT_CREATED_BY').'</td><td>'.$userc : JText::_('EVENTS_CAL_LANG_EVENT_NEWEVENT');?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_MODIFIED'); ?></td>
								<td><?php echo $row->modified ? $row->modified.'</td></tr><tr><td class="key">'.JText::_('EVENTS_CAL_LANG_EVENT_MODIFIED_BY').'</td><td>'.$userm : JText::_('EVENTS_CAL_LANG_EVENT_NOTMODIFIED');?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TYPE'); ?></td>
								<td>
									<input type="radio" name="announcement" value="0" <?php if ($row->announcement == 0) { echo 'checked="checked"'; } ?> /> <?php echo JText::_('EVENT'); ?></label><br />
									<input type="radio" name="announcement" value="1" <?php if ($row->announcement == 1) { echo 'checked="checked"'; } ?> /> <?php echo JText::_('ANNOUNCEMENT'); ?></label>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACCESSLEVEL'); ?></td>
								<td><?php echo $glist; ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div><div class="clr"></div>
		
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="sid" value="<?php echo $row->sid; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="images" value="" />
		</form>
		<?php
	}
	
	//-----------

	public function configure($option, $config) 
	{
		?>
		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
				<legend><?php echo JText::_('EVENTS_CAL_LANG_CONFIG'); ?></legend>
				
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key" style="width:265px;"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_ADMINMAIL'); ?></td>
							<td><input type="text" name="config[adminmail]" size="30" maxlength="50" value="<?php echo $config->adminmail; ?>" /></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_ADMINLEVEL'); ?></td>
							<td><?php
							$level[] = JHTML::_('select.option', '0', JText::_('All registered users'), 'value', 'text' );
							$level[] = JHTML::_('select.option', '1', JText::_('Only special rights and admins'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $level, 'config[adminlevel]', '', 'value', 'text', $config->adminlevel, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_FIRSTDAY'); ?></td>
							<td><?php
							$first[] = JHTML::_('select.option', '0', JText::_('Sunday first'), 'value', 'text' );
							$first[] = JHTML::_('select.option', '1', JText::_('Monday first'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $first, 'config[starday]', '', 'value', 'text', $config->starday, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_VIEWMAIL'); ?></td>
							<td><?php
							$viewm[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							$viewm[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $viewm, 'config[mailview]', '', 'value', 'text', $config->mailview, false, false );
							?></td>
						</tr>      
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_VIEWBY'); ?></td>
							<td><?php
							$viewb[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							$viewb[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $viewb, 'config[byview]', '', 'value', 'text', $config->byview, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_VIEWHITS'); ?></td>
							<td><?php
							$viewh[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							$viewh[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $viewh, 'config[hitsview]', '', 'value', 'text', $config->hitsview, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_VIEWREPEAT'); ?></td>
							<td><?php
							$viewr[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							$viewr[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $viewr, 'config[repeatview]', '', 'value', 'text', $config->repeatview, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_DATEFORMAT'); ?></td>
							<td><?php
							$datef[] = JHTML::_('select.option', '0', JText::_('French-English'), 'value', 'text' );
							$datef[] = JHTML::_('select.option', '1', JText::_('US'), 'value', 'text' );
                			$datef[] = JHTML::_('select.option', '2', JText::_('Deutsch'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $datef, 'config[dateformat]', '', 'value', 'text', $config->dateformat, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_TIMEFORMAT'); ?></td>
							<td><?php
							$stdTime[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							$stdTime[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $stdTime, 'config[calUseStdTime]', '', 'value', 'text', $config->calUseStdTime, false, false );
							?></td>
						</tr>
						<!-- <tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_NAVCOLOR'); ?></td>
							<td><?php
							$navcol[] = JHTML::_('select.option', 'green', JText::_('Green'), 'value', 'text' );
							$navcol[] = JHTML::_('select.option','orange', JText::_('Orange'), 'value', 'text' );
							$navcol[] = JHTML::_('select.option', 'blue', JText::_('Blue'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $navcol, 'config[navbarcolor]', '', 'value', 'text', $config->navbarcolor, false, false );
							?></td>
						</tr> -->
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_STARTPAGE'); ?></td>
							<td><?php
							$startpg[] = JHTML::_('select.option', 'day', JText::_('EVENTS_CAL_LANG_REP_DAY'), 'value', 'text' );
							$startpg[] = JHTML::_('select.option', 'week', JText::_('EVENTS_CAL_LANG_REP_WEEK'), 'value', 'text' );
							$startpg[] = JHTML::_('select.option', 'month', JText::_('EVENTS_CAL_LANG_REP_MONTH'), 'value', 'text' );
							$startpg[] = JHTML::_('select.option', 'year', JText::_('EVENTS_CAL_LANG_REP_YEAR'), 'value', 'text' );
							$startpg[] = JHTML::_('select.option', 'categories', JText::_('EVENTS_CAL_LANG_EVENT_CATEGORIES'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $startpg, 'config[startview]', '', 'value', 'text', $config->startview, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_NUMEVENTS'); ?></td>
							<td><input type="text" size="3" name="config[calEventListRowsPpg]" value="<?php echo $config->calEventListRowsPpg; ?>" /></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CONFIG_SIMPLEFORM'); ?></td>
							<td><?php
							$formOpt[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							$formOpt[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $formOpt, 'config[calSimpleEventForm]', '', 'value', 'text', $config->calSimpleEventForm, false, false );
							?></td>
						</tr>
						<!-- <tr>
							<td class="key">Default Event Color ?</td>
							<td><?php
							$defColor[] = JHTML::_('select.option', 'random', JText::_('Random'), 'value', 'text' );
							$defColor[] = JHTML::_('select.option', 'none', JText::_('None'), 'value', 'text' );
							$defColor[] = JHTML::_('select.option', 'category', JText::_('Category'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $defColor, 'config[defColor]', '', 'value', 'text', $config->defColor, false, false );
							?></td>
						</tr>
						<tr>
							<td class="key">Hide Event Color Selection in Event Form, and force Event Color to Category Color<br/>(front end only, back end event entry unaffected)</td>
							<td><?php
							$colCatOpt[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
							$colCatOpt[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
							echo JHTML::_('select.genericlist', $colCatOpt, 'config[calForceCatColorEventForm]', '', 'value', 'text', $config->calForceCatColorEventForm, false, false );
							?></td>
						</tr> -->
					</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('EVENTS_CAL_LANG_CUSTOM_FIELDS'); ?></legend>
				
				<table class="admintable">
					<thead>
						<tr>
							<th><?php echo JText::_('EVENTS_CAL_LANG_FIELD'); ?></th>
							<th><?php echo JText::_('EVENTS_CAL_LANG_TYPE'); ?></th>
							<th><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></th>
							<th><?php echo JText::_('EVENTS_CAL_LANG_SHOW'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$fields = $config->fields;
					$r = count($fields);
					if ($r > 10) {
						$n = $r;
					} else {
						$n = 10;
					}
					for ($i=0; $i < $n; $i++)
					{
						if ($r == 0 || !isset($fields[$i])) {
							$fields[$i] = array();
							$fields[$i][0] = NULL;
							$fields[$i][1] = NULL;
							$fields[$i][2] = NULL;
							$fields[$i][3] = NULL;
							$fields[$i][4] = NULL;
						}
						?>
						<tr>
							<td><input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $fields[$i][1]; ?>" maxlength="255" /></td>
							<td><select name="fields[<?php echo $i; ?>][type]">
								<option value="text"<?php echo ($fields[$i][2]=='text') ? ' selected="selected"':''; ?>><?php echo JText::_('EVENTS_CAL_LANG_TEXT'); ?></option>
								<option value="checkbox"<?php echo ($fields[$i][2]=='checkbox') ? ' selected="selected"':''; ?>><?php echo JText::_('EVENTS_CAL_LANG_CHECKBOX'); ?></option>
							</select></td>
							<td><input type="checkbox" name="fields[<?php echo $i; ?>][required]" value="1"<?php echo ($fields[$i][3]) ? ' checked="checked"':''; ?> /></td>
							<td><input type="checkbox" name="fields[<?php echo $i; ?>][show]" value="1"<?php echo ($fields[$i][4]) ? ' checked="checked"':''; ?> /></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</fieldset>

			<input type="hidden" name="task" value="saveconfig" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
		</form>
		<?php
	}
	
	//-----------
	
	public function cats( $option, &$rows, $section, $section_name, $myid, $pageNav ) 
	{
		?>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminlist">
			 <thead>
			  <tr>
			   <th>#</th>
			   <th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
			   <th><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_NAME'); ?></th>
			   <th><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_NUM_RECORDS'); ?></th>
			   <th><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_NUM_CHECKEDOUT'); ?></th>
			   <th><?php echo JText::_('EVENTS_E_PUBLISHING'); ?></th>
			   <th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_CHECKEDOUT'); ?></th>
			   <th><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACCESS'); ?></th>
			   <th colspan="2"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_REORDER'); ?></th>
			  </tr>
			 </thead>
			 <tfoot>
			 	<tr>
			 		<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
			 	</tr>
			 </tfoot>
			 <tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				$row = &$rows[$i];
				$class = $row->published ? 'published' : 'unpublished';
				$alt = $row->published ? 'Published' : 'Unpublished';
				$task = $row->published ? 'unpublishcat' : 'publishcat';

				if ( $row->groupname == 'Public' ) {
					$color_access = 'style="color: green;"';
				} else if ( $row->groupname == 'Special' ) {
					$color_access = 'style="color: red;"';
				} else {
					$color_access = 'style="color: black;"';
				}
			?>
			  <tr class="<?php echo "row$k"; ?>">
			   <td><?php echo $i+$pageNav->limitstart+1;?></td>
			   <td><?php 
			   	if ($row->checked_out && $row->checked_out != $myid) { 
					?>&nbsp;<?php	
				} else { 
					?><input type="checkbox" id="cb<?php echo $i;?>" name="id[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /><?php 
				} ?></td>
			   <td width="35%"><a href="index.php?option=<?php echo $option; ?>&amp;task=editcat&amp;id=<?php echo $row->id;?>"><?php echo "$row->name ($row->title)"; ?></a></td>
		 	   <td><?php echo $row->num; ?></td>
			   <td><?php echo $row->checked_out; ?></td>
			   <td><a class="<?php echo $class;?>" href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><span><?php echo $alt; ?></span></a></td>
			   <td><?php echo $row->editor; ?></td>
			   <td><span <?php echo $color_access;?>><?php echo $row->groupname;?></span></td>
			   <td><?php	
				if ($i > 0 || ($i+$pageNav->limitstart > 0)) { 
					?><a href="#reorder" class="order up" onclick="return listItemTask('cb<?php echo $i;?>','orderup')" title="Move Up"><img src="images/uparrow.png" alt="Move up" /></a><?php
				} else { 
					?>&nbsp;<?php 
				} 
				?></td>
			   <td><?php	
				if ($i < $n-1 || $i+$pageNav->limitstart < $pageNav->total-1) { 
					?><a href="#reorder" class="order down" onclick="return listItemTask('cb<?php echo $i;?>','orderdown')" title="Move Down"><img src="images/downarrow.png" alt="Move down" /></a><?php 
				} else { 
					?>&nbsp;<?php
				} 
				?></td>
			  </tr>
			<?php
				$k = 1 - $k;
			} // for loop 
			?>
			 </tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="section" value="<?php echo $section; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="chosen" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	//-----------

	public function editcat( $option, &$row, $imagelist, $iposlist, $orderlist, $glist, $color='' ) 
	{
		if ($row->image == '') {
			$row->image = 'blank.png';
		}
		
		$editor =& JFactory::getEditor();
		?>
		
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton, section) 
		{
			if (pressbutton == 'cancelcat') {
				submitform( pressbutton );
				return;
			}
			
			if (document.adminForm.name.value == ''){
				alert("Category must have a name");
			} else {
				submitform(pressbutton);
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm">
			<fieldset class="adminform">
			<legend><?php echo $row->name; ?></legend>

			<table class="admintable">
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_TITLE'); ?>:</td>
			  <td colspan="2"><input type="text" name="title" value="<?php echo $row->title; ?>" size="50" maxlength="50" title="A short name to appear in menus" /></td>
			 </tr>
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_NAME'); ?>:</td>
			  <td colspan="2"><input type="text" name="name" value="<?php echo $row->name; ?>" size="50" maxlength="255" title="A long name to be displayed in headings" /></td>
			 </tr>
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_IMAGE'); ?>:</td>
			  <td><?php echo $imagelist; ?></td>
			  <td rowspan="4">
				<script type="text/javascript">
					if (document.forms[0].image.options.value!=''){
					  jsimg='../images/stories/' + getSelectedValue( 'adminForm', 'image' );
					} else {
					  jsimg='../images/M_images/blank.png';
					}
					document.write('<img src=' + jsimg + ' name="imagelib" width="80" height="80" border="2" alt="Preview" />');
				</script>
			  </td>
			 </tr>
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_IMAGE_POSITION'); ?>:</td>
			  <td><?php echo $iposlist; ?></td>
			 </tr>
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_CATEGORY_ORDERING'); ?>:</td>
			  <td><?php echo $orderlist; ?></td>
			 </tr>
			 <tr>
			  <td class="key"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_ACCESSLEVEL'); ?>:</td>
			  <td><?php echo $glist; ?></td>
			 </tr>
			 <tr>
			  <td class="key" style="vertical-align: top;"><?php echo JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:</td>
			  <td colspan="2"><?php echo $editor->display('description', $row->description, 'auto', 'auto', '45', '10', false); ?></td>
			 </tr>
			</table>

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="section" value="<?php echo $row->section; ?>" />
			<input type="hidden" name="oldtitle" value="<?php echo $row->title ; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="task" value="savecat" />
			</fieldset>
		</form>
		<?php 
	}
}
?>