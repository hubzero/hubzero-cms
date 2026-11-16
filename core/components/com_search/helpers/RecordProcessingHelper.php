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

    /**
     * @param   array<mixed>  $args
     */
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

    /**
     * @param   string  $message
     * @param   string  $url
     * @return void
     */
    public function handleSaveSuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /**
     * @param   mixed  $record
     * @return void
     */
    public function handleSaveFail($record)
    {
        $this->generateErrorNotification($record);
        $this->controller->setView($this->controller->name, 'new');
        $this->controller->newTask($record);
    }

    /**
     * @param   string  $message
     * @param   string  $url
     * @return void
     */
    public function handleUpdateSuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /**
     * @param   mixed  $record
     * @return void
     */
    public function handleUpdateFail($record)
    {
        $this->generateErrorNotification($record);
        $this->controller->setView($this->controller->name, 'edit');
        $this->controller->editTask($record);
    }

    /**
     * @param   string  $message
     * @param   string  $url
     * @return void
     */
    public function handleDestroySuccess($message, $url)
    {
        $this->handleSuccess($message, $url);
    }

    /**
     * @param   string  $message
     * @param   string  $url
     * @return void
     */
    public function handleDestroyFail($message, $url)
    {
        $this->notifyOfError($message);
        $this->redirectTo($url);
    }

    /**
     * @param   mixed  $message
     * @param   mixed  $url
     * @return void
     */
    protected function handleSuccess($message, $url)
    {
        $this->notifyOfSuccess($message);
        $this->redirectTo($url);
    }

    /**
     * @param   mixed  $message
     * @return void
     */
    protected function notifyOfSuccess($message)
    {
        $this->notify->success($message);
    }

    /**
     * @param   mixed  $url
     * @return void
     */
    protected function redirectTo($url)
    {
        $this->app->redirect($url);
    }

    /**
     * @param   mixed  $record
     * @return void
     */
    protected function generateErrorNotification($record)
    {
        $errors = $record->getErrors();
        $message = $this->errorMessageHelper->generateErrorMessage($errors);
        $this->notifyOfError($message);
    }

    /**
     * @param   mixed  $message
     * @return void
     */
    protected function notifyOfError($message)
    {
        $this->notify->error($message);
    }
}
