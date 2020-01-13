<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Hubzero\Utility\Arr;

class ApiNullResponse
{

	protected $_errorMessage, $_result, $_status, $_successMessage;

	/**
	 * Constructs ApiNullResponse instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_result = Arr::getValue($args, 'result', null);
		$this->_status = Arr::getValue($args, 'status', '');
		$this->_errorMessage = Arr::getValue($args, 'error_message', '');
		$this->_successMessage = Arr::getValue($args, 'success_message', '');
	}

	/**
	 * Returns array representation of $this
	 *
	 * @return   array
	 */
	public function toArray()
	{
		if ($this->_succeeded())
		{
			$thisAsArray = $this->_succeededArray();
		}
		else
		{
			$thisAsArray = $this->_failedArray();
		}

		return $thisAsArray;
	}

	/**
	 * Returns array representation of this if succeeded
	 *
	 * @return   array
	 */
	protected function _succeededArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'success';
		$thisAsArray['message'] = $this->_successMessage;

		return $thisAsArray;
	}

	/**
	 * Returns array representation of this if failed
	 *
	 * @return   array
	 */
	protected function _failedArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'error';
		$thisAsArray['message'] = $this->_errorMessage;

		return $thisAsArray;
	}

	/**
	 * Indicates if result should be considered successful
	 *
	 * return   bool
	 */
	protected function _succeeded()
	{
		$result = $this->_result;
		$resultSucceeded = $result && $result->succeeded();
		$successStatus = $this->_status == 'success';

		$succeeded = $resultSucceeded || $successStatus;

		return $succeeded;
	}

}
