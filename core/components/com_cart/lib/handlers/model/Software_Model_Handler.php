<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class Software_Model_Handler extends Model_Handler
{
	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId, $tId)
	{
		parent::__construct($item, $crtId, $tId);
	}

	public function handle()
	{
		$itemInfo = $this->item['info'];
		$itemMeta = $this->item['meta'];
		$itemCartInfo = $this->item['cartInfo'];

		// Check the serial management. If multiple -- need to update the transaction info items with the serials and mark the serials as used
		if (isset($itemMeta['serialManagement']) && $itemMeta['serialManagement'] == 'multiple')
		{
			// Get the required number of serials
			$serialsNeeded = $itemCartInfo->qty;

			require_once \Component::path('com_storefront') . DS . 'helpers' . DS . 'Serials.php';
			// Get the serial numbers
			$serialNumbers = \Components\Storefront\Helpers\Serials::issueSerials($itemInfo->sId, $serialsNeeded);

			$this->item['meta']['serials'] = $serialNumbers;
			// Update the transaction items with serials
			require_once dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php';
			\Components\Cart\Models\Cart::updateTransactionItem($this->tId, $this->item);
		}
	}
}
