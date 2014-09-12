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
$html = '';
$nc = 1;
?>
<ol class="comments" id="comments_<?php echo $this->activityid; ?>">
<?php
foreach ($this->comments as $comment) 
{
	$ctimeclass = $this->project->lastvisit && $this->project->lastvisit <= $comment->created ? ' class="urgency"' : '';

	// Is user allowed to delete item?
	$deletable = ($comment->created_by == $this->uid or $this->project->role == 1) ? 1 : 0;
	
	$author = $comment->admin == 1 ? JText::_('COM_PROJECTS_ADMIN') : $comment->author; 

	// Display comment
	$html .= ' <li class="quote" id="c_'.$comment->id.'">';
	if($deletable) {
		$html .= '<span class="delit-container"><span class="delit m_options" id="pu_'.$comment->id.'">';
		$html .= '<a href="'. JRoute::_('index.php?option='.$this->option.a.$this->goto.a.'task=view'.a.'active=feed').'/?action=deletecomment'.a.'cid='.$comment->id.'">&nbsp;</a>';
		$html .= '</span></span>';
	}
	$html .= stripslashes(ProjectsHtml::replaceUrls($comment->comment, 'external'));
	$html .= '<span class="block mini faded">'.$author.' &middot; <span'.$ctimeclass.'>'.ProjectsHtml::timeAgo($comment->created).' '.JText::_('COM_PROJECTS_AGO').'</span></span>';
	$html .= ' </li>';
	$nc++;							
}
echo $html;
?>
</ol>
