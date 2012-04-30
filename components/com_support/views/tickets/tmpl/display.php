<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->filters['_show'] != '') 
{
	$fstring = urlencode(trim($this->filters['_show']));
} 
else 
{
	$fstring = urlencode(trim($this->filters['_find']));
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
<?php if ($this->acl->check('read', 'tickets')) { ?>
		<li><a class="stats" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=stats'); ?>"><?php echo JText::_('Stats'); ?></a></li>
<?php } ?>
		<li class="last"><a class="new-ticket" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>"><?php echo JText::_('SUPPORT_NEW_TICKET'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display'); ?>" method="post" name="adminForm">
			<fieldset class="filters">
<?php if ($this->acl->check('read', 'tickets')) { ?>
				<label>
					<?php echo JText::_('SUPPORT_FIND'); ?>:
					<input type="text" name="find" id="find" value="<?php echo ($this->filters['_show'] == '') ? htmlentities($this->filters['_find']) : ''; ?>" />
				</label>
				
				<a title="DOM:guide" class="fixedImgTip" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=display&action=help'); ?>"><?php echo JText::_('SUPPORT_HELP'); ?></a>
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
<?php } ?>
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
						<?php if ($this->acl->check('read', 'tickets')) { ?>
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
<?php if ($this->acl->check('delete', 'tickets')) { ?>
						<th scope="col"><?php echo JText::_('SUPPORT_COL_ACTION'); ?></th>
<?php } ?>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo ($this->acl->check('delete', 'tickets')) ? '8' : '7'; ?>"><?php 
						$html = $this->pageNav->getListFooter();
						$html = str_replace('support/?', 'support/tickets/?',$html);
						$html = str_replace('/?/tickets&amp;', '/?',$html);
						if ($this->filters['_show'] && !strstr($html, 'show=')) {
							$html = str_replace('/?', '/?show='.$this->filters['_show'].'&amp;',$html);
						}
						if ($this->filters['_find'] && !strstr($html, 'find=')) {
							$html = str_replace('/?', '/?find='.$this->filters['_find'].'&amp;',$html);
						}
						echo $html;
						?></td>
					</tr>
				</tfoot>
				<tbody>
<?php
$k = 0;
$sc = new SupportComment($this->database);
$st = new SupportTags($this->database);

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$comments = $sc->countComments($this->acl->check('read', 'private_comments'), $row->id);
	if ($comments > 0) 
	{
		$lastcomment = $sc->newestComment($this->acl->check('read', 'private_comments'), $row->id);
	}

	if ($row->status == 2) 
	{
		$status = 'closed';
	} 
	elseif ($comments == 0 && $row->status == 0 && $row->owner == '' && $row->resolved == '') 
	{
		$status = 'new';
	} 
	elseif ($row->status == 1) 
	{
		$status = 'waiting';
	}
	else 
	{
		if ($row->resolved != '') 
		{
			$status = 'reopened';
		} 
		else 
		{
			$status = 'open';
		}
	}

	$lnk = 'index.php?option=com_whois&amp;query=uid%3D' . $row->login;
	$targetuser = null;
	if ($row->login) 
	{
		jimport('joomla.user.helper');
		if (($id = JUserHelper::getUserId($row->login)))
		{
			$targetuser =& JUser::getInstance($row->login);
			$lnk = JRoute::_('index.php?option=com_members&id=' . $targetuser->id);
		}
	}

	$when = SupportHtml::timeAgo($row->created);

	//$row->report = htmlentities(stripslashes($row->report),ENT_QUOTES);
	$row->report = stripslashes($row->report);
	if (!trim($row->summary)) 
	{
		$row->summary = substr($row->report, 0, 70);
		if (strlen($row->summary) >= 70) 
		{
			$row->summary .= '...';
		}
	}

	$tags = $st->get_tag_cloud(3, 1, $row->id);
?>
					<tr class="<?php echo ($row->status == 2) ? 'closed' : $row->severity; ?>">
						<td><?php echo $row->id; ?></td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id='.$row->id.'&find='.$fstring.'&limit='.$this->filters['limit'].'&limitstart='.$this->filters['start']); ?>" title="<?php echo htmlentities($row->report); ?>">
								<?php echo stripslashes($row->summary); ?>
							</a>
							<span class="reporter">
								by <?php echo $this->escape($row->name); echo ($row->login) ? ' (<a href="' . $lnk . '">' . $this->escape($row->login) . '</a>)' : ''; if ($tags) { ?>, 
								<?php echo JText::_('TAGS'); ?>: <span class="tags"><?php echo $tags; ?></span><?php } ?>
							</span>
						</td>
						<td style="white-space: nowrap;">
							<span class="<?php echo $status; ?> status">
								<?php echo ($row->status == 2) ? '&radic; ' : ''; echo $status; echo ($row->status == 2) ? ' (' . $this->escape($row->resolved) . ')' : ''; ?>
							</span>
						</td>
						<td style="white-space: nowrap;">
							<?php echo $this->escape($row->group); ?>
						</td>
						<td style="white-space: nowrap;">
							<?php echo $this->escape($row->owner); ?>
						</td>
						<td style="white-space: nowrap;">
							<?php echo $when; ?>
						</td>
						<td style="white-space: nowrap;">
							<?php echo $comments; echo ($comments > 0) ? ' (' . SupportHtml::timeAgo($lastcomment) . ')' : ''; ?>
						</td>
<?php if ($this->acl->check('delete', 'tickets')) { ?>
						<td>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&id=' . $row->id); ?>" title="<?php echo JText::_('SUPPORT_DELETE'); ?>">
								<?php echo JText::_('SUPPORT_DELETE'); ?>
							</a>
						</td>
<?php } ?>
					</tr>
<?php
	$k = 1 - $k;
}
?>
				</tbody>
			</table>
		
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="display" />
	</form>
</div><!-- /.main section -->
