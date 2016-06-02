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
		if ($itemMeta['serialManagement'] == 'multiple')
		{
			// Get the required number of serials
			$serialsNeeded = $itemCartInfo->qty;

			require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'helpers' . DS . 'Serials.php';
			// Get the serial numbers
			$serialNumbers = \Components\Storefront\Helpers\Serials::issueSerials($itemInfo->sId, $serialsNeeded);

			$this->item['meta']['serials'] = $serialNumbers;
			// Update the transaction items with serials
			require_once(dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php');
			\Components\Cart\Models\Cart::updateTransactionItem($this->tId, $this->item);
		}
	}
}