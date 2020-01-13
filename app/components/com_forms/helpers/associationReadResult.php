<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class AssociationReadResult
{

	/**
	 * Constructs AssociationReadResult instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_data = null;
		$this->_accessor = $args['accessor'];
		$this->_model = $args['model'];
	}

	/**
	 * Indicates whether read succeeded
	 *
	 * @return   bool
	 */
	public function succeeded()
	{
		$modelIsNew = $this->_model->isNew();

		$readSucceeded = $this->_retrieveAssociations();

		return !$modelIsNew && $readSucceeded;
	}

	/**
	 * Getter for _data attribute
	 *
	 * @return   mixed
	 */
	public function getData()
	{
		if ($this->_data === null)
		{
			$this->_retrieveAssociations();
		}

		return $this->_data;
	}

	/**
	 * Retrieves associations from object using given accessor
	 *
	 * @return   bool
	 */
	protected function _retrieveAssociations()
	{
		try
		{
			$accessor = $this->_accessor;
			$this->_data = $this->_model->$accessor();
			$readSucceeded = true;
		}
		catch (Exception $e)
		{
			$readSucceeded = false;
		}

		return $readSucceeded;
	}

}
