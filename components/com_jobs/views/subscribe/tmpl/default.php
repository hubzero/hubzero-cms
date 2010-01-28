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

	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$juser 	  =& JFactory::getUser();
	
	// get some configs
	$infolink = isset($this->config->parameters['infolink']) && $this->config->parameters['infolink']!=''  ? $this->config->parameters['infolink'] : 'kb/jobs';
	$premium_infolink = isset($this->config->parameters['premium_infolink']) && $this->config->parameters['premium_infolink']!=''  ? $this->config->parameters['_premium_infolink'] : 'kb/points/premium';
	$usepremium = isset($this->config->parameters['usepremium']) && $this->config->parameters['usepremium']==1  ? 1: 0;
	$promoline = isset($this->config->parameters['promoline']) ? $this->config->parameters['promoline'] : '';

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

 <?php if($this->emp) {  ?>
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="myjobs" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=dashboard'); ?>"><?php echo JText::_('Employer Dashboard'); ?></a></li>
        <li><a class="shortlist" href="<?php echo JRoute::_('index.php?option='.$option.a.'task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('Candidate Shortlist'); ?></a></li>
   
</ul>
</div><!-- / #content-header-extra -->
 <?php } ?>  

<div class="main section">
<form action="<?php echo JRoute::_('index.php?option='.$option.a.'task=confirm'); ?>" method="post" id="hubForm"  >
	<div class="explaination">
			<p><?php echo JText::_('Please edit and confirm your details as an employer, specifying the company name, location and website address.') ?></p>		
	</div>
    <fieldset id="subForm">
		<h3><?php echo JText::_('Employer Information'); ?></h3>

		<label>
			<?php echo JText::_( 'Company Name' ); ?>:          <span class="required">required</span>
			<input class="inputbox" type="text" id="companyName" name="companyName" size="50" maxlength="100" value="<?php echo $this->employer->companyName; ?>" />
		</label>
        <label>
			<?php echo JText::_( 'Company Location' ); ?>:          <span class="required">required</span>
			<input class="inputbox" type="text" id="companyLocation" name="companyLocation" size="50" maxlength="200" value="<?php echo $this->employer->companyLocation; ?>" />
   
		</label>
         <label>
			<?php echo JText::_( 'Company Website' ); ?>:
			<input class="inputbox" type="text" id="companyWebsite" name="companyWebsite" size="50" maxlength="200" value="<?php echo $this->employer->companyWebsite; ?>" />
		</label>


		
	</fieldset>
    <div class="clear"></div>
    <div class="explaination">
			<p><?php echo JText::_('Pick a subscription service and specify a period for subscription. You will be abe to renew or cancel the subscription at any time.') ?></p>		
           <h4><?php echo JText::_('Next Step: Payment for Subscription') ?></h4>
            <p><?php echo JText::_('If payment is expected, your subscription will be activated once the funds are received. After your subscription order is placed, we will contact you within 24-72 hrs to make payment arrangements. Please provide a valid contact phone number for us to reach you.') ?></p>
            <?php if($promoline) { ?> 
            <p class="promo"><?php echo $promoline; ?></p>   
            <?php } ?>     		
	</div>
    <fieldset>
		<h3><?php echo JText::_('Subscription Details'); ?></h3>

		<label>
			<?php echo JText::_( 'Select Service' ); ?>:          <span class="required">required</span>
            </label>
			<?php //echo $this->serviceChoice; 
				$html = '';
				for ($i=0, $n=count( $this->services ); $i < $n; $i++)  {
					
					$now = date( 'Y-m-d H:i:s', time() );
					
					// do we have an active subscription?
					$thissub = ($this->services[$i]->id == $this->subscription->serviceid) ? 1 : 0;
													
					// Determine expiration date
					if($thissub) {
						$length = $this->subscription->status==0 ? $this->subscription->pendingunits : $this->subscription->units;
						
						$expires  = $this->subscription->expires > $now && $this->subscription->status==1 ?  '<p class="yes">' : '<p class="no">';
						$expires .= JText::_( 'Your' ).' '.$length.'-'.$this->services[$i]->unitmeasure.' '.JText::_( 'subscription' ).' ';
						if($this->subscription->status==1) {
						$expires .= $this->subscription->expires > $now ? strtolower(JText::_( 'expires' )) : strtolower(JText::_( 'expired' )) ;
						$expires .= ' '.JText::_( 'on' ).' '.JHTML::_('date', $this->subscription->expires, '%d %b %Y').'.';
						}
						else {
						$expires .= JText::_( 'is pending approval.' ) ;
						}
						
						$expires .= '</p>'.n;
						$expires .= $this->subscription->expires > $now ? ' <a href="'.JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'uid='.$this->uid).'" class="cancelit" id="showconfirm">[ '.JText::_( 'Cancel this subscription.' ).' ]</a>' : '';
						$expires .= $this->subscription->pendingunits > 0 && $this->subscription->status==1  ? '<p class="no">'.JText::_( 'Your request to extend subscription is pending.' ).'</p>' :'';
						
					}
					
					$units_select = array();
					$numunits = $this->services[$i]->maxunits / $this->services[$i]->unitsize;															
					$unitsize = $this->services[$i]->unitsize;
					
					if ($thissub) { $units_select[0] = 0; }
					for ($p=1; $p <= $numunits; $p++)
					{						
						$units_select[$unitsize] = $unitsize;
						$unitsize = $unitsize + $this->services[$i]->unitsize;					
					}
					
					$unitsChoice = JobsHtml::formSelect('units_'.$this->services[$i]->id, $units_select, '', "option units");
					$iniprice = $thissub ? 0 : $this->services[$i]->unitprice;
							
					
					//$html .= '</label>'.n;
					$html .= '<div class="bindtogether product">'.n;
					$html .= t.t.t.'  <input class="option service" type="radio" name="serviceid" id="service_'.$this->services[$i]->id.'" value="'.$this->services[$i]->id.'" ';
					if($thissub or ($this->subscription->serviceid==0 && $i==0) ){
					$html .= 'checked="checked"';
					}
					$html .= ' /> ';
					$html .= $this->services[$i]->title.' - <span class="priceline">'.$this->services[$i]->currency.' '.$this->services[$i]->unitprice.'  '.JText::_( 'per' ).' '.$this->services[$i]->unitmeasure.'</span>'.n;
					$html .= '<span> '.$this->services[$i]->description.'</span>'.n;
					
					$html .= '<div class="subdetails" id="plan_'.$this->services[$i]->id.'">'.n;
					$html .= $thissub ? $expires : '';	
					$html .= JobsHtml::confirmscreen(JRoute::_('index.php?option='.$option.a.'task=dashboard'.a.'uid='.$this->uid), JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'uid='.$this->uid));		
					$html .= t.t.t.'<label> ';
					$html .= $thissub ? JText::_( 'Renew/Extend' ) : JText::_( 'Sign up' );
					$html .= ' '.JText::_( 'for' ).' '.n;
					$html .= t.t.t.$unitsChoice;
					//$html .= t.t.t.$this->services[$i]->unitmeasure.'(s) '.JText::_( 'at' ).' '.$this->services[$i]->unitprice.' '.$this->services[$i]->currency.' '.JText::_( 'per' ).' '.$this->services[$i]->unitmeasure.'</label>'.n;
					$html .= t.t.t.$this->services[$i]->unitmeasure.'(s) </label>'.n;
					$html .= '<span class="totalprice">'.JText::_( 'Your total' ).' ';
					$html .= $thissub ? strtolower(JText::_( 'new' )).' ' : '';
					$html .= JText::_( 'payment will be' ).' <span class="no">'.$this->services[$i]->currency.'</span> <span id="injecttotal_'.$this->services[$i]->id.'"> '.$iniprice.'</span>';
					$html .= '.</span>'.n;
					
					// GOOGLE Checkout (TBD)
					$html .= '<input type="hidden" class="product-price" value="'.$this->services[$i]->unitprice.'" />'.n;
					$html .= '<input type="hidden" class="product-title" value="'.$this->services[$i]->title.'" />'.n;
					//$html .= '<div  role="button" alt="Add to cart" tabindex="0" class="googlecart-add-button"> </div>';
					
					$html .= '</div>'.n;
					$html .= '</div>'.n;
					$html .= '<input type="hidden" name="price_'.$this->services[$i]->id.'" id="price_'.$this->services[$i]->id.'" value="'.$this->services[$i]->unitprice.'" />'.n;
					
					
				}
				echo $html;
				$btn = $this->subscription->id ? JText::_( 'Save Subscription' ) : JText::_( 'Process Subscription Order' );
			?>
		
          <label>
			<?php echo JText::_( 'Contact Phone' ).': <span class="required">'.JText::_( 'REQUIRED WITH PAYMENT' ).'</span>'; ?>
			<input class="inputbox" type="text" id="contact" name="contact" size="50" maxlength="15" value="<?php echo $this->subscription->contact; ?>" />
		</label>
  	<div class="submitblock">
    	 <input type="hidden" name="subid" value="<?php echo $this->employer->subscriptionid; ?>" />
   		 <input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
		 <input type="submit" class="option" value="<?php echo $btn; ?>" />
    </div>
		
	</fieldset>
</form>

<div class="clear"></div>
</div>
<?php if(1==2) { ?>
<script id="googlecart-script" type="text/javascript"
  src="http://checkout.google.com/seller/gsc/v2/cart.js?mid=MERCHANT_ID"
  aid="UA-8883888-8"
  currency="USD">
</script>
<?php } ?>