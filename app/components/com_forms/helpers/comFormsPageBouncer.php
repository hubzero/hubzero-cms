<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";
require_once "$componentPath/helpers/pageBouncer.php";

use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Helpers\FormsRouter as RoutesHelper;
use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class ComFormsPageBouncer extends PageBouncer
{

	/**
	 * Constructs PageBouncer instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_notify = Arr::getValue($args, 'notify', new MockProxy(['class' => 'Notify']));
		$this->_routes = new RoutesHelper();
		$this->_userHelper = Arr::getValue($args, 'user', new MockProxy(['class' => 'User']));
		parent::__construct($args);
	}

	/**
	 * Redirects user if prereqs' responses have not been accepted
	 *
	 * @param    object   $form   Form model
	 * @return   void
	 */
	public function redirectIfPrereqsNotAccepted($form)
	{
		$formId = $form->get('id');
		$userId = $this->_userHelper->get('id');
		$url = $this->_routes->formsDisplayUrl($formId);

		if (!$form->prereqsAccepted($userId))
		{
			$message = Lang::txt('COM_FORMS_NOTICES_FORMS_PREREQS_INCOMPLETE');
			$this->_notify->warning($message);
			$this->_router->redirect($url);
		}
	}

}
