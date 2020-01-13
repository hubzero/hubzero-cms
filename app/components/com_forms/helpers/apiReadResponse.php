<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;


class ApiReadResponse
{

	protected $_readResult, $_errorMessage, $_successMessage;

	/**
	 * Constructs ApiReadResponse instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_readResult = $args['result'];
		$this->_errorMessage = $args['error_message'];
		$this->_successMessage = $args['success_message'];
	}

	/**
	 * Returns array representation of $this
	 *
	 * @return   array
	 */
	public function toArray()
	{
		if ($this->_readResult->succeeded())
		{
			$thisAsArray = $this->_readSucceededArray();
		}
		else
		{
			$thisAsArray = $this->_readFailedArray();
		}

		return $thisAsArray;
	}

	/**
	 * Returns array representation of this if read succeeded
	 *
	 * @return   array
	 */
	protected function _readSucceededArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'success';
		$thisAsArray['message'] = $this->_successMessage;
		$thisAsArray['associations'] = $this->_mapAssociationsToArray();

		return $thisAsArray;
	}

	protected function _mapAssociationsToArray()
	{
		$associations = array_map(function($association) {
			return $association->toArray();
		}, $this->_readResult->getData());

		return $associations;
	}

	/**
	 * Returns array representation of this if read failed
	 *
	 * @return   array
	 */
	protected function _readFailedArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'error';
		$thisAsArray['message'] = $this->_errorMessage;

		return $thisAsArray;
	}

}
