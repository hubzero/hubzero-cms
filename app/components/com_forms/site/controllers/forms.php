<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/mockProxy.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/query.php";
require_once "$componentPath/helpers/relationalCrudHelper.php";
require_once "$componentPath/helpers/relationalSearch.php";
require_once "$componentPath/models/form.php";
require_once "$componentPath/models/formResponse.php";

use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\MockProxy;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\Query;
use Components\Forms\Helpers\RelationalCrudHelper as CrudHelper;
use Components\Forms\Helpers\RelationalSearch as Search;
use Components\Forms\Models\Form;
use Components\Forms\Models\FormResponse;
use Hubzero\Component\SiteController;
use \Date;
use \User;

class Forms extends SiteController
{

	/**
	 * Task mapping
	 *
	 * @var  array
	 */
	protected $_taskMap = [
		'__default' => 'list'
	];

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'archived',
		'closing_time',
		'description',
		'disabled',
		'id',
		'name',
		'opening_time',
		'responses_locked',
	];

	/**
	 * Executes the requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->bouncer = new PageBouncer([
			'component' => $this->_option
		]);
		$this->crudHelper = new CrudHelper([
			'controller' => $this,
			'errorSummary' => Lang::txt('COM_FORMS_FORM_SAVE_ERROR')
		]);
		$this->name = $this->_controller;
		$this->params = new Params(
			['whitelist' => self::$_paramWhitelist]
		);
		$this->routes = new RoutesHelper();
		$this->search = new Search([
			'class' => new MockProxy(['class' => 'Components\Forms\Models\Form'])
		]);

		parent::execute();
	}

	/**
	 * Renders searchable list of forms
	 *
	 * @return   void
	 */
	public function listTask()
	{
		$formListUrl = $this->routes->formListUrl();
		$searchFormAction = $this->routes->queryUpdateUrl();
		$query = Query::load();

		$forms = $this->search->findBy($query->toArray())
			->paginated('limitstart', 'limit')
			->order('name', 'asc');

		$this->view
			->set('formListUrl', $formListUrl)
			->set('forms', $forms)
			->set('query', $query)
			->set('searchFormAction', $searchFormAction)
			->display();
	}

	/**
	 * Renders new form page
	 *
	 * @param    object   $form   Form to be created
	 * @return   void
	 */
	public function newTask($form = false)
	{
		$this->bouncer->redirectUnlessAuthorized('core.create');

		$createTaskUrl = $this->routes->formsCreateUrl();
		$form = $form ? $form : Form::blank();

		$this->view
			->set('formAction', $createTaskUrl)
			->set('form', $form)
			->display();
	}

	/**
	 * Attempts to create form record using submitted data
	 *
	 * @return   void
	 */
	public function createTask()
	{
		$this->bouncer->redirectUnlessAuthorized('core.create');

		$formData = $this->params->getArray('form');
		$formData['created'] = Date::toSql();
		$formData['created_by'] = User::get('id');

		$form = Form::blank();
		$form->set($formData);

		if ($form->save())
		{
			$formId = $form->get('id');
			$forwardingUrl = $this->routes->formsEditUrl($formId);
			$successMessage = Lang::txt('COM_FORMS_FORM_SAVE_SUCCESS');
			$this->crudHelper->successfulCreate($forwardingUrl, $successMessage);
		}
		else
		{
			$this->crudHelper->failedCreate($form);
		}
	}

	/**
	 * Renders form edit page
	 *
	 * @return   void
	 */
	public function manageTask($form = false)
	{
		$this->bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->params->get('id');
		$form = $form ? $form : Form::oneOrFail($formId);

		$updateTaskUrl = $this->routes->formsUpdateUrl($formId);

		$this->view
			->set('formAction', $updateTaskUrl)
			->set('form', $form)
			->display();
	}

	/**
	 * Handles updating of given form using provided data
	 *
	 * @return   void
	 */
	public function updateTask()
	{
		$this->bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->params->get('id');
		$formData = $this->params->getArray('form');
		$formData['modified'] = Date::toSql();
		$formData['modified_by'] = User::get('id');

		$form = Form::oneOrFail($formId);
		$form->set($formData);

		if ($form->save())
		{
			$forwardingUrl = $this->routes->formsEditUrl($formId);
			$successMessage = Lang::txt('COM_FORMS_FORM_SAVE_SUCCESS');
			$this->crudHelper->successfulUpdate($forwardingUrl, $successMessage);
		}
		else
		{
			$this->crudHelper->failedUpdate($form);
		}
	}

	/**
	 * Renders form display page
	 *
	 * @return   void
	 */
	public function displayTask()
	{
		$formId = Request::getInt('id');
		$form = Form::oneOrFail($formId);
		$response = FormResponse::oneWhere([
			'form_id' => $formId,
			'user_id' => User::get('id')
		]);

		$this->view
			->set('form', $form)
			->set('response', $response)
			->display();
	}

}
