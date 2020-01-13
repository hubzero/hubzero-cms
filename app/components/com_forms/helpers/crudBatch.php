<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class CrudBatch
{

	/**
	 * Constructs CrudBatch instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_failedSaves = [];
		$this->_successfulSaves = [];
		$this->_failedDestroys = [];
		$this->_successfulDestroys = [];
	}

	/**
	 * Indicates whether CRUD operations succeed for each mdoel in batch
	 *
	 * @return   bool
	 */
	public function succeeded()
	{
		$succeeded = empty($this->_failedSaves) && empty($this->_failedDestroys);

		return $succeeded;
	}

	/**
	 * Adds model to failed saves
	 *
	 * @param    object   $model   Model that failed to be saved
	 * @return   void
	 */
	public function addFailedSave($model)
	{
		array_push($this->_failedSaves, $model);
	}

	/**
	 * Adds model to successful saves
	 *
	 * @param    object   $model   Model that was saved
	 * @return   void
	 */
	public function addSuccessfulSave($model)
	{
		array_push($this->_successfulSaves, $model);
	}

	/**
	 * Adds model to failed destroys
	 *
	 * @param    object   $model   Model that failed to be destroyed
	 * @return   void
	 */
	public function addFailedDestroy($model)
	{
		array_push($this->_failedDestroys, $model);
	}

	/**
	 * Adds model to successful destroys
	 *
	 * @param    object   $model   Model that was destroyed
	 * @return   void
	 */
	public function addSuccessfulDestroy($model)
	{
		array_push($this->_successfulDestroys, $model);
	}

	/**
	 * Returns all models that failed to be saved
	 *
	 * @return   array
	 */
	public function getFailedSaves()
	{
		return $this->_failedSaves;
	}

	/**
	 * Returns all models that failed to be destroyed
	 *
	 * @return   array
	 */
	public function getFailedDestroys()
	{
		return $this->_failedDestroys;
	}

	/**
	 * Returns all models that were saved
	 *
	 * @return   array
	 */
	public function getSuccessfulSaves()
	{
		return $this->_successfulSaves;
	}

}
