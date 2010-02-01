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
	
	/* Employer Dashboard View */
	
	// load some classes
	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();
	
	// get some configs
	$infolink = isset($this->config->parameters['infolink']) && $this->config->parameters['infolink']!=''  ? $this->config->parameters['infolink'] : 'kb/jobs';
	$premium_infolink = isset($this->config->parameters['premium_infolink']) && $this->config->parameters['premium_infolink']!=''  ? $this->config->parameters['_premium_infolink'] : 'kb/points/premium';
	$usepremium = isset($this->config->parameters['usepremium']) && $this->config->parameters['usepremium']==1  ? 1: 0;
	$promoline = isset($this->config->parameters['promoline']) ? $this->config->parameters['promoline'] : '';
		
	$allowed_ads = $this->service->maxads - $this->activejobs;
	$allowed_ads = $allowed_ads < 0 ? 0 : $allowed_ads;
	
	$class= 'no';
	switch( $this->subscription->status ) 
	{
		case '0':    $status = JText::_('Pending approval');	break;
		case '1':    $status = JText::_('Active'); 
					 $class  = 'yes';    	
					  											break;
		case '2':    $status = JText::_('Cancelled');    		break;
		default: 	 $status = JText::_('N/A');					break; 
	}
	
	$today = date( 'Y-m-d'); 
	
	$status = $this->subscription->expires < $today && $this->subscription->status==1 ? JText::_('Expired') : $status;
	$length = $this->subscription->status==0 ? $this->subscription->pendingunits : $this->subscription->units;
	$pending = $this->subscription->pendingunits && $this->subscription->status==1 ? ' <span class="no">('.$this->subscription->pendingunits.' '.JText::_('additional').' '.$this->service->unitmeasure.'(s)'.JText::_(' pending').')</span>' : '';
	$expiredate = $this->subscription->expires ? JHTML::_('date', $this->subscription->expires, '%d %b %Y') : JText::_('N/A');
	
	// site admins
	$admin = ($this->admin && $this->employer->id == 1) ? 1 : 0;
	if($admin) {
		$this->subscription->code = JText::_(' N/A');
		$this->service->title = JText::_('Administrator (unlimited access)');
		$class  = 'yes';
		$status = JText::_('Active admin');	  
	}

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

 <?php if($this->emp && !$admin) {  ?>
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><span class="myjobs"><?php echo JText::_('Employer Dashboard'); ?></span></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
   
</ul>
</div><!-- / #content-header-extra -->
 <?php } ?>  

<div class="main section">
<?php if($this->msg) { echo $this->msg;  } ?>

<div class="columns two first">
	<div id="activities">
    	<h3><?php echo JText::_('Activities'); ?></h3>
    	<h4><?php echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'">'.JText::_('Browse Resumes').' ('.$this->stats['total_resumes'].')</a>'; ?></h4>
        <span class="sub-heading"><?php echo JText::_('Total pool'); ?></span>
        <p><span class="view"><?php echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'" class="cancelit">[ '.JText::_( 'view' ).' ]</a>'; ?></span><?php echo $this->stats['total_resumes']; ?> </p>
        <span class="sub-heading"><?php echo JText::_('Shortlisted'); ?></span>
        <p><span class="view"><?php if($this->stats['shortlisted'] > 0) { echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=batch').'?pile=shortlisted" class="cancelit">[ '.JText::_( 'download all' ).' ]</a> &nbsp;&nbsp;&nbsp;'; } echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted" class="cancelit">[ '.JText::_( 'view' ).' ]</a>'; ?></span><?php echo $this->stats['shortlisted']; ?></p>       
        <span class="sub-heading"><?php echo JText::_('Applied to your ad(s)'); ?></span>
        <p><span class="view"><?php echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=applied" class="cancelit">[ '.JText::_( 'view' ).' ]</a>'; ?></span><?php echo $this->stats['applied']; ?></p>
        <div class="spacer"></div>
        <h4><span><?php echo JText::_('Manage Job Ads').' ('.count($this->myjobs).')'; ?></span></h4>
       
        <p class="reg">
        	<span><?php echo JText::_('You have currently').' '.$this->activejobs.' '.JText::_('published job ad(s).'); if(!$admin) { ?> <br /><?php echo $allowed_ads.' '.JText::_('more published ad(s) allowed with your level of service'); } ?></span>			
        </p>
        <?php if(count($this->myjobs) > 0) { foreach ($this->myjobs as $mj) { ?>
        <p class="reg myjob<?php if($mj->status == 3) { echo '_inactive'; } else if($mj->status == 4 or $mj->status == 0) { echo '_pending'; } ?>">  <span class="view"><?php if($mj->status == 1) { echo $mj->applications.' '.JText::_('applications').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=job'.a.'id='.$mj->id).'#applications" class="cancelit">[ '.JText::_( 'view' ).' ]</a>'; } else if($mj->status == 4) { echo JText::_('(draft)'); } else if($mj->status == 0) { echo JText::_('(pending approval)'); } else if($mj->status == 3) { echo JText::_('(inactive)'); } ?></span> <?php echo '<span class="code">'.$mj->code.'</span>: <a href="'.JRoute::_('index.php?option='.$option.a.'task=job'.a.'id='.$mj->id).'">'.JobsHtml::shortenText($mj->title, 50, 0).'</a>';  ?>
       
        </p>
        <?php } } ?>
    <?php if($this->subscription->status == 1 or $admin) { ?>
        <p class="reg">
        	<?php //if ($allowed_ads > 0 ) {  ?><a class="add" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=addjob'); ?>"><?php echo JText::_('Add a new job ad draft'); ?></a> <?php //} ?></p>
        <!--<p class="reg"><?php echo JText::_('This feature is not yet available.'); ?></p>-->
         <?php } ?>
    </div>
</div>
<div class="columns two second">
	<div id="subinfo">
    	<h3><?php echo JText::_('Subscription Details'); ?><span><?php echo JText::_('Reference code').': '.$this->subscription->code; ?></span></h3>
    	<span class="sub-heading"><?php echo JText::_('Service'); ?></span>
        <p><?php echo $this->service->title; ?></p>
        <span class="sub-heading"><?php echo JText::_('Status'); ?></span>
        <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
        
        <?php if(!$admin) { ?>
        <span class="sub-heading"><?php echo JText::_('Length'); ?></span>
        <p><?php echo $length.'-'.$this->service->unitmeasure.$pending; ?></p>
        <span class="sub-heading"><?php echo JText::_('Expire date'); ?></span>
        <p><?php echo $expiredate; ?></p>
        <p><?php echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=subscribe').'" class="cancelit">[ '.JText::_( 'Extend / Renew' ).' ]</a> | <a href="'.JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'uid='.$this->uid).'" class="cancelit" id="showconfirm">[ '.JText::_( 'Cancel this subscription' ).' ]</a>'; ?></p>
       <?php echo JobsHtml::confirmscreen(JRoute::_('index.php?option='.$option.a.'task=dashboard'.a.'uid='.$this->uid), JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'uid='.$this->uid)); ?>
    	
        <div class="spacer"></div>
        <h3><?php echo JText::_('Employer Information'); ?><span><?php echo JText::_('Username').': '.$this->login; ?></span></h3>
        <span class="sub-heading"><?php echo JText::_('Company'); ?></span>
        <p><?php $emp_com = $this->employer->companyName ? $this->employer->companyName : JText::_('Not specified') ; echo $emp_com ?></p>
        <span class="sub-heading"><?php echo JText::_('Location'); ?></span>
        <p><?php $emp_loc = $this->employer->companyLocation ? $this->employer->companyLocation : JText::_('Not specified') ; echo $emp_loc ?></p>
        <span class="sub-heading"><?php echo JText::_('Website'); ?></span>
        <p><?php $emp_web = $this->employer->companyWebsite ? $this->employer->companyWebsite : JText::_('Not specified') ; echo $emp_web ?></p>
         <p><?php echo '<a href="'.JRoute::_('index.php?option='.$option.a.'task=subscribe').'" class="cancelit">[ '.JText::_( 'Edit employer information' ).' ]</a>'; ?></p>
         <?php } ?>
    </div>
</div>

<div class="clear"></div>
</div>