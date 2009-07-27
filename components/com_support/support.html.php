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

if (!defined('n')) {
	define('n',"\n");
	define('t',"\t");
	define('r',"\r");
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

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}

	//-----------
	
	public function getStatus($int)
	{
		switch ($int)
		{
			case 0: $status = JText::_('TICKET_STATUS_NEW');      break;
			case 1: $status = JText::_('TICKET_STATUS_WAITING');  break;
			case 2: $status = JText::_('TICKET_STATUS_RESOLVED'); break;
		}
		return $status;
	}
	
	//-----------

	public function shortenText($text, $chars=500) 
	{
		$text = strip_tags($text);
		$text = trim($text);
		
		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
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

	public function writeUseroptions($admin=0)
	{
		$html  = '<ul id="useroptions">'.n;
		$html .= ' <li class="last"><a href="'.JRoute::_('index.php?option=com_support&task=tickets').'">';
		$html .= ($admin) ? '' : 'My ';
		$html .= 'Support Tickets</a></li>'.n;
		$html .= '</ul>'.n;
		return $html;
	}
	
	//-----------

	public function collapseFilters( $filters )
	{
		$fstring = array();
		foreach ($filters as $key=>$val)
		{
			if (substr($key,0,1) != '_' && $key != 'limit' && $key != 'start') {
				if ($val !== '') {
					$fstring[] = $key.':'.$val;
				}
			}
		}
		$fstring = implode(' ',$fstring);
		return trim($fstring);
	}
	
	//-----------

	public function tickets( $database, &$rows, &$pageNav, $option, $filters, $total, $admin, $lists, $title ) 
	{
		if ($filters['_show'] != '') {
			$fstring = urlencode(trim($filters['_show']));
		} else {
			$fstring = urlencode(trim($filters['_find']));
		}
		
		$xhub =& XFactory::getHub();
		?>
		<div id="content-header">
			<h2><?php echo $title; ?></h2>
		</div><!-- / #content-header -->
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last"><a class="new-ticket" href="/feedback/report_problems/"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->

		<div class="main section">
			<form action="<?php echo JRoute::_('index.php?option='.$option.a.'task=tickets'); ?>" method="post" name="adminForm">
					<fieldset class="filters">
						<label>
							<?php echo JText::_('SUPPORT_FIND'); ?>:
							<input type="text" name="find" id="find" value="<?php echo ($filters['_show'] == '') ? htmlentities($filters['_find']) : ''; ?>" />
						</label>
						
						<a title="DOM:guide" class="fixedImgTip" href="<?php echo JRoute::_('index.php?option='.$option.'&task=tickets&action=help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
						<div style="display:none;" id="guide">
							<table id="keyword-guide" summary="<?php echo JText::_('SUPPORT_KEYWORD_TBL_SUMMARY'); ?>">
								<thead>
									<tr>
										<th colspan="2"><?php echo JText::_('SUPPORT_KEYWORD_GUIDE'); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th>q:</th>
										<td>"search term"</td>
									</tr>
									<tr>
										<th>status:</th>
										<td>open, closed, all</td>
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
							</table>
						</div>
						<?php echo JText::_('OR'); ?>
						<label>
							<?php echo JText::_('SHOW'); ?>:
							<select name="show">
								<option value=""<?php if ($filters['_show'] == '') { echo ' selected="selected"'; } ?>>--</option>
								<option value="status:open"<?php if ($filters['_show'] == 'status:open') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_OPEN'); ?></option>
								<option value="status:closed"<?php if ($filters['_show'] == 'status:closed') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_CLOSED'); ?></option>
								<option value="status:all"<?php if ($filters['_show'] == 'status:all') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_ALL'); ?></option>
								<?php if ($admin) { ?>
								<option value="reportedby:me"<?php if ($filters['_show'] == 'reportedby:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_REPORTED_BY_ME'); ?></option>
								<option value="status:open owner:me"<?php if ($filters['_show'] == 'status:open owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_OPEN'); ?></option>
								<option value="status:closed owner:me"<?php if ($filters['_show'] == 'status:closed owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_CLOSED'); ?></option>
								<option value="status:all owner:me"<?php if ($filters['_show'] == 'status:all owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_ALL'); ?></option>
								<?php } ?>
							</select>
						</label>

						<input type="submit" value="<?php echo JText::_('GO'); ?>" />
						
						<?php /*<a class="feed" id="ticket-feed" href="<?php echo $xhub->getCfg('hubLongURL'); ?>/support/tickets/feed.rss<?php echo ($fstring) ? '?'.$fstring : ''; ?>"><?php echo JText::_('SUPPORT_RSS'); ?></a>*/ ?>
					</fieldset>
					
					<table id="tktlist">
						<thead>
							<tr>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_NUM'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_SUMMARY'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_STATUS'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_GROUP'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_OWNER'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_AGE'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_COMMENTS'); ?></th>
								<th scope="col"><?php echo JText::_('SUPPORT_COL_ACTION'); ?></th>
							</tr>
						</thead>
<?php //if (count($rows) > $filters['limit']) { ?>
						<tfoot>
							<tr>
								<td colspan="8"><?php 
								$html = $pageNav->getListFooter();
								$html = str_replace('support/?','support/tickets/?',$html);
								$html = str_replace('/?/tickets&amp;','/?',$html);
								if ($filters['_show'] && !strstr( $html, 'show=' )) {
									$html = str_replace('/?','/?show='.$filters['_show'].'&amp;',$html);
								}
								if ($filters['_find'] && !strstr( $html, 'find=' )) {
									$html = str_replace('/?','/?find='.$filters['_find'].'&amp;',$html);
								}
								echo $html;
								?></td>
							</tr>
						</tfoot>
<?php //} ?>
						<tbody>
<?php
		$k = 0;
		$sc = new SupportComment( $database );
		$st = new SupportTags( $database );
		
		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row = &$rows[$i];
			
			$comments = $sc->countComments($admin, $row->id);
			if ($comments > 0) {
				$lastcomment = $sc->newestComment($admin, $row->id);
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
			
			$row->report = htmlentities(stripslashes($row->report),ENT_QUOTES);
			$row->report = str_replace(r,'',$row->report);
			$row->report = str_replace(n,'',$row->report);
			$row->report = str_replace(t,'',$row->report);
			
			$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
							<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
								<td><?php echo $row->id; ?></td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option='.$option.'&task=ticket&id='.$row->id); echo ($fstring != '') ? '?find='.$fstring : ''; ?>" title="<?php echo $row->report; ?>"><?php echo htmlentities(stripslashes($row->summary),ENT_QUOTES); ?></a>
									<span class="reporter">by <?php echo $row->name; echo ($row->login) ? ' (<a href="index.php?option=com_whois&amp;query=uid%3D'.$row->login.'">'.$row->login.'</a>)' : ''; ?>, <?php echo JText::_('TAGS'); ?>: <?php echo $tags; ?></span>
								</td>
								<td style="white-space: nowrap;"><span class="<?php echo $status; ?> status"><?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' ('.$row->resolved.')' : ''; ?></span></td>
								<td style="white-space: nowrap;"><?php echo $row->group; ?></td>
								<td style="white-space: nowrap;"><?php echo $row->owner; ?></td>
								<td style="white-space: nowrap;"><?php echo $when; ?></td>
								<td style="white-space: nowrap;"><?php echo $comments; echo ($comments > 0) ? ' ('.SupportHtml::timeAgo($lastcomment).')' : ''; ?></td>
								<td><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=delete&id='.$row->id); ?>"><?php echo JText::_('SUPPORT_DELETE'); ?></a></td>
							</tr>
<?php
			$k = 1 - $k;
		}
?>
						</tbody>
					</table>
				
				<input type="hidden" name="option" value="<?php echo $option ?>" />
				<input type="hidden" name="task" value="tickets" />
			</form>
		</div><!-- /.main section -->
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
	
	public function view(&$row, $option, &$comments, $admin, $error, $lists, $filters, $title) 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();

		$status = SupportHtml::getStatus($row->status);
		
		$fstring = urlencode(trim($filters['_find']));
?>
		<div id="content-header">
			<h2><?php echo $title; ?></h2>
			<h3><?php echo JText::_('TICKET_SUBMITTED_ON').' '.JHTML::_('date',$row->created, '%d %b, %Y').' '.JText::_('AT').' '.JHTML::_('date', $row->created, '%I:%M %p').' '.JText::_('BY'); ?> <a href="mailto:<?php echo $row->email; ?>"><?php echo ($row->login) ? $row->name.' ('.$row->login.')' : $row->name; ?></a></h3>
		</div><!-- / #content-header -->
		
		<div id="content-header-extra">
			<ul id="useroptions">
				<li><?php
				if ($row->prev) {
					echo '<a href="'.JRoute::_('index.php?option='.$option.'&task=ticket&id='. $row->prev).'?find='.$fstring.'">'.JText::_('PREVIOUS_TICKET').'</a>';
				} else {
					echo '<span>'.JText::_('PREVIOUS_TICKET').'</span>';
				}
				?></li>
<?php if (!$juser->get('guest')) { ?>
				<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=tickets').'?'.$fstring; ?>"><?php echo JText::_('TICKETS'); ?></a></li>
<?php } ?>
				<li class="last"><?php
				if ($row->next) {
					echo '<a href="'.JRoute::_('index.php?option='.$option.'&task=ticket&id='. $row->next) .'?find='.$fstring.'">'.JText::_('NEXT_TICKET').'</a>';
				} else {
					echo '<span>'.JText::_('NEXT_TICKET').'</span>';
				}
				?></li>
			</ul>
		</div><!-- / #content-header-extra -->
		
<?php if ($error) { echo SupportHtml::error( $error ); } ?>

		<div class="overview section">
			<div class="aside">
				<p><?php echo ($row->status == 2) ? '<strong class="closed">'.JText::_('TICKET_STATUS_CLOSED_TICKET').'</strong>' : '<strong class="open">'.JText::_('TICKET_STATUS_OPEN_TICKET').'</strong>'; ?></p>
				<p><?php echo JText::_('TICKET'); ?> #: <strong><?php echo $row->id; ?></strong></p>
<?php
	if ($comments) {
		$lc = end($comments);
?>
				<p><?php echo JText::_('TICKET_LAST_ACTIVITY'); ?>: <strong><?php echo SupportHtml::timeAgo($lc->created); ?> ago</strong></p>
<?php
	}
?>
			</div><!-- / .aside -->

			<div class="subject">
				<?php 
				if ($row->summary) {
					echo '<h4>'.$row->summary.'</h4>'.n;
				}
				?>
				<blockquote cite="<?php echo ($row->login) ? $row->login : $row->name; ?>">
					<p><?php echo $row->report; ?></p>
				</blockquote>

				<table id="ticket-details" summary="<?php echo JText::_('TICKET_DETAILS_TBL_SUMMARY'); ?>">
					<caption id="toggle-details"><?php echo JText::_('TICKET_DETAILS'); ?></caption>
					<tbody id="ticket-details-body" class="hide">
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_EMAIL'); ?>:</th>
							<td><?php echo $row->email; ?></td>
						</tr>
						<!-- <tr>
							<th><?php //echo JText::_('TICKET_DETAILS_SECTION'); ?>:</th>
							<td><?php //echo $row->section; ?></td>
						</tr>
						<tr>
							<th><?php //echo JText::_('TICKET_DETAILS_CATEGORY'); ?>:</th>
							<td><?php //echo $row->category; ?></td>
						</tr> -->
						<tr>
							<td class="key"><?php echo JText::_('TICKET_DETAILS_TAGS'); ?>:</td>
							<td><?php echo $lists['tagcloud']; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_SEVERITY'); ?>:</th>
							<td><?php echo $row->severity; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_OWNER'); ?>:</th>
							<td><?php echo ($row->owner) ? $row->owner : 'none'; ?></td>
						</tr>
<?php if ($admin) { ?>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_OS'); ?>:</th>
							<td><?php echo $row->os; ?> / <?php echo $row->browser; ?> (<?php echo ($row->cookies) ? JText::_('COOKIES_ENABLED') : JText::_('COOKIES_DISABLED'); ?>)</td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_IP'); ?>:</th>
							<td><?php echo $row->ip; ?> (<?php echo $row->hostname; ?>)</td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_REFERRER'); ?>:</th>
							<td><?php echo ($row->referrer) ? $row->referrer : ' '; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_INSTANCES'); ?>:</th>
							<td><?php echo $row->instances; ?></td>
						</tr>
						<tr>
							<th><?php echo JText::_('TICKET_DETAILS_UASTRING'); ?>:</th>
							<td><?php echo $row->uas; ?></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
			</div><!-- / .subject -->
		</div><!-- / .section -->

		<div class="main section">
			<h3><a name="comments"></a><?php echo JText::_('TICKET_COMMENTS'); ?></h3>
<?php if (count($comments) > 0) { ?>			
			<div class="aside">
				<p class="add"><a href="#commentform"><?php echo JText::_('ADD_COMMENT'); ?></a></p>
			</div><!-- / .aside -->

			<div class="subject">
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
						
						$xuser =& XUser::getInstance( $comment->created_by );
						$name = 'Unknown';
						if (is_object($xuser)) {
							$name = $xuser->get('name');
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
						$html .= t.t.t.t.t.t.t.'<p class="name"><strong>'. $name.' ('.$comment->created_by .')</strong></p>'.n;
						if ($comment->comment) {
							/*$comment->comment = str_replace("<br />","",$comment->comment);
							$comment->comment = htmlentities(stripslashes($comment->comment));
							$comment->comment = nl2br($comment->comment);
							$comment->comment = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$comment->comment);*/
							
							$html .= t.t.t.t.t.t.t.'<blockquote cite="'. $comment->created_by .'">'.n;
							if (strstr( $comment->comment, '</p>' ) || strstr( $comment->comment, '<pre class="wiki">' )) {
								$html .= t.t.t.t.t.t.t.t.$comment->comment.n;
							} else {
								$html .= t.t.t.t.t.t.t.t.'<p>'.$comment->comment.'</p>'.n;
							}
							$html .= t.t.t.t.t.t.t.'</blockquote>'.n;
						}
						$html .= '<div class="changelog">'.$comment->changelog.'</div>';
						$html .= t.t.t.t.t.t.'</div>'.n;
						$html .= t.t.t.t.t.'</li>'.n;
					}
					$html .= t.t.t.t.'</ol>'.n;
					echo $html;
?>
			</div><!-- / .subject -->
			<div class="clear"></div>
<?php } ?>
		</div><!-- / .main section -->

<?php if ((!$juser->get('guest') && ($juser->get('username') == $row->login || $admin))) { ?>
	<div class="section">
		<div class="aside">
<?php if ($admin) { ?>
			<p><?php echo JText::_('COMMENT_FORM_EXPLANATION'); ?></p>
<?php } else { ?>
			<p>Please remember to describe problems in detail, including any steps you may have taken before encountering an error.</p>
<?php } ?>
			<?php /*
			<p>
				To include a file in a comment, use the file's reference tag. For example: "As you can see from the screenshot there is a bug. {attachment#123}"
			</p>
			<iframe width="100%" height="370" name="filer" id="filer" src="index.php?option=<?php echo $option; ?>&amp;task=media&amp;no_html=1&amp;listdir=<?php echo $row->id; ?>"></iframe>
			*/ ?>
		</div><!-- / .aside -->

		<div class="subject">
			<form action="index.php" method="post" id="hubForm" enctype="multipart/form-data">
				<h4><?php echo JText::_('COMMENT_FORM'); ?></h4>
				<fieldset>
					<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="username" value="<?php echo $juser->get('username'); ?>" />
					<input type="hidden" name="find" value="<?php echo htmlentities(urldecode($fstring), ENT_QUOTES); ?>" />
		<?php if (!$admin) { ?>
					<input type="hidden" name="access" value="0" />
		<?php } ?>

					<a name="commentform"></a>
		<?php if ($admin) { ?>
					<div class="group">
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
						<label>
							<?php echo JText::_('COMMENT_SEVERITY'); ?>:
							<?php echo SupportHtml::selectArray('severity',$lists['severities'],$row->severity); ?>
						</label>
					</div>
					<div class="clear"></div>
		<?php } ?>
					<div class="group threeup">
						<label>
							<?php echo JText::_('COMMENT_GROUP'); 
							$document =& JFactory::getDocument();
							$document->addScript('components'.DS.'com_support'.DS.'observer.js');
							$document->addScript('components'.DS.'com_support'.DS.'autocompleter.js');
							$document->addStyleSheet('components'.DS.'com_support'.DS.'autocompleter.css');
							?>:
							<input type="text" name="group" value="<?php echo $row->group; ?>" id="acgroup" value="" autocomplete="off" />
						</label>
						
						<label>
							<?php echo JText::_('COMMENT_OWNER'); ?>:
							<?php echo $lists['owner']; ?>
						</label>

						<label>
							<?php echo JText::_('COMMENT_STATUS'); ?>:
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
					</div>
					<div class="clear"></div>

					<fieldset>
						<legend><?php echo JText::_('COMMENT_LEGEND_COMMENTS'); ?>:</legend>
		<?php if ($admin) { ?>
						<div class="top group">
							<label>
								<?php
								$hi = array();
								$o  = '<select name="messages" id="messages">'.n;
								$o .= t.'<option value="mc">'.JText::_('COMMENT_CUSTOM').'</option>'.n;
								foreach ($lists['messages'] as $message)
								{
									$message->message = str_replace('"','&quot;',$message->message);
									$message->message = str_replace('&quote;','&quot;',$message->message);
									$message->message = str_replace('#XXX','#'.$row->id,$message->message);
									$message->message = str_replace('{ticket#}','#'.$row->id,$message->message);

									$o .= t.'<option value="m'.$message->id.'">'.stripslashes($message->title).'</option>'."\n";

									$hi[] = '<input type="hidden" name="m'.$message->id.'" id="m'.$message->id.'" value="'.htmlentities(stripslashes($message->message), ENT_QUOTES).'" />'.n;
								}
								$o .= '</select>'.n;
								$hi = implode(n,$hi);
								echo $o.$hi;
								?>
							</label>

							<label>
								<input class="option" type="checkbox" name="access" id="make-private" value="1" />
								<?php echo JText::_('COMMENT_PRIVATE'); ?>
							</label>
						</div>
						<div class="clear"></div>
		<?php } ?>
						<textarea name="comment" id="comment" rows="13" cols="35"></textarea>
					</fieldset>
					
					<fieldset>
						<legend><?php echo JText::_('COMMENT_LEGEND_ATTACHMENTS'); ?></legend>
						<div class="group">
							<label>
								<?php echo JText::_('COMMENT_FILE'); ?>:
								<input type="file" name="upload" id="upload" />
							</label>

							<label>
								<?php echo JText::_('COMMENT_FILE_DESCRIPTION'); ?>:
								<input type="text" name="description" value="" />
							</label>
						</div>
					</fieldset>

		<?php if ($admin) { ?>
					<fieldset>
						<legend><?php echo JText::_('COMMENT_LEGEND_EMAIL'); ?>:</legend>
						<div class="group">
						<!-- <div class="group threeup">
							<label>
								<input class="option" type="checkbox" name="email_admin" id="email_admin" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_ADMIN'); ?>
							</label> -->
							<label>
								<input class="option" type="checkbox" name="email_submitter" id="email_submitter" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_SUBMITTER'); ?>
							</label>
							<label>
								<input class="option" type="checkbox" name="email_owner" id="email_owner" value="1" checked="checked" /> 
								<?php echo JText::_('COMMENT_SEND_EMAIL_OWNER'); ?>
							</label>
						</div>
						<div class="clear"></div>

						<label>
							<?php echo JText::_('COMMENT_SEND_EMAIL_CC'); ?>: <span class="hint"><?php echo JText::_('COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'); ?></span>
							<input type="text" name="cc" value="" size="38" />
						</label>
					</fieldset>
		<?php } ?>
					<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT_COMMENT'); ?>" /></p>
				</fieldset>
			</form>
		</div><!-- / .subject -->
	</div><!-- / .section -->
	<div class="clear"></div>
<?php
		}
	}
	
	//-----------
	
	public function thanks( $refid, $option, $returnlink) 
	{
		$html  = SupportHtml::div( SupportHtml::hed(2,JText::_('SUPPORT')), 'full', 'content-header');
		$html .= '<div class="main section">'.n;
		$html .= t.SupportHtml::hed(3,JText::_('REPORT_ABUSE'),'').n;
		$html .= t.'<p>'.JText::_('REPORT_ABUSE_THANKS');
		if ($returnlink) {
			$html .= ' <a href="'.$returnlink.'">'.JText::_('REPORT_ABUSE_CONTINUE').'</a>'.n;
		}
		$html .= '</p>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		return $html;
	}

	//-----------

	public function reportabuse( $juser, $report, $cat, $refid, $parent, $option, $error)
	{
		$html  = SupportHtml::div( SupportHtml::hed(2,JText::_('SUPPORT')), 'full', 'content-header');

		if ($report && !$error) {
			$name = JText::_('ANONYMOUS');
			if ($report->anon == 0) {
				$xuser =& XUser::getInstance( $report->author);
				$name = JText::_('UNKNOWN');
				if (is_object($xuser)) {
					$name = $xuser->get('name');
				}
			}
			
			$html .= '<div class="main section">'.n;
			$html .= '<form action="index.php" method="post" id="hubForm">'.n;
			$html .= t.'<div class="explaination">'.n;
			$html .= t.t.'<p>'.JText::_('REPORT_ABUSE_EXPLANATION').'</p>'.n;
			$html .= t.t.'<p>'.JText::_('REPORT_ABUSE_DESCRIPTION_HINT').'</p>'.n;
			$html .= t.'</div>'.n;
			$html .= t.'<fieldset>'.n;
			$html .= t.t.t.SupportHtml::hed(3,JText::_('REPORT_ABUSE')).n;
			
			$html .= t.'<div class="abuseitem">'.n;
			$html .= t.t.'<h4>';
			$html .= ($report->href) ? '<a href="'.$report->href.'">': '';
			$html .= ucfirst($cat).' by ';
			$html .= ($report->anon != 0) ? JText::_('ANONYMOUS') : $name;
			$html .= ($report->href) ? '</a>': '';
			$html .= '</h4>'.n;
			$html .= $report->subject ? t.t.'<p><strong>'.stripslashes($report->subject).'</strong></p>'.n : '';
			$html .= t.t.'<p>'.stripslashes($report->text).'</p>'.n;
			$html .= t.'</div>'.n;
			
			$html .= t.t.'<p class="multiple-option">'.n;
			$html .= t.t.t.'<label class="option"><input type="radio" class="option" name="subject" id="subject1" value="'.JText::_('OFFENSIVE_CONTENT').'" checked="checked" /> '.JText::_('OFFENSIVE_CONTENT').'</label>'.n;
			$html .= t.t.t.'<label class="option"><input type="radio" class="option" name="subject" id="subject2" value="'.JText::sprintf('STUPID',$cat).'" /> '.JText::sprintf('STUPID',$cat).'</label>'.n;
			$html .= t.t.t.'<label class="option"><input type="radio" class="option" name="subject" id="subject3" value="'.JText::_('SPAM').'" /> '.JText::_('SPAM').'</label>'.n;
			$html .= t.t.t.'<label class="option"><input type="radio" class="option" name="subject" id="subject4" value="'.JText::_('OTHER').'" /> '.JText::_('OTHER').'</label>'.n;
			$html .= t.t.'</p>'.n;
			$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
			$html .= t.t.'<input type="hidden" name="task" value="savereport" />'.n;
			$html .= t.t.'<input type="hidden" name="category" value="'.$report->parent_category.'" />'.n;
			$html .= t.t.'<input type="hidden" name="referenceid" value="'.$refid.'" />'.n;
			$html .= t.t.'<input type="hidden" name="link" value="'.$report->href.'" />'.n;
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('REPORT_ABUSE_DESCRIPTION').': '.n;
			$html .= t.t.t.'<textarea name="report" rows="10" cols="50"></textarea>'.n;
			$html .= t.t.'</label>'.n;
			$html .= t.'</fieldset>'.n;
			$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
			$html .= '</form>'.n;
			$html .= '<div class="clear"></div></div>'.n;
		} else {
			if ($error) {
				$html .= SupportHtml::error( $error ).n;
			} else {
				$html .= SupportHtml::warning( JText::_('ERROR_NO_INFO_ON_REPORTED_ITEM') ).n;
			}
		}

		return $html;
	}
}
?>