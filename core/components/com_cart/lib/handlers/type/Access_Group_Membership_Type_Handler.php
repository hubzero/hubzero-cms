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

class Access_Group_Membership_Type_Handler extends Type_Handler
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
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php';
		$ms = new \Components\Storefront\Models\Memberships();

		/* NEW
		$subscription = StorefrontModelMemberships::getSubscriptionObject($this->type, $this->pId, $this->uId);
		// Get the expiration for the current subscription (if any)
		$currentExpiration = $subscription->getExpiration();
		*/

		// Get current registration
		$membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
		$expiration = $membership['crtmExpires'];

		/* Add the user to the corresponding user access group (pull access group ID from the meta) */
		try
		{
			// Get user ID for the cart
			require_once (dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php');
			$userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

			// Get the user group ID to set the user to (from meta)
			require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php';
			$userGId = \Components\Storefront\Models\Product::getMetaValue($this->item['info']->pId, 'userGroupId');

			$add = \JUserHelper::addUserToGroup($userId, $userGId);
			if ($add instanceof \Exception) {
				mail(Config::get('mailfrom'), 'Error adding to the group', $add->getMessage() . ' Cart #' . $this->crtId);
			}

			$table = \JTable::getInstance('User', 'JTable', array());
			$table->load($userId);

			// Trigger the onAftereStoreUser event
			Event::trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
		}
		catch (Exception $e)
		{
			// Error
			return false;
		}
	}
}
