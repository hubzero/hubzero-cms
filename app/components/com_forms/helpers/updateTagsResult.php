<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use stdClass;

class UpdateTagsResult
{

	/**
	 * Constructs UpdateTagsResult instance
	 *
	 * @return   void
	 */
	public function __construct()
	{
		$this->_failures = [];
		$this->_successes = [];
	}

	/**
	 * Indicates whether tags were updated for all records
	 *
	 * @return   bool
	 */
	public function succeeded()
	{
		$tagsUpdated = !empty($this->_successes);
		$noFailures = empty($this->_failures);

		return $tagsUpdated && $noFailures;
	}

	/**
	 * Adds model and errors to failures
	 *
	 * @param    object   $record   Given record
	 * @param    array    $errors   Tag update errors
	 * @return   void
	 */
	public function addFailure($record, $errors)
	{
		$taggingFailure = new stdClass;

		$taggingFailure->record = $record;
		$taggingFailure->errors = $errors;

		$this->_failures[] = $taggingFailure;
	}

	/**
	 * Returns failures
	 *
	 * @return   array
	 */
	public function getFailures()
	{
		return $this->_failures;
	}

	/**
	 * Adds model to successes
	 *
	 * @param    object   $record   Given record
	 * @return   void
	 */
	public function addSuccess($record)
	{
		$taggingSuccess = new stdClass;

		$taggingSuccess->record = $record;

		$this->_successes[] = $taggingSuccess;
	}

	/**
	 * Returns successes
	 *
	 * @return   array
	 */
	public function getSuccesses()
	{
		return $this->_successes;
	}

	/**
	 * Returns records' tagging errors
	 *
	 * @return   array
	 */
	public function getErrors()
	{
		$errors = [];
		$failures = $this->getFailures();

		foreach ($failures as $failure)
		{
			$this->_collectTagUpdateErrors($failure, $errors);
		}

		return $errors;
	}

	/**
	 * Adds record's tag update errors to collection of all errors
	 *
	 * @param   object   $failure   Tagging failure
	 * @param   array    $errors    Collection of all records' errors
	 */
	protected function _collectTagUpdateErrors($failure, &$errors)
	{
		$record = $failure->record;

		$errors[$record->id] = $failure->errors;
	}

}
