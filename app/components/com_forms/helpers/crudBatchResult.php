<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

class CrudBatchResult
{

	/**
	 * Constructs CrudBatchResult instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_batches = $args['batches'];
	}

	/**
	 * Indicates whether CRUD operations succeeded for all batches
	 *
	 * @return   bool
	 */
	public function succeeded()
	{
		foreach ($this->_batches as $batch)
		{
			if (!$batch->succeeded()) return false;
		}

		return true;
	}

	/**
	 * Returns errors for models that failed to be saved
	 *
	 * @return   bool
	 */
	public function getErrors()
	{
		$failedSaves = $this->getFailedSaves();

		$errors = array_map(function($record) {
			return $this->_collectRecordsErrors($record);
		}, $failedSaves);

		if ($errors)
		{
			$errors = array_merge(...$errors);
		}

		return $errors;
	}

	/**
	 * Collects records errors under record identifier
	 *
	 * @param    object   $record   Relational record
	 * @return   array
	 */
	protected function _collectRecordsErrors($record)
	{
		$errors = $record->getErrors();
		$recordIdentifier = $this->_getRecordIdentifier($record);

		return [$recordIdentifier => $errors];
	}

	/**
	 * Retrieves given records user identifier or uses default
	 *
	 * @param    object    $record   Given record
	 * @return   string
	 */
	protected function _getRecordIdentifier($record)
	{
		if (method_exists($record, 'getUserIdentifier'))
		{
			$identifier = $record->getUserIdentifier();
		}
		else
		{
			$identifier = 'Record ' . $record->get('id');
		}

		return $identifier;
	}

	/**
	 * Returns all models that failed to be saved
	 *
	 * @return   array
	 */
	public function getFailedSaves()
	{
		$failedSaves = array_reduce($this->_batches, function($failedSaves, $batch) {
			return array_merge($failedSaves, $batch->getFailedSaves());
		}, []);

		return $failedSaves;
	}

	/**
	 * Returns all models that failed to be destroyed
	 *
	 * @return   array
	 */
	public function getFailedDestroys()
	{
		$failedDestroys = array_reduce($this->_batches, function($failedDestroys, $batch) {
			return array_merge($failedDestroys, $batch->getFailedDestroys());
		}, []);

		return $failedDestroys;
	}

	/**
	 * Returns all models that were successfully saved
	 *
	 * @return   array
	 */
	public function getSuccessfulSaves()
	{
		$successfulSaves = array_reduce($this->_batches, function($successfulSaves, $batch) {
			return array_merge($successfulSaves, $batch->getSuccessfulSaves());
		}, []);

		return $successfulSaves;
	}

}
