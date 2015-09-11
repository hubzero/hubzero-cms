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
 * @package   hubzero-cms
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once(PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'auditors' . DS . 'BaseAuditor.php');

class Membership_Auditor extends BaseAuditor
{

	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($type, $pId, $crtId)
	{
		parent::__construct($type, $pId, $crtId);
	}

	/**
	 * Main handler. Does all the checks
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function audit()
	{
		/* Membership may have a limit on when it can be extended */

		/* If no user, some checks may be skipped... */
		// Get user
		if (!User::isGuest())
		{
			// Check if there is a limitation on when the subscription can be extended
			require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Product.php');
			$subscriptionMaxLen = StorefrontModelProduct::getMeta($this->pId, 'subscriptionMaxLen');
			if ($subscriptionMaxLen)
			{
				/* Check if the current user has the existing subscription and how much is left on it
				 i.e. figure out if he may extend his current subscription */

				/*
				 *  This is not working very well for multiple SKUs with multiple subscriptionMaxLen's
				 *  at this point code doesn't know what SKU will be added,
				 *  so for one SKU subscriptionMaxLen should
				 *  be set to time less than actual membership length, ie if membership is sold for 1 year and
				 *  cannot be renewed more than 6 month before it expires the subscriptionMaxLen must be set to 6 MONTH
				 *  if it cannot be renewed more than 3 month before it expires the subscriptionMaxLen must be set to 3 MONTH
				 *
				 *  so subscriptionMaxLen = XX is actually "let renew XX time before expiration"
				 */

				// Get the proper product type subscription object reference
				require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
				$subscription = StorefrontModelMemberships::getSubscriptionObject($this->type, $this->pId, $this->uId);

				// Get the expiration for the current subscription (if any)
				$currentExpiration = $subscription->getExpiration();
				if ($currentExpiration && $currentExpiration['crtmActive'])
				{
					// Do the check
					$currentExpirationTime = $currentExpiration['crtmExpires'];
					// See if current expiration is later than max allowed time from now (max allowed time + now)
					if (strtotime('+' . $subscriptionMaxLen) < strtotime($currentExpirationTime))
					{
						// Expiration is not allowed -- the current expiration is too far in the future
						$this->setResponseStatus('error');
						$this->setResponseNotice('You already have an active subscription to this item. Subscription extension is not available at this time.');
						$this->setResponseError(': you already have an active subscription. Subscription extension is not available at this time.');
					}
				}

			}
		}

		return($this->getResponse());
	}
}