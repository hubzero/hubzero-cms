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

$projects = $this->rows;
$juser = $this->juser;
$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
switch ($this->which) 
{
	case 'group':  		$title = JText::_('PLG_GROUPS_PROJECTS_SHOW_GROUP');     	break;
	case 'owned': 		$title = JText::_('PLG_GROUPS_PROJECTS_SHOW_OWNED');   	break;
	case 'other':    	$title = JText::_('PLG_GROUPS_PROJECTS_SHOW_OTHER');    	break; 
	default:
	case 'all': 		$title = JText::_('PLG_GROUPS_PROJECTS_SHOW_ALL');     	break;        
}
?>
<?php if ($projects && count($projects) > 0) { ?>
<table class="listing entries">
	<caption><?php echo $title.' ('.count($projects).')'; ?></caption>
	<thead>
		<tr>
			<th class="th_image" colspan="2"></th>
			<th><?php echo JText::_('COM_PROJECTS_TITLE'); ?></th>
			<th><?php echo JText::_('COM_PROJECTS_STATUS'); ?></th>
			<th><?php echo JText::_('COM_PROJECTS_MY_ROLE'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
	$i = 0;
	foreach ($projects as $row)
	{
			$thumb = ProjectsHTML::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
			$goto  = 'alias=' . $row->alias;
			$owned_by = '';
			if($row->owned_by_group) {
				$owned_by .= JText::_('PLG_GROUPS_GROUP').' <a href="">'.Hubzero_View_Helper_Html::shortenText($row->groupname, 40, 0).'</a> | ';
			}
			else if($row->created_by_user == $juser->get('id')) {
				//$owned_by .= JText::_('PLG_GROUPS_ME');
			}	
			else {
				$owned_by .= '<a href="">'.$row->authorname.'</a> | ';
			}
			$role = $row->role == 1 ? JText::_('PLG_GROUPS_STATUS_MANAGER') : JText::_('PLG_GROUPS_STATUS_COLLABORATOR');
			$setup = ($row->setup_stage < $setup_complete) ? JText::_('PLG_GROUPS_STATUS_SETUP') : '';

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
							$html .= '<span class="active"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('COM_PROJECTS_GO_TO_PROJECT').'">&raquo; '.JText::_('PLG_GROUPS_STATUS_ACTIVE').'</a></span>';
						}
						else if ($row->setup_stage < $setup_complete) {
								$html .= '<span class="setup"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('COM_PROJECTS_CONTINUE_SETUP').'">&raquo; '.JText::_('PLG_GROUPS_STATUS_SETUP').'</a></span> ';
						}
						else if($row->state == 0) {
							$html .= '<span class="faded italic">'.JText::_('PLG_GROUPS_STATUS_SUSPENDED').'</span> ';
						}
					}
					echo $html;
				
				?>
				</td>
				<td class="th_role">
					<?php echo $row->role == 1 ? JText::_('PLG_GROUPS_STATUS_MANAGER') : JText::_('PLG_GROUPS_STATUS_COLLABORATOR') ;?>
				</td>
			</tr>
<?php
	}
?>
	</tbody>
	</table>
<?php } else { ?>
	<div class="entries">
		<h4 class="th_header"><?php echo $title.' ('.count($projects).')'; ?></h4>
		<p class="noprojects"><?php echo JText::_('PLG_GROUPS_NO_PROJECTS'); ?></p>
	</div>
<?php } ?>