<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$use_alias = $this->config->get('use_alias', 0);
$projects = $this->rows;
$juser = $this->juser;
$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
switch ($this->which) 
{
	case 'group':  		$title = JText::_('PLG_MEMBERS_PROJECTS_SHOW_GROUP');     	break;
	case 'owned': 		$title = JText::_('PLG_MEMBERS_PROJECTS_SHOW_OWNED');   	break;
	case 'other':    	$title = JText::_('PLG_MEMBERS_PROJECTS_SHOW_OTHER');    	break; 
	default:
	case 'all': 		$title = JText::_('PLG_MEMBERS_PROJECTS_SHOW_ALL');     	break;        
}
?>
<h4 class="th_header"><?php echo $title.' ('.count($projects).')'; ?></h4>
<?php if ($projects && count($projects) > 0) { ?>
<table class="listing">
	<thead>
		<tr>
			<th class="th_image" colspan="2"></th>
			<th<?php if($this->filters['sortby'] == 'title') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id').a.'active=projects').'/?action=all'.a.'sortby=title'.a.'sortdir='.$sortbyDir?>" class="re_sort"><?php echo JText::_('PLG_MEMBERS_PROJECTS_TITLE'); ?></a></th>
			<th<?php if($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id').a.'active=projects').'/?action=all'.a.'sortby=status'.a.'sortdir='.$sortbyDir?>" class="re_sort"><?php echo JText::_('PLG_MEMBERS_PROJECTS_STATUS'); ?></a></th>
			<th<?php if($this->filters['sortby'] == 'role') { echo ' class="activesort"'; } ?>><a href="<?php echo JRoute::_('index.php?option=com_members'.a.'id='.$juser->get('id').a.'active=projects').'/?action=all'.a.'sortby=role'.a.'sortdir='.$sortbyDir?>" class="re_sort"><?php echo JText::_('PLG_MEMBERS_PROJECTS_MY_ROLE'); ?></a></th>
		</tr>
	</thead>
	<tbody>
<?php
	$i = 0;
	foreach ($projects as $row)
	{
			$thumb = ProjectsHTML::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
			$goto  = $use_alias ? 'alias='.$row->alias : 'id='.$row->id;
			$role = $row->role == 1 ? JText::_('PLG_MEMBERS_PROJECTS_STATUS_MANAGER') : JText::_('PLG_MEMBERS_PROJECTS_STATUS_COLLABORATOR');
			$setup = ($row->setup_stage < $setup_complete) ? JText::_('PLG_MEMBERS_PROJECTS_STATUS_SETUP') : '';

			$i++; ?>
			<tr class="mline">
				<td class="th_image"><a href="<?php echo JRoute::_('index.php?option=com_projects'.a.'task=view'.a.$goto); ?>" title="<?php echo htmlentities(ProjectsHtml::cleanText($row->title)).' ('.$row->alias.')'; ?>"><img src="<?php echo $thumb; ?>" alt="<?php echo htmlentities(ProjectsHtml::cleanText($row->title)); ?>"  class="project-image" /></a> <?php if($row->newactivity && $row->state == 1 && !$setup) { ?><span class="s-new"><?php echo $row->newactivity; ?></span><?php } ?></td>
				<td class="th_privacy"><?php if($row->private == 1) { echo '<span class="privacy-icon">&nbsp;</span>' ;} ?></td>
				<td class="th_title"><a href="<?php echo JRoute::_('index.php?option=com_projects'.a.'task=view'.a.$goto); ?>" title="<?php echo htmlentities(ProjectsHtml::cleanText($row->title)).' ('.$row->alias.')'; ?>"><?php echo ProjectsHtml::cleanText($row->title); ?></a>
				<?php if($this->which != 'owned') { ?><span class="block">
				<?php echo ($row->owned_by_group) ? $row->groupname : $row->authorname; ?></span>
				<?php } ?>
				</td>
				<td class="th_status">
				<?php
					$html = '';
					if($row->owner && $row->confirmed == 1) {
						if($row->state == 1 && $row->setup_stage >= $setup_complete) {
							$html .= '<span class="active"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('PLG_MEMBERS_PROJECTS_GO_TO_PROJECT').'">&raquo; '.JText::_('PLG_MEMBERS_PROJECTS_STATUS_ACTIVE').'</a></span>';
						}
						else if ($row->setup_stage < $setup_complete) {
								$html .= '<span class="setup"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('PLG_MEMBERS_PROJECTS_CONTINUE_SETUP').'">&raquo; '.JText::_('PLG_MEMBERS_PROJECTS_STATUS_SETUP').'</a></span> ';
						}
						else if($row->state == 0) {
							$html .= '<span class="suspended">'.JText::_('PLG_MEMBERS_PROJECTS_STATUS_SUSPENDED').'</span> ';
						}
						else if($row->state == 5) {
							$html .= '<span class="pending">'.JText::_('PLG_MEMBERS_PROJECTS_STATUS_PENDING').'</span> ';
						}
					}
					echo $html;
				
				?>
				</td>
				<td class="th_role">
					<?php echo $row->role == 1 ? JText::_('PLG_MEMBERS_PROJECTS_STATUS_MANAGER') : JText::_('PLG_MEMBERS_PROJECTS_STATUS_COLLABORATOR') ;?>
				</td>
			</tr>
<?php
	}
?>
	</tbody>
	</table>
<?php } else { ?>
	<p class="noresults"><?php echo JText::_('PLG_MEMBERS_PROJECTS_NO_PROJECTS'); ?></p>
<?php } ?>