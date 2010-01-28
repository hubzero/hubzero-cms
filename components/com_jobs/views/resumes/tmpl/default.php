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
<?php if($this->emp or $this->admin) {  ?>
<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->emp) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
        <?php if($filters['filterby'] == 'shortlisted') { ?>
        <li><a class="complete" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes'); ?>"><?php echo JText::_('All Candidates'); ?></a></li>
        <?php } else { ?>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
        <?php } ?>
    <?php } else {  ?>  	
    	<li><?php echo JText::_('You are logged in as a site administrator.'); ?> <a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Administrator Dashboard'); ?></a></li>
    <?php } ?>  		
	</ul>
</div><!-- / #content-header-extra -->
<?php }  

		$html  = '';
		$html .= '<div class="main section">'.n;
		$html .= t.'<form method="post" action="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.n;
		$html .= t.'<div class="aside">'.n;
		
		// search
		if($filters['filterby']!='shortlisted') {
			$html .= t.t.'<fieldset id="matchsearch">'.n;
			$html .= t.t.t.'<label>'.JText::_('Sort by').': '.n;
			$html .= t.t.t.'     <div class="together"><input class="option" type="radio" name="sortby" value="lastupdate" ';
			if($filters['sortby']!='bestmatch') {
			$html .= 'checked="checked"';
			}
			$html .= ' /> '.JText::_('last update').n;
			$html .= t.t.t.'      &nbsp; <input class="option" type="radio" name="sortby" value="bestmatch" ';
			if($filters['sortby']=='bestmatch') {
			$html .= 'checked="checked"';
			}
			else if(!$filters['match']) {
			$html .= ' disabled';
			}
			$html .= ' /> '.JText::_('best match').'</div>'.n;
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('Keywords').': <span class="questionmark tooltips" title="Keywords Search :: Use skill and action keywords separated by commas, e.g. XML, web, MBA etc."></span>'.n;
			$html .= t.t.t.'<input name="q" maxlength="250" type="text" value="'.$filters['search'].'" />';
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('Category sought').':'.n;
			$html .= JobsHtml::formSelect('category', $cats, $filters['category'], '', '');
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label> '.JText::_('Type sought').':'.n;
			$html .= JobsHtml::formSelect('type', $types, $filters['type'], '', '');
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.t.'<label>'.n;
			$html .= t.t.t.' <div class="together"><input  type="checkbox" name="saveprefs" value="1" checked="checked" /> ';
			$html .= JText::_('Save my search preferences').n;
			$html .= t.t.t.' </div></label>'.n;
			$html .= t.t.t.'<input type="hidden" name="performsearch" value="1" />'.n;
			$html .= t.t.t.'<input type="submit" class="submit" value="'.JText::_('Search').'" />'.n;
			$html .= t.t.'</fieldset>'.n;
		}
		else {
			$html .= t.t.'<p>'.JText::_('The listed candidates are those you bookmarked for further contact. Return to a list of ').'<a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.JText::_('All Candidates').'</a>.</p>'.n;
		}
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		if($filters['filterby']== 'shortlisted') {
			$html .= t.'<h4>'.JText::_('Candidate Shortlist ').'</h4>'.n;
		}
		
		if(count($seekers) > 0) {
			// show how many
			$html .= t.t.t.'<p class="note_total" >'.JText::_('Displaying ');
			if($filters['start'] == 0) {
				$html .= $pageNav->total > count($seekers) ? ' top '.count($seekers).' out of '.$pageNav->total : strtolower(JText::_('all')).' '.count($seekers) ;
			}
			else {
				$html .= ($filters['start'] + 1);
				$html .= ' - ';
				$html .=$filters['start'] + count($seekers);
				$html .=' out of '.$pageNav->total;
			}
			$html .= ' ';
			$html .= $filters['filterby']=='shortlisted' ? JText::_('shortlisted').' ' : '';
			$html .= strtolower(JText::_('candidates'));
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
			$html  .= $filters['filterby']=='shortlisted' ? JText::_('You haven\'t yet included any candidates on your shortlist. Keep searching!') : JText::_('Sorry, no resumes found at the moment.');
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
 