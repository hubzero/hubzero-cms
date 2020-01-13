<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/crudHelper.php";

use Components\Forms\Helpers\CrudHelper;

class RelationalCrudHelper extends CrudHelper
{

	/**
	 * Constructs RelationalCrudHelper instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_controller = $args['controller'];

		parent::__construct($args);
	}

	/**
	 * Handles successful update of record
	 *
	 * @param    string   $successMessage   Create sucess message
	 * @param    string   $url              URL to redirect user to
	 * @return   void
	 */
	public function successfulUpdate($url, $successMessage = '')
	{
		$this->_notifyUserOfSuccess($successMessage);

		parent::successfulUpdate($url);
	}

	/**
	 * Handles failed update of record
	 *
	 * @param    object   $record
	 * @return   void
	 */
	public function failedUpdate($record)
	{
		$this->_forwardUserToEditPage($record);

		parent::failedUpdate($record);
	}

	/**
	 * Handles failed update of records
	 *
	 * @param    string   $forwardingUrl
	 * @param    object   $updateResult
	 * @param    string   $errorIntro
	 * @return   void
	 */
	public function failedBatchUpdate($forwardingUrl, $updateResult, $errorIntro = '')
	{
		if ($errorIntro)
		{
			$this->_errorSummary = $errorIntro;
		}

		parent::failedUpdate($updateResult);

		$this->_router->redirect($forwardingUrl);
	}


	/**
	 * Forwards user to record edit page
	 *
	 * @param    object   $record   Record that failed to be created
	 * @return   void
	 */
	protected function _forwardUserToEditPage($record)
	{
		$controllerName = $this->_controller->name;

		$this->_controller->setView($controllerName, 'edit');
		$this->_controller->editTask($record);
	}

	/**
	 * Handles failed creation of record
	 *
	 * @param    object   $record   Record that failed to be created
	 * @param    array    $args     Supplementary data
	 * @return   void
	 */
	public function failedCreate($record, $args = [])
	{
		$this->_forwardUserToNewPage($record, $args);

		parent::failedCreate($record);
	}

	/**
	 * Forwards user to new record creation page
	 *
	 * @param    object   $record   Record that failed to be created
	 * @param    array    $args     Supplementary data
	 * @return   void
	 */
	protected function _forwardUserToNewPage($record, $args)
	{
		$controllerName = $this->_controller->name;

		$this->_controller->setView($controllerName, 'new');
		$this->_controller->newTask($record, $args);
	}

}
