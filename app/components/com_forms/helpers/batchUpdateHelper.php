<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/iterableHelper.php";
require_once "$componentPath/helpers/updateDelta.php";

use Components\Forms\Helpers\IterableHelper;
use Components\Forms\Helpers\UpdateDelta;
use Hubzero\Utility\Arr;

class BatchUpdateHelper
{

	/**
	 * Constructs BatchUpdateHelper instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_iterableHelper = Arr::getValue($args, 'iterable_helper', new IterableHelper());
	}

	/**
	 * Determines which models should be destroyed or saved
	 *
	 * @param    array   $currentModels     Current models
	 * @param    array   $submittedModels   Submitted models
	 * @return   object
	 */
	public function updateDelta($currentModels, $submittedModels)
	{
		$modelsToDestroy = $this->_determineToBeDestroyed($currentModels, $submittedModels);

		return new UpdateDelta([
			'save' => $submittedModels,
			'destroy' => $modelsToDestroy
		]);
	}

	/**
	 * Determines which models should be destroyed
	 *
	 * @param    array   $currentModels     Current models
	 * @param    array   $submittedModels   Submitted models
	 * @return   array
	 */
	protected function _determineToBeDestroyed($currentModels, $submittedModels)
	{
		$currentModelsIds = $this->_iterableHelper->functionMap($currentModels, 'get', ['id']);
		$submittedModelsIds = $this->_iterableHelper->functionMap($submittedModels, 'get', ['id']);
		$toDestroy = [];

		foreach ($currentModelsIds as $i => $id)
		{
			if (!in_array($id, $submittedModelsIds))
			{
				$toDestroy[] = $currentModels[$i];
			}
		}

		return $toDestroy;
	}

}
