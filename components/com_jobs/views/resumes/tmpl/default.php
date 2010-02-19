<?php 
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
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
	
	/* Resume List */
	
	$title 		= $this->title;
	$option 	= $this->option;
	$seekers 	= $this->seekers;
	$filters 	= $this->filters;
	$emp 		= $this->emp;
	$admin 		= $this->admin;
	$pageNav 	= $this->pageNav;
	$cats 		= $this->cats;
	$types 		= $this->types;

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->emp) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <?php if($filters['filterby'] == 'shortlisted') { ?>
        <li><a class="complete" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes'); ?>"><?php echo JText::_('ALL_CANDIDATES'); ?></a></li>
        <?php } else { ?>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('JOBS_SHORTLIST'); ?></a></li>
        <?php } ?>
     <?php } else { ?> 
     <li><?php echo JText::_('NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('JOBS_ADMIN_DASHBOARD'); ?></a></li> 
     <?php } ?>      
	</ul>
</div><!-- / #content-header-extra -->

<?php 
		$html  = '';
		$html .= '<div class="main section">'.n;
		$html .= t.'<form method="post" action="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.n;
		$html .= t.'<div class="aside">'.n;
		
		// search
		if($filters['filterby']!='shortlisted') {
			$html .= t.t.'<fieldset id="matchsearch">'.n;
			$html .= t.t.t.'<label>'.JText::_('SORTBY').': '.n;
			$html .= t.t.t.'     <div class="together"><input class="option" type="radio" name="sortby" value="lastupdate" ';
			if($filters['sortby']!='bestmatch') {
			$html .= 'checked="checked"';
			}
			$html .= ' /> '.JText::_('RESUMES_LAST_UPDATE').n;
			$html .= t.t.t.'      &nbsp; <input class="option" type="radio" name="sortby" value="bestmatch" ';
			if($filters['sortby']=='bestmatch') {
			$html .= 'checked="checked"';
			}
			else if(!$filters['match']) {
			$html .= ' disabled';
			}
			$html .= ' /> '.JText::_('SORTBY_BEST_MATCH').'</div>'.n;
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('SEARCH_KEYWORDS').': <span class="questionmark tooltips" title="'.JText::_('TIP_SEARCH_JOBS_BY_KEYWORDS').'"></span>'.n;
			$html .= t.t.t.'<input name="q" maxlength="250" type="text" value="'.$filters['search'].'" />';
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('SEARCH_CATEGORY_SOUGHT').':'.n;
			$html .= JobsHtml::formSelect('category', $cats, $filters['category'], '', '');
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('SEARCH_TYPE_SOUGHT').':'.n;
			$html .= JobsHtml::formSelect('type', $types, $filters['type'], '', '');
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label>'.n;
			$html .= t.t.t.' <div class="together"><input  type="checkbox" name="saveprefs" value="1" checked="checked" /> ';
			$html .= JText::_('SEARCH_SAVE_PREFS').n;
			$html .= t.t.t.' </div></label>'.n;
			$html .= t.t.t.'<input type="hidden" name="performsearch" value="1" />'.n;
			$html .= t.t.t.'<input type="submit" class="submit" value="'.JText::_('ACTION_SEARCH').'" />'.n;
			$html .= t.t.'</fieldset>'.n;
		}
		else {
			$html .= t.t.'<p>'.JText::_('RESUME_NOTICE_BOOKMARKED').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.JText::_('All Candidates').'</a>.</p>'.n;
		}
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		if($filters['filterby']== 'shortlisted') {
			$html .= t.'<h4>'.JText::_('ALL_CANDIDATES').'</h4>'.n;
		}
		
		if(count($seekers) > 0) {
			// show how many
			$html .= t.t.t.'<p class="note_total" >'.JText::_('NOTICE_DISPLAYING').' ';
			if($filters['start'] == 0) {
				$html .= $pageNav->total > count($seekers) ? ' '.JText::_('NOTICE_TOP').' '.count($seekers).' '.JText::_('NOTICE_OUT_OF').' '.$pageNav->total : strtolower(JText::_('ALL')).' '.count($seekers) ;
			}
			else {
				$html .= ($filters['start'] + 1);
				$html .= ' - ';
				$html .=$filters['start'] + count($seekers);
				$html .=' out of '.$pageNav->total;
			}
			$html .= ' ';
			$html .= $filters['filterby']=='shortlisted' ? JText::_('shortlisted').' ' : '';
			$html .= strtolower(JText::_('CANDIDATES'));
			$html .='</p>'.n;
			
			$html  .= t.'<ul id="candidates">'.n;
			
			JPluginHelper::importPlugin( 'members','resume' );
			$dispatcher =& JDispatcher::getInstance();	
			foreach ($seekers as $seeker) {
				$html  .= t.'<li>'.n;				
				// show seeker info
				$out   = $dispatcher->trigger( 'showSeeker', array($seeker, $emp, $admin, 'com_members', $list=1) );
				if (count($out) > 0) {
					$html .= $out[0];
				}
				$html  .= t.'</li>'.n;
			}
			$html  .= t.'</ul>'.n;			
		} 
		else {
			// no candidates found
			$html  .= t.'<p>'.n;
			$html  .= $filters['filterby'] == 'shortlisted' ? JText::_('RESUMES_NONE_SHORTLISTED') : '';
			$html  .= $filters['filterby'] == 'applied' ? JText::_('RESUMES_NONE_APPLIED') : '';
			$html  .= $filters['filterby'] != 'shortlisted' && $filters['filterby'] != 'applied' ? JText::_('RESUMES_NONE_FOUND') : '';
			$html  .= t.'</p>'.n;
		}
		
		// Insert page navigation
		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('jobs/?','jobs/resumes/?',$pagenavhtml);
		$html .= t.t.$pagenavhtml;
		
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= t.'<div class="clear"></div>';
		
		$html .= t.'</form>'.n;
		$html .= '</div>'.n;	
		
		echo $html;
?>