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
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class SupportHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------
	
	public function getStatus($int)
	{
		switch ($int)
		{
			case 0: $status = 'new';      break;
			case 1: $status = 'accepted'; break;
			case 2: $status = 'resolved'; break;
		}
		return $status;
	}

	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		
		if ($text == '') {
			$text = '...';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}
	
	//-----------
	
	public function selectArray($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $anode) 
		{
			$selected = ($anode == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode.'"'.$selected.'>'.stripslashes($anode).'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}

	//-----------

	public function selectObj($name, $array, $value, $class='', $js='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $anode) 
		{
			$selected = ($anode->txt == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.stripslashes($anode->txt).'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------
	
	/*public function categories( &$rows, &$pageNav, $option ) 
	{
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
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('SUPPORT_COL_ID'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_CATEGORY'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_SECTION'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
?>
					<tr>
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editcat&amp;id=<? echo $row->id; ?>"><?php echo $row->category; ?></a></td>
						<td><?php echo $row->section; ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
		
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}
	
	//-----------
	
	public function sections( &$rows, &$pageNav, $option ) 
	{
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

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('SUPPORT_COL_ID'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_SECTION'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
?>
					<tr>
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editsec&amp;id=<? echo $row->id; ?>"><?php echo $row->section; ?></a></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
		
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}*/

	//-----------

	public function resolutions( &$rows, &$pageNav, $option ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancelres') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class="adminlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('SUPPORT_COL_ID'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_RESOLUTION'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
?>
					<tr>
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editres&amp;id=<? echo $row->id; ?>"><?php echo stripslashes($row->title); ?> (<?php echo $row->alias; ?>)</a></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
		
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}

	//-----------

	public function messages( &$rows, &$pageNav, $option ) 
	{
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

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class="adminlist" id="tktlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('SUPPORT_COL_NUM'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_MESSAGE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				$row = &$rows[$i];
?>
					<tr>
						<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id; ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=editmsg&amp;id=<? echo $row->id; ?>"><?php echo $row->title; ?></a></td>
					</tr>
<?php
				$k = 1 - $k;
			}
?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}

	//-----------
	
	public function tickets( $database, &$rows, &$pageNav, $option, $filters ) 
	{
		if ($filters['_show'] != '') {
			$fstring = urlencode(trim($filters['_show']));
		} else {
			$fstring = urlencode(trim($filters['_find']));
		}
		
		JHTML::_('behavior.tooltip');
		?>

		<form action="index.php?option=<?php echo $option; ?>" method="post" name="adminForm">
			<fieldset id="filter">
				<label>
					<?php echo JText::_('SUPPORT_FIND'); ?>:
					<input type="text" name="find" id="find" value="<?php echo ($filters['_show'] == '') ? htmlentities($filters['_find']) : ''; ?>" />
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
							<td>me, [username]</td>
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
				</table>" class="editlinktip hasTip" href="<?php echo JRoute::_('index.php?option='.$option.'&task=tickets#help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
				
				<span><?php echo JText::_('OR'); ?></span>
				
				<label>
					<?php echo JText::_('SHOW'); ?>:
					<select name="show">
						<option value=""<?php if ($filters['_show'] == '') { echo ' selected="selected"'; } ?>>--</option>
						<option value="status:new"<?php if ($filters['_show'] == 'status:new') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_NEW'); ?></option>
						<option value="status:open"<?php if ($filters['_show'] == 'status:open') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_OPEN'); ?></option>
						<option value="status:waiting"<?php if ($filters['_show'] == 'status:waiting') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_WAITING'); ?></option>
						<option value="status:closed"<?php if ($filters['_show'] == 'status:closed') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_CLOSED'); ?></option>
						<option value="status:all"<?php if ($filters['_show'] == 'status:all') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_ALL'); ?></option>
						<option value="reportedby:me"<?php if ($filters['_show'] == 'reportedby:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_REPORTED_BY_ME'); ?></option>
						<option value="status:open owner:me"<?php if ($filters['_show'] == 'status:open owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_OPEN'); ?></option>
						<option value="status:closed owner:me"<?php if ($filters['_show'] == 'status:closed owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_CLOSED'); ?></option>
						<option value="status:all owner:me"<?php if ($filters['_show'] == 'status:all owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_ALL'); ?></option>
					</select>
				</label>
				
				<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sortdir']; ?>" />
				
				<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
			</fieldset>
		
			<table class="adminlist" id="tktlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_NUM'), 'id', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_SUMMARY'), 'summary', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_STATUS'), 'status', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_GROUP'), 'group', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_OWNER'), 'owner', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JHTML::_('grid.sort', JText::_('SUPPORT_COL_AGE'), 'created', $filters['sortdir'], $filters['sort'] ); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_COMMENTS'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php		
		$k = 0;
		$sc = new SupportComment( $database );
		$st = new SupportTags( $database );
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$comments = $sc->countComments(true, $row->id);
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
			
			$when = SupportHtml::timeAgo($row->created);
			
			if ($row->owner == '') {
				$row->owner = '&nbsp';
			}
			
			$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
					<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><?php echo $row->id; ?></td>
						<td>
							<a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id=<? echo $row->id; echo ($fstring) ? '&amp;find='.$fstring : ''; ?>"><?php echo stripslashes($row->summary); ?></a>
							<span class="reporter">by <?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]='.$row->login.'">'.$row->login.'</a>)' : ''; ?>, tags: <?php echo $tags; ?></span>
						</td>
						<td><span class="<?php echo $status; ?> status"><?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' ('.$row->resolved.')' : ''; ?></span></td>
						<td><?php echo $row->group; ?></td>
						<td><?php echo $row->owner; ?></td>
						<td><?php echo $when; ?></td>
						<td><?php echo $comments; echo ($comments > 0) ? ' ('.SupportHtml::timeAgo($lastcomment).')' : ''; ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
		
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}

	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		
		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
		
		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
		
		// Ensure the script has found a match
		if ($val < 0) $val = 0;
		
		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);
		
		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val].= "s";
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= SupportHtml::timeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$timestamp = SupportHtml::mkt($timestamp);
		$text = SupportHtml::timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];

		return $text;
	}
	
	//-----------
	
	public function editTicket( $database, $row, $option, $lists, $comments, $filters ) 
	{
		$juser =& JFactory::getUser();
		
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		if ($filters['_show'] != '') {
			$fstring = urlencode(trim($filters['_show']));
		} else {
			$fstring = urlencode(trim($filters['_find']));
		}
?>

<?php if ($row->id) { ?>
		<h3><?php echo JText::_('TICKET'); echo ($row->id) ? ' #'.$row->id : ''; ?></h3>
		
<?php
				if ($row->id) {
					echo '<p id="prev-next">';
					$prv = $row->getTicketId('prev', $filters, 'admin');
					if ( $prv ) {
						echo '<a href="index.php?option='.$option.'&amp;task=edit&amp;id='. $prv .'&amp;find='.$fstring.'">'.JText::_('PREVIOUS_TICKET').'</a>';
					} else {
						echo '<span style="color:#ccc;">'.JText::_('PREVIOUS_TICKET').'</span>';
					}
					echo ' &nbsp;&nbsp; ';
					$nxt = $row->getTicketId('next', $filters, 'admin');
					if ( $nxt ) {
						echo '<a href="index.php?option='.$option.'&amp;task=edit&amp;id='. $nxt .'&amp;find='.$fstring.'">'.JText::_('NEXT_TICKET').'</a>';
					} else {
						echo '<span style="color:#ccc;">'.JText::_('NEXT_TICKET').'</span>';
					}
					echo '</p>';
				}
?>
		
		<p><strong><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$row->created, '%d %b, %Y').' '.JText::_('AT').' '.JHTML::_('date', $row->created, '%I:%M %p').' '.JText::_('BY'); ?> <?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_members&amp;task=edit&amp;id[]='.$row->login.'">'.$row->login.'</a>)' : ''; ?></strong></p>
		
		<div class="col width-70">
			<div class="overview">
				<blockquote cite="<?php echo ($row->login) ? $row->name : $row->name; ?>">
					<p><?php echo $row->report; ?></p>
				</blockquote>
				
				<table class="admintable" id="ticket-details" summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
					<caption id="toggle-details"><?php echo JText::_('TICKET_DETAILS'); ?></caption> 
					<tbody id="ticket-details-body" class="hide">
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_EMAIL'); ?>:</td>
							<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
						</tr>
						<!-- <tr>
							<td class="key"><?php //echo JText::_('TICKET_DETAILS_SECTION'); ?>:</td>
							<td><?php //echo $row->section; ?></td>
						</tr>
						<tr>
							<td class="key"><?php //echo JText::_('TICKET_DETAILS_CATEGORY'); ?>:</td>
							<td><?php //echo $row->category; ?></td>
						</tr> -->
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_TAGS'); ?>:</td>
							<td><?php echo $lists['tagcloud']; ?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_SEVERITY'); ?>:</td>
							<td><?php echo $row->severity; ?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_OWNER'); ?>:</td>
							<td><?php echo ($row->owner) ? $row->owner : ' '; ?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_OS'); ?>:</td>
							<td><?php echo $row->os; ?> / <?php echo $row->browser; ?> (<?php echo ($row->cookies) ? JText::_('COOKIES_ENABLED') : JText::_('COOKIES_DISABLED'); ?>)</td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_IP'); ?>:</td>
							<td><?php echo $row->ip; ?> (<?php echo $row->hostname; ?>)</td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_REFERRER'); ?>:</td>
							<td><?php echo ($row->referrer) ? $row->referrer : ' '; ?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_INSTANCES'); ?>:</td>
							<td><?php echo $row->instances; ?></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_UASTRING'); ?>:</td>
							<td><?php echo ($row->uas) ? $row->uas : '&nbsp;'; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col width-30">
			<p><?php echo ($row->status == 2) ? '<strong class="closed">'.JText::_('TICKET_STATUS_CLOSED_TICKET').'</strong>' : '<strong class="open">'.JText::_('TICKET_STATUS_OPEN_TICKET').'</strong>'; ?></p>
		</div>
		<div class="clr"></div>
		
<?php if (count($comments) > 0) { ?>
		<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>
		<div class="col width-70">
<?php
					$o = 'even';
					$html  = t.t.t.t.'<ol class="comments">'.n;
					foreach ($comments as $comment) 
					{
						if ($comment->access == 1) { 
							$access = 'private';
						} else {
							$access = 'public';
						}
						if ($comment->created_by == $row->login && $comment->access != 1) {
							$access = 'submitter';
						}
						
						$name = 'Unknown';
						if ($comment->created_by) {
							$juseri =& JUser::getInstance( $comment->created_by );
							if (is_object($juseri)) {
								$name = $juseri->get('name');
							}
						}
						
						$o = ($o == 'odd') ? 'even' : 'odd';
						
						$html .= t.t.t.t.t.'<li class="';
						$html .= $access.' comment '.$o.'" id="c'.$comment->id.'">'.n;
						$html .= t.t.t.t.t.t.'<dl class="comment-details">'.n;
						$html .= t.t.t.t.t.t.t.'<dt class="type"><span><span>'.$access.' comment</span></span></dt>'.n;
						$html .= t.t.t.t.t.t.t.'<dd class="date">'.JHTML::_('date',$comment->created, '%d %b, %Y').'</dd>'.n;
						$html .= t.t.t.t.t.t.t.'<dd class="time">'.JHTML::_('date',$comment->created, '%I:%M %p').'</dd>'.n;
						$html .= t.t.t.t.t.t.'</dl>'.n;
						$html .= t.t.t.t.t.t.'<div class="cwrap">'.n;
						$html .= t.t.t.t.t.t.t.'<p class="commenter"><strong>'. $name.' ('.$comment->created_by .')</strong></p>'.n;
						if ($comment->comment) {
							/*$comment->comment = preg_replace('/<br\\\\s*?\\/??>/i', "\n", $comment->comment);
							$comment->comment = str_replace("<br />","\n",$comment->comment);*/
							$comment->comment = stripslashes($comment->comment);
							$comment->comment = str_replace("<br />","",$comment->comment);
							//$comment->comment = htmlentities($comment->comment);
							$comment->comment = nl2br($comment->comment);
							$comment->comment = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$comment->comment);
							
							$html .= t.t.t.t.t.t.t.'<blockquote cite="'. $comment->created_by .'">'.n;
							$html .= t.t.t.t.t.t.t.t.'<p>'.$comment->comment.'</p>'.n;
							$html .= t.t.t.t.t.t.t.'</blockquote>'.n;
						}
						$html .= '<div class="changelog">'.$comment->changelog.'</div>';
						$html .= t.t.t.t.t.t.'</div>'.n;
						$html .= t.t.t.t.t.'</li>'.n;
					}
					$html .= t.t.t.t.'</ol>'.n;
					echo $html;
?>
		</div><!-- / .col width-70 -->
		<div class="col width-30">
			<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
		</div><!-- / .col width-30 -->
		<div class="clr"></div>
<?php } // end if (count($comments) > 0) ?>
<?php } // end if ($row->id) ?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		 <div class="col width-70">
			<fieldset class="adminform" id="primary">
<?php if (!$row->id) { ?>
				<legend><?php echo JText::_('TICKET'); ?></legend>
				
				<input type="hidden" name="summary" id="summary" value="<?php echo $row->summary; ?>" size="50" />
				
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="login">Login:</label></td>
							<td><input type="text" name="login" id="login" value="<?php echo $row->login; ?>" size="50" /></td>
						</tr>
						<tr>
							<td class="key"><label for="name">Name:</label></td>
							<td><input type="text" name="name" id="name" value="<?php echo $row->name; ?>" size="50" /></td>
						</tr>
						<tr>
							<td class="key"><label for="email">E-mail:</label></td>
			 				<td><input type="text" name="email" id="email" value="<?php echo $row->email; ?>" size="50" /></td>
						</tr>
		 				<tr>
							<td class="key" style="vertical-align:top;"><label for="report">Description:</label></td>
							<td><?php echo $editor->display('report', $row->report, '360px', '200px', '50', '10'); ?></td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="section" value="1" />
				<input type="hidden" name="uas" value="<?php echo $_SERVER['HTTP_USER_AGENT']; ?>" />
				<input type="hidden" name="severity" value="normal" />
<?php } else { ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('TICKET_DETAILS'); ?>:</legend>
					<table class="admintable" width="100%">
						<tbody>
							<tr>
								<td width="50%">
									<label>
										<?php echo JText::_('COMMENT_TAGS'); ?>:<br />
										<input type="text" name="tags" id="tags" value="<?php echo $lists['tags']; ?>" size="35" />
										<?php
										/*$html  = '<select name="category" id="category">'.n;
										foreach ($lists['sections'] as $section) 
										{
											$selected = ($section->txt == $row->section && $row->category == '')
													  ? ' selected="selected"'
													  : '';
											$html .= '<optgroup label="'.htmlentities(stripslashes($section->txt)).'">'.n;
											$html .= '<option value="'.$section->id.':"'.$selected.'>All '.htmlentities(stripslashes($section->txt)).'</option>'.n;
											// Get categories
											$sa = new SupportCategory( $database );
											$categories = $sa->getCategories( $section->id );
											foreach ($categories as $category) 
											{
												$selected = ($category->txt == $row->category)
														  ? ' selected="selected"'
														  : '';
												$html .= '<option value="'.$section->id.':'.$category->id.'"'.$selected.'>'.htmlentities(stripslashes($category->txt)).'</option>'.n;
											}
											$html .= '</optgroup>'.n;
										}
										$html .= '</select>'.n;
										echo $html;*/
										?>
									</label>
								</td>
								<td width="50%">
									<label>
										<?php echo JText::_('COMMENT_SEVERITY'); ?>:<br />
										<?php echo SupportHtml::selectArray('severity',$lists['severities'],$row->severity); ?>
									</label>
								</td>
							</tr>
						</tbody>
					</table>
					<table class="admintable" width="100%">
						<tbody>
							<tr>
								<td width="33%">
									<label>
										<?php echo JText::_('COMMENT_GROUP'); ?>:<br />
										<input type="text" name="group" id="group" value="<?php echo $row->group; ?>" size="30" />
									</label>
								</td>
								<td width="33%">
									<label>
										<?php echo JText::_('COMMENT_OWNER'); ?>:<br />
										<?php echo $lists['owner']; ?>
									</label>
								</td>
								<td width="33%">
									<label>
										Status:<br />
										<?php 
										$html  = '<select name="resolved" id="status">'.n;
										$html .= t.'<option value=""';
										if ($row->status == 0 || $row->resolved == '') {
											$html .= ' selected="selected"';
										}
										$html .= '>'.JText::_('COMMENT_OPT_OPEN').'</option>'.n;
										$html .= t.'<option value="1"';
										if ($row->status == 1) {
											$html .= ' selected="selected"';
										}
										$html .= '>'.JText::_('COMMENT_OPT_WAITING').'</option>'.n;
										$html .= t.'<optgroup label="Closed">'.n;
										$html .= t.t.'<option value="noresolution"';
										if ($row->status == 2 && $row->resolved == 'noresolution') {
											$html .= ' selected="selected"';
										}
										$html .= '>'.JText::_('COMMENT_OPT_CLOSED').'</option>'.n;
										if (isset($lists['resolutions']) && $lists['resolutions']!='') {
											foreach ($lists['resolutions'] as $anode) 
											{
												$selected = ($anode->alias == $row->resolved)
														  ? ' selected="selected"'
														  : '';
												$html .= t.t.'<option value="'.$anode->alias.'"'.$selected.'>'.stripslashes($anode->title).'</option>'.n;
											}
										}
										$html .= t.'</optgroup>'.n;
										$html .= '</select>'.n;
										echo $html;
										?>
									</label>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
					<table class="admintable" width="100%">
						<tbody>
							<tr>
								<td width="50%">
									<label>
										<?php 
										$hi = array();
										$o  = '<select name="messages" id="messages" onchange="getMessage();">'.n;
										$o .= t.'<option value="mc">'.JText::_('COMMENT_CUSTOM').'</option>'.n;
										foreach ($lists['messages'] as $message)
										{
											$message->message = str_replace('"','&quot;',$message->message);
											$message->message = str_replace('&quote;','&quot;',$message->message);
											$message->message = str_replace('#XXX','#'.$row->id,$message->message);
											$message->message = str_replace('{ticket#}','#'.$row->id,$message->message);

											$o .= t.'<option value="m'.$message->id.'">'.$message->title.'</option>'."\n";

											$hi[] = '<input type="hidden" name="m'.$message->id.'" id="m'.$message->id.'" value="'.$message->message.'" />'.n;
										}
										$o .= '</select>'.n;
										$hi = implode(n,$hi);
										echo $o.$hi;
										?>
									</label>
								</td>
								<td width="50%" style="text-align: right;">
									<label>
										<input type="checkbox" name="access" id="make-private" value="1" /> 
										<?php echo JText::_('COMMENT_PRIVATE'); ?>
									</label>
								</td>
							</tr>
			 				<tr>
								<td colspan="2">
									<textarea name="comment" id="comment" rows="13" cols="45" style="width: 100%;"></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('COMMENT_LEGEND_ATTACHMENTS'); ?>:</legend>
					<table class="admintable" width="100%">
						<tbody>
			 				<tr>
								<td>
									<label>
										<?php echo JText::_('COMMENT_FILE'); ?>:
										<input type="file" name="upload" id="upload" />
									</label>
								</td>
								<td>
									<label>
										<?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?>:
										<input type="text" name="description" value="" />
									</label>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('COMMENT_LEGEND_EMAIL'); ?>:</legend>
					<table class="admintable" width="100%">
						<tbody>
			 				<tr>
								<!-- <td>
									<label>
										<input class="option" type="checkbox" name="email_admin" id="email_admin" value="1" checked="checked" /> 
										<?php echo JText::_('COMMENT_SEND_EMAIL_ADMIN'); ?>
									</label>
								</td> -->
								<td>
									<label>
										<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" /> 
										<?php echo JText::_('COMMENT_SEND_EMAIL_SUBMITTER'); ?>
									</label>
								</td>
								<td>
									<label>
										<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" /> 
										<?php echo JText::_('COMMENT_SEND_EMAIL_OWNER'); ?>
									</label>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<label>
										<?php echo JText::_('COMMENT_SEND_EMAIL_CC'); ?>: <?php echo JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?><br />
										<input type="text" name="cc" id="cc" value="" style="width: 100%;" />
									</label>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
<?php } ?>
			</fieldset>
		 </div><!-- / .col width-70 -->
		 <div class="col width-30">
			<p><?php echo JText::_('COMMENT_FORM_EXPLANATION'); ?></p>
		 </div><!-- / .col width-30 -->
		<div class="clr"></div>
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="find" value="<?php echo urlencode($filters['_find']); ?>" />
			<input type="hidden" name="show" value="<?php echo urlencode($filters['_show']); ?>" />
			<input type="hidden" name="filter_order" value="<?php echo $filters['sort']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $filters['sortdir']; ?>" />
		</form>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			//if (form.summary.value == '') {
			//	alert( 'Ticket must have a summary' );
			//} else {
				submitform( pressbutton );
			//}
		}
		function getMessage()
		{
			var id = getSelectedOption( 'adminForm', 'messages' );
			if(id.value != 'mc') {
				var hi = document.getElementById(id.value).value;
				var co = document.getElementById('comment');
				co.value = hi;
			} else {
				var co = document.getElementById('comment');
				co.value = '';
			}
		}
		
		if ($('toggle-details')) {
			$('toggle-details').onclick = function() {
				var tbody = $('ticket-details-body');
				if (tbody.hasClass('hide')) {
					tbody.removeClass('hide');
				} else {
					tbody.addClass('hide');
				}
				return false;
			}	
		}
		
		if ($('make-private')) {
			$('make-private').onclick = function() {
				var es = $('email_submitter');
				if (this.checked == true) {
					if (es.checked == true) {
						es.checked = false;
						es.disabled = true;
					}
				} else {
					es.disabled = false;
				}
			}
		}
		</script>
		<?php
	}
	
	//-----------
	
	public function editMessage( $row, $action, $option ) 
	{
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancelmsg') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.message.value == '') {
				alert( '<?php echo JText::_('MESSAGE_ERROR_NO_TEXT'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class="col width-60">
				<fieldset class="adminform">
					<legend><?php echo JText::_('MESSAGE_LEGEND'); ?></legend>
					
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="title"><?php echo JText::_('MESSAGE_SUMMARY'); ?>: <span class="required">*</span></label></td>
								<td><input type="text" name="title" id="title" value="<?php echo $row->title; ?>" size="50" /></td>
							</tr>
				 			<tr>
								<td class="key" style="vertical-align: top;"><label for="message"><?php echo JText::_('MESSAGE_TEXT'); ?>: <span class="required">*</span></label></th>
								<td><?php echo $editor->display('message', stripslashes($row->message), '360px', '200px', '50', '10'); ?></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-40">
				<p><?php echo JText::_('MESSAGE_TEXT_EXPLANATION'); ?></p>
				<p><?php echo JText::_('MESSAGE_TICKET_NUM_EXPLANATION'); ?></p>
			</div>
			<div class="clr"></div>
		
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savemsg" />
		</form>
		<?php
	}
	
	//-----------
	
	public function editCategory( $row, $action, $option, $sections ) 
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancelcat') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.category.value == '') {
				alert( '<?php echo JText::_('CATEGORY_ERROR_NO_TEXT'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<fieldset class="adminform">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="section"><?php echo JText::_('CATEGORY_SECTION'); ?>: <span class="required">*</span></label></td>
							<td><?php echo SupportHtml::selectObj('section',$sections,$row->section); ?></td>
						</tr>
						<tr>
							<td class="key"><label for="category"><?php echo JText::_('CATEGORY_TEXT'); ?>: <span class="required">*</span></label></td>
							<td><input type="text" name="category" id="category" value="<?php echo $row->category; ?>" size="50" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savecat" />
		</form>
		<?php
	}
	
	//-----------
	
	public function editSection( $row, $action, $option ) 
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancelsec') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.section.value == '') {
				alert( '<?php echo JText::_('SECTION_ERROR_NO_TEXT'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<fieldset class="adminform">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="section"><?php echo JText::_('SECTION_TEXT'); ?>:</label></td>
							<td><input type="text" name="section" id="section" value="<?php echo $row->section; ?>" size="50" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savesec" />
		</form>
		<?php
	}
	
	//-----------
	
	public function editResolution( $row, $action, $option ) 
	{
?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			
			if (pressbutton == 'cancelres') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.title.value == '') {
				alert( '<?php echo JText::_('RESOLUTION_ERROR_NO_TEXT'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<fieldset class="adminform">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="title"><?php echo JText::_('RESOLUTION_TEXT'); ?>:</label></td>
							<td><input type="text" name="title" id="title" value="<?php echo htmlentities(stripslashes($row->title), ENT_QUOTES); ?>" size="50" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<input type="hidden" name="alias" value="<?php echo $row->alias; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="savesec" />
		</form>
		<?php
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function media( $sconfig, $listdir, $option ) 
	{
		?>
		<form action="index3.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
			<p>To include a file in a comment, use the file's reference tag. For example: "As you can see from the screenshot there is a bug. {attachment#123}"</p>
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="index3.php?option=<?php echo $option; ?>&amp;task=list&amp;listdir=<?php echo $listdir; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
				</div>
			</fieldset>
			
			<fieldset>
				<table cellpadding="0" cellspacing="0">
				 <tbody>
				  <tr>
				   <td><input type="file" name="upload" id="upload" /></td>
				   <td><input type="submit" value="Upload" /></td>
				  </tr>
				  <tr>
				   <td><?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?><br /><input type="text" name="description" value="" /></td>
				   <td></td>
				  </tr>
				 </tbody>
				</table>

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="listdir" id="listdir" value="<?php echo $listdir; ?>" />
				<input type="hidden" name="task" value="upload" />
			</fieldset>
			<p style="color:#ccc;">listdir = <?php echo JPATH_ROOT.$sconfig['webpath'].DS.$listdir; ?></p>
		</form>
		<?php
	}

	//-----------

	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1){
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		echo '<p>'.JText::_('NO_FILES_FOUND').'</p>'.n;
	}

	//-----------

	public function draw_table_header() 
	{
		echo t.t.'<form action="index.php" method="post" name="filelist" id="filelist">'.n;
		echo t.t.'<table border="0" cellpadding="0" cellspacing="0">'.n;
	}

	//-----------

	public function draw_table_footer() 
	{
		echo t.t.'</table>'.n;
		echo t.t.'</form>'.n;
	}

	//-----------

	public function show_doc($doc, $listdir, $icon, $id, $desc='') 
	{
		$html  = t.t.'<tr>'.n;
		$html .= t.t.t.'<td>{attachment#'. $id .'}</td>'.n;
		$html .= t.t.t.'<td><img src="'. $icon .'" alt="'. $doc .'" width="16" height="16" /></td>'.n;
		$html .= t.t.t.'<td width="100%" style="padding-left: 0;">'. $doc;
		if ($desc) { 
			$html .= '<br /><small>'. $desc .'</small>';
		}
		$hmtl .= '</td>'.n;
		$html .= t.t.t.'<td><a class="delete" href="index3.php?option=com_support&amp;task=deletefile&amp;delFile='. $doc .'&amp;listdir='. $listdir .'" onclick="return deleteImage(\''. $doc .'\');" title="Delete this document">Delete</a></td>'.n;
		$html .= t.t.'</tr>'.n;

		return $html;
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} elseif ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	}
	
	//-----------
	
	public function imageStyle($listdir)
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for (i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if ((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo $listdir ;} else { echo '/';}  ?>')
				{
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if (confirm("Delete file \""+file+"\"?"))
				return true;

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if (numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if (confirm('Delete folder "'+folder+'"?'))
				return true;
	
			return false;
		}
		</script>
		<?php
	}
	
	public function abusereport( $report, $reported, $option, $parentid, $title )
	{
		$reporter =& JUser::getInstance($report->created_by);
		
		$link = '';
	
		if (is_object($reported)) {
			$author =& JUser::getInstance($reported->author);
			
			if (is_object($author) && $author->get('username')) {
				$title .= ' by '.$author->get('username'); 
			} else {
				$title .= ' by '.JText::_('UNKNOWN'); 
			}
			$title .= ($reported->anon) ? '('.JText::_('ANONYMOUS').')':'';
			
			$link = '../'.$reported->href;
		}

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
			<table class="adminlist">
				<thead>
					<tr>
						<td colspan="2"><?php echo JText::_('ITEM_REPORTED_AS_ABUSIVE'); ?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<h4><?php echo '<a href="'.$link.'">'.$title.'</a>: ' ?></h4>
							<p><?php echo (is_object($reported)) ? stripslashes($reported->text) : ''; ?></p>
                            <?php if (is_object($reported) && isset($reported->subject) && $reported->subject!='') { echo '<p>'.stripslashes($reported->subject) .'</p>';   }?>
							<p style="color:#999;">
								<?php echo JText::_('REPORTED_BY'); ?> <?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : JText::_('UNKNOWN'); ?>, <?php echo JText::_('RECEIVED'); ?> <?php echo JHTML::_('date', $report->created, '%d %b, %Y'); ?>: 
								<?php 
								if ($report->report) {
									echo stripslashes($report->report);
								} else {
									echo stripslashes($report->subject);
								}
								?>
							</p>
						</td>
						<td >
						<?php if ($report->state==0) { ?>
							<?php echo JText::_('TAKE_ACTION'); ?>:<br />
							<label><input type="radio" name="task" value="releasereport" /> <?php echo JText::_('RELEASE_ITEM'); ?></label><br />
							<label><input type="radio" name="task" value="deletereport" /> <?php echo JText::_('DELETE_ITEM'); ?> (Append explanation below - optional)</label><br />
                            <label><textarea name="note" id="note" rows="5" cols="25" style="width: 100%;"></textarea></label><br />
							<label><input type="radio" name="task" value="abusereports" /> <?php echo JText::_('DECIDE_LATER'); ?></label>
						<?php } else { ?>
							<input type="hidden" name="task" value="view" />
						<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="id" value="<?php echo $report->id ?>" />
			<input type="hidden" name="parentid" value="<?php echo $parentid ?>" />
		</form>
        <?php
	}
	
	//-----------
	
	public function abusereports( $database, $rows, $pageNav, $option, $filters )
	{
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
					<?php echo JText::_('SHOW'); ?>:
					<select name="state" onchange="document.adminForm.submit( );">
						<option value="0"<?php if ($filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('OUTSTANDING'); ?></option>
						<option value="1"<?php if ($filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('RELEASED'); ?></option>
					</select>
				</label> 

				<label>
					<?php echo JText::_('SORT_BY'); ?>:
					<select name="sortby" onchange="document.adminForm.submit( );">
						<option value="a.category"<?php if ($filters['sortby'] == 'a.category') { echo ' selected="selected"'; } ?>><?php echo JText::_('CATEGORY'); ?></option>
						<option value="a.created DESC"<?php if ($filters['sortby'] == 'a.created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('MOST_RECENT'); ?></option>
					</select>
				</label> 
			</fieldset>
		
			<table class="adminlist">
				<thead>
					<tr>
						<th><?php echo JText::_('STATUS'); ?></th>
						<th><?php echo JText::_('REPORTED_ITEM'); ?></th>
						<th><?php echo JText::_('REASON'); ?></th>
						<th><?php echo JText::_('BY'); ?></th>
						<th><?php echo JText::_('DATE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$status = '';
			switch ($row->state) 
			{
				case '1':
					$status = JText::_('RELEASED');
					break;
				case '0':
					$status = '<span class="yes">'.JText::_('NEW').'</span>';
					break;
			}
	
			$juser =& JUser::getInstance($row->created_by);
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><?php echo $status;  ?></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=abusereport&amp;id=<?php echo $row->id; ?>&amp;cat=<?php echo $row->category; ?>"><?php echo ($row->category.' #'.$row->referenceid); ?></a></td>
						<td><?php echo $row->subject; ?></td>
						<td><?php echo $juser->get('username');  ?></td>
						<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y'); ?></td>	   
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="abusereports" />
		</form>
		<?php
	}
	
	//-----------

	public function taggroup( &$rows, &$pageNav, $option ) 
	{
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

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class="adminlist" id="tktlist">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('SUPPORT_COL_TAG'); ?></th>
						<th><?php echo JText::_('SUPPORT_COL_GROUP'); ?></th>
						<th colspan="3"><?php echo JText::_('SUPPORT_COL_PRIORITY'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) 
			{
				$row = &$rows[$i];
				$row->position = null;
?>
					<tr>
						<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edittg&amp;id=<? echo $row->id; ?>"><?php echo $row->tag; ?></a></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edittg&amp;id=<? echo $row->id; ?>"><?php echo $row->description.' ('.$row->cn.')'; ?></a></td>
						<td><?php echo $row->priority; ?></td>
						<td><?php echo $pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?></td>
						<td><?php echo $pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?></td>
					</tr>
<?php
				$k = 1 - $k;
			}
?>
				</tbody>
			</table>

			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
<?php
	}
	
		//-----------

		public function edittg( $row, $tag, $group, $action, $option ) 
		{
	?>
			<script type="text/javascript">
			function submitbutton(pressbutton) 
			{
				var form = document.adminForm;

				if (pressbutton == 'canceltg') {
					submitform( pressbutton );
					return;
				}

				// form field validation
				if (form.tag.value == '') {
					alert( '<?php echo JText::_('TAG_ERROR_NO_TEXT'); ?>' );
				} else {
					submitform( pressbutton );
				}
			}
			</script>

			<form action="index.php" method="post" name="adminForm" id="adminForm">
				<fieldset class="adminform">
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="tag"><?php echo JText::_('TAG_TEXT'); ?>: <span class="required">*</span></label></td>
								<td><input type="text" name="tag" id="tag" value="<?php echo $tag->tag; ?>" size="50" /></td>
							</tr>
							<tr>
								<td class="key"><label for="group"><?php echo JText::_('GROUP_TEXT'); ?>: <span class="required">*</span></label></td>
								<td><input type="text" name="group" id="group" value="<?php echo $group->cn; ?>" size="50" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>

				<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
				<input type="hidden" name="tagid" value="<?php echo $row->tagid; ?>" />
				<input type="hidden" name="groupid" value="<?php echo $row->groupid; ?>" />
				<input type="hidden" name="priority" value="<?php echo $row->priority; ?>" />
				
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="savecat" />
			</form>
			<?php
		}
}
?>