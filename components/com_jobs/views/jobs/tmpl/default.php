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
	
	/* Jobs List */
	
	$juser 	  =& JFactory::getUser();	
	$jobs = $this->jobs;
	$option = $this->option;
	$jobs = $this->jobs;
	$filters = $this->filters;
	$allowsubscriptions = $this->allowsubscriptions;
	$infolink = isset($this->config->parameters['infolink']) && $this->config->parameters['infolink']!=''  ? $this->config->parameters['infolink'] : 'kb/jobs';

if(!$this->mini) {	
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<?php if($this->emp or $this->admin) {  ?>
<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($this->emp) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
    <?php } else {  ?>  	
    	<li><?php echo JText::_('You are logged in as a site administrator.'); ?> <a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Administrator Dashboard'); ?></a></li>
    <?php } ?>  		
	</ul>
</div><!-- / #content-header-extra -->
<?php }  
}
		$html  = '';
		if (!$this->mini) {	
		$html .= '<div class="main section">'.n;
		$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.n;
		$html .= t.'<div class="aside minimenu">'.n;
		if($allowsubscriptions) {
		$html .= t.t.'<h3>'.JText::_('EMPLOYERS').' ';
		$html .= '</h3>'.n;
		$html .= t.t.'<ul>'.n;
		$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=addjob').'">'.JText::_('POST_JOB').'</a></li>'.n;
		$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.JText::_('BROWSE_RESUMES').'</a></li>'.n;
		$html .= t.t.'</ul>'.n;
		$html .= t.t.'<h3>'.JText::_('SEEKERS').'</h3>'.n;
		$html .= t.t.'<ul>'.n;
		$html .= t.t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=addresume').'">'.JText::_('POST_RESUME').'</a></li>'.n;
		$html .= t.t.'</ul>'.n;
		}
		$html .= '<p><a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_THE_PROCESS').'.</p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		
		// show how many
		$totalnote = JText::_('Displaying ');
		if($filters['start'] == 0) {
			$totalnote .= ($this->pageNav->total > count($jobs)) ? ' top '.count($jobs).' out of '.$this->pageNav->total : strtolower(JText::_('all')).' '.count($jobs) ;
		}
		else {
			$totalnote .= ($filters['start'] + 1);
			$totalnote .= ' - ';
			$totalnote .=$filters['start'] + count($jobs);
			$totalnote .=' out of '.$this->pageNav->total;
		}
		$totalnote .= ' '.strtolower(JText::_('job opening(s)'));

		$html .= JobsHtml::browseForm_Jobs($option, $filters, $this->admin, $totalnote);
		}
		else {
		$html .= t.t.'<h3>'.JText::_('Latest Job Postings').'</h3> ';
		}
		
		if(count($jobs) > 0 ) {
			$jt = new JobType ( $this->database );
			$jc = new JobCategory ( $this->database );
			$curtype = $jobs[0]->type > 0 ? $jt->getType($jobs[0]->type) : '';
			$curcat = $jobs[0]->cid > 0 ? $jc->getCat($jobs[0]->cid) : '';
			
			$html .= t.t.'<table class="postings">'.n;
			$html .= t.t.t.'<tr class="headings">'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Job Title').'</td>'.n;
			if($this->admin && !$this->emp && !$this->mini) {
			$html .= t.t.t.t.'<td>'.JText::_('Status').'</td>'.n;
			}
			$html .= t.t.t.t.'<td>'.JText::_('Company').'</td>'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Location').'</td>'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Category').'</td>'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Type').'</td>'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Posted').'</td>'.n;
			$html .= t.t.t.t.'<td>'.JText::_('Apply by').'</td>'.n;
			if($filters['search']) {
			$html .= t.t.t.t.'<td>'.JText::_('Relevance').'</td>'.n;
			}
			$html .= t.t.t.'</tr>'.n;
				
			/*
			if($filters['sortby'] != 'opendate') {
				$html .= t.t.t.'<li class="cattitle">';
				$html .= $filters['sortby']=='category' ?  $curcat : $curtype;
				//$html .= ' '.JText::_('Positions');
				$html .= '</li>'.n;
			}
			*/
			
			ximport('wiki.parser');
			$p = new WikiParser( 'jobs', $this->option, 'jobs.browse', 'jobs', 1);
			$maxscore = $filters['search'] && $jobs[0]->keywords > 0 ? $jobs[0]->keywords : 1;
						
			for ($i=0, $n=count( $jobs ); $i < $n; $i++) {	
				//$thiscat = $jobs[$i]->cid > 0  ? $jc->getCat($jobs[$i]->cid) : JText::_('Miscellaneous');
				//$thistype = $jobs[$i]->type > 0 ? $jt->getType($jobs[$i]->type) : JText::_('Miscellaneous') ;
				$txt = $p->parse( n.stripslashes($jobs[$i]->description) );	
				$closedate = ($jobs[$i]->closedate && $jobs[$i]->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$jobs[$i]->closedate, '%d&nbsp;%b&nbsp;%y') : 'ASAP';
			
				/*
				if($filters['sortby'] != 'opendate' && $i > 0 ) {
					if($filters['sortby']=='category' && $thiscat != $jc->getCat($jobs[($i - 1)]->cid) ) {
						$html .= t.t.t.'<li class="cattitle">'.$thiscat.'</li>';
					}
					else if($filters['sortby']=='type' && $thistype != $jt->getType($jobs[($i - 1)]->type)) {
						$html .= t.t.t.'<li class="cattitle">'.$thistype.'</li>';
					}
				}*/
				
				// compute relevance to search keywords
				if($filters['search']) {
					$relscore = $jobs[$i]->keywords > 0 ? floor(($jobs[$i]->keywords * 100) / $maxscore) : 0;				
				}
				
				
				// what's the job status?
				if($this->admin && !$this->emp && !$this->mini) {
					$status = '';
					$class =  '';
					switch( $jobs[$i]->status ) 
					{
						case 0:    		$status =  JText::_('Pending Approval');
										$class  = 'post_pending';  		
																				break;
						case 1:    		$status =  $jobs[$i]->inactive 
										? JText::_('Invalid Subscription') 
										: JText::_('Active'); 
										$class  = $jobs[$i]->inactive 
										? 'post_invalidsub'
										: 'post_active';  			
																				break;
						case 3:    		$status =  JText::_('Inactive');  		
										$class  = 'post_inactive';
										break;  
						case 4:    		$status =  JText::_('Draft');  			
										$class  = 'post_draft';
										break;  
					}
				}
								
				$html .= t.t.t.'<tr>'.n;
				$html .= t.t.t.t.'<td class="jobtitle">'.n;		
				$html .= t.t.t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=job'.a.'id='.$jobs[$i]->id).'" title="'.JobsHtml::shortenText($txt, 250, 0).'">';
				$html .= $jobs[$i]->title.'</a>'.n;
				$html .= t.t.t.t.'</td>'.n;
				if($this->admin && !$this->emp && !$this->mini) {
					$html .= t.t.t.t.'<td ';
					$html .= $class ? 'class="'.$class.'"' : '';
					$html .='>'.n;
					$html .= t.t.t.t.t.$status;
					$html .= t.t.t.t.'</td>'.n;
				}
				$html .= t.t.t.t.'<td>'.n;
				$html .= t.t.t.t.t.$jobs[$i]->companyName;
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td>'.n;
				$html .= t.t.t.t.t.$jobs[$i]->companyLocation.', '.$jobs[$i]->companyLocationCountry.n;				
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td class="secondary">'.$curcat.'</td>'.n;
				$html .= t.t.t.t.'<td class="secondary">'.$curtype.'</td>'.n;				
				$html .= t.t.t.t.'<td class="secondary">'.n;
				$html .= t.t.t.t.t.'<span class="datedisplay">'.JHTML::_('date',$jobs[$i]->added, '%d&nbsp;%b&nbsp;%y').'</span>'.n;				
				$html .= t.t.t.t.'</td>'.n;
				$html .= t.t.t.t.'<td>'.n;
				if($jobs[$i]->applied) {
				$applieddate = JHTML::_('date',$jobs[$i]->applied, '%d&nbsp;%b&nbsp;%y');
				$html .= '<span class="alreadyapplied">'.JText::_('Applied on').' <span class="datedisplay">'.$applieddate.'</span></span>';
				}
				else if($jobs[$i]->withdrawn) {
				$withdrew = JHTML::_('date',$jobs[$i]->withdrawn, '%d&nbsp;%b&nbsp;%y');
				$html .= '<span class="withdrawn">'.JText::_('Withdrew on').' <span class="datedisplay">'.$withdrew.'</span></span>';
				}
				else {
				$html .= $closedate ? '<span class="datedisplay">'.$closedate.'</span>' : '';
				}
				$html .= t.t.t.t.'</td>'.n;
				if($filters['search']) {
				$html .= t.t.t.t.'<td class="relevancescore ';
				$html .= $relscore > 0 ? 'yes' : 'no';
				$html .= '">'.$relscore.' %</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
							
			}
			$html .= t.t.'</table>'.n;
		}
		else {
		$html .= t.t.t.'<p>'.JText::_('NO_JOBS_FOUND').'</p>'.n;
		}
		
		if(!$this->mini) {
		// Insert page navigation
		$pagenavhtml = $this->pageNav->getListFooter();
		$pagenavhtml = str_replace('jobs/?','jobs/browse/?',$pagenavhtml);
		$html .= t.t.$pagenavhtml;
		
		$html .= t.'</div>'.n;		
		$html .= t.'</form>'.n;	
		$html .= '<div class="clear"></div></div>'.n;
		}	
		
		echo $html;

?>
 