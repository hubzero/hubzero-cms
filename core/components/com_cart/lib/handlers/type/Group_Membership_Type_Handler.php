<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\User\Group;
require_once dirname(dirname(__DIR__)) . DS . 'cartmessenger' . DS . 'CartMessenger.php';

class Group_Membership_Type_Handler extends Type_Handler
{
	/**
	 * Constructor
	 *
	 * @param   object   $item
	 * @param   integer  $crtId
	 * @return  void
	 */
	public function __construct($item, $crtId)
	{
		parent::__construct($item, $crtId);
	}

	/**
	 * Handle
	 *
	 * @return  bool
	 */
	public function handle()
	{
		/* Add the user to the corresponding group (pull group ID from the meta) */
		try
		{
			// Get user ID for the cart
			require_once dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php';
			$userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);
			// Get the group ID to set the user to (from meta)
			require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Product.php';
			$groupIds = \Components\Storefront\Models\Product::getMetaValue($this->item['info']->pId, 'groupId');
			$groupIds = explode(',', $groupIds);

			foreach ($groupIds as $groupId) {
				$group = new Group();
				$group->read(trim($groupId));
				$group->add('members', array($userId));
				$group->update();
			}
		}
		catch (Exception $e)
		{
			$logger = new \CartMessenger('Group Membership Type Handler');
			$logger->setMessage('Group(s) assignment failed.');
			$logger->log(\LoggingLevel::ERROR);

			return false;
		}
	}
}
