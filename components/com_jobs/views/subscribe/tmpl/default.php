<?php 
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	$dateFormat = '%d %b %Y';
	$tz = null;

	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		$dateFormat = 'd M Y';
		$tz = false;
	}

	/* Subscription screen */

	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
	$juser 	  =& JFactory::getUser();

	// get some configs
	$promoline = $this->config->get('promoline') ? $this->config->get('promoline') : '';
	$infolink = $this->config->get('infolink') ? $this->config->get('infolink') : '';

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($juser->get('guest')) { ?> 
    	<li><?php echo JText::_('COM_JOBS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=view').'?action=login">'.JText::_('COM_JOBS_ACTION_LOGIN').'</a> '.JText::_('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('COM_JOBS_SHORTLIST'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li><?php echo JText::_('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
        	<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="myresume btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=addresume'); ?>"><?php echo JText::_('COM_JOBS_MY_RESUME'); ?></a></li>
    <?php } ?>  		
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=confirm'); ?>" method="post" id="hubForm">
		<div class="explaination">
				<p><?php echo JText::_('COM_JOBS_SUBSCRIBE_HINT_EMPLOYER_INFO') ?></p>
		</div>
		<fieldset id="subForm">
			<legend><?php echo JText::_('COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION'); ?></legend>

			<label for="companyName">
				<?php echo JText::_( 'COM_JOBS_EMPLOYER_COMPANY_NAME' ); ?>:
				<span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<input class="inputbox" type="text" id="companyName" name="companyName" size="50" maxlength="100" value="<?php echo $this->escape($this->employer->companyName); ?>" />
			</label>
			<label for="companyLocation">
				<?php echo JText::_( 'COM_JOBS_EMPLOYER_COMPANY_LOCATION' ); ?>:
				<span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
				<input class="inputbox" type="text" id="companyLocation" name="companyLocation" size="50" maxlength="200" value="<?php echo $this->escape($this->employer->companyLocation); ?>" />  
			</label>
			<label for="companyWebsite">
				<?php echo JText::_( 'COM_JOBS_EMPLOYER_COMPANY_WEBSITE' ); ?>:
				<input class="inputbox" type="text" id="companyWebsite" name="companyWebsite" size="50" maxlength="200" value="<?php echo $this->escape($this->employer->companyWebsite); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination">
			<p><?php echo JText::_('COM_JOBS_SUBSCRIBE_HINT_PICK') ?></p>
			<h4><?php echo JText::_('COM_JOBS_SUBSCRIBE_NEXT_STEP') ?></h4>
			<p><?php echo JText::_('COM_JOBS_SUBSCRIBE_HINT_PAYMENT') ?></p>
		<?php if ($promoline) { ?> 
			<p class="promo"><?php echo $promoline; ?></p>
		<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_JOBS_SUBSCRIPTION_DETAILS'); ?></legend>

			<label>
				<?php echo JText::_( 'COM_JOBS_SUBSCRIBE_SELECT_SERVICE' ); ?>:
				<span class="required"><?php echo JText::_('COM_JOBS_REQUIRED'); ?></span>
			</label>
			<?php 
				$html = '';
				$now = date( 'Y-m-d H:i:s', time() );
				for ($i=0, $n=count( $this->services ); $i < $n; $i++)  {
					// do we have an active subscription?
					$thissub = ($this->services[$i]->id == $this->subscription->serviceid) ? 1 : 0;

					// Determine expiration date
					if($thissub) {
						$length = $this->subscription->status==0 ? $this->subscription->pendingunits : $this->subscription->units;
						$expires  = $this->subscription->expires > $now && $this->subscription->status==1 ?  '<p class="yes">' : '<p class="no">';
						$expires .= JText::_( 'COM_JOBS_YOUR' ).' '.$length.'-'.$this->services[$i]->unitmeasure.' '.JText::_( 'COM_JOBS_SUBSCRIPTION' ).' ';
						if($this->subscription->status==1) {
							$expires .= $this->subscription->expires > $now ? strtolower(JText::_( 'COM_JOBS_SUBSCRIPTION_STATUS_EXPIRES' )) : strtolower(JText::_( 'COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED' )) ;
							$expires .= ' '.JText::_( 'COM_JOBS_ON' ).' '.JHTML::_('date', $this->subscription->expires, $dateFormat, $tz).'.';
						}
						else {
						$expires .= JText::_( 'COM_JOBS_SUBSCRIPTION_IS_PENDING' ) ;
						}

						$expires .= '</p>'."\n";
						$expires .= $this->subscription->expires > $now ? ' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=cancel&uid='.$this->uid).'" class="cancelit" id="showconfirm">[ '.JText::_( 'COM_JOBS_SUBSCRIPTION_CANCEL_THIS' ).' ]</a>' : '';
						$expires .= $this->subscription->pendingunits > 0 && $this->subscription->status==1  ? '<p class="no">'.JText::_( 'COM_JOBS_SUBSCRIPTION_EXTEND_REQUEST_PENDING' ).'</p>' :'';

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

					$html .= '<div class="bindtogether product">'."\n";
					$html .= '  <input class="option service" type="radio" name="serviceid" id="service_'.$this->services[$i]->id.'" value="'.$this->services[$i]->id.'" ';
					if($thissub or ($this->subscription->serviceid==0 && $i==0) ){
						$html .= 'checked="checked"';
					}
					$html .= ' /> ';
					$html .= $this->services[$i]->title.' - <span class="priceline">'.$this->services[$i]->currency.' '.$this->services[$i]->unitprice.'  '.JText::_( 'COM_JOBS_PER' ).' '.$this->services[$i]->unitmeasure.'</span>'."\n";
					$html .= '<span> '.$this->services[$i]->description.'</span>'."\n";

					$html .= '<div class="subdetails" id="plan_'.$this->services[$i]->id.'">'."\n";
					$html .= $thissub ? $expires : '';
					if($thissub or ($this->subscription->serviceid==0 && $i==0))
					{
						$html .= JobsHtml::confirmscreen(JRoute::_('index.php?option='.$this->option.'&task=dashboard&uid='.$this->uid), JRoute::_('index.php?option='.$this->option.'&task=cancel&uid='.$this->uid));
					}
				
					$html .= '<label> ';
					$html .= $thissub ? JText::_( 'COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW' ) : JText::_( 'COM_JOBS_ACTION_SIGN_UP' );
					$html .= ' '.JText::_( 'for' ).' '."\n";
					$html .= $unitsChoice;
					$html .= $this->services[$i]->unitmeasure.'(s) </label>'."\n";
					$html .= '<span class="totalprice">'.JText::_( 'COM_JOBS_SUBSCRIBE_YOUR_TOTAL' ).' ';
					$html .= $thissub ? strtolower(JText::_( 'COM_JOBS_NEW' )).' ' : '';
					$html .= JText::_( 'COM_JOBS_SUBSCRIBE_PAYMENT_WILL_BE' ).' <span class="no">'.$this->services[$i]->currency.'</span> <span id="injecttotal_'.$this->services[$i]->id.'"> '.$iniprice.'</span>';
					$html .= '.</span>'."\n";

					// GOOGLE Checkout (TBD)
					$html .= '<input type="hidden" class="product-price" value="'.$this->escape($this->services[$i]->unitprice).'" />'."\n";
					$html .= '<input type="hidden" class="product-title" value="'.$this->escape($this->services[$i]->title).'" />'."\n";
					//$html .= '<div  role="button" alt="Add to cart" tabindex="0" class="googlecart-add-button"> </div>';

					$html .= '</div>'."\n";
					$html .= '</div>'."\n";
					$html .= '<input type="hidden" name="price_'.$this->services[$i]->id.'" id="price_'.$this->services[$i]->id.'" value="'.$this->escape($this->services[$i]->unitprice).'" />'."\n";
				}
				echo $html;
				$btn = $this->subscription->id ? JText::_( 'COM_JOBS_SUBSCRIPTION_SAVE' ) : JText::_( 'COM_JOBS_SUBSCRIPTION_PROCESS_ORDER' );
			?>
			<label for="contact">
				<?php echo JText::_( 'COM_JOBS_SUBSCRIPTION_CONTACT_PHONE' ).': <span class="required">'.JText::_( 'COM_JOBS_REQUIRED_WITH_PAYMENT' ).'</span>'; ?>
				<input class="inputbox" type="text" id="contact" name="contact" size="50" maxlength="15" value="<?php echo $this->escape($this->subscription->contact); ?>" />
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

<?php if (1==2) { // GOOGLE Checkout (TBD) ?>
<script id="googlecart-script" type="text/javascript"
	src="http://checkout.google.com/seller/gsc/v2/cart.js?mid=MERCHANT_ID"
	aid="UA-8883888-8"
	currency="USD">
</script>
<?php } ?>
