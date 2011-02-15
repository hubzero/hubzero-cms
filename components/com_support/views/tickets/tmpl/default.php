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

if ($this->filters['_show'] != '') {
	$fstring = urlencode(trim($this->filters['_show']));
} else {
	$fstring = urlencode(trim($this->filters['_find']));
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
<?php if ($this->authorized == 'admin') { ?>
		<li><a class="stats" href="/support/stats"><?php echo JText::_('Stats'); ?></a></li>
<?php } ?>
		<li class="last"><a class="new-ticket" href="/feedback/report_problems/"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=tickets'); ?>" method="post" name="adminForm">
			<fieldset class="filters">
				<label>
					<?php echo JText::_('SUPPORT_FIND'); ?>:
					<input type="text" name="find" id="find" value="<?php echo ($this->filters['_show'] == '') ? htmlentities($this->filters['_find']) : ''; ?>" />
				</label>
				
				<a title="DOM:guide" class="fixedImgTip" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=tickets&action=help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
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
					</table>
				</div>
				<?php echo JText::_('OR'); ?>
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
						<?php if ($this->authorized) { ?>
						<option value="reportedby:me"<?php if ($this->filters['_show'] == 'reportedby:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_REPORTED_BY_ME'); ?></option>
						<option value="status:open owner:me"<?php if ($this->filters['_show'] == 'status:open owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_OPEN'); ?></option>
						<option value="status:closed owner:me"<?php if ($this->filters['_show'] == 'status:closed owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_CLOSED'); ?></option>
						<option value="status:all owner:me"<?php if ($this->filters['_show'] == 'status:all owner:me') { echo ' selected="selected"'; } ?>><?php echo JText::_('SUPPORT_OPT_MINE_ALL'); ?></option>
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
				<tfoot>
					<tr>
						<td colspan="8"><?php 
						$html = $this->pageNav->getListFooter();
						$html = str_replace('support/?','support/tickets/?',$html);
						$html = str_replace('/?/tickets&amp;','/?',$html);
						if ($this->filters['_show'] && !strstr( $html, 'show=' )) {
							$html = str_replace('/?','/?show='.$this->filters['_show'].'&amp;',$html);
						}
						if ($this->filters['_find'] && !strstr( $html, 'find=' )) {
							$html = str_replace('/?','/?find='.$this->filters['_find'].'&amp;',$html);
						}
						echo $html;
						?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
$k = 0;
$sc = new SupportComment( $this->database );
$st = new SupportTags( $this->database );

for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row = &$this->rows[$i];
	
	$comments = $sc->countComments($this->authorized, $row->id);
	if ($comments > 0) {
		$lastcomment = $sc->newestComment($this->authorized, $row->id);
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
	
	$lnk = 'index.php?option=com_whois&amp;query=uid%3D'.$row->login;
	$targetuser = null;
	if ($row->login) {
		$targetuser =& JUser::getInstance($row->login);
		if (is_object($targetuser) && $targetuser->id) {
			$lnk = JRoute::_('index.php?option=com_members&id='.$targetuser->id);
		}
	}
	
	$when = SupportHtml::timeAgo($row->created);
	
	if ($row->owner == '') {
		$row->owner = '&nbsp';
	}
	
	//$row->report = htmlentities(stripslashes($row->report),ENT_QUOTES);
	$row->report = stripslashes($row->report);
	//$row->report = str_replace(r,'',$row->report);
	//$row->report = str_replace(n,'',$row->report);
	//$row->report = str_replace(t,'',$row->report);
	
	$tags = $st->get_tag_cloud( 3, 1, $row->id );
?>
					<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
						<td><?php echo $row->id; ?></td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=ticket&id='.$row->id.'&find='.$fstring.'&limit='.$this->filters['limit'].'&limitstart='.$this->filters['start']); ?>" title="<?php echo $row->report; ?>"><?php echo stripslashes($row->summary); ?></a>
							<span class="reporter">by <?php echo $row->name; echo ($row->login) ? ' (<a href="'.$lnk.'">'.$row->login.'</a>)' : ''; if ($tags) { ?>, <?php echo JText::_('TAGS'); ?>: <span class="tags"><?php echo $tags; ?></span><?php } ?></span>
						</td>
						<td style="white-space: nowrap;"><span class="<?php echo $status; ?> status"><?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' ('.$row->resolved.')' : ''; ?></span></td>
						<td style="white-space: nowrap;"><?php echo $row->group; ?></td>
						<td style="white-space: nowrap;"><?php echo $row->owner; ?></td>
						<td style="white-space: nowrap;"><?php echo $when; ?></td>
						<td style="white-space: nowrap;"><?php echo $comments; echo ($comments > 0) ? ' ('.SupportHtml::timeAgo($lastcomment).')' : ''; ?></td>
						<td><a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$row->id); ?>" title="<?php echo JText::_('SUPPORT_DELETE'); ?>"><?php echo JText::_('SUPPORT_DELETE'); ?></a></td>
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