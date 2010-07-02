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

$maxtextlen = 42;
$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="group" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>"><?php echo JText::_('GROUPS_CREATE_GROUP'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" method="get">
	<div class="main section">
		<div class="aside">
			<fieldset>
				<label>
					<?php echo JText::_('SORT_BY'); ?>
					<select name="sortby">
						<option value="description ASC"<?php if ($this->filters['sortby'] == 'description ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Title'); ?></option>
						<option value="cn ASC"<?php if ($this->filters['sortby'] == 'cn ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Alias'); ?></option>
					</select>
				</label>
				<label>
					<?php echo JText::_('GROUPS_SEARCH'); ?>
					<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
				</label>
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>
		</div><!-- / .aside -->
		<div class="subject">

			<p id="letter-index">
<?php
$qs = array();
foreach ($this->filters as $f=>$v) 
{
	$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'type' && $f != 'fields') ? $f.'='.$v : '';
}
$qs[] = 'limitstart=0';
$qs = implode('&amp;',$qs);

$url  = 'index.php?option='.$this->option.'&task=browse';
$url .= ($qs) ? '&'.$qs : '';

$html  = "\t\t\t\t".'<a href="'.JRoute::_($url).'"';
if ($this->filters['index'] == '') {
	$html .= ' class="active-index"';
}
$html .= '>'.JText::_('ALL').'</a> '."\n";

$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
foreach ($letters as $letter)
{
	$url  = 'index.php?option='.$this->option.'&task=browse&index='.strtolower($letter);
	$url .= ($qs) ? '&'.$qs : '';
	
	$html .= "\t\t\t\t".'<a href="'.JRoute::_($url).'"';
	if ($this->filters['index'] == strtolower($letter)) {
		$html .= ' class="active-index"';
	}
	$html .= '>'.$letter.'</a> '."\n";
}
echo $html;
?>
			</p>

			<table id="grouplist" summary="<?php echo JText::_('GROUPS_BROWSE_TBL_SUMMARY'); ?>">
				<thead>
					<tr>
						<th><?php echo JText::_('Title'); ?></th>
						<th><?php echo JText::_('Join Policy'); ?></th>
<?php if ($this->authorized) { ?>
						<th><?php echo JText::_('Status'); ?></th>
						<th><?php echo JText::_('Options'); ?></th>
<?php } else { ?>
						<th> </th>
						<th> </th>
<?php } ?>
					</tr>
				</thead>
				<tbody>
<?php
if ($this->groups) {
	$cls = 'even';
	$html = '';
	foreach ($this->groups as $group) 
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';

		// Only display if the group is registered
		if (isset($group->published) && $group->published) {
			// Determine the join policy
			switch ($group->join_policy) 
			{
				case 3: $policy = '<span class="closed join-policy">'.JText::_('Closed').'</span>';      break;
				case 2: $policy = '<span class="inviteonly join-policy">'.JText::_('Invite Only').'</span>'; break;
				case 1: $policy = '<span class="restricted join-policy">'.JText::_('Restricted').'</span>';  break;
				case 0:
				default: $policy = '<span class="open join-policy">'.JText::_('Open').'</span>'; break;
			}
			
			$html .= "\t\t\t\t".'<tr class="'.$cls.'">'."\n";
			$html .= "\t\t\t\t\t".'<td><a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn) .'"';
			if (trim($group->public_desc) != '') {
				$html .= ' class="tooltips" title="Description :: '.Hubzero_View_Helper_Html::shortenText(stripslashes($group->public_desc),100,0).'"';
			}
			$html .= '>'. htmlentities($group->description) .'</a></td>'."\n";
			$html .= "\t\t\t\t\t".'<td>'.$policy.'</td>'."\n";
			if ($this->authorized) {
				$html .= "\t\t\t\t\t".'<td>';
				if ($group->manager && $group->published) {
					$html .= '<span class="manager status">'.JText::_('GROUPS_STATUS_MANAGER').'</span>';
					$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&active=members') .'">'.JText::_('GROUPS_ACTION_MANAGE').'</a>';
					$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=edit') .'">'.JText::_('GROUPS_ACTION_EDIT').'</a>';
					$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=delete') .'">'.JText::_('GROUPS_ACTION_DELETE').'</a>';
				} else {
					if (!$group->published) {
						$html .= JText::_('GROUPS_STATUS_NEW_GROUP');
					} else {
						if ($group->registered) {
							if ($group->regconfirmed) {
								$html .= '<span class="member status">'.JText::_('GROUPS_STATUS_APPROVED').'</span>';
								$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel&return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							} else {
								$html .= '<span class="pending status">'.JText::_('GROUPS_STATUS_PENDING').'</span>';
								$opt = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel&return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							}
						} else {
							if ($group->regconfirmed) {
								$html .= '<span class="invitee status">'.JText::_('GROUPS_STATUS_INVITED').'</span>';
								$opt  = '<a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=accept&return=browse') .'">'.JText::_('GROUPS_ACTION_ACCEPT').'</a>';
								$opt .= ' <a href="'.JRoute::_('index.php?option='.$this->option.'&gid='.$group->cn.'&task=cancel&return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							} else {
								$html .= '<span class="status"> </span>';
								$opt = '';
							}
						}
					}
				}
				$html .= '</td>'."\n";
				$html .= "\t\t\t\t\t".'<td>'.$opt.'</td>'."\n";
			} else {
				$html .= "\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
				$html .= "\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
			}
			$html .= "\t\t\t\t".'</tr>'."\n";
		} else {
			if (isset($group->cn) && $group->cn) {
				$html .= "\t\t\t\t".'<tr class="'.$cls.'">'."\n";
				$html .= "\t\t\t\t\t".'<td><a href="'.JRoute::_('index.php?option='.$this->option.a.'gid='. $group->cn) .'">'. htmlentities($group->description) .'</a></td>'."\n";
				$html .= "\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
				if ($this->authorized) {
					$html .= "\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
					$html .= "\t\t\t\t\t".'<td>&nbsp;</td>'."\n";
				}
				$html .= "\t\t\t\t".'</tr>'."\n";
			}
		}
	}
	echo $html;
} else { ?>
					<tr class="odd">
						<td colspan="4"><?php echo JText::_('NONE'); ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
<?php
$pn = $this->pageNav->getListFooter();
if (!strstr($pn,'/browse')) {
	$pn = str_replace('/?','/browse/?',$pn);
}
echo $pn;
?>
		</div><!-- / .subject -->
	</div><!-- / .main section -->
	<div class="clear"></div>
</form>