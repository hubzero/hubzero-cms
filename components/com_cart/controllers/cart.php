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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Cart controller class
 */
class CartControllerCart extends ComponentController
{	
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{		
		// Get the task
		$this->_task  = JRequest::getVar('task', '');
		
		$this->_getStyles();
		
		if (empty($this->_task))
		{
			$this->_task = 'home';
			$this->registerTask('__default', $this->_task);
		}
		
		parent::execute();
	}
	
	/**
	 * Display default page
	 * 
	 * @return     void
	 */
	public function homeTask() 
	{
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart();
		
		// update cart if needed for non-ajax transactions
		$updateCartRequest = JRequest::getVar('updateCart', false, 'post');
		
		$pIds = JRequest::getVar('pId', false, 'post');
		
		//print_r($pIds); die;
		
		// If pIds are posted, convert them to SKUs
		if (!empty($pIds))
		{		
			$skus = array();
			ximport('Hubzero_Storefront_Warehouse');
			$warehouse = new Hubzero_Storefront_Warehouse();
			
			foreach($pIds as $pId => $qty)
			{
				$product_skus = $warehouse->getProductSkus($pId);
				
				// must be only one sku to work
				if (sizeof($product_skus) != 1)
				{
					continue;
				}
				
				$skus[$product_skus[0]] = $qty;
				
				// each pId must map to one SKU, otherwise ignored
			}			
			
		}
		else {
			$skus = JRequest::getVar('skus', false, 'post');
		}		
		//print_r($skus); die;
		
		// Initialize errors array
		$errors = array();
		
		if ($updateCartRequest && $skus)
		{
			// Turn off syncing to prevent redundant session update queries
			$cart->setSync(false);
			foreach ($skus as $sId => $qty)
			{
				try
				{
					$cart->update($sId, $qty);
				}
				catch (Exception $e)
				{
					$updateErrors[] = $e->getMessage();
				}
			}
			
			if (!empty($errors)) 
			{
				$redirect = false;
			}
			else {
				// set flag to redirect
				$redirect = true;				
			}			
			
		}
		
		// add coupon if needed
		$addCouponRequest = JRequest::getVar('addCouponCode', false, 'post');
		$couponCode = JRequest::getVar('couponCode', false, 'post');
		
		if ($addCouponRequest && $couponCode)
		{
			// Sync cart before pontial coupons applying
			$cart->getCartInfo(true);
			
			// Initialize errors array
			$couponErrors = array();
						
			// Add coupon
			try
			{
				$cart->addCoupon($couponCode);
			}
			catch (Exception $e)
			{
				$errors[] = $e->getMessage();
			}
			
			if (!empty($errors)) 
			{
				$redirect = false;
			}
			else {
				// set flag to redirect
				$redirect = true;
			}
		}
		
		if (!empty($redirect) && $redirect)
		{
			// prevent resubmitting form by refresh
			// If not an ajax call, redirect to cart
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
			$app  = & JFactory::getApplication();
			$app->redirect($redirect_url);		
		}
		
		// Set errors
		$this->view->setError($errors);
		
		// Get the latest synced cart info, it will also enable cart syncing that was turned off before
		$cartInfo = $cart->getCartInfo(true);
		//print_r($cartInfo); die;
		$this->view->cartInfo = $cartInfo;
		
		// Handle coupons
		$couponPerks = $cart->getCouponPerks(); 
		//print_r($couponPerks); die;
		$this->view->couponPerks = $couponPerks;
		
		// Handle memberships
		$membershipInfo = $cart->getMembershipInfo(); 
		//print_r($membershipInfo); die;
		$this->view->membershipInfo = $membershipInfo;
		
		// Check if there are changes to display
		if ($cart->cartChanged())
		{
			$cartChanges = $cart->getCartChanges();
			$this->view->setError($cartChanges);
		}
				
		$this->view->display();
		
		$cart->printCartInfo();
	}
	
	/**
	 * Display default page
	 * 
	 * @return     void
	 */
	public function testgroundTask() 
	{	
		// CREATE COUPON
		ximport('Hubzero_Storefront_Coupon');		
		try 
		{
			// Constructor take the coupon code
			$coupon = new Hubzero_Storefront_Coupon('couponcode3');
			// Couponn description (shows up in the cart)
			$coupon->setDescription('Test coupon, 100% off product with ID 1');
			// Expiration date 
			$coupon->setExpiration('Feb 22, 2022');
			// Number of times coupon can be used (unlimited by default)			
			$coupon->setUseLimit(1);
			
			// Product the coupon will be applied to: 
			// first parameter: product ID
			// second parameter [optional, unlimited by default]: max quantity of products coupon will be applied to (if buying multiple)
			$coupon->addObject(1, 1);
			// Action, only 'discount' for now
			// second parameter either percentage ('10%') or absolute dollar value ('20')
			$coupon->setAction('discount', '100%');
			// Add coupon
			$coupon->add();
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}		
		return;	
		
		return;
		
		// --------------------------------------
		
		// CREATE COUPON
		ximport('Hubzero_Storefront_Coupon');		
		try 
		{
			// Constructor take the coupon code
			$coupon = new Hubzero_Storefront_Coupon('couponcode3');
			// Couponn description (shows up in the cart)
			$coupon->setDescription('Test coupon, 10% off product with ID 111');
			// Expiration date 
			$coupon->setExpiration('Feb 22, 2022');
			// Number of times coupon can be used (unlimited by default)			
			$coupon->setUseLimit(1);
			
			// Product the coupon will be applied to: 
			// first parameter: product ID
			// second parameter [optional, unlimited by default]: max quantity of products coupon will be applied to (if buying multiple)
			$coupon->addObject(111, 1);
			// Action, only 'discount' for now
			// second parameter either percentage ('10%') or absolute dollar value ('20')
			$coupon->setAction('discount', '10%');
			// Add coupon
			$coupon->add();
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}		
		return;
		
		// DELETE COUPON
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		try 
		{
			$warehouse->deleteCoupon('couponcode3');
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;
		
		// CREATE NEW COURSE
		ximport('Hubzero_Storefront_Product');
		
		$course = new Hubzero_Storefront_Course();
		$course->setName('Name of the course');
		$course->setDescription('Short description');
		$course->setPrice(12.00);
		// Membership model: membership duration period (must me in MySQL date format: 1 DAY, 2 MONTH, 3 YEAR...) 
		$course->setTimeToLive('1 YEAR');
		// Course alias id
		$course->setCourseId('nanoscaletransistors');
		try 
		{
			// Returns object with values, pId is the new product ID to link to
			$info = $course->add();			
			//print_r($info);
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;
		
		// GET EXISTING COURSE, modify it and save
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		try 
		{		
			// Get course by pID returned with $course->add() above
			$course = $warehouse->getCourse(1023);
			$course->setName('New course name');
			$course->setDescription('New description');
			$course->setPrice(55);
			$course->setTimeToLive('10 YEAR');
			$course->update();
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;
				
		// UPDATE COURSE by recreatiing it				
		ximport('Hubzero_Storefront_Product');
		$course = new Hubzero_Storefront_Course();
		$course->setName('Operations Management 104');
		$course->setDescription('Operations Management 104 is some kind of test course for now...');
		$course->setPrice(13.05);
		$course->setCourseId(5);
		
		// Existing course ID (pID returned with $course->add() when the course was created). Must be set to be able to update.
		$course->setId(1023);		
		try 
		{
			$info = $course->update();			
			//print_r($info);
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;	
		
		// DELETE COURSE
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		// Delete by existing course ID (pID returned with $course->add() when the course was created)
		$warehouse->deleteProduct(1023);
		return;
	}
	
	public function postTask() 
	{		
		$doc =& JFactory::getDocument();
		$doc->addScript(DS . 'components' . DS . 'com_cart' . DS . 'assets' . DS . 'js' . DS . 'test.js');
		
		$this->view->display();
	}
}

