<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Site\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/helpers/params.php";
require_once "$componentPath/helpers/virtualCrudHelper.php";
require_once "$componentPath/models/form.php";
require_once "$componentPath/models/responseComment.php";

use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\Params;
use Components\Forms\Helpers\VirtualCrudHelper;
use Components\Forms\Models\Form;
use Components\Forms\Models\ResponseComment;
use Hubzero\Component\SiteController;

class FeedComments extends SiteController
{

	/**
	 * Parameter whitelist
	 *
	 * @var  array
	 */
	protected static $_paramWhitelist = [
		'content'
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
		$this->_params = new Params([
			'whitelist' => self::$_paramWhitelist
		]);
		$this->_routes = new RoutesHelper();
		$this->_vCrudHelper = new VirtualCrudHelper([
			'errorSummary' => Lang::txt('COM_FORMS_NOTICES_RESPONSE_COMMENT_CREATE_ERROR')
		]);

		parent::execute();
	}

	/**
	 * Triggers response comment creation
	 *
	 * @return   void
	 */
	public function createResponseCommentTask()
	{
		$formId = $this->_params->getVar('form_id');
		$form = Form::oneOrFail($formId);

		$this->_bouncer->redirectUnlessCanEditForm($form);
		$this->_bouncer->redirectUnlessAuthorized('core.create');

		$commentContent = $this->_params->getString('comment');
		$responseId = $this->_params->getInt('response_id');

		$responseComment = new ResponseComment([
			'content' => $commentContent,
			'response_id' => $responseId
		]);

		if ($responseComment->save())
		{
			$feedUrl = $this->_routes->responseFeedUrl($responseId);
			$successMessage = Lang::txt('COM_FORMS_NOTICES_RESPONSE_COMMENT_CREATE_SUCCESS');
			$this->_vCrudHelper->successfulCreate($feedUrl, $successMessage);
		}
		else
		{
			$forwardData = ['comment' => $commentContent];
			$feedUrl = $this->_routes->responseFeedUrl($responseId, $forwardData);
			$this->_vCrudHelper->failedCreate($responseComment, $feedUrl);
		}
	}

}
