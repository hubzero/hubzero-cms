<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Product handler. Handles purchased products/items. Runs a proper handler on each purchased item.
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

		$handlersPath = PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'handlers';

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