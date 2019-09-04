<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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

	public function handleSaveSuccess($message, $redirectUrl)
	{
		$this->handleSuccess($message, $redirectUrl);
	}

	public function handleSaveFail($record)
	{
		$this->notifyOfErrors($record);
		$this->controller->setView($this->controller->name, 'new');
		$this->controller->newTask($record);
	}

	public function handleUpdateSuccess($message, $redirectUrl)
	{
		$this->handleSuccess($message, $redirectUrl);
	}

	public function handleUpdateFail($record)
	{
		$this->notifyOfErrors($record);
		$this->controller->setView($this->controller->name, 'edit');
		$this->controller->editTask($record);
	}

	protected function handleSuccess($message, $redirectUrl)
	{
		$this->notifyOfSuccess($message);
		$this->redirectTo($redirectUrl);
	}

	protected function notifyOfSuccess($message)
	{
		$this->notify->success($message);
	}

	protected function redirectTo($redirectUrl)
	{
		$this->app->redirect($redirectUrl);
	}

	protected function notifyOfErrors($record)
	{
		$errors = $record->getErrors();
		$errorMessage = $this->errorMessageHelper->generateErrorMessage($errors);
		$this->notify->error($errorMessage);
	}

}
