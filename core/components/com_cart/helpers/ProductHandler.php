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

namespace Components\Cart\Helpers;

use Components\Storefront\Models\Warehouse;

require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Product handler. Handles purchased products/items. Runs a proper handler on each purchased item.
 */
class CartProductHandler
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

		$warehouse = new Warehouse();

		$ptIdTypeInfo = $warehouse->getProductTypeInfo($ptId);

		// Run both product model handler and type handler if needed.
		// Model handlers must go first for type handlers to potentially use their updates

		$handlersPath = PATH_CORE . DS. 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'handlers';

		// MODEL HANDLER
		$modelHandlerClass = str_replace(' ', '_', ucwords(strtolower($ptIdTypeInfo['ptModel']))) . '_Model_Handler';
		if (file_exists($handlersPath . DS . 'model' . DS . $modelHandlerClass . '.php')) {
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
		if (file_exists($handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php')) {
			// Include the parent class
			include_once($handlersPath . DS . 'TypeHandler.php');

			// Include the handler file
			include_once($handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php');

			$typeHandler = new $typeHandlerClass($this->item, $this->crtId);
			$typeHandler->handle();
		}


		// CUSTOM HANDLERS (if any)
		if (!empty($this->item['meta']['customHandler']))
		{
			$customHandler = $this->item['meta']['customHandler'];
			$customHandlerClass = str_replace(' ', '_', ucwords(strtolower($customHandler))) . '_Custom_Handler';

			if (file_exists($handlersPath . DS . 'custom' . DS . $customHandlerClass . '.php')) {
				// Include the parent class
				include_once($handlersPath . DS . 'CustomHandler.php');

				// Include the handler file
				include_once($handlersPath . DS . 'custom' . DS . $customHandlerClass . '.php');

				$customHandler = new $customHandlerClass($this->item, $this->crtId);
				$customHandler->handle();
			}
		}
	}

}