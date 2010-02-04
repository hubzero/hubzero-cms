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
	
	/* Job Posting */
	
	// load some classes
	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();
	
	$job = $this->job;
	
	$job->cat = $job->cat ? $job->cat : 'unspecified';
	$job->type = $job->type ? $job->type : 'unspecified';
	
	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->startdate, '%d %b %Y') : 'N/A';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->closedate, '%d %b %Y') : 'ASAP';
	
	$link = JRoute::_('index.php?option='.$this->option.a.'task=editjob'.a.'id='.$job->id);
	$txt = JText::_('Edit this job');
	
	ximport('wiki.parser');
	$p = new WikiParser( $job->code, $this->option, 'job'.DS.$job->id, 'jobs', $job->id);
	$maintext = $p->parse( n.stripslashes($job->description) );	
	
	$owner = ($juser->get('id') == $job->employerid or $this->admin) ? 1 : 0;	
	
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?>
    <?php if($owner) { echo '<a class="edit button" href="'. $link .'" title="'. $txt .'">'. $txt .'</a>'; } ?>
    </h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($juser->get('guest')) { ?> 
    	<li><?php echo JText::_('Please').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=view').'?action=login">'.JText::_('Login').'</a> '.JText::_('to view extra options'); ?></li>
    <?php } else if($this->emp) {  ?>
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('You are logged in as a site administrator.'); ?> <a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Administrator Dashboard'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="myresume" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addresume'); ?>"><?php echo JText::_('My Resume'); ?></a></li>
    <?php } ?>  
</ul>
</div><!-- / #content-header-extra -->
<?php
		
		$html = '';
		
		$job->title = trim(stripslashes($job->title));
		$job->description = trim(stripslashes($job->description));
		$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
		$job->description = JobsHtml::txt_unpee($job->description);
		
		$html .= '<div class="clear"></div>'.n;	
		$html .= '<div class="main section">'.n;
		$html .= $this->msg ? $this->msg : '';		
	
		if($owner) {
			$html .= $job->status==1 ? JobsHtml::statusmsg (JText::_('This job ad is published.').' '.JText::_('You can').' <a href="'.$link.'">'.JText::_('edit this job').'</a> '.JText::_('at any time. If you need to take down this listing, please use the "Unpublish this job" link at the bottom of the posting.'), 'activead') : '';
			
			$html .= $job->status==3 ? JobsHtml::statusmsg (JText::_('This job ad is unpublished.').' '.JText::_('You can reopen this posting by clicking the "Re-open this job" link at the bottom of the posting.'), 'inactive') : '';
			
			$html .= $job->status==4 ? JobsHtml::statusmsg (JText::_('This job ad has not been published yet. To include this ad in the jobs listing, please click the "Publish Ad" link at the bottom of this listing.'), 'inactive') : '';
			
			$html .= $job->status==0 ? JobsHtml::statusmsg (JText::_('This job ad is pending administrator approval for being posted on the site.'), 'inactive') : '';		
		}
			
		$html .= t.'<div id="jobinfo">'.n;	
		$html .= t.t.'<h3><span>'.JText::_('Reference code').': '.$job->code.'</span>'.$job->title.' - ';
		$html .= preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName;
		$html .= ', '.$job->companyLocation.', '.$job->companyLocationCountry.'</h3>'.n;
		$html .= '<div class="clear"></div>'.n;	
		$html .= t.t.'<div class="apply"><p>'.n;
		if($job->applied) {
			$html .= t.t.'<span class="alreadyapplied">'.JText::_('Applied on').' '.JHTML::_('date',$job->applied, '%d %b %Y').'<span>'.n;
			$html .= t.t.'<span><a href="'.JRoute::_('index.php?option='.$option.a.'task=editapp'.a.'id='.$job->id).'">'.JText::_('Edit application').'</a> | <a href="'.JRoute::_('index.php?option='.$option.a.'task=withdraw'.a.'jid='.$job->id).'" id="showconfirm">'.JText::_('Withdraw application').'</a><span>'.n;
		}
		else if($job->withdrawn) {
			$html .= t.t.'<span class="withdrawn">'.JText::_('Withdrew on').' '.JHTML::_('date',$job->withdrawn, '%d %b %Y').'<span>'.n;
			$html .= t.t.'<span><a href="'.JRoute::_('index.php?option='.$option.a.'task=apply'.a.'id='.$job->id).'">'.JText::_('Re-apply').'</a><span>'.n;
		}
		else {
			if($job->applyExternalUrl) {
				$html .= t.t.'<a class="extlink" href="'.$job->applyExternalUrl.'">'.JText::_('Apply externally').'</a>'.n;
				//$html .= t.t.'<a class="extlink" href="'.$job->applyExternalUrl.'">'.$job->applyExternalUrl.'</a>'.n;
				$html .= $job->applyInternal ? '<span class="or">'.strtolower(JText::_('OR')).'</span>'.n : ''.n;
			}		
			if($job->applyInternal) {
				$html .= t.t.'<span class="applybtn"><a href="'.JRoute::_('index.php?option='.$option.a.'task=apply'.a.'id='.$job->id).'">'.JText::_('Apply through').' '.$hubShortName.'</a></span>'.n;
			}
		}
		$html .= t.t.'</p>';
		$html .= ($job->applied) ? JobsHtml::confirmscreen(JRoute::_('index.php?option='.$option.a.'task=job'.a.'id='.$job->id), JRoute::_('index.php?option='.$option.a.'task=withdraw'.a.'jid='.$job->id), $action = "withdrawapp").n : '';
		$html .='</div>'.n;
		$html .= '<div>'.n;
		$html .= t.t.'<span class="sub-heading">'.JText::_('Category').'</span>'.n;
		$html .= t.t.'<p>'.$job->cat.'</p>'.n;
		$html .= t.t.'<span class="sub-heading">'.JText::_('Type').'</span>'.n;
		$html .= t.t.'<p>'.$job->type.'</p>'.n;
		$html .= t.t.'<span class="sub-heading">'.JText::_('Start date').'</span>'.n;
		$html .= t.t.'<p>'.$startdate.'</p>'.n;
		$html .= t.t.'<span class="sub-heading">'.JText::_('Apply by').'</span>'.n;
		$html .= t.t.'<p>'.$closedate.'</p>'.n;
		$html .= t.t.'<div class="reg details">'.$maintext.'</div>'.n;
		if($job->contactName) {
			$html .= t.t.'<p class="reg details">'.JText::_('For more information please contact').':</p>'.n;
			$html .= t.t.'<p class="reg">'.n;
			$html .= t.t.'<span class="contactname">'.$job->contactName.'</span>'.n;
			$html .= $job->contactPhone ? t.t.'<span class="contactinfo">'.JText::_('Tel.').': '.$job->contactPhone.'</span>'.n : '';
			$html .= $job->contactEmail ? t.t.'<span class="contactinfo">'.JText::_('Email').': '.$job->contactEmail.'</span>'.n : '';
			$html .= t.t.'</p>'.n;
		}
		$html .= t.'</div>'.n;	
		$html .= '</div>'.n;
		
		if($owner) {
		$html .= $job->status==4 ? '<p class="confirmPublish"><span><a href="'.JRoute::_('index.php?option='.$option.a.'task=confirmjob'.a.'id='.$job->id).'">'.JText::_('Publish Ad').'</a></span> <span class="alternative">'.JText::_('or').'</span> <span class="makechanges"><a href="'.JRoute::_('index.php?option='.$option.a.'task=editjob'.a.'id='.$job->id).'">'.JText::_('Make Changes').'</a></span> <span class="alternative">'.JText::_('or').'</span> <span class="makechanges"><a href="'.JRoute::_('index.php?option='.$option.a.'task=remove'.a.'id='.$job->id).'">'.JText::_('Remove Ad').'</a></span> </p> ' : '';
		
		$html .= $job->status==1 ? '<p class="manageroptions"><span><a href="'.JRoute::_('index.php?option='.$option.a.'task=unpublish'.a.'id='.$job->id).'">'.JText::_('Unpublish this job').'</a> '.JText::_('(Access to user applications will be preserved)').'</span> </p>' : '';
		
		$html .= $job->status==3 ? '<p class="manageroptions"><span><a href="'.JRoute::_('index.php?option='.$option.a.'task=reopen'.a.'id='.$job->id).'">'.JText::_('Re-open this job').'</a> '.JText::_('(Include in public listing)').'</span> <span class="alternative">|</span> <span><a href="'.JRoute::_('index.php?option='.$option.a.'task=remove'.a.'id='.$job->id).'">'.JText::_('Delete this job').'</a> '.JText::_('(Delete all records)').'</span> </p>' : '';
		
		}
				
		echo $html;

?>
 <?php if($owner) {  ?>
 	<a name="applications"></a>
    <h3><?php echo JText::_('Applications').' ('.count($job->applications).' '.JText::_('total').')'; ?></h3>
    <?php if (count($job->applications) <= 0 ){  ?>
    <p><?php echo JText::_('Noone has yet applied to this posting via this site.'); ?></p>
    <?php }  else { 
		
		$html = '';
		$html  .= t.'<ul id="candidates">'.n;
		
		JPluginHelper::importPlugin( 'members','resume' );
		$dispatcher =& JDispatcher::getInstance();
		$k = 1;
		for ($i=0, $n=count( $job->applications ); $i < $n; $i++) {	
			if($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {				
				$applied = ($job->applications[$i]->applied !='0000-00-00 00:00:00') ? JHTML::_('date',$job->applications[$i]->applied, '%d %b %Y') : 'N/A';
				$html  .= t.'<li class="applic">'.n;
				$html  .= t.'<span class="countc">'.$k.'</span> '.$job->applications[$i]->seeker->name.' '.JText::_('applied on').' '.$applied.n;
				$html  .= $job->applications[$i]->cover ? '<blockquote>'.trim(stripslashes($job->applications[$i]->cover)).'</blockquote>' : '';
				$html  .= t.'</li>'.n;
			
				$html  .= t.'<li>'.n;				
				// show seeker info
				$out   = $dispatcher->trigger( 'showSeeker', array($job->applications[$i]->seeker, $this->emp, $this->admin, 'com_members', $list=0) );
				if (count($out) > 0) {
					$html .= $out[0];
				}
				$html  .= t.'</li>'.n;
				$html  .= t.'<li class="applicbot">'.n;
				$html  .= t.'</li>'.n;
				$k++;
			}
		}
		if(count($job->withdrawnlist) > 0) {
			for ($i=0, $n=count( $job->withdrawnlist ); $i < $n; $i++) {	
				$n = $k;
				
				$n++;
			}
		}
		
			$html  .= t.'</ul>'.n;
		if(count($job->withdrawnlist) > 0) {
			$html  .= t.'<p>'.count($job->withdrawnlist).' '.JText::_('candidate(s) withdrew application(s)').'</p>'.n;
		}
		
		echo $html;
	} ?>
 <?php } ?>
 </div>