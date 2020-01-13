<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/email.php";
require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/respondentsHelper.php";
require_once "$componentPath/helpers/sortableResponses.php";
require_once "$componentPath/helpers/virtualCrudHelper.php";
require_once "$componentPath/models/form.php";

use Components\Forms\Helpers\CrudHelper as BaseCrudHelper;
use Components\Forms\Helpers\Email;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\RespondentsHelper;
use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\SortableResponses;
use Components\Forms\Helpers\VirtualCrudHelper;
use Components\Forms\Models\Form;
use Hubzero\Component\SiteController;

class RespondentEmails extends SiteController
{

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'email',
		'form_id',
		'response_ids'
	];

	/**
	 * Executes the requested task
	 *
	 * @return   void
	 */
	public function execute()
	{
		$this->_bouncer = new PageBouncer([
			'component' => $this->_option
		]);
		$this->_bCrudHelper = new BaseCrudHelper([
			'errorSummary' => Lang::txt('COM_FORMS_EMAIL_SEND_ERROR')
		]);
		$this->_vCrudHelper = new VirtualCrudHelper([]);
		$this->_params = new Params(
			['whitelist' => self::$_paramWhitelist]
		);
		$this->_respondentsHelper = new RespondentsHelper();
		$this->_routes = new RoutesHelper();

		parent::execute();
	}

	/**
	 * Renders respondent emailing page
	 *
	 * @return   void
	 */
  public function responsesTask($email = null)
  {
		$formId = $this->_params->getVar('form_id');
		$form = Form::oneOrFail($formId);

		$this->_bouncer->redirectUnlessCanEditForm($form);
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$email = $email ? $email : new Email();
		$responseIds = $this->_params->getVar('response_ids');
		$responses = $form->getResponses()
			->whereIn('id', $responseIds);
		$responses = $this->_sortResponses($responses);
		$sendEmailUrl = $this->_routes->sendResponsesEmailUrl();
		$sortingAction = $this->_routes->responsesEmailUrl($formId, $responseIds);

		$this->view
			->set('email', $email)
			->set('form', $form)
			->set('responses', $responses)
			->set('responseIds', $responseIds)
			->set('sendEmailUrl', $sendEmailUrl)
			->set('sortingAction', $sortingAction)
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
	 * Send email to given user(s)
	 *
	 * @return   void
	 */
	public function sendTask()
	{
		$formId = $this->_params->getVar('form_id');
		$form = Form::oneOrFail($formId);

		$this->_bouncer->redirectUnlessCanEditForm($form);
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$emailData = $this->_params->get('email');
		$responseIds = $this->_params->get('response_ids');
		$emailData['reply_to'] = array_filter(explode(',', $emailData['reply_to']));
		$emailData['to'] = $this->_respondentsHelper->getEmails($responseIds);

		$email = new Email($emailData);
		$email->send();

		if ($email->sentSuccessfully())
		{
			$emailSentMessage = Lang::txt('COM_FORMS_EMAIL_SENT');
			$responseList = $this->_routes->formsResponseList($formId);
			$this->_vCrudHelper->successfulCreate($responseList, $emailSentMessage);
		}
		else
		{
			$this->_bCrudHelper->failedCreate($email);
			$this->setView('respondentemails', 'responses');
			$this->responsesTask($email);
		}
	}

}
