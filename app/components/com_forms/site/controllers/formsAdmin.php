<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/csvHelper.php";
require_once "$componentPath/helpers/formPageElementDecorator.php";
require_once "$componentPath/helpers/formResponseActivityHelper.php";
require_once "$componentPath/helpers/formsAuth.php";
require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/relationalCrudHelper.php";
require_once "$componentPath/helpers/responsesCsvDecorator.php";
require_once "$componentPath/helpers/sortableResponses.php";
require_once "$componentPath/models/form.php";
require_once "$componentPath/models/formResponse.php";

use Components\Forms\Helpers\CsvHelper;
use Components\Forms\Helpers\FormPageElementDecorator as ElementDecorator;
use Components\Forms\Helpers\FormResponseActivityHelper;
use Components\Forms\Helpers\FormsAuth as AuthHelper;
use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\RelationalCrudHelper as CrudHelper;
use Components\Forms\Helpers\ResponsesCsvDecorator as CsvDecorator;
use Components\Forms\Helpers\SortableResponses;
use Components\Forms\Models\Form;
use Components\Forms\Models\FormResponse;
use Hubzero\Content\Server;
use Hubzero\Component\SiteController;

class FormsAdmin extends SiteController
{

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'form_id',
		'response_id'
	];

	/**
	 * Executes the requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->_auth = new AuthHelper();
		$this->_bouncer = new PageBouncer([
			'component' => $this->_option
		]);
		$this->_crudHelper = new CrudHelper(['controller' => $this]);
		$this->_csvHelper = new CsvHelper();
		$this->_decorator = new ElementDecorator();
		$this->_fileServer = new Server();
		$this->_params = new Params(
			['whitelist' => self::$_paramWhitelist]
		);
		$this->_responseActivity = new FormResponseActivityHelper();
		$this->_routes = new RoutesHelper();

		parent::execute();
	}

	/**
	 * Renders users' responses for given form
	 *
	 * @return   void
	 */
	public function responsesTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$formId = $this->_params->getInt('form_id');
		$form = Form::oneOrFail($formId);

		$responses = $form->getResponses()
			->paginated('limitstart', 'limit');
		$responses = $this->_sortResponses($responses);
		$responsesCopy = clone $responses;
		$responseIds = array_map(function($response) {
			return $response['id'];
		}, $form->getResponses()->rows()->toArray());
		$responseListUrl = $this->_routes->formsResponseList($formId);
		$responsesEmailUrl = $this->_routes->responsesEmailUrl($formId, $responseIds);
		$responsesTagsUrl = $this->_routes->responsesTagsUrl($formId, $responseIds);

		$this->view
			->set('responsesEmailUrl', $responsesEmailUrl)
			->set('responsesTagsUrl', $responsesTagsUrl)
			->set('form', $form)
			->set('responseListUrl', $responseListUrl)
			->set('responses', $responses)
			->display();
	}

	/**
	 * Sort responses using given field and direction
	 *
	 * @param    object   $responses   Form's responses
	 * @return   void
	 */
	protected function _sortResponses($responses)
	{
		$sortDirection = $this->_params->getString('sort_direction', 'asc');
		$sortField = $this->_params->getString('sort_field', 'id');
		$sortingCriteria = ['field' => $sortField, 'direction' => $sortDirection];

		$this->view->set('sortingCriteria', $sortingCriteria);
		$sortableResponses = new SortableResponses(['responses' => $responses]);
		$sortableResponses->order($sortField, $sortDirection);

		return $sortableResponses;
	}

	/**
	 * Renders user's responses to form's fields
	 *
	 * @return   void
	 */
	public function fieldResponsesTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$isComponentAdmin = $this->_auth->currentCanCreate();
		$responseId = $this->_params->getInt('response_id');
		$response = FormResponse::oneOrFail($responseId);
		$form = $response->getForm();
		$pageElements = $form->getFieldsOrdered();
		$userId = $response->get('user_id');
		$decoratedPageElements = $this->_decorator->decorateForRendering($pageElements, $userId);
		$reponseAcceptanceUrl = $this->_routes->responseApprovalUrl();

		foreach ($pageElements as $element)
		{
			$element->_returnDefault = false;
		}

		$this->view
			->set('acceptanceAction', $reponseAcceptanceUrl)
			->set('form', $form)
			->set('pageElements', $decoratedPageElements)
			->set('response', $response)
			->set('userIsAdmin', $isComponentAdmin)
			->display();
	}

	/**
	 * Updates approval status of given form response
	 *
	 * @return   void
	 */
	public function approveTask()
	{
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$responseAccepted = !!$this->_params->get('accepted');
		$accepted = $responseAccepted ? Date::toSql() : null;
		$responseId = $this->_params->getInt('response_id');
		$response = FormResponse::oneOrFail($responseId);
		$formId = $response->getFormId();

		$response->set('reviewed_by', User::get('id'));
		$response->set('accepted', $accepted);

		if ($response->save())
		{
			$this->_responseActivity->logReview($responseId, $responseAccepted);
			$forwardingUrl = $this->_routes->formsResponseList($formId);
			$message = Lang::txt('Response acceptance udpated');
			$this->_crudHelper->successfulUpdate($forwardingUrl, $message);
		}
		else
		{
			$forwardingUrl = $this->_routes->responseFeedUrl($responseId);
			$message = Lang::txt('The issues below prevented the response from being udpated.');
			$this->_crudHelper->failedBatchUpdate($forwardingUrl, $response, $message);
		}
	}

	/**
	 * Exports users' responses to CSV
	 *
	 * @return   void
	 */
	public function exportResponsesTask()
	{
		$formId = $this->_params->getInt('form_id');
		$form = Form::oneOrFail($formId);

		$this->_bouncer->redirectUnlessCanEditForm($form);

		$responses = $form->getResponses()->rows();
		$csvResponses = new CsvDecorator(['responses' => $responses]);
		$csvFile = $this->_csvHelper->generateCsv('responses', $csvResponses);

		if (!$this->_serve($csvFile->getPath()))
		{
			App::abort(500, Lang::txt('COM_FORMS_NOTICES_RESPONSES_EXPORT_ERROR'));
		}

		exit;
	}

	/**
	 * Serves file at given path
	 *
	 * @return   bool
	 */
	protected function _serve($filePath)
	{
		$server = $this->_fileServer;

		$server->filename($filePath);
		$server->disposition('attachment');
		$server->acceptranges(false);

		return $server->serve();
	}

}
