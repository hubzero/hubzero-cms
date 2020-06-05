<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Auditors;

use Components\Storefront\Models\Product;
use Components\Storefront\Models\Memberships;
use User;

require_once __DIR__ . DS . 'BaseAuditor.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Product.php';
require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Memberships.php';

class Membership_Auditor extends BaseAuditor
{
	/**
	 * Constructor
	 *
	 * @param   string   $type
	 * @param   integer  $pId
	 * @param   integer  $crtId
	 * @return  void
	 */
	public function __construct($type, $pId, $crtId)
	{
		parent::__construct($type, $pId, $crtId);
	}

	/**
	 * Main handler. Does all the checks
	 *
	 * @return  void
	 */
	public function audit()
	{
		// Membership may have a limit on when it can be extended

		// If no user, some checks may be skipped...
		// Get user
		$user = User::getInstance();
		if (!$user->get('guest'))
		{
			// Check if there is a limitation on when the subscription can be extended
			$subscriptionMaxLen = Product::getMetaValue($this->pId, 'subscriptionMaxLen');
			if ($subscriptionMaxLen)
			{
				// Check if the current user has the existing subscription and how much is left on it
				// i.e. figure out if he may extend his current subscription

				// This is not working very well for multiple SKUs with multiple subscriptionMaxLen's
				// at this point code doesn't know what SKU will be added,
				// so for one SKU subscriptionMaxLen should
				// be set to time less than actual membership length, ie if membership is sold for 1 year and
				// cannot be renewed more than 6 month before it expires the subscriptionMaxLen must be set to 6 MONTH
				// if it cannot be renewed more than 3 month before it expires the subscriptionMaxLen must be set to 3 MONTH
				// so subscriptionMaxLen = XX is actually "let renew XX time before expiration"

				// Get the proper product type subscription object reference
				$subscription = Memberships::getSubscriptionObject($this->type, $this->pId, $this->uId);

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

		return $this->getResponse();
	}
}
