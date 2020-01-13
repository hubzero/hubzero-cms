<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class ComponentAuth
{

	protected $_componentName, $_permitter;

	protected static $_CREATE_PERMISSION = 'core.create';

	/**
	 * Constructs ComponentAuth instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_componentName = $args['component'];
		$this->_permitter = Arr::getValue($args, 'permitter', new MockProxy(['class' => 'User']));
	}

	/**
	 * Determines if current user has necessary permission
	 *
	 * @param    string   $permission   Permission name
	 * @return   bool
	 */
	public function currentIsAuthorized($permission)
	{
		$isAuthorized = $this->_permitter->authorize(
			$permission, $this->_componentName
		);

		return $isAuthorized;
	}

	/**
	 * Determines if current user has component create permissions
	 *
	 * @return   bool
	 */
	public function currentCanCreate()
	{
		$currentCanCreate = $this->currentIsAuthorized(self::$_CREATE_PERMISSION);

		return $currentCanCreate;
	}

}
