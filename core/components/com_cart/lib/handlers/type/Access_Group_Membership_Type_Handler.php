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
		include_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
		$ms = new StorefrontModelMemberships();

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
			require_once(PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');
			$userId = CartModelCart::getCartUser($this->crtId);

			// Get user group ID to set the user to (from meta)
			require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php');
			$userGId = StorefrontModelProduct::getMeta($this->item['info']->pId, 'userGroupId');

			$add = JUserHelper::addUserToGroup($userId, $userGId);
			if ($add instanceof Exception)
			{
				mail(Config::get('mailfrom'), 'Error adding to the group', $add->getMessage() . ' Cart #' . $this->crtId);
			}

			$table = JTable::getInstance('User', 'JTable', array());
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
