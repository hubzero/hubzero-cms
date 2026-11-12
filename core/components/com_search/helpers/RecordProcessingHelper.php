<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Components\Search\Helpers\ErrorMessageHelper;
use Components\Search\Helpers\MockProxy;
use Hubzero\Utility\Arr;
use Notify;

class RecordProcessingHelper
{
    protected $controller;
    protected $errorMessageHelper;
    protected $app;
    protected $notify;

    public function __construct($args = [])
    {
        $this->controller = $args['controller'];
        $this->errorMessageHelper = Arr::getValue(
            $args,
            'error',
            new ErrorMessageHelper(),
        );
        $this->app = Arr::getValue(
            $args,
            'app',
            new MockProxy(['class' => 'App']),
        );
        $this->notify = Arr::getValue(
            $args,
            'notify',
            new MockProxy(['class' => 'Notify']),
        );
    }

    /** @return void */
    public function handleSaveSuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /** @return void */
    public function handleSaveFail($record)
    {
        $this->generateErrorNotification($record);
        $this->controller->setView($this->controller->name, 'new');
        $this->controller->newTask($record);
    }

    /** @return void */
    public function handleUpdateSuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /** @return void */
    public function handleUpdateFail($record)
    {
        $this->generateErrorNotification($record);
        $this->controller->setView($this->controller->name, 'edit');
        $this->controller->editTask($record);
    }

    /** @return void */
    public function handleDestroySuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /** @return void */
    public function handleDestroyFail($message, $url)
    {
        $this->notifyOfError($message);
        $this->redirectTo($url);
    }

    /** @return void */
    protected function handleSuccess($message, $url)
    {
        $this->notifyOfSuccess($message);
        $this->redirectTo($url);
    }

    /** @return void */
    protected function notifyOfSuccess($message)
    {
        $this->notify->success($message);
    }

    /** @return void */
    protected function redirectTo($url)
    {
        $this->app->redirect($url);
    }

    /** @return void */
    protected function generateErrorNotification($record)
    {
        $errors = $record->getErrors();
        $message = $this->errorMessageHelper->generateErrorMessage($errors);
        $this->notifyOfError($message);
    }

    /** @return void */
    protected function notifyOfError($message)
    {
        $this->notify->error($message);
    }
}
