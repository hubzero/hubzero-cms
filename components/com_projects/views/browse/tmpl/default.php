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
	$dateFormat = 'b d, Y';
	$tz = false;
}

$html  = '';
$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=start'); ?>"><?php echo JText::_('COM_PROJECTS_START_NEW'); ?></a></li>		
	</ul>
</div><!-- / #content-header-extra -->
<div class="main section">
<?php
	$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$this->option.a.'task=browse').'">'.n;
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

	$sortbys = array('title'=>JText::_('COM_PROJECTS_TITLE'),'created'=>JText::_('COM_PROJECTS_CREATED'),'type'=>JText::_('COM_PROJECTS_TYPE'));
	$filterbys = array('all'=>JText::_('COM_PROJECTS_ALL'),'mine'=>JText::_('COM_PROJECTS_MINE'));
	$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

	// Loop through results
	$html .= '<div class="list-editing"><p>'.JText::_('COM_PROJECTS_SHOWING');
	if($this->total <= count($this->rows)) {
		$html .= ' '.JText::_('COM_PROJECTS_ALL').' <span class="prominent">'.$this->total.'</span> ';
	}
	else {
		$html .= ' <span class="prominent">'.count($this->rows).'</span> '.JText::_('COM_PROJECTS_OUT_OF').' '.$this->total;	
	}
	$html .= ' '.strtolower(JText::_('COM_PROJECTS_PROJECTS')).'</p></div>';
	if(count($this->rows) > 0) {		
		$html .= t.t.'<table class="listing" id="projectlist">'.n;
		$html .= t.t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .='<th class="th_image" colspan="2"></th>'.n;
		$html .= t.t.t.t.'<th';
		if($this->filters['sortby'] == 'title') { 
			$html .= ' class="activesort"'; 
		} 
		$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=browse').'/?sortby=title'
		. a . 'sortdir=' . $sortbyDir . '" class="re_sort" title="' 
			. JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_TITLE') . '">';
		$html .= JText::_('COM_PROJECTS_TITLE').'</a></th>'.n;
		$html .= t.t.t.t.'<th';
		if($this->filters['sortby'] == 'owner') { 
			$html .= ' class="activesort"'; 
		} 
		$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=browse')
		.'/?sortby=owner'.a.'sortdir='.$sortbyDir.'" class="re_sort" title="' 
		. JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_OWNER') . '">';
		$html .= JText::_('COM_PROJECTS_OWNER').'</a></th>'.n;
		
		$html .= t.t.t.t.'<th';
		if(!$this->guest) {
			if($this->filters['sortby'] == 'status') { 
				$html .= ' class="activesort"';
			}
				$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=browse').'/?sortby=status'
				.a.'sortdir='.$sortbyDir.'" class="re_sort" title="' 
					. JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_STATUS') . '">'; 
				$html .= JText::_('COM_PROJECTS_STATUS').'</a>';
		}
		else {
			$html .= '>';
		}
		$html .='</th>'.n;
		$html .= t.t.t.t.'<th';
		if(!$this->guest) {
			if($this->filters['sortby'] == 'role') { 
				$html .= ' class="activesort"';
			}
				$html .= '><a href="'. JRoute::_('index.php?option='.$this->option.a.'task=browse').'/?sortby=role'
				.a.'sortdir='.$sortbyDir.'" class="re_sort" 	 title="' 
					. JText::_('COM_PROJECTS_SORT_BY') . ' ' . JText::_('COM_PROJECTS_MY_ROLE') . '">'; 
				$html .= JText::_('COM_PROJECTS_MY_ROLE').'</a>';
		}
		else {
			$html .= '>';
		}
		$html .='</th>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.t.'</thead>'.n;
		$html .= t.t.t.'<tbody>'.n;
		foreach($this->rows as $row) {	
			if($row->owned_by_group && !$row->groupcn) {
				continue; // owner group has been deleted
			}
			$goto  = 'alias=' . $row->alias;				
			$thumb = ProjectsHtml::getThumbSrc($row->id, $row->alias, $row->picture, $this->config);
			$html .= t.t.t.'<tr class="mline" id="tr_'.$row->id.'">'.n;
			$html .= t.t.t.t.'<td class="th_image">';
			$html .='<a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'"><img src="'.$thumb.'" alt="'.htmlentities(ProjectsHtml::cleanText($row->title)).'" /></a></td>'.n;
			$html .= t.t.t.t.'<td class="th_privacy">';
			$html .= '<span class="privacy-icon';
			$html .= ($row->private == 1) ? ' private' : '';
			$html .= '">&nbsp;</span>';
			$html .= '</td>'.n;
			$html .= t.t.t.t.'<td class="th_title"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" >'.ProjectsHtml::cleanText($row->title).'</a></td>'.n;
			$html .= '<td class="mini faded">';
			$html .= ($row->owned_by_group) ? '<span class="i_group"><a href="/groups/'.$row->groupcn.'">'.$row->groupname.'</a></span>' : '<span class="i_user"><a href="/members/'.$row->created_by_user.'">'.$row->authorname.'</a></span>';
			$html .= '</td>'.n;

			$html .= t.t.t.t.'<td class="mini faded">';
			if($row->owner && $row->confirmed == 1) {
				if($row->state == 1 && $row->setup_stage >= $setup_complete) {
					$html .= '<span class="active"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('COM_PROJECTS_GO_TO_PROJECT').'">&raquo; '.JText::_('COM_PROJECTS_ACTIVE').'</a></span> '.JText::_('COM_PROJECTS_SINCE').' '.JHTML::_('date', $row->created, $dateFormat, $tz);
				}
				else if ($row->setup_stage < $setup_complete) {
						$html .= '<span class="setup"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.$goto).'" title="'.JText::_('COM_PROJECTS_CONTINUE_SETUP').'">&raquo; '.JText::_('COM_PROJECTS_STATUS_SETUP').'</a></span> '.JText::_('COM_PROJECTS_IN_PROGRESS');
				}
				else if($row->state == 0) {
					$html .= '<span class="faded italic">'.JText::_('COM_PROJECTS_STATUS_INACTIVE').'</span> ';
				}
				else if($row->state == 5) {
					$html .= '<span class="italic pending">'.JText::_('COM_PROJECTS_STATUS_PENDING').'</span> '.JText::_('COM_PROJECTS_SINCE').' '.JHTML::_('date', $row->created, $dateFormat, $tz);
				}
			}
			$html .= '</td>'.n;
			$html .= t.t.t.t.'<td class="mini faded">';
			if($row->owner && $row->confirmed == 1) {
				$html .= $row->role == 1 ? JText::_('COM_PROJECTS_LABEL_OWNER') : JText::_('COM_PROJECTS_LABEL_COLLABORATOR') ;
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
			$html .= JText::_('COM_PROJECTS_NO_AUTHOROZED_PROJECTS_FOUND');
		}
		$html .= '</p>'.n;
	}
	
	// Insert page navigation
	if(count($this->rows) > 0) {	
		$pagenavhtml = $this->pageNav->getListFooter();
		$pagenavhtml = str_replace('projects/?','projects/browse/?',$pagenavhtml);
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.$pagenavhtml;
	}
	$html .= t.t.'</fieldset>'.n;	
	$html .= t.'</form>'.n;	
	echo $html;
?>
	<div class="clear"></div>
</div><!-- / .main section -->
