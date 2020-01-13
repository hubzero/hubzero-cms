<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/batchUpdateHelper.php";
require_once "$componentPath/helpers/crudBatch.php";
require_once "$componentPath/helpers/crudBatchResult.php";
require_once "$componentPath/helpers/mockProxy.php";

use Components\Forms\Helpers\BatchUpdateHelper;
use Components\Forms\Helpers\CrudBatch;
use Components\Forms\Helpers\CrudBatchResult;
use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class Factory
{

	/**
	 * Constructs Factory instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_batchUpdateHelper = Arr::getValue($args, 'batch_helper', new BatchUpdateHelper());
		$this->_modelName = $args['model_name'];
		$this->_modelClass = Arr::getValue(
			$args, 'model_class', new MockProxy(['class' => $this->_modelName])
		);
	}

	/**
	 * Updates, creates, destroys records based on given data
	 *
	 * @param    object   $currentRecords   Current records
	 * @param    array    $submittedData    Submitted records' data
	 * @return   object
	 */
	public function batchUpdate($existingRecords, $submittedData)
	{
		$updateDelta = $this->_calculateUpdateDelta($existingRecords, $submittedData);

		$updateResult = $this->_resolveUpdateDelta($updateDelta);

		return $updateResult;
	}

	/**
	 * Determines which records are being added, updated, destroyed
	 *
	 * @param    object   $currentRecords   Current records
	 * @param    array    $submittedData    Submitted records' data
	 * @return   object
	 */
	protected function _calculateUpdateDelta($existingRecords, $submittedData)
	{
		$submittedRecords = $this->instantiateMany($submittedData);

		$updateDelta = $this->_batchUpdateHelper->updateDelta(
			$existingRecords,
			$submittedRecords
		);

		return $updateDelta;
	}

	/**
	 * Saves, creates, or destroys records based on update delta
	 *
	 * @param    object   $updateDelta   Update delta
	 * @return   object
	 */
	protected function _resolveUpdateDelta($updateDelta)
	{
		$saveResult = $this->_saveMany($updateDelta->getModelsToSave());
		$destroyResult = $this->_destroyMany($updateDelta->getModelsToDestroy());

		return new CrudBatchResult(['batches' => [$saveResult, $destroyResult]]);
	}


	/**
	 * Instantiates many models using provided data
	 *
	 * @param   array   $modelsData   Data to instantiate models with
	 * @return  array
	 */
	public function instantiateMany($modelsData)
	{
		$models = [];

		foreach ($modelsData as $modelData)
		{
			$model = $this->instantiate($modelData);
			$models[] = $model;
		}

		return $models;
	}

	/**
	 * Instantiate a model using provided data
	 *
	 * @param   array   $modelData   Data to instantiate model with
	 * @return  object
	 */
	public function instantiate($modelData = [])
	{
		$model = $this->_modelClass->blank();

		$model->set($modelData);

		return $model;
	}

	/**
	 * Attempts to save models
	 *
	 * @param   array   $models   Models to save
	 * @return  object
	 */
	protected function _saveMany($models)
	{
		$result = new CrudBatch();

		foreach ($models as $model)
		{
			$this->_save($model, $result);
		}

		return $result;
	}

	/**
	 * Attempts to save a model
	 *
	 * @param   object   $model    Model to save
	 * @param   object   $result   Trackss outcome of saving model
	 * @return  void
	 */
	protected function _save($model, $result)
	{
		if (!$model->save())
		{
			$result->addFailedSave($model);
		}
		else
		{
			$result->addSuccessfulSave($model);
		}
	}

	/**
	 * Attempts to destroy models
	 *
	 * @param   array   $models   Models to destroy
	 * @return  object
	 */
	protected function _destroyMany($models)
	{
		$result = new CrudBatch();

		foreach ($models as $model)
		{
			$this->_destroy($model, $result);
		}

		return $result;
	}

	/**
	 * Attempts to destroys a model
	 *
	 * @param   object   $model    Model to destroy
	 * @param   object   $result   Trackss outcome of destroying model
	 * @return  void
	 */
	protected static function _destroy($model, $result)
	{
		if (!$model->destroy())
		{
			$result->addFailedDestroy($model);
		}
		else
		{
			$result->addSuccessfulDestroy($model);
		}
	}

  /**
   * Adds modified data if record data was changed
   *
   * @param    array   $recordsData   Records' data
   * @return   array
   */
  protected function _addModifiedIfAltered($recordsData)
  {
    $augmentedRecordsData = [];

    foreach ($recordsData as $recordData)
    {
      if (!empty($recordData['id']))
      {
        $id = $recordData['id'];
        $record = $this->_getRecordById($id);

        if ($this->_dataDiffers($recordData, $record))
        {
          $recordData = $this->_addModified($recordData);
        }
      }

      $augmentedRecordsData[] = $recordData;
    }

    return $augmentedRecordsData;
  }

  /**
   * Queries for record with given ID
   *
   * @param    array   $id   Record's ID
   * @return   object
   */
  protected function _getRecordById($id)
  {
    $record = $this->_modelClass->oneOrFail($id);

    return $record;
  }

  /**
   * Determines if new data differs from record's persisted data
   *
   * @param    array    $recordData   Record's data
   * @param    object   $record       Record object
   * @return   bool
   */
  protected function _dataDiffers($recordData, $record)
  {
    foreach ($recordData as $name => $value)
    {
      $dataDiffers = $record->get($name) != $value;

      if ($dataDiffers) break;
    }

    return $dataDiffers;
  }

  /**
   * Adds modified data
   *
   * @param    array   $recordData   Record's data
   * @return   array
   */
  protected function _addModified($recordData)
  {
    $currentDatetime = Date::toSql();

    $recordData['modified'] = $currentDatetime;
    $recordData['modified_by'] = User::get('id');

    return $recordData;
  }

}
