<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

require_once "$componentPath/helpers/errorMessageHelper.php";
require_once "$componentPath/helpers/mockProxy.php";

use Components\Search\Helpers\ErrorMessageHelper;
use Components\Search\Helpers\MockProxy;
use Hubzero\Utility\Arr;
use Notify;

class RecordProcessingHelper
{

	protected $controller,
		$errorMessageHelper,
		$app,
		$notify;

	public function __construct($args = [])
	{
		$this->controller = $args['controller'];
		$this->errorMessageHelper = Arr::getValue(
			$args, 'error', new ErrorMessageHelper()
		);
		$this->app = Arr::getValue(
			$args, 'app', new MockProxy(['class' => 'App'])
		);
		$this->notify = Arr::getValue(
			$args, 'notify', new MockProxy(['class' => 'Notify'])
		);
	}

	public function handleSaveSuccess($message, $url)
	{
		$this->handleSuccess($message, $url);
	}

	public function handleSaveFail($record)
	{
		$this->generateErrorNotification($record);
		$this->controller->setView($this->controller->name, 'new');
		$this->controller->newTask($record);
	}

	public function handleUpdateSuccess($message, $url)
	{
		$this->handleSuccess($message, $url);
	}

	public function handleUpdateFail($record)
	{
		$this->generateErrorNotification($record);
		$this->controller->setView($this->controller->name, 'edit');
		$this->controller->editTask($record);
	}

	public function handleDestroySuccess($message, $url)
	{
		$this->handleSuccess($message, $url);
	}

	public function handleDestroyFail($message, $url)
	{
		$this->notifyOfError($message);
		$this->redirectTo($url);
	}

	protected function handleSuccess($message, $url)
	{
		$this->notifyOfSuccess($message);
		$this->redirectTo($url);
	}

	protected function notifyOfSuccess($message)
	{
		$this->notify->success($message);
	}

	protected function redirectTo($url)
	{
		$this->app->redirect($url);
	}

	protected function generateErrorNotification($record)
	{
		$errors = $record->getErrors();
		$message = $this->errorMessageHelper->generateErrorMessage($errors);
		$this->notifyOfError($message);
	}

	protected function notifyOfError($message)
	{
		$this->notify->error($message);
	}

}
