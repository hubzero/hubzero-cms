<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Models;

use Components\Storefront\Models\Product;
use Hubzero\Base\Model;
use Lang;
use Components\Storefront\Models\Warehouse;
use Components\Cart\Helpers\CartHelper;
use Components\Cart\Helpers\Audit;

require_once dirname(__DIR__) . DS. 'helpers' . DS . 'Helper.php';
require_once dirname(__DIR__) . DS. 'helpers' . DS . 'Audit.php';
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Core shopping cart
 */
abstract class Cart
{
	/**
	 * Database instance
	 *
	 * @var  object
	 */
	var $db = null;

	/**
	 * Cart ID
	 *
	 * @var  int
	 */
	var $crtId = null;

	/**
	 * Debug mode
	 *
	 * @var  bool
	 */
	var $debug = false;

	/**
	 * Salt
	 *
	 * @var  string
	 */
	protected static $securitySalt = 'ERDCVcvk$sad!ccsso====++!w';

	/**
	 * Cart constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Initialize DB
		$this->_db = \App::get('db');

		// Load language file
		\App::get('language')->load('com_cart');

		$this->warehouse = new Warehouse();
	}

	/**
	 * Add SKU to cart
	 *
	 * @param   int   $sId  SKU ID
	 * @param   int   $qty  Quantity
	 * @return  void
	 */
	public function add($sId, $qty = 1)
	{
		$this->doItem($sId, 'add', $qty);
	}

	/**
	 * Update/set SKU in cart
	 *
	 * @param   int   $sId             SKU ID
	 * @param   int   $qty             Quantity
	 * @param   bool  $retainOldValue  Retain old value
	 * @return  void
	 */
	public function update($sId, $qty = 1, $retainOldValue = false)
	{
		$this->doItem($sId, 'set', $qty, $retainOldValue);
	}

	/**
	 * Delete SKU from cart
	 *
	 * @param   int   $sId  SKU ID
	 * @return  void
	 */
	public function delete($sId)
	{
		// Not sure if it is still in use
		throw new \Exception('I thought it was not in use anymore...');
		$this->doItem($sId, 'set', 0);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Gets all saved shipping addresses for this user
	 *
	 * @param   int    $uId  Currently logged in user ID
	 * @return  mixed
	 */
	public function getSavedShippingAddresses($uId)
	{
		if (!CartHelper::isNonNegativeInt($uId, false))
		{
			throw new \Exception(JGLOBAL_AUTH_USER_NOT_FOUND);
		}

		// Get all user addresses
		$sql = 'SELECT * FROM `#__cart_saved_addresses` WHERE `uidNumber` = ' . $this->_db->quote($uId);
		$this->_db->setQuery($sql);
		$shippingAddresses = $this->_db->loadObjectList();

		if (empty($shippingAddresses))
		{
			return false;
		}

		return $shippingAddresses;
	}

	/**
	 * Gets transactions for a cart
	 *
	 * @param   array  $filters
	 * @param   bool   $completedOnly
	 * @return  mixed
	 */
	public function getTransactions($filters = array(), $completedOnly = true)
	{
		$filters['crtId'] = $this->crtId;
		return self::getAllTransactions($filters, $completedOnly);
	}

	/**
	 * Get all transactions
	 *
	 * @param   array  $filters
	 * @param   bool   $completedOnly
	 * @return  mixed
	 */
	public static function getAllTransactions($filters = array(), $completedOnly = true)
	{
		$db = \App::get('db');

		// Get info
		$sql = "SELECT DISTINCT ";
		if ((!empty($filters['userInfo']) && $filters['userInfo']) || (isset($filters['search']) && $filters['search']))
		{
			$sql .= " x.`id` AS uidNumber, x.`name`, crt.`crtId`, ";
		}
		if (!empty($filters['report-notes']) && $filters['report-notes'])
		{
			$sql .= " ti.`tiNotes`, ";
		}
		$sql .= "t.`tId`, `tLastUpdated`, `tStatus`, ti.`tiPayment` FROM `#__cart_transactions` t";
		if ((!empty($filters['userInfo']) && $filters['userInfo']) || (isset($filters['search']) && $filters['search']))
		{
			$sql .= " LEFT JOIN `#__cart_carts` crt ON (crt.`crtId` = t.`crtId`)";
			$sql .= ' LEFT JOIN `#__users` x ON (crt.`uidNumber` = x.`id`)';
		}
		$sql .= " LEFT JOIN `#__cart_transaction_info` ti ON (ti.`tId` = t.`tId`)";

		$where = array();

		if (isset($filters['search']) && $filters['search'])
		{
			$sql .= " LEFT JOIN `#__cart_transaction_items` tis ON (t.tId = tis.tId)";
			$sql .= " LEFT JOIN `#__storefront_skus` sku on (sku.sId = tis.sId)";
			$sql .= " LEFT JOIN `#__storefront_products` p on (sku.pId = p.pId)";

			$where[] = "(
				x.`name` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR x.`username` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR sku.`sSku` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR p.`pName` LIKE " . $db->quote('%' . $filters['search'] . '%') . "
				OR t.`tId` = " . $db->quote($filters['search']) . "
			)";
		}

		if (!empty($filters['uidNumber']) && $filters['uidNumber'])
		{
			$where[] = "crt.`uidNumber` = " . intval($filters['uidNumber']);
		}

		if (!empty($filters['crtId']) && $filters['crtId'])
		{
			$where[] = "t.`crtId` = {$filters['crtId']}";
		}

		if (!empty($filters['report-notes']) && $filters['report-notes'])
		{
			$where[] = "(ti.`tiNotes` IS NOT NULL AND ti.`tiNotes` != '')";
		}

		if ($completedOnly)
		{
			$where[] = "t.`tStatus` = 'completed'";
		}

		if (isset($filters['report-from']) && strtotime($filters['report-from']))
		{
			$showFrom = date("Y-m-d", strtotime($filters['report-from']));
			$where[] = "t.`tLastUpdated` >= " . $db->quote($showFrom);
		}

		if (isset($filters['report-to']) && strtotime($filters['report-to']))
		{
			// Add one day to include all the records of the end day
			$showTo = strtotime($filters['report-to'] . ' +1 day');
			$showTo = date("Y-m-d 00:00:00", $showTo);
			$where[] = "t.`tLastUpdated` <= " . $db->quote($showTo);
		}

		if (count($where))
		{
			$sql .= " WHERE " . implode(" AND ", $where) . " ";
		}

		if (isset($filters['sort']) && (empty($filters['count']) || !$filters['count']))
		{
			$sql .= " ORDER BY " . $filters['sort'];

			if (isset($filters['sort_Dir']))
			{
				$sql .= ' ' . $filters['sort_Dir'];
			}
		}
		else {
			$sql .= " ORDER BY `tLastUpdated` DESC";
		}

		if (isset($filters['limit']) && isset($filters['start']) && (empty($filters['count']) || !$filters['count']))
		{
			$sql .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		$db->setQuery($sql);
		//print_r($db->toString()); die;
		$db->query();

		$totalRows = $db->getNumRows();

		if (!empty($filters['count']) && $filters['count'])
		{
			return $totalRows;
		}

		if (!$totalRows)
		{
			return false;
		}

		if (isset($filters['returnFormat']) && $filters['returnFormat'] == 'array')
		{
			return $db->loadAssocList();
		}
		else
		{
			$transactions = $db->loadObjectList();
		}
		return $transactions;
	}

	/**
	 * Get cart items from the database
	 *
	 * @return  object  Object with two elements: array of SKU info in the cart and array of SKU IDs in the cart
	 */
	protected function getCartItems()
	{
		if ($this->debug)
		{
			echo '<br>Getting items from DB';
		}

		$sql = "SELECT `sId`, `crtiQty`, `crtiPrice`, `crtiOldPrice` FROM `#__cart_cart_items` crti WHERE crti.`crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		// Get all info
		$allSkuInfo = $this->_db->loadObjectList('sId');
		// Get just sku IDs
		$skus = $this->_db->loadColumn();

		$items = new \stdClass();
		$items->allSkuInfo = $allSkuInfo;
		$items->skus = $skus;
		return $items;
	}

	/**
	 * Get a single item in the cart
	 *
	 * @param   int    $sId  SKU ID
	 * @return  mixed  SKU cart info
	 */
	protected function getCartItem($sId)
	{
		$sql = 'SELECT `crtiQty` FROM `#__cart_cart_items`
				WHERE `sId` = ' . $this->_db->quote($sId) . ' AND `crtId` = ' . $this->_db->quote($this->crtId);
		$this->_db->setQuery($sql);
		$skuCartInfo = $this->_db->loadObject();

		if ($skuCartInfo)
		{
			return $skuCartInfo;
		}
		return false;
	}

	/**
	 * Check if cart is linked to any member's ID
	 *
	 * @param   int   $crtId  Cart ID
	 * @return  bool
	 */
	protected function cartIsLinked($crtId)
	{
		$sql = "SELECT COUNT(`crtId`) FROM `#__cart_carts` WHERE `crtId` = {$crtId} AND `uidNumber` IS NOT NULL";
		$this->_db->setQuery($sql);
		$isLinked = $this->_db->loadResult();

		if ($isLinked)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if user's cart exists and return its ID
	 * @param   int    $uId  User ID
	 * @return  mixed  Cart ID, false if no user cart exists
	 */
	protected function getUserCartId($uId)
	{
		$sql = "SELECT `crtId` FROM `#__cart_carts` WHERE `uidNumber` = '{$uId}'";
		$this->_db->setQuery($sql);
		$crtId = $this->_db->loadResult();

		if (!empty($crtId))
		{
			return $crtId;
		}
		return false;
	}

	/**
	 * Check if the cart exists.
	 *
	 * @return  bool
	 */
	protected function exists()
	{
		$sql = 'SELECT `crtId` FROM `#__cart_carts` WHERE `crtId` = ' . $this->_db->quote($this->crtId);
		$this->_db->setQuery($sql);
		$crtId = $this->_db->loadResult();

		if (!empty($crtId))
		{
			return true;
		}
		return false;
	}

	/**
	 * Update cart SKU, set as unavailable (if SKU or product get deleted or become unavailable)
	 *
	 * @param   int   $sId  SKU ID
	 * @return  void
	 */
	protected function markItemUnavailable($sId)
	{
		$sql = "UPDATE `#__cart_cart_items` SET `crtiAvailable` = 0
				WHERE `crtId` = {$this->crtId} AND `sId` = " . $this->_db->quote($sId);
		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Update/add SKU/quantity to cart, update the price in the cart, save old price and inventory level (if requested)
	 *
	 * @param   int       SKU ID
	 * @param   string    Update Method:  add - adds to the existing quantity,
	 *                                  set - ignores existing quantity and sets a new value,
	 *                                  sync - simply checks/updates inventory and pricing
	 * @param   int       Quantity
	 * @param   bool      Flag determining whether the old qty should be saved (only when it goes down);
	 *                    price get saved in either case
	 * @return  void
	 */
	protected function doItem($sId, $mode = 'add', $qty = 1, $retainOldValue = false)
	{
		// Check quantity: must be a positive integer or zero
		if (!CartHelper::isNonNegativeInt($qty))
		{
			//throw new \Exception(Lang::txt('COM_CART_INCORRECT_QTY'));
			throw new \Exception('Product quantity is incorrect');
		}
		elseif ($qty == 0 && !$retainOldValue)
		{
			// Delete if quantity is set to zero
			if ($mode == 'set')
			{
				$this->deleteItem($sId);
				return;
			}
			else
			{
				//throw new \Exception(Lang::txt('COM_CART_INCORRECT_QTY'));
				throw new \Exception('Product quantity is incorrect');
			}
		}

		// Check if there is enough inventory (if tracking inventory) taking into account current quantity in the cart

		// Get the quantity already in the cart (if appending or simply syncing)
		if ($mode == 'add' || $mode == 'sync')
		{
			$skuCartInfo = $this->getCartItem($sId);
		}
		// If setting, ignore the current cart value
		else
		{
			$skuCartInfo = new \stdClass();
			$skuCartInfo->crtiQty = 0;
		}

		// Get SKU pricing and inventory level & policies as well as permissions to access products
		$warehouse = $this->warehouse;
		try
		{
			$allSkuInfo = $warehouse->getSkuInfo($sId, false);
		}
		catch (\Exception $e)
		{
			throw new \Exception(Lang::txt($e->getMessage()));
		}

		if (empty($allSkuInfo))
		{
			Lang::load('com_storefront', Component::path('com_storefront') . DS . 'site');
			//throw new \Exception(Lang::txt('COM_STOREFRONT_SKU_NOT_FOUND'));
			throw new \Exception('Requested product could not be found. Please check your selection');
		}

		$skuInfo = $allSkuInfo['info'];
		$skuName = $skuInfo->pName;
		if (!empty($allSkuInfo['options']) && count($allSkuInfo['options']))
		{
			foreach ($allSkuInfo['options'] as $oName)
			{
				$skuName .= ', ' . $oName;
			}
		}

		// Check inventory rules (sync mode doesn't check inventory level, just pricing)
		if ($mode != 'sync')
		{
			// Don't allow purchasing multiple products (same & different SKUs) for those that are not allowed
			if (!$skuInfo->pAllowMultiple)
			{
				// Check this SKU qty to make sure no multiple SKUs are there
				if ((!empty($skuCartInfo->crtiQty) && $skuCartInfo->crtiQty > 0) || ($qty > 1))
				{
					//throw new \Exception($skuInfo->pName . Lang::txt('COM_CART_NO_MULTIPLE_ITEMS'));
					throw new \Exception($skuInfo->pName . " is already in the cart and cannot be added multiple times");
				}
				// Check if there is this product already in the cart (different SKU)
				$allSkus = $warehouse->getProductSkus($skuInfo->pId);
				if (is_array($allSkus) || is_object($allSkus))
				{
					foreach ($allSkus as $skuId)
					{
						// Skip the current SKU, look only at other SKUs
						if ($skuId != $sId)
						{
							$otherSkuInfo = $this->getCartItem($skuId);
							// Error if there is already another SKU of the same product in the cart
							if (!empty($otherSkuInfo->crtiQty) && $otherSkuInfo->crtiQty > 0)
							{
								//throw new \Exception($skuInfo->pName . Lang::txt('COM_CART_NO_MULTIPLE_ITEMS'));
								throw new \Exception($skuInfo->pName . " is already in the cart and cannot be added multiple times");
							}
						}

					}
				}
			}
			// Don't allow purchasing multiple SKUs for those that are not allowed
			if (!$skuInfo->sAllowMultiple && ((!empty($skuCartInfo->crtiQty) && $skuCartInfo->crtiQty > 0) || ($qty > 1)))
			{
				//throw new \Exception($skuName . Lang::txt('COM_CART_NO_MULTIPLE_ITEMS'));
				throw new \Exception($skuName . " is already in the cart and cannot be added multiple times");
			}

			// Make sure there is enough inventory
			if ($skuInfo->sTrackInventory)
			{
				// See if qty can be added
				if ($qty > $skuInfo->sInventory)
				{
					//throw new \Exception(Lang::txt('COM_CART_NOT_ENOUGH_INVENTORY'));
					throw new \Exception('You are trying to add too many products to your cart. We do not have enough inventory.');
				}
				elseif (!empty($skuCartInfo->crtiQty) && ($qty + $skuCartInfo->crtiQty > $skuInfo->sInventory))
				{
					// This is how much they can add: $skuInfo->sInventory - $skuCartInfo->crtiQty
					//throw new \Exception(Lang::txt('COM_CART_ADD_TOO_MANY_CART'));
					throw new \Exception('You are trying to add too many products to your cart. You already have this product in your cart.');
				}
			}
		}

		// Run the auditor
		if ($mode != 'sync')
		{
			$auditor = Audit::getAuditor($skuInfo, $this->crtId);
			$auditor->setSku($skuInfo->sId);

			$auditorResponse = $auditor->audit();

			if ($auditorResponse->status == 'error')
			{
				throw new \Exception($skuInfo->pName . ', ' . $skuInfo->sSku . $auditor->getResponseError());
			}
		}

		// Insert new values, if exists save the previous price (for possible price changes messaging)
		// and old inventory level (if needed)
		$sql = "INSERT INTO `#__cart_cart_items`
				(`crtId`, `sId`, `crtiQty`, `crtiOldQty`, `crtiPrice`, `crtiOldPrice`, `crtiName`)
				VALUES
				({$this->crtId}, '{$sId}', {$qty}, NULL, {$skuInfo->sPrice}, NULL, " . $this->_db->quote($skuName) . ")
				ON DUPLICATE KEY UPDATE `crtiOldPrice` = `crtiPrice`, `crtiPrice` = {$skuInfo->sPrice}, `crtiName` = " . $this->_db->quote($skuName);

		// Check if old value has to be retained
		if ($retainOldValue)
		{
			$sql .= ", `crtiOldQty` = `crtiQty`";
		}
		else
		{
			$sql .= ", `crtiOldQty` = NULL";
		}

		// add to the existing qty value
		if ($mode == 'add')
		{
			$sql .= ", `crtiQty` = `crtiQty` + {$qty}";
		}
		// set new qty value
		elseif ($mode == 'set')
		{
			$sql .= ", `crtiQty` = {$qty}";
		}
		// keep the qty value if syncing

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Delete SKU from cart
	 *
	 * @param   int   $sId  SKU ID
	 * @return  void
	 */
	private function deleteItem($sId)
	{
		// delete cart item
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `sId` = '{$sId}' AND `crtId` = {$this->crtId}";

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/********************************* Static functions *********************************/

	/**
	 * Generate security token
	 *
	 * @param   int     $tId    Transaction ID
	 * @return  string
	 */
	public static function generateSecurityToken($tId)
	{
		if (!CartHelper::isNonNegativeInt($tId, false))
		{
			throw new \Exception(Lang::txt('COM_CART_NO_TRANSACTION_FOUND'));
		}
		return md5(self::$securitySalt . $tId);
	}

	/**
	 * Verify security token
	 *
	 * @param   string  $token  string token
	 * @param   int     $tId    Transaction ID
	 * @return  bool
	 */
	public static function verifySecurityToken($token, $tId)
	{
		if (!CartHelper::isNonNegativeInt($tId, false))
		{
			throw new \Exception(Lang::txt('COM_CART_NO_TRANSACTION_FOUND'));
		}
		return md5(self::$securitySalt . $tId) == $token;
	}

	/**
	 * Get user ID associated with the provided cart ID
	 *
	 * @param   int  $crtId   cart ID
	 * @return  int  user ID, false if no cart found
	 */
	public static function getCartUser($crtId)
	{
		$db = \App::get('db');

		$sql = 'SELECT `uidNumber` AS uId FROM `#__cart_carts` WHERE `crtId` = ' . $db->quote($crtId);
		$db->setQuery($sql);
		$uId = $db->loadResult();

		if (!empty($uId))
		{
			return $uId;
		}
		return false;
	}

	/**
	 * Remove given quantity of SKU from cart
	 *
	 * @param   int   $sId    SKU ID
	 * @param   int   $qty    Qty
	 * @param   int   $crtId  Cart ID
	 * @return  void
	 */
	protected static function removeItem($sId, $qty, $crtId)
	{
		$db = \App::get('db');

		$sql = "UPDATE `#__cart_cart_items` SET `crtiQty` = `crtiQty` - {$qty} WHERE `sId` = '{$sId}' AND `crtId` = {$crtId}";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Delete cart and all cart items
	 *
	 * @param   int   $crtId  Cart ID
	 * @return  void
	 */
	protected static function kill($crtId)
	{
		$db = \App::get('db');

		// delete cart items
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `crtId` = {$crtId}";
		$db->setQuery($sql);
		$db->query();

		// delete cart coupons
		$sql = "DELETE FROM `#__cart_coupons` WHERE `crtId` = {$crtId}";
		$db->setQuery($sql);
		$db->query();

		// delete cart memberships
		$sql = "DELETE FROM `#__cart_memberships` WHERE `crtId` = {$crtId}";
		$db->setQuery($sql);
		$db->query();

		// delete the cart
		$sql = "DELETE FROM `#__cart_carts` WHERE `crtId` = {$crtId}";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Get main transaction facts (total, other verification info)
	 *
	 * @param   int    $tId
	 * @return  array  List of items in the transaction or false on failed attempt
	 */
	public static function getTransactionFacts($tId)
	{
		if (!is_numeric($tId))
		{
			return false;
		}

		$items = self::getTransactionItems($tId);
		if (!$items)
		{
			return false;
		}

		// Can be purchased -- get transaction items
		$transaction = new \stdClass();
		$transaction->items = $items;

		$tInfo = self::getTransactionInfo($tId);

		$transaction->info = $tInfo;

		return $transaction;
	}

	/**
	 * Get all items in the transaction
	 *
	 * @param   int    $tId  transaction ID
	 * @param   bool   $verifySkuInfo  a flag wheretr the sku info should be verified for availability
	 * @return  mixed  List of items in the transaction, false if no items in transaction
	 */
	public static function getTransactionItems($tId, $verifySkuInfo = true, $returnSimpleInfo = false)
	{
		$db = \App::get('db');

		$sql = "SELECT `sId`, `tiQty`, `tiPrice`, `tiMeta` FROM `#__cart_transaction_items` ti WHERE ti.`tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		if (!$db->getNumRows())
		{
			return false;
		}

		$allSkuInfo = $db->loadObjectList('sId');
		if ($returnSimpleInfo)
		{
			return $allSkuInfo;
		}

		$skus = $db->loadColumn();

		$warehouse = new Warehouse();

		$skuInfo = $warehouse->getSkusInfo($skus);
		if (empty($skuInfo))
		{
			if (!$verifySkuInfo)
			{
				foreach ($allSkuInfo as $sId => $skuInfo)
				{
					$info = array();
					$info['info'] = new \stdClass();
					$info['meta'] = false;

					$transactionInfo = new \stdClass();
					$transactionInfo->qty = $skuInfo->tiQty;
					$transactionInfo->tiPrice = $skuInfo->tiPrice;
					$transactionInfo->tiMeta = json_decode($skuInfo->tiMeta);
					$info['transactionInfo'] = $transactionInfo;
					$allSkuInfo[$sId] = $info;
				}
				return $allSkuInfo;
			}
			return false;
		}

		// Update skuInfo with transaction info
		foreach ($skuInfo as $sId => $sku)
		{
			$transactionInfo = new \stdClass();
			$transactionInfo->qty = $allSkuInfo[$sId]->tiQty;
			$transactionInfo->tiPrice = $allSkuInfo[$sId]->tiPrice;
			$transactionInfo->tiMeta = json_decode($allSkuInfo[$sId]->tiMeta);
			$skuInfo[$sId]['transactionInfo'] = $transactionInfo;
			unset($transactionInfo);
		}

		return $skuInfo;
	}

	/**
	 * Gets all transaction related info
	 *
	 * @param   int    $tId  Transaction ID
	 * @return  mixed  False on no results
	 */
	public static function getTransactionInfo($tId)
	{
		$db = \App::get('db');

		// Get info
		$sql = 'SELECT t.*, TIMESTAMPDIFF(MINUTE, t.`tLastUpdated`, NOW()) AS tAge, ti.*
				FROM `#__cart_transactions` t LEFT JOIN `#__cart_transaction_info` ti ON t.`tId` = ti.`tId`
				WHERE t.`tId` = ' . $db->quote($tId);
		$db->setQuery($sql);
		$db->query();

		if (!$db->getNumRows())
		{
			return false;
		}

		$transactionInfo = $db->loadObject();
		return $transactionInfo;
	}

	/**
	 * Complete the transaction, mark it as completed, done, success...
	 *
	 * @param   object  $tInfo  Transaction info
	 * @return  void
	 */
	public static function completeTransaction($tInfo, $paymentInfo = false)
	{
		$tId = $tInfo->info->tId;
		$crtId = $tInfo->info->crtId;

		// Extract transaction items
		$transactionItems = unserialize($tInfo->info->tiItems);

		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'ProductHandler.php';

		// Handle each item in the transaction
		foreach ($transactionItems as $sId => $item)
		{
			$productHandler = new \Components\Cart\Helpers\CartProductHandler($item, $crtId, $tId);
			$productHandler->handle();
		}

		// Mark transaction as completed
		self::updateTransactionStatus('completed', $tId);

		// Remove items from cart
		self::removeTransactionItemsFromCart($tInfo);

		// remove coupons from cart
		self::removeTransactionCouponsFromCart($tInfo);

		// Save payment info
		if (!empty($paymentInfo) && is_array($paymentInfo))
		{
			self::saveTransactionPaymentInfo($paymentInfo, $tId);
		}

		// Clean up cart
		$db = \App::get('db');

		// Delete zero and negative qty items in the cart
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `crtiQty` <= 0 AND `crtId` = {$tInfo->info->crtId}";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Set a single transaction item info
	 *
	 * @param   int    $tId   Transaction ID
	 * @param   array  $item
	 * @return  bool
	 */
	public static function updateTransactionItem($tId, $item)
	{
		$tInfo = self::getTransactionInfo($tId);
		$tItems = unserialize($tInfo->tiItems);

		$sId = $item['info']->sId;

		// Find the existing item in the transaction
		if (empty($tItems[$sId]))
		{
			throw new \Exception('Missing transaction item.');
		}

		$tItems[$sId] = $item;

		self::setTransactionItems($tId, $tItems);
		return true;
	}

	/**
	 * Set transaction items (the initial transaction at #__cart_transaction_info)
	 *
	 * @param   int  $tId    Transaction ID
	 * @param   obj  $items  Items
	 * @return  bool
	 */
	private static function setTransactionItems($tId, $items)
	{
		$db = \App::get('db');

		$sql = "UPDATE `#__cart_transaction_info` SET `tiItems` = " . $db->quote(serialize($items)) . " WHERE `tId` = " . $db->quote($tId);

		$db->setQuery($sql);
		$db->query();

		$affectedRows = $db->getAffectedRows();

		if (!$affectedRows)
		{
			return false;
		}
		return true;
	}

	/**
	 * Update the existing items in the transaction (#__cart_transaction_items). Update can be partial retaining the rest of the info.
	 *
	 * @param   int    $tId  transaction ID
	 * @param   obect  $tiInfo  transaction items info
	 * @param   bool   $returnChanges flag whether the changes should be recorded and returned
	 * @return  mixed  bool if $returnChanges is set to false, array if $returnChanges is set to true
	 */
	public static function updateTransactionItems($tId, $tiInfo, $returnChanges = true)
	{
		$db = \App::get('db');

		// Get the current transaction items simple info to properly handle the meta
		$transactionItems = self::getTransactionItems($tId, false, true);

		foreach ($transactionItems as $transactionItem)
		{
			$transactionItem->tiMeta = json_decode($transactionItem->tiMeta);
		}

		// We can check what changes have been made here and return them

		if ($returnChanges)
		{
			$transactionItemsChanges = array();
		}

		foreach ($tiInfo as $sId => $sInfo)
		{
			$setSql = array();
			foreach ($sInfo as $key => $val)
			{
				// Handle each update, except for meta
				if ($key != 'meta')
				{
					$setSql[] = '`' . $key . '` = ' . $val;

					// note the changes
					if ($returnChanges && $transactionItems[$sId]->$key != $val)
					{
						$transactionItemsChanges[] = array('object' => 'cart_transaction_item', 'sId' => $sId, 'key' => $key, 'old' => $transactionItems[$sId]->$key, 'new' => $val);
					}
				}
				else
				{
					// get the current object to save the other values that are not being saved
					$currentMetaObj = array();
					if ($transactionItems[$sId]->tiMeta)
					{
						$currentMetaObj = $transactionItems[$sId]->tiMeta;
					}

					// update the current meta with the submitted values
					foreach ($val as $k => $v)
					{
						// note the changes
						if ($returnChanges && $currentMetaObj->$k != $v)
						{
							$transactionItemsChanges[] = array('object' => 'cart_transaction_item', 'sId' => $sId, 'key' => array('tiMeta' => $k), 'old' => $currentMetaObj->$k, 'new' => $v);
						}

						$currentMetaObj->$k = $v;
					}

					$setSql[] = '`tiMeta` = ' . $db->quote(json_encode($currentMetaObj));
				}
			}

			$setSql = (implode(', ', $setSql));

			$sql = "UPDATE `#__cart_transaction_items` SET " . $setSql . " WHERE `tId` = " . $db->quote($tId) . " AND `sId` = " . $db->quote($sId);
			$db->setQuery($sql);
			$db->query();
		}

		if ($returnChanges)
		{
			return $transactionItemsChanges;
		}

		return true;
	}

	/**
	 * Update transaction status
	 *
	 * @param   string  $status
	 * @param   int     $tId     Transaction ID
	 * @return  bool    Success or failure
	 */
	public static function updateTransactionStatus($status, $tId)
	{
		$db = \App::get('db');

		$sql = "UPDATE `#__cart_transactions` SET `tStatus` = '{$status}' WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		$affectedRows = $db->getAffectedRows();

		if (!$affectedRows)
		{
			return false;
		}
		return true;
	}

	/**
	 * Update transaction info
	 *
	 * @param   int     $tId     Transaction ID
	 * @param   array   $tInfo		Transaction info
	 * @param   bool   $returnChanges flag whether the changes should be recorded and returned
	 * @return  mixed  bool if $returnChanges is set to false, array if $returnChanges is set to true
	 */
	public static function updateTransactionInfo($tId, $tInfo, $returnChanges = true)
	{
		$db = \App::get('db');

		if ($returnChanges)
		{
			// get transaction info to check the changes against
			$currerntTransactionInfo = self::getTransactionInfo($tId);
			$transactionChanges = array();
		}

		$setSql = array();
		foreach ($tInfo as $key => $val)
		{
			$setSql[] = '`' . $key . '` = ' . $db->quote($val);

			// note the changes
			if ($returnChanges && $currerntTransactionInfo->$key != $val)
			{
				$transactionChanges[] = array('object' => 'cart_transaction_info', 'tId' => $tId, 'key' => $key, 'old' => $currerntTransactionInfo->$key, 'new' => $val);
			}
		}

		$setSql = implode(', ', $setSql);

		$sql = "UPDATE `#__cart_transaction_info` SET " . $setSql . " WHERE `tId` = " . $db->quote($tId);
		$db->setQuery($sql);
		$db->query();

		// Need to recalculate the transaction total

		// Subtotal
		$transactionItems = self::getTransactionItems($tId);
		$subtotal = 0;

		foreach ($transactionItems as $transactionItem)
		{
			$transactionItemInfo = $transactionItem['transactionInfo'];
			$subtotal += $transactionItemInfo->qty * $transactionItemInfo->tiPrice;
		}

		$tiTotal = $subtotal + $currerntTransactionInfo->tiTax + $currerntTransactionInfo->tiShipping - $currerntTransactionInfo->tiShippingDiscount - $currerntTransactionInfo->tiDiscounts;

		$sql = "UPDATE `#__cart_transaction_info` SET
				`tiTotal` = " . $db->quote($tiTotal) . ",
				`tiSubtotal` = " . $db->quote($subtotal) . "
				WHERE `tId` = " . $db->quote($tId);

		$db->setQuery($sql);
		$db->query();

		if ($returnChanges)
		{
			return $transactionChanges;
		}

		$affectedRows = $db->getAffectedRows();

		if (!$affectedRows)
		{
			return false;
		}

		return true;
	}

	/**
	 * Save transaction payment info
	 *
	 * @param   array  $paymentInfo
	 * @param   int     $tId     Transaction ID
	 * @return  bool    Success or failure
	 */
	public static function saveTransactionPaymentInfo($paymentInfo, $tId)
	{
		$db = \App::get('db');

		$sql = "UPDATE `#__cart_transaction_info` SET `tiPayment` = '{$paymentInfo[0]}', `tiPaymentDetails` = '{$paymentInfo[1]}' WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		$affectedRows = $db->getAffectedRows();

		if (!$affectedRows)
		{
			return false;
		}
		return true;
	}

	/**
	 * Remove transaction items from the cart associated with it
	 *
	 * @param   object  $tInfo  transaction info
	 * @return  void
	 */
	private static function removeTransactionItemsFromCart($tInfo)
	{
		// remove each item from the cart
		foreach ($tInfo->items as $sId => $item)
		{
			self::removeItem($sId, $item['transactionInfo']->qty, $tInfo->info->crtId);
		}
	}

	/**
	 * Remove transaction coupons from the cart associated with it
	 *
	 * @param   object  $tInfo  transaction info
	 * @return  bool
	 */
	private static function removeTransactionCouponsFromCart($tInfo)
	{
		$perks = $tInfo->info->tiPerks;

		if (empty($perks))
		{
			return true;
		}

		$perks = unserialize($perks);

		// remove each coupon from the cart
		$couponIds = array();
		foreach ($perks as $k => $val)
		{
			if ($k != 'info')
			{
				if (is_array($val))
				{
					foreach ($val as $coupon)
					{
						$couponIds[] = $coupon->couponId;
					}
				}
				else
				{
					$couponIds[] = $val->couponId;
				}
			}
		}

		$db = \App::get('db');

		$sqlCoupons = '0';
		foreach ($couponIds as $cnId)
		{
			$sqlCoupons .= ' OR `cnId` = ' . $db->quote($cnId);
		}

		$sql = "UPDATE `#__cart_coupons` SET `crtCnStatus` = 'applied'
				WHERE ({$sqlCoupons}) AND `crtId` = {$tInfo->info->crtId}";
		$db->setQuery($sql);
		$db->query();

		return true;
	}

	/**
	 * Handle the error processing the transaction
	 *
	 * @param   int     $tId    transaction ID
	 * @param   object  $error
	 * @return  void
	 */
	public static function handleTransactionError($tId, $error)
	{
		// Release transaction items back to inventory
		self::releaseTransaction($tId);

		// Update status to 'error processing'
		self::updateTransactionStatus('error processing', $tId);
	}

	/**
	 * Releases locked transaction items back to inventory and marks the transaction status as 'released'
	 *
	 * @param   int   $tId  Transaction ID
	 * @return  void
	 */
	public static function releaseTransaction($tId)
	{
		$db = \App::get('db');

		// Check if the transaction can be released (status is pending)
		// Get info
		$sql = "SELECT t.`tStatus` FROM `#__cart_transactions` t WHERE t.`tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		if (!$db->getNumRows())
		{
			return false;
		}

		// Get transaction items
		$tItems = self::getTransactionItems($tId);

		// Go through each item and release the quantity back to inventory if needed
		$warehouse = new Warehouse();

		if (!empty($tItems))
		{
			require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Sku.php';

			foreach ($tItems as $sId => $itemInfo)
			{
				$qty = $itemInfo['transactionInfo']->qty;
				$sku = \Components\Storefront\Models\Sku::getInstance($sId);
				$sku->releaseInventory($qty);
			}
		}
		// update status
		self::updateTransactionStatus('released', $tId);
	}

	/**
	 * Kill transaction
	 *
	 * @param   int   $tId  transaction ID to kill
	 * @return  void
	 */
	protected static function killTransaction($tId)
	{
		$db = \App::get('db');

		$sql = "DELETE FROM `#__cart_transactions` WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		$sql = "DELETE FROM `#__cart_transaction_items` WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		$sql = "DELETE FROM `#__cart_transaction_info` WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();

		$sql = "DELETE FROM `#__cart_transaction_steps` WHERE `tId` = {$tId}";
		$db->setQuery($sql);
		$db->query();
	}

	/**
	 * Kill all expired transactions
	 *
	 * @return  void
	 */
	public static function killExpiredTransactions()
	{
		$db = \App::get('db');
		$params =  Component::params('com_cart');
		$transactionTTL = ($params->get('transactionTTL', 120));

		$sql = "SELECT t.tId
				FROM `#__cart_transactions` t
				WHERE t.`tStatus` = 'pending' OR t.`tStatus` = 'released' AND TIMESTAMPDIFF(MINUTE, t.`tLastUpdated`, NOW()) > {$transactionTTL}";

		$db->setQuery($sql);
		$db->query();
		$tIds = $db->loadColumn();

		foreach ($tIds as $tId)
		{
			self::releaseTransaction($tId);
			self::killTransaction($tId);
		}
	}
}
