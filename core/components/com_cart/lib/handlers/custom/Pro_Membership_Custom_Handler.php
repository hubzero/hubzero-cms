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

class Pro_Membership_Custom_Handler extends Custom_Handler
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
		// Get user ID for the cart
		require_once(dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php');
		$userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

		// Get number of points to add
		if (!empty($this->item['meta']['addPoints']) && is_numeric($this->item['meta']['addPoints']))
		{
			// Update points account
			$db = \App::get('db');
			$BTL = new \Hubzero\Bank\Teller($db, $userId);
			$BTL->deposit($this->item['meta']['addPoints'], 'PRO Membership Bonus', 'PRO', $this->item['info']->sId);
		}
	}
}
