<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;


class ApiBatchUpdateResponse
{

	protected $_updateResult, $_errorMessage, $_successMessage;

	/**
	 * Constructs ApiBatchUpdateResponse instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_updateResult = $args['result'];
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
		if ($this->_updateResult->succeeded())
		{
			$thisAsArray = $this->_updateSucceededArray();
		}
		else
		{
			$thisAsArray = $this->_updateFailedArray();
		}

		return $thisAsArray;
	}

	/**
	 * Returns array representation of this if update succeeded
	 *
	 * @return   array
	 */
	protected function _updateSucceededArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'success';
		$thisAsArray['message'] = $this->_successMessage;

		return $thisAsArray;
	}

	/**
	 * Returns array representation of this if update failed
	 *
	 * @return   array
	 */
	protected function _updateFailedArray()
	{
		$thisAsArray = [];

		$thisAsArray['status'] = 'error';
		$thisAsArray['message'] = $this->_errorMessage;
		$thisAsArray['models'] = [];
		$thisAsArray['models']['failed_saves'] = $this->_collectFailedSaves();
		$thisAsArray['models']['failed_destroys'] = $this->_collectFailedDestroys();

		return $thisAsArray;
	}

	/**
	 * Collects data of models that failed to persist
	 *
	 * @return   array
	 */
	protected function _collectFailedSaves()
	{
		$failedSaves = $this->_updateResult->getFailedSaves();

		$failedSaves = $this->_collectFailedModelsData($failedSaves);

		return $failedSaves;
	}

	/**
	 * Collects data of models that failed to be destroyed
	 *
	 * @return   array
	 */
	protected function _collectFailedDestroys()
	{
		$failedDestroys = $this->_updateResult->getFailedDestroys();

		$failedDestroys = $this->_collectFailedModelsData($failedDestroys);

		return $failedDestroys;
	}

	/**
	 * Collects failed models IDs and errors
	 *
	 * @param    iterable   $models   Models that CRUD operation failed for
	 * @return   array
	 */
	protected function _collectFailedModelsData($models)
	{
		$modelsData = array_map(function($model) {
			return [
				'id' => $model->get('id'),
				'errors' => $model->getErrors()
			];
		}, $models);

		return $modelsData;
	}

}
