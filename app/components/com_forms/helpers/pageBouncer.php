<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsAuth.php";
require_once "$componentPath/helpers/mockProxy.php";

use Components\Forms\Helpers\FormsAuth;
use Components\Forms\Helpers\MockProxy;
use Hubzero\Utility\Arr;

class PageBouncer
{

	protected $_permitter,	$_router;

	/**
	 * Constructs PageBouncer instance
	 *
	 * @param    array   $args   Instantiation state
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_permitter = Arr::getValue($args, 'permitter', new FormsAuth());
		$this->_router = Arr::getValue($args, 'router', new MockProxy(['class' => 'App']));
	}

	/**
	 * Redirects user if they cannot edit given form
	 *
	 * @param    object   $form   Form record
	 * @param    string   $url    URL to redirect to
	 * @return   void
	 */
	public function redirectUnlessCanEditForm($form, $url = null)
	{
		$url = $url ? $url : '/forms';

		$this->redirectUnlessAuthorized('core.create', $url);

		$canEdit = $this->_permitter->canCurrentUserEditForm($form);

		if (!$canEdit)
		{
			$this->_router->redirect($url);
		}
	}

	/**
	 * Redirects user if the form is disabled
	 *
	 * @param    object   $form   Form record
	 * @param    string   $url    URL to redirect to
	 * @return   void
	 */
	public function redirectIfFormDisabled($form, $url = null)
	{
		$url = $url ? $url : '/forms';

		if ($form->get('disabled'))
		{
			$this->_router->redirect($url);
		}
	}

	/**
	 * Redirects user if form response has been submitted
	 *
	 * @param    object   $response   Form response
	 * @param    string   $url    URL to redirect to
	 * @return   void
	 */
	public function redirectIfResponseSubmitted($response, $url = '/forms')
	{
		$url = $url ? $url : '/forms';

		if ($response->get('submitted'))
		{
			$this->_router->redirect($url);
		}
	}

	/**
	 * Redirects user if the form is not open or is disabled
	 *
	 * @param    object   $form   Form record
	 * @param    string   $url    URL to redirect to
	 * @return   void
	 */
	public function redirectIfFormNotOpen($form, $url = null)
	{
		$url = $url ? $url : '/forms';

		if (!$form->isOpen() || $form->get('disabled'))
		{
			$this->_router->redirect($url);
		}
	}

	/**
	 * Redirects user if they cannot view form response
	 *
	 * @param    object   $response   Form response
	 * @param    string   $url        URL to redirect to
	 * @return   void
	 */
	public function redirectUnlessCanViewResponse($response, $url = null)
	{
		$url = $url ? $url : '/forms';

		$canView = $this->_permitter->canCurrentUserViewResponse($response);

		if (!$canView)
		{
			$this->_router->redirect($url);
		}
	}

	/**
	 * Redirects users without given permission
	 *
	 * @param    string   $permission   Permission name
	 * @return   void
	 */
	public function redirectUnlessAuthorized($permission, $url = '/')
	{
		$isAuthorized = $this->_permitter->currentIsAuthorized($permission);

		if (!$isAuthorized)
		{
			$this->_router->redirect($url);
		}
	}

}
