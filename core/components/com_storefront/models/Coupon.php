<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

/**
 *
 * Coupon class
 *
 */
class Coupon
{
	// Database instance
	var $db = null;
	var $data;

	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($code = false)
	{
		// Load language file
		\App::get('language')->load('com_storefront');

		if ($code)
		{
			$this->setCode($code);
		}
	}

	/**
	 * Set code
	 *
	 * @param	string	coupon code
	 * @return	bool	true on success
	 */
	public function setCode($code)
	{
		$this->data->code = $code;
		return true;
	}

	public function getCode()
	{
		if (!empty($this->data->code))
		{
			return $this->data->code;
		}
		return false;
	}

	/**
	 * Set code description
	 *
	 * @param	string	coupon description
	 * @return	bool	true on success
	 */
	public function setDescription($description)
	{
		$this->data->description = $description;
		return true;
	}

	public function getDescription()
	{
		if (!empty($this->data->description))
		{
			return $this->data->description;
		}
		return false;
	}

	/**
	 * Set code use limit
	 *
	 * @param	int		use limit
	 * @return	bool	true on success
	 */
	public function setUseLimit($limit)
	{
		if (!is_numeric($limit) || $limit <= 0)
		{
			throw new \Exception(Lang::txt('Use limit must be a positive number'));
		}

		$this->data->useLimit = floor($limit);
		return true;
	}

	public function getUseLimit()
	{
		if (!empty($this->data->useLimit))
		{
			return $this->data->useLimit;
		}
		return 'DEFAULT';
	}

	/**
	 * Set the limit of objects coupon can be applied to
	 *
	 * @param	int		object limit
	 * @return	bool	true on success
	 */
	public function setObjectLimit($limit)
	{
		if (!is_numeric($limit) || $limit < 0)
		{
			throw new \Exception(Lang::txt('Use limit must be a non-negative number'));
		}

		$this->data->objectLimit = floor($limit);
		return true;
	}

	public function getObjectLimit()
	{
		if (!empty($this->data->objectLimit))
		{
			return $this->data->objectLimit;
		}
		return 'DEFAULT';
	}

	/**
	 * Set active status
	 *
	 * @param	bool		status
	 * @return	bool		true
	 */
	public function setActiveStatus($activeStatus)
	{
		if ($activeStatus)
		{
			$this->data->activeStatus = 1;
		}
		else
		{
			$this->data->activeStatus = 0;
		}
		return true;
	}

	/**
	 * Get active status
	 *
	 * @param	void
	 * @return	bool		status
	 */
	public function getActiveStatus()
	{
		if (!isset($this->data->activeStatus))
		{
			return 'DEFAULT';
		}
		return $this->data->activeStatus;
	}

	/**
	 * Set expiration date (if needed)
	 *
	 * @param	char	date
	 * @return	bool	true on success
	 */
	public function setExpiration($expires)
	{
		// try to understand the date
		$expires = strtotime($expires);
		if (!$expires)
		{
			throw new \Exception(Lang::txt('Bad expiration date'));
		}

		$this->data->expires = $expires;
		return true;
	}

	public function getExpiration()
	{
		if (!empty($this->data->expires))
		{
			return $this->data->expires;
		}
		return 0;
	}

	/**
	 * Set code object
	 *
	 * @param	string		object type
	 * @return	bool		true on success
	 */
	public function setObjectType($objectType = 'product')
	{
		$allowedObjectsTypes = array('order', 'sku', 'product', 'shipping');

		if (!in_array($objectType, $allowedObjectsTypes))
		{
			throw new \Exception(Lang::txt('Bad coupon object.'));
		}

		$this->data->objectType = $objectType;
		return true;
	}

	public function getObjectType()
	{
		if (!empty($this->data->objectType))
		{
			return $this->data->objectType;
		}
		return false;
	}

	/**
	 * Set code action
	 *
	 * @param	string		object action
	 * @return	bool		true on success
	 */
	public function setAction($action, $actionValue)
	{
		$allowedActions = array('discount');

		if (!in_array($action, $allowedActions))
		{
			throw new \Exception(Lang::txt('Bad coupon action.'));
		}

		if ($action == 'discount' && !is_numeric($actionValue))
		{
			$lastChar = substr($actionValue, strlen($actionValue) - 1);
			$val = substr($actionValue, 0, strlen($actionValue) - 1);

			if (!is_numeric($val) || $lastChar != '%')
			{
				throw new \Exception(Lang::txt('Bad action value.'));
			}

		}

		$act = new \stdClass();
		$act->action = $action;
		$act->value = $actionValue;
		$this->data->action = $act;
		return true;
	}

	public function getAction()
	{
		if (!empty($this->data->action))
		{
			return $this->data->action;
		}
		return false;
	}

	/**
	 * Add object
	 *
	 * @param	int			object ID
	 * @param	int			limit of obejct coupon will be applied to (when purchasing multiple quantities of the same object)
	 * @return	bool		true on success
	 */
	public function addObject($object, $objectLimit = 'DEFAULT')
	{
		if (!is_numeric($objectLimit) || $objectLimit < 0)
		{
			throw new \Exception(Lang::txt('Use limit must be a non-negative integer number'));
		}

		$obj = new \stdClass();
		$obj->id = $object;
		$obj->objectLimit = ceil($objectLimit);

		$this->data->objects[] = $obj;
		return true;
	}

	public function getObjects()
	{
		if (!empty($this->data->objects))
		{
			return $this->data->objects;
		}
		return false;
	}

	/**
	 * Add coupon to the warehouse
	 *
	 * @param  void
	 * @return object	info
	 */
	public function add()
	{
		$this->verify();

		require_once __DIR__ . DS . 'Option.php';
		$warehouse = new Warehouse();

		return $warehouse->addCoupon($this);
	}

	public function verify()
	{
		if (empty($this->data->code))
		{
			throw new \Exception(Lang::txt('Code must be set'));
		}
		if (empty($this->data->description))
		{
			throw new \Exception(Lang::txt('Description must be set'));
		}
		if (empty($this->data->objectType))
		{
			$this->setObjectType();
		}
		if (empty($this->data->action))
		{
			throw new \Exception(Lang::txt('Action must be set'));
		}

		return true;
	}
}
