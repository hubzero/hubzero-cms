<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Helpers;

use Components\Storefront\Models\Warehouse;

require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Warehouse.php';

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
	public function __construct($item, $crtId, $tId)
	{
		$this->item = $item;
		$this->crtId = $crtId;
		$this->tId = $tId;
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

		$handlersPath = dirname(__DIR__) . DS . 'lib' . DS . 'handlers';

		// MODEL HANDLER
		$modelHandlerClass = str_replace(' ', '_', ucwords(strtolower($ptIdTypeInfo['ptModel']))) . '_Model_Handler';
		if (file_exists($handlersPath . DS . 'model' . DS . $modelHandlerClass . '.php'))
		{
			// Include the parent class
			include_once $handlersPath . DS . 'ModelHandler.php';

			// Include the handler file
			include_once $handlersPath . DS . 'model' . DS . $modelHandlerClass . '.php';

			$modelHandler = new $modelHandlerClass($this->item, $this->crtId, $this->tId);
			$modelHandler->handle();
		}


		// TYPE HANDLER
		$typeHandlerClass = str_replace(' ', '_', ucwords(strtolower($ptIdTypeInfo['ptName']))) . '_Type_Handler';
		//print_r($typeHandlerClass); die;
		if (file_exists($handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php'))
		{
			// Include the parent class
			include_once $handlersPath . DS . 'TypeHandler.php';

			// Include the handler file
			include_once $handlersPath . DS . 'type' . DS . $typeHandlerClass . '.php';

			$typeHandler = new $typeHandlerClass($this->item, $this->crtId);
			$typeHandler->handle();
		}


		// CUSTOM HANDLERS (if any)
		if (!empty($this->item['meta']['customHandler']))
		{
			$customHandler = $this->item['meta']['customHandler'];
			$customHandlerClass = str_replace(' ', '_', ucwords(strtolower($customHandler))) . '_Custom_Handler';

			if (file_exists($handlersPath . DS . 'custom' . DS . $customHandlerClass . '.php'))
			{
				// Include the parent class
				include_once $handlersPath . DS . 'CustomHandler.php';

				// Include the handler file
				include_once $handlersPath . DS . 'custom' . DS . $customHandlerClass . '.php';

				$customHandler = new $customHandlerClass($this->item, $this->crtId);
				$customHandler->handle();
			}
		}
	}
}
