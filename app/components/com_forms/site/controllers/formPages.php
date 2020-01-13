<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formPagesFactory.php";
require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/relationalCrudHelper.php";
require_once "$componentPath/models/form.php";
require_once "$componentPath/models/formPage.php";

use Components\Forms\Helpers\FormPagesFactory;
use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\RelationalCrudHelper as CrudHelper;
use Components\Forms\Models\Form;
use Components\Forms\Models\FormPage;
use Hubzero\Component\SiteController;

class FormPages extends SiteController
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
		'order',
		'title',
		'form_id'
	];

	/**
	 * Executes requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->_bouncer = new PageBouncer([
			'component' => $this->_option
		]);
		$this->_crudHelper = new CrudHelper([
			'controller' => $this,
			'errorSummary' => Lang::txt('COM_FORMS_FORM_SAVE_ERROR')
		]);
		$this->_factory = new FormPagesFactory();
		$this->name = $this->_controller;
		$this->_params = new Params(
			['whitelist' => self::$_paramWhitelist]
		);
		$this->_routes = new RoutesHelper();

		parent::execute();
	}

	/**
	 * Renders list of given form's pages
	 *
	 * @return   void
	 */
	public function listTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->_params->get('form_id');
		$form = Form::oneOrFail($formId);
		$batchUpdateTaskUrl = $this->_routes->batchPagesUpdateUrl();

		$pages = FormPage::all()
			->whereEquals('form_id', $formId)
			->order('order', 'asc')
			->rows();

		$this->view
			->set('form', $form)
			->set('pages', $pages)
			->set('updateAction', $batchUpdateTaskUrl)
			->display();
	}

	/**
	 * Renders new pages view
	 *
	 * @return   void
	 */
	public function newTask($page = false)
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->_params->get('form_id');
		$form = Form::oneOrFail($formId);

		$page = $page ? $page : FormPage::blank();
		$createTaskUrl = $this->_routes->formsPagesCreateUrl($formId);

		$this->view
			->set('form', $form)
			->set('action', $createTaskUrl)
			->set('page', $page)
			->display();
	}

	/**
	 * Attempts to create page record using submitted data
	 *
	 * @return   void
	 */
	public function createTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->_params->get('form_id');
		$pageData = $this->_params->getArray('page');
		$pageData['created'] = Date::toSql();
		$pageData['created_by'] = User::get('id');
		$pageData['form_id'] = $formId;

		$page = FormPage::blank();
		$page->set($pageData);

		if ($page->save())
		{
			$pageId = $page->get('id');
			$forwardingUrl = $this->_routes->pagesEditUrl($pageId);
			$successMessage = Lang::txt('COM_FORMS_PAGE_SAVE_SUCCESS');
			$this->_crudHelper->successfulCreate($forwardingUrl, $successMessage);
		}
		else
		{
			$this->_crudHelper->failedCreate($page);
		}
	}

	/**
	 * Renders page edit view
	 *
	 * @return   void
	 */
	public function editTask($page = false)
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$pageId = $this->_params->get('id');
		$page = $page ? $page : FormPage::oneOrFail($pageId);
		$form = $page->getForm();

		$updateTaskUrl = $this->_routes->pagesUpdateUrl($pageId);
		$editFieldsUrl = $this->_routes->pagesFieldsEditUrl($pageId);

		$this->view
			->set('action', $updateTaskUrl)
			->set('editFieldsUrl', $editFieldsUrl)
			->set('form', $form)
			->set('page', $page)
			->display();
	}

	/**
	 * Handles updating of given page using provided data
	 *
	 * @return   void
	 */
	public function updateTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$pageId = $this->_params->get('id');
		$pageData = $this->_params->getArray('page');
		$pageData['modified'] = Date::toSql();
		$pageData['modified_by'] = User::get('id');

		$page = FormPage::oneOrFail($pageId);
		$page->set($pageData);

		if ($page->save())
		{
			$forwardingUrl = $this->_routes->pagesEditUrl($pageId);
			$message = Lang::txt('COM_FORMS_PAGE_SAVE_SUCCESS');
			$this->_crudHelper->successfulUpdate($forwardingUrl, $message);
		}
		else
		{
			$this->_crudHelper->failedUpdate($page);
		}
	}

	/**
	 * Attempts to update page records
	 *
	 * @return   void
	 */
	public function batchUpdateTask()
	{
		$formId = $this->_params->getInt('form_id');
		$form = Form::oneOrFail($formId);

		$this->_bouncer->redirectUnlessCanEditForm($form);

		$currentPages =  $form->getPagesInArray();
		$submittedPagesInfo = $this->_params->get('pages');
		$updateResult = $this->_factory->updateFormsPages($currentPages, $submittedPagesInfo);

		if ($updateResult->succeeded())
		{
			$forwardingUrl = $this->_routes->formsPagesUrl($formId);
			$message = Lang::txt('COM_FORMS_NOTICES_PAGES_SUCCESSFUL_UPDATE');
			$this->_crudHelper->successfulUpdate($forwardingUrl, $message);
		}
		else
		{
			$forwardingUrl = $this->_routes->formsPagesUrl($formId);
			$message = Lang::txt('COM_FORMS_NOTICES_PAGES_FAILED_UPDATE');
			$this->_crudHelper->failedBatchUpdate($forwardingUrl, $updateResult, $message);
		}
	}

}
