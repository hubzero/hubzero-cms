<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Api\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsAuth.php";
require_once "$componentPath/helpers/listErrorMessage.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/models/form.php";
require_once "$componentPath/models/formPage.php";

use Components\Forms\Helpers\FormsAuth;
use Components\Forms\Helpers\ListErrorMessage as ErrorMessage;
use Components\Forms\Helpers\Params;
use Components\Forms\Models\Form;
use Components\Forms\Models\FormPage;
use Hubzero\Component\ApiController;

class FormPagesv1_0 extends ApiController
{

	/**
	 * Controller version
	 *
	 * @var  string
	 */
	protected static $version = '1.0';

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'id'
	];

	/**
	 * Executes the requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->_auth = new FormsAuth();
		$this->_params = new Params([
			'whitelist' => self::$_paramWhitelist
		]);

		parent::execute();
	}

	/**
	 * Attempts to destroy FormPage with given ID
	 *
	 * @apiMethod DELETE
	 * @apiUri    /api/v1.0/forms/formpages/destroy
	 * @apiParameter {
	 * 		"name":          id,
	 * 		"description":   FormPage ID,
	 * 		"type":          int,
	 * 		"required":      true
	 * }
	 * @return   object
	 */
	public function destroyTask()
	{
		$this->requiresAuthentication();

		$pageId = $this->_params->getInt('id');
		$page = FormPage::oneOrFail($pageId);
		$form = $page->getForm();

		$canEdit = $this->_auth->canCurrentUserEditForm($form);

		if (!$canEdit)
		{
			$status = 'error';
			$message = Lang::txt('COM_FORMS_PAGES_FAILED_DESTROY_PERMISSION');
		}
		elseif ($page->destroy())
		{
			$status = 'success';
			$message = Lang::txt('COM_FORMS_PAGES_SUCCESSFUL_DESTROY');
		}
		else
		{
			$status = 'error';
			$message = $this->_generateDestroyErrorMessage($page->getErrors());
		}

		$this->send([
			'status' => $status,
			'message' => $message
		]);
	}

	/**
	 * Generates failed destroy error message
	 *
	 * @param    array    $errors   Record's errors
	 * @return   string
	 */
	protected function _generateDestroyErrorMessage($errors)
	{
		$errorIntro = Lang::txt('COM_FORMS_PAGES_FAILED_DESTROY');

		$errorMessage = $this->_generateErrorMessage($errors, $errorIntro);

		return $errorMessage;
	}

	/**
	 * Generates error message
	 *
	 * @param    array     $errors       Record's errors
	 * @param    string    $errorIntro   Top-level error text
	 * @return   string
	 */
	protected function _generateErrorMessage($errors, $errorIntro = '')
	{
		$messageObject = new ErrorMessage([
			'errorIntro' => $errorIntro,
			'errors' => $errors
		]);

		return $messageObject->toString();
	}

}
