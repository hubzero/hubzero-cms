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
 * @package   Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Coupon
 */
class Hubzero_Cart_Coupons
{
	// Database instance
	var $db = NULL;
	
	// Debug moode
	var $debug = false;	
	
	/**
	 * Coupons constructor
	 * 
	 * @param 	void
	 * @return 	void
	 */
	public function __construct()
	{
		// Initialize DB
		$this->_db =& JFactory::getDBO();
		
		// Load language file
		JFactory::getLanguage()->load('com_cart');
	}
	
	/**
	 * Check if coupon is valid
	 * 
	 * @param 	string		$couponCode coupon code
	 * @return 	int			coupon id if the code is valid
	 */
	public function isValid($couponCode)
	{
		ximport('Hubzero_Storefront_Coupons');
		$coupons = new Hubzero_Storefront_Coupons;
		return $coupons->isValid($couponCode);
	}
	
	/**
	 * Do the maintenance of coupons -- make sure all coupons are valid and in order, remove unnecesary coupons...
	 * Get all perks available for the cart
	 * 
	 * @param 	object 		$cartInfo 
	 * @param	object		$cartCoupons
	 * @return 	object		perks
	 */
	public function getPerks($cartInfo, $cartCoupons)
	{	
		/*
		echo "Cart info: \n";
		print_r($cartInfo);
		echo "\n============== \n\n";
		
		echo "Cart coupons: \n";
		print_r($cartCoupons);
		echo "\n============== \n\n";
		*/
		
		$cartItems = $cartInfo->items;
		// initialize perks
		$perks = array();
		
		// Since coupons in $cartCoupons are expected to be ordered with item coupons coming first, item coupons will be processed first in bulk
		// set the flag if currently processing item coupons
		$itemCoupon = true;
		
		// Initialize $itemsDiscountsTotal -- the total sum of all item discount amounts
		$itemsDiscountsTotal = 0;
		
		ximport('Hubzero_Storefront_Coupons');
		
		/* 
			Ititialize a global SKU/Other object to coupon mapping. 
			The coupons are ordered by time applied, so if there is more than one coupon for one SKU, 
			we need to apply the most recently applied only. As we iterate through coupons we only want to record the recent one.
			Same for non item coupons -- there is only one coupon per object type is allowed (shipping, total order discount, etc...)			
		*/
		$couponMappings = array();
				
		// go through each coupon in the cart	
		foreach ($cartCoupons as $cn) {
			
			// Check if coupon applies to cart items
			if (!$cn->itemCoupon)
			{
				$itemCoupon = false;
			}
			
			$coupon = Hubzero_Storefront_Coupons::getCouponInfo($cn->cnId, $itemCoupon); // Load objects for itemCoupons only
						
			// check if coupon applies and if it does, get the perk info 			
			/*
			echo "Coupon info: \n";
			print_r($coupon);
			echo "\n============== \n\n";
			*/
			
			// First check coupon conditions
			// Big TODO
						
			// get object type
			$couponObjectType = $cn->cnObject;
			
			switch ($couponObjectType) 
			{
				case 'sku':
					break;
				case 'product':
					break;
				case 'order':
					break;
				case 'shipping':
					break;
				default:
					throw new Exception(JText::_('Invalid coupon. Invalid object type.'));
			}
			
			// Get coupon action in case we need to apply it
			// right now ther is only one action -- discount			
			$couponAction = $coupon->action->cnaAction;
			
			if ($couponAction == 'discount') 
			{
				// find out whether it is a absolute amount or percentage
				if (substr($coupon->action->cnaVal, -1) == '%') 
				{
					$couponDiscountUnit = 'percentage';
					$couponDiscount = substr($coupon->action->cnaVal, 0, strlen($coupon->action->cnaVal) - 1);
				}
				else 
				{
					$couponDiscountUnit = 'absolute';
					$couponDiscount = $coupon->action->cnaVal;
				}
				
				// make sure discount is numeric
				if (!is_numeric($couponDiscount)) 
				{
					throw new Exception(JText::_('Invalid coupon. Invalid discount amount ' . $couponDiscount . '.'));	
				}
				
			}
			else {
				throw new Exception(JText::_('Invalid coupon. Invalid action type.'));
			}
			
			// Check if we need to match against the objet type to make sure the coupon is applicable
			if ($itemCoupon)
			{
				// check if there are options available
				if (empty($coupon->objects)) 
				{
					throw new Exception(JText::_('Invalid coupon. No object found.'));
				}
								
				// Go through each object and try to find a match in a cart
				foreach ($coupon->objects as $couponObject)
				{
					foreach ($cartItems as $sId => $cartItem) 
					{						
						// try to find a match	
						if(	($couponObjectType == 'sku' && $sId == $couponObject->cnoObjectId) ||
							($couponObjectType == 'product' && $cartItem['info']->pId == $couponObject->cnoObjectId))
						{
							// Initialize the perk
							unset($perk);						
							$perk->name = $cn->cnDescription;
							$perk->forSku = $sId;
							$perk->couponId = $cn->cnId;
							
							// Save current/overwrite previous mapping
							$couponMappings[$sId] = $cn->cnId;
							
							// figure out the perk
							if ($couponAction == 'discount') 
							{	
								/* 
								TODO see if there is a limit of same products this can be applied to.
								Also ssee a note about figuring out the most expensive items if there is a limit.
								*/
								
								// Calculate discount
								if ($couponDiscountUnit == 'absolute') 
								{						
									// make sure $couponDiscount is not more than the item price
									if ($cartItem['info']->sPrice < $couponDiscount)
									{
										$couponDiscount = $cartItem['info']->sPrice;
									}
									
									$discountAmount = $couponDiscount * $cartItem['cartInfo']->qty;
								}
								elseif ($couponDiscountUnit == 'percentage') 
								{						
									// make sure $couponDiscount is <= 100%
									if ($couponDiscount > 100)
									{
										$couponDiscount = 100;	
									}									
									
									$discountAmount = ($cartItem['info']->sPrice * $cartItem['cartInfo']->qty) *  ($couponDiscount / 100);
								}
								
								$perk->discount = round($discountAmount, 2, PHP_ROUND_HALF_DOWN);
								$itemsDiscountsTotal += $perk->discount;
								
								$perks['items'][$perk->forSku] = $perk;
							}
							else 
							{
								throw new Exception(JText::_('Invalid coupon. Only discounts are afailable for skus and products'));
							}
							
							if ($couponObjectType == 'sku')
							{
								break;
							}
							//no break for products -- keep going maybe there are several SKUs of the same product in the cart
						}
						// No match
					}
					
				}
			}
			// Coupon if generic, not item based. All item coupons have been processed by this time, save to calculate total discounts
			else {
				// Initialize the perk
				unset($perk);						
				$perk->name = $cn->cnDescription;
				$perk->couponId = $cn->cnId;
				
				$couponMappings[$couponObjectType] = $cn->cnId;
				
				// figure out the perk
				if ($couponAction == 'discount') 
				{	
					switch ($couponObjectType) 
					{
						case 'order':
							$amountToDiscount = $cartInfo->totalCart - $itemsDiscountsTotal;
							break;
						case 'shipping':
							$amountToDiscount = 0;
							if (!empty($cartInfo->shipping))
							{
								$amountToDiscount = $cartInfo->shipping;
							}
							break;
					}
					
					// Quit if there is no amount to discount
					if (!$amountToDiscount) {
						//break;	
					}
				
					/* 
					TODO see if there is a limit of same products this can be applied to.
					Also ssee a note about figuring out the most expensive items if there is a limit.
					*/
					
					// Calculate discount
					if ($couponDiscountUnit == 'absolute') 
					{						
						$discountAmount = $couponDiscount;
					}
					elseif ($couponDiscountUnit == 'percentage') 
					{						
						// make sure $couponDiscount is <= 100%
						if ($couponDiscount > 100)
						{
							$couponDiscount = 100;	
						}									
						
						$discountAmount = $amountToDiscount * ($couponDiscount / 100);
					}
					
					$perk->discount = round($discountAmount, 2);
					
					$perks['generic'][$couponObjectType] = $perk;
				}
			}
		}
		
		//print_r($couponMappings); die;
		
		// Do the coupon maintenance
		/*
			At this point $couponMappings has all the coupons that need to be applied to cart -- all other unsued coupons have to be released
		*/
		
		// Go through each coupon again and see if it needs to be applied. If not -- release it
		foreach ($cartCoupons as $cn) 
		{
			if (!in_array($cn->cnId, $couponMappings))
			{
				echo 'Remove' . $cn->cnId . '<br>';
			}
		}
		
		$perksInfo->itemsDiscountsTotal = $itemsDiscountsTotal;
		$perks['info'] = $perksInfo;
		
		//echo "Perks: \n";
		//print_r($perks);		
		//die;
		
		
		return $perks;
	}
		
}

