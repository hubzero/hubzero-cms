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
 * Product handler. Handles purchased products/items. Runs a popper handler on each purchased item.
 */
class Cart_ProductHandler
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

		//print_r($crtId);;
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

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$ptIdTypeInfo = $warehouse->getProductTypeInfo($ptId);

		// Run both product model handler and type handler if needed.
		// Model handlers must go first for type handlers to potentially use their updates

		// MODEL HANDLER
		$handlersPath = JPATH_ROOT . DS . 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'handlers';
		$modelHandlerClass = str_replace(' ', '_', ucwords(strtolower($ptIdTypeInfo['ptModel']))) . '_Model_Handler';
		if (file_exists($handlersPath . DS . 'model' . DS .$modelHandlerClass . '.php'))
		{
			// Include the parent class
			include_once($handlersPath . DS . 'ModelHandler.php');

			// Include the handler file
			include_once($handlersPath . DS . 'model' . DS . $modelHandlerClass . '.php');

			$modelHandler = new $modelHandlerClass($this->item, $this->crtId);
			$modelHandler->handle();
		}

		// TYPE HANDLER
		$typeHandlerClass = str_replace(' ', '_', ucwords(strtolower($ptIdTypeInfo['ptName']))) . '_Type_Handler';
		//print_r($typeHandlerClass); die;
		if (file_exists($handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php'))
		{
			// Include the parent class
			include_once($handlersPath . DS . 'TypeHandler.php');

			// Include the handler file
			include_once($handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php');

			$typeHandler = new $typeHandlerClass($this->item, $this->crtId);
			$typeHandler->handle();
		}

	}

}