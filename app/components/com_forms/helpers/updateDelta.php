<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Hubzero\Utility\Arr;

class UpdateDelta
{

	protected $_modelsToDestroy, $_modelsToSave;

	/**
	 * Constructs UpdateDelta instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_modelsToDestroy = Arr::getValue($args, 'destroy', []);
		$this->_modelsToSave = Arr::getValue($args, 'save', []);
	}

	/**
	 * Getter for $_modelsToSave
	 *
	 * @return   array
	 */
	public function getModelsToSave()
	{
		return $this->_modelsToSave;
	}

	/**
	 * Getter for $_modelsToDestroy
	 *
	 * @return   array
	 */
	public function getModelsToDestroy()
	{
		return $this->_modelsToDestroy;
	}

}
