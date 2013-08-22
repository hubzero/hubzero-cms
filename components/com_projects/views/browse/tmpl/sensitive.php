<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

$html  = '';
$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section" id="reviewer-list">
	<div class="status-msg">
	<?php 
		// Display error or success message
		if ($this->getError()) { 
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
<?php
	$html .= t.'<form method="get" id="browseForm" action="'.JRoute::_('index.php?option='.$this->option.a.'task=browse').'">'.n;
	// show how many
	$totalnote = JText::_('COM_PROJECTS_NOTICE_DISPLAYING').' ';
	if($this->filters['start'] == 0) {
		$totalnote .= ($this->pageNav->total > count($this->rows)) ? ' '.JText::_('COM_PROJECTS_NOTICE_TOP').' '.count($this->rows).' '.JText::_('COM_PROJECTS_NOTICE_OUT_OF').' '.$this->pageNav->total : JText::_('COM_PROJECTS_NOTICE_ALL').' '.count($this->rows) ;
	}
	else {
		$totalnote .= ($this->filters['start'] + 1);
		$totalnote .= ' - ';
		$totalnote .=$this->filters['start'] + count($this->rows);
		$totalnote .=' '.JText::_('COM_PROJECTS_NOTICE_OUT_OF').' '.$this->pageNav->total;
	}
	$totalnote .= ' '.JText::_('COM_PROJECTS_NOTICE_PROJECTS');

	$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
	
	// Loop through results
	$html .= '<div class="list-editing"><p>'.JText::_('COM_PROJECTS_SHOWING');
	if($this->total <= count($this->rows)) {
		$html .= ' '.JText::_('COM_PROJECTS_ALL').' <span class="prominent">'.$this->total.'</span> ';
	}
	else {
		$html .= ' <span class="prominent">'.count($this->rows).'</span> '.JText::_('COM_PROJECTS_OUT_OF').' '.$this->total;	
	}
	$html .= $this->filters['filterby'] == 'pending' ? strtolower(JText::_('COM_PROJECTS_PENDING')) : '';
	$html .= ' '.strtolower(JText::_('COM_PROJECTS_PROJECTS'));
	$html .= t.t.t.'<label class="ipadded"> '.JText::_('COM_PROJECTS_BROWSE_SHOW').n;
	$html .= t.t.t.' <input class="option filterby" name="filterby" ';
	$html .= $this->filters['filterby'] == 'pending' ? ' checked="checked" ' : '';
	$html .= 'type="radio" value="pending" /> ';
	$html .= JText::_('COM_PROJECTS_FILTER_PENDING').n;
	$html .= t.t.t.'</label> '.n;
	$html .= t.t.t.'<label>'.n;
	$html .= t.t.t.' &nbsp;<input class="option filterby" name="filterby" ';
	$html .= $this->filters['filterby'] == 'all' ? ' checked="checked" ' : '';
	$html .= 'type="radio" value="all" /> ';
	$html .= JText::_('COM_PROJECTS_FILTER_ALL').n;
	$html .= t.t.t.'</label> '.n;
	$html .= '<input type="hidden" name="reviewer" value="' . $this->reviewer . '" />';
	$html .= '<input type="hidden" name="limit" value="' . $this->filters['limit'] . '" />';
	$html .= '<input type="hidden" name="start" value="' . $this->filters['start'] . '" />';
	$html .= '<input type="hidden" name="sortby" value="' . $this->filters['sortby'] . '" />';
	$html .= '<input type="hidden" name="sortdir" value="' . $this->filters['sortdir'] . '" />';
	$html .= '</p></div>';
	
	if(count($this->rows) > 0) {		
		$html .= t.t.'<table class="listing" id="projectlist">'.n;
		$html .= t.t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .='<th class="th_image" colspan="2"></th>'.n;
		$html .= t.t.t.t.'<th';
		if($this->filters['sortby'] == 'title') { 
			$html .= ' class="activesort"'; 
		} 
		$html .= '><a href="'. JRoute::_('index.php?option=' . $this->option . a 
		. 'task=browse') . '/?sortby=title' . a . 'sortdir=' . $sortbyDir . a 
		. 'reviewer=' . $this->reviewer . a. 'filterby=' . $this->filters['filterby'] . '" class="re_sort">';
		$html .= JText::_('COM_PROJECTS_TITLE').'</a></th>'.n;
		$html .= t.t.t.t.'<th';
		if($this->filters['sortby'] == 'created') { 
			$html .= ' class="activesort"'; 
		} 
		$html .= '><a href="'. JRoute::_('index.php?option=' . $this->option . a 
		. 'task=browse').'/?sortby=created' . a . 'sortdir='.$sortbyDir. a 
		. 'reviewer=' . $this->reviewer . a. 'filterby=' . $this->filters['filterby'] . '" class="re_sort">';
		$html .= JText::_('COM_PROJECTS_CREATED').'</a></th>'.n;

		$html .= t.t.t.t.'<th>' . ucfirst(JText::_('COM_PROJECTS_CREATED_BY')) . '</th>'.n;
		$html .= t.t.t.t.'<th>' . JText::_('COM_PROJECTS_TYPE_OF_DATA') . '</th>'.n;
		$html .= t.t.t.t.'<th';
		if(!$this->guest) {
			if($this->filters['sortby'] == 'status') { 
				$html .= ' class="activesort"';
			}
				$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=browse').'/?sortby=status'.a.'sortdir='.$sortbyDir . a 
				. 'reviewer=' . $this->reviewer . a. 'filterby=' . $this->filters['filterby'] . '" class="re_sort">'; 
				$html .= JText::_('COM_PROJECTS_STATUS').'</a>';
		}
		else {
			$html .= '>';
		}
		$html .='</th>'.n;
		$html .= t.t.t.t.'<th></th>';
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.t.'</thead>'.n;
		$html .= t.t.t.'<tbody>'.n;
		foreach($this->rows as $row) {	
			if($row->owned_by_group && !$row->groupcn) {
				continue; // owner group has been deleted
			}
			
			// Get project params
			$params = new JParameter( $row->params );
				
			$goto  = 'alias=' . $row->alias;				
			$thumb = ProjectsHtml::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
			$html .= t.t.t.'<tr class="mline" id="tr_'.$row->id.'">'.n;
			$html .= t.t.t.t.'<td class="th_privacy">';
			$html .= '<span class="privacy-icon';
			$html .= ($row->private == 1) ? ' private' : '';
			$html .= '">&nbsp;</span>';
			$html .= '</td>'.n;
			$html .= t.t.t.t.'<td class="th_image">';
			$html .='<a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'"><img src="'.$thumb.'" alt="'.htmlentities(ProjectsHtml::cleanText($row->title)).'" /></a></td>'.n;
			$html .= t.t.t.t.'<td class="th_title"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'"  title="';
			$html .= $row->about ? htmlentities(ProjectsHtml::cleanText($row->about)) 
			: htmlentities(ProjectsHtml::cleanText($row->title));
			$html .='">'.ProjectsHtml::cleanText($row->title).'</a><span class="block mini faded">'.$row->alias.'</span></td>'.n;
			$html .= '<td class="mini faded">';
			$html .= JHTML::_('date', $row->created, $dateFormat, $tz);
			$html .= '</td>'.n;
			$html .= '<td class="mini faded">' . '<a href="/members/'.$row->created_by_user.'">'.$row->authorname.'</a>' ;
			$profile = Hubzero_User_Profile::getInstance($row->created_by_user);
			if($profile)
			{
				$html .= '<span class="block">'. $profile->get('email').'</span>';
				if($profile->get('phone'))	
				{
					$html .= '<span class="block"> Tel.'. $profile->get('phone').'</span>';
				}
			}
			$html .= '</td>'.n;
			$html .= t.t.t.t.'<td class="mini faded">';
			if($params->get('hipaa_data') == 'yes') {
				$html .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_TERMS_HIPAA') . '</span>';
			}
			if($params->get('ferpa_data') == 'yes') {
				$html .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_TERMS_FERPA') . '</span>';
			}
			if($params->get('export_data') == 'yes') {
				$html .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_EXPORT_CONTROLLED') . '</span>';
			}
			if($params->get('irb_data') == 'yes') {
				$html .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_IRB') . '</span>';
			}
			if($params->get('restricted_data') == 'maybe' && $params->get('followup') == 'yes') {
				$html .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_FOLLOW_UP_NECESSARY') . '</span>';
			}
			$html .= '</td>'.n;	
			$html .= t.t.t.t.'<td class="mini faded">';

			if($row->state == 1 && $row->setup_stage >= $setup_complete) {
				$html .= '<span class="active green">'.JText::_('COM_PROJECTS_ACTIVE').'</span>';
			}
			else if ($row->setup_stage < $setup_complete) {
					$html .= '<span class="setup">'.JText::_('COM_PROJECTS_STATUS_SETUP').'</span> '.JText::_('COM_PROJECTS_IN_PROGRESS');
			}
			else if($row->state == 0) {
				$html .= '<span class="faded italic">'.JText::_('COM_PROJECTS_STATUS_INACTIVE').'</span> ';
			}
			else if($row->state == 5) {
				$html .= '<span class="italic pending">'.JText::_('COM_PROJECTS_STATUS_PENDING').'</span>';
			}
			$comment_count = 0;
			if(isset($row->admin_notes) && $row->admin_notes) {
				$comment_count = ProjectsHtml::getAdminNoteCount($row->admin_notes, 'sensitive');
				$html .= ProjectsHtml::getLastAdminNote($row->admin_notes, 'sensitive');
			}
			$html .= '<span class="block mini"><a href="' . JRoute::_('index.php?option=' . $this->option . a . 'task=process' . a . 'id=' . $row->id ) . '?reviewer=' . $this->reviewer . a . 'action=addcomment'  . a . 'filterby=' . $this->filters['filterby'] . '" class="showinbox">' . $comment_count . ' ' . JText::_('COM_PROJECTS_COMMENTS') . '</a></span>';

			$html .= '</td>'.n;	
			$html .= t.t.t.t.'<td>';
			if($row->state == 5) {
				$html .= '<span class="manage mini"><a href="' . JRoute::_('index.php?option=' . $this->option . a . 'task=process' . a . 'id=' . $row->id ) . '?reviewer=' . $this->reviewer . a. 'filterby=' . $this->filters['filterby'] . '" class="showinbox">' . JText::_('COM_PROJECTS_APPROVE') . '</a></span>';
			}
		
			$html .= '</td>'.n;	
			$html .= t.t.t.'</tr>'.n;
		}
		$html .= t.t.t.'</tbody>'.n;
		$html .= t.t.'</table>'.n;
	}
	else {
		$html .= t.t.t.'<p class="noresults">';
		if($this->guest) {
			$html .= JText::_('COM_PROJECTS_NO_PROJECTS_FOUND').' '.JText::_('COM_PROJECTS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$this->option.a.'task=browse').'?action=login">'.JText::_('COM_PROJECTS_LOGIN').'</a> '.JText::_('COM_PROJECTS_TO_VIEW_PRIVATE_PROJECTS');
		}
		else {
			$html .= $this->filters['filterby'] == 'pending' 
			? JText::_('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_PENDING')
			: JText::_('COM_PROJECTS_NO_REVIEWER_PROJECTS_FOUND_ALL');
		}
		$html .= '</p>'.n;
	}
	
	// Insert page navigation
	if(count($this->rows) > 0) {	
		$pagenavhtml = $this->pageNav->getListFooter();
		$pagenavhtml = str_replace('projects/?','projects/browse/?',$pagenavhtml);
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.$pagenavhtml;
		$html .= t.t.'</fieldset>'.n;
	}	
	$html .= t.'</form>'.n;	
	echo $html;
?>
	<div class="clear"></div>
</div><!-- / .main section -->
