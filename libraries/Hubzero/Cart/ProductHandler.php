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
 * @package   Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product handler. Handled purchased products/items.
 */
class Hubzero_Cart_ProductHandler
{
	// Item info
	var $item;
	var $crtId;
	
	/**
	 * Constructor
	 * 
	 * @param 	object			item info
	 * @param	int				cart ID
	 * @return 	void
	 */
	public function __construct($item, $crtId)
	{
		$this->item = $item;
		$this->crtId = $crtId;
		
		//print_r($crtId); die;
	}
	
	/**
	 * Process item
	 * 
	 * @param 	void
	 * @return 	bool
	 */
	public function handle()
	{
		// Get product type info
		$ptId = $this->item['info']->ptId;
		
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		
		$ptIdIndo = $warehouse->getProductTypeInfo($ptId);
		
		// run both product model and type handlers if needed. Model handlers must go first for type handlers to potentially use their updates
		
		$modelHandlerclass = ucfirst($ptIdIndo['ptModel']) . '_Model_Handler';
		if (class_exists($modelHandlerclass))
		{
			$modelHandler = new $modelHandlerclass($this->item, $this->crtId);	
			$modelHandler->handle();			
		}
		
		$typeHandlerClass = ucfirst($ptIdIndo['ptName']) . '_Type_Handler';		
		if (class_exists($typeHandlerClass))
		{
			$typeHandler = new $typeHandlerClass($this->item, $this->crtId);	
			$typeHandler->handle();
		}				
	}
	
}

// ==================================================== Type handlers

class Type_Handler
{
	// Database instance
	var $db = NULL;
	
	// Item info
	var $item;
	
	var $crtId;
	
	/**
	 * Constructor
	 * 
	 */
	public function __construct($item, $crtId)
	{
		$this->item = $item;
		$this->crtId = $crtId;
	}	
}

class Course_Type_Handler extends Type_Handler
{
	/**
	 * Constructor
	 * 
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId)
	{
		parent::__construct($item, $crtId);
	}	
	
	public function handle()
	{
		ximport('Hubzero_Storefront_Memberships');
		$ms = new Hubzero_Storefront_Memberships();
		
		// Get current registration
		$membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
		$expiration = $membership['crtmExpires'];
		
		// Get course ID
		$courseId = $this->item['meta']['courseId'];
		
		// Initialize static cart
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart(NULL, true);
		
		// Get user id
		$userId = $cart->getCartUser($this->crtId);
				
		// Load courses model and register
		// registerForCourse($userId, $courseId, $expiration);
		
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
		
		$course = CoursesModelCourse::getInstance($this->item['meta']['courseId']);
		
		if (!$course->offerings()->count()) {
			// error enrolling
		}
		else 
		{
			// Get to the first and probably the only offering
			//$offering = $course->offerings()->current();
			$offering = $course->offering($this->item['meta']['offeringId']);
			
			$offering->add($userId);	
			//$offering->remove($userId);				
		}					
	}
}

// ==================================================== Model handlers

class Model_Handler
{
	// Database instance
	var $db = NULL;
	
	// Item info
	var $item;
	
	var $crtId;
	
	/**
	 * Constructor
	 * 
	 */
	public function __construct($item, $crtId)
	{
		$this->item = $item;
		$this->crtId = $crtId;
	}	
}

class Membership_Model_Handler extends Model_Handler
{
	/**
	 * Constructor
	 * 
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId)
	{
		parent::__construct($item, $crtId);
	}	
	
	public function handle()
	{
		ximport('Hubzero_Storefront_Memberships');
		$ms = new Hubzero_Storefront_Memberships();
		
		// Get new expiraton date
		$productMembership = $ms->getNewExpirationInfo($this->crtId, $this->item);
		
		// Update/Create membership expiration date with new value
		$ms->setMembershipExpiration($this->crtId, $this->item['info']->pId, $productMembership->newExpires);		
	}
}