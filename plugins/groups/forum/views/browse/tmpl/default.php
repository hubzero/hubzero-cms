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
?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<table id="forum-list">
<?php if ($this->pageNav) { ?>
		<caption>
			<fieldset>
				<label>
					<?php echo JText::_('PLG_GROUPS_FORUM_SEARCH'); ?>
					<input type="text" name="q" value="<?php echo htmlentities($this->search, ENT_QUOTES); ?>" />
				</label>
				<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_GO'); ?>" />
			</fieldset>
			<span class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_NEW_TOPIC'); ?></a></span>
		</caption>
<?php } ?>
		<thead>
			<tr>
				<th><?php echo JText::_('PLG_GROUPS_FORUM_TOPIC'); ?></th>
				<th><?php echo JText::_('PLG_GROUPS_FORUM_REPLIES'); ?></th>
				<th><?php echo JText::_('PLG_GROUPS_FORUM_AUTHOR'); ?></th>
				<th><?php echo JText::_('PLG_GROUPS_FORUM_LAST_POST'); ?></th>
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
				<th colspan="2"><?php echo JText::_('PLG_GROUPS_FORUM_CONTROLS'); ?></th>
<?php } ?>
			</tr>
		</thead>
		<tbody>
<?php 
	if ($this->rows) {
		$cls = 'even';
		
		foreach ($this->rows as $row) 
		{
			$name = JText::_('ANONYMOUS');
			if (!$row->anonymous) {
				$juser =& JUser::getInstance( $row->created_by );
				if (is_object($juser) && $juser->get('name')) {
					$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.stripslashes($juser->get('name')).'</a>';
				}
			}
			
			$lpname = '';
			$lastpost = $this->forum->getLastPost( $row->id );
			if ($lastpost && count($lastpost) > 0) {
				$lastpost = $lastpost[0];
				
				if (!$lastpost->anonymous) {
					$vuser =& JUser::getInstance( $lastpost->created_by );
					if (is_object($vuser) && $vuser->get('name')) {
						$lpname = '<a href="'.JRoute::_('index.php?option=com_members&id='.$lastpost->created_by).'">'.stripslashes($vuser->get('name')).'</a>';
					}
				} else {
					$lpname = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
				}
			}

			$forumpages = new XForumPagination( $row->replies, 0, $this->limit, $row->id );
			
			$cls = (($cls == 'even') ? 'odd' : 'even');
?>
			<tr class="<?php echo $cls; ?>">
				<td>
					<?php if ($row->sticky == 1) {
						echo '<strong>'.JText::_('PLG_GROUPS_FORUM_STICKY').'</strong> ';
					} ?>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id); ?>"><?php echo stripslashes($row->topic); ?></a>
					<?php if ($row->replies > $this->limit) {
						echo '<br /><span class="forum-pages">('.$forumpages->getPagesLinks().')</span>';
					} ?>
				</td>
				<td><?php echo $row->replies; ?></td>
				<td>
					<?php echo $name; ?><br />
					<span class="post-date"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id); ?>"><?php echo $row->created; ?></a></span>
				</td>
				<td>
<?php if ($lpname) { ?>
					<?php echo $lpname; ?><br />
					<span class="lastpost-date"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id.'#c'.$lastpost->id); ?>"><?php echo $lastpost->created; ?></a></span>
<?php } ?>
				</td>
<?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
				<td><a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id.'&task=deletetopic'); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?></a></td>
				<td><a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&topic='.$row->id.'&task=edittopic'); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?></a></td>
<?php } ?>
			</tr>
<?php
		}
	} else {
?>
			<tr class="odd">
				<td colspan="5"><?php echo JText::_('PLG_GROUPS_FORUM_NO_TOPICS_FOUND'); ?></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
	<?php 
	if ($this->pageNav) {
             // @FIXME: Nick's Fix Based on Resources View
             $pf = $this->pageNav->getListFooter();
             $nm = str_replace('com_','',$this->option);
             $pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?',$pf);
             echo $pf;
             //echo $this->pageNav->getListFooter();
             // @FIXME: End Nick's Fix
	}
	?>
</form>
