<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/pageBouncer.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\PageBouncer;
use Components\Forms\Tests\Traits\canMock;

class PageBouncerTest extends Basic
{
	use canMock;

	public function testRedirectUnlessAuthorizedInvokesAuthorize()
	{
		$permitter = $this->mock([
			'class' => 'FormsAuth', 'methods' => ['currentIsAuthorized']
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'permitter' => $permitter,
			'router' => $router
		]);

		$permitter->expects($this->once())
			->method('currentIsAuthorized');

		$bouncer->redirectUnlessAuthorized('test');
	}

	public function testRedirectUnlessAuthorizedInvokesRedirect()
	{
		$permitter = $this->mock([
			'class' => 'FormsAuth', 'methods' => ['currentIsAuthorized']
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'permitter' => $permitter,
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectUnlessAuthorized('test');
	}

	public function testRedirectUnlessCanEditFormInvokesCurrentAuthorized()
	{
		$auth = $this->mock([
			'class' => 'FormsAuth',
			'methods' => ['currentIsAuthorized', 'canCurrentUserEditForm']
		]);
		$form = $this->mock(['class' => 'Form']);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'permitter' => $auth,
			'router' => $router
		]);

		$auth->expects($this->once())
			->method('currentIsAuthorized')
			->with('core.create');

		$bouncer->redirectUnlessCanEditForm($form);
	}

	public function testRedirectUnlessCanEditFormInvokesCanCurrentUserEditForm()
	{
		$auth = $this->mock([
			'class' => 'FormsAuth',
			'methods' => [
				'currentIsAuthorized' => true,
				'canCurrentUserEditForm' => true
			]
		]);
		$form = $this->mock(['class' => 'Form']);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'permitter' => $auth,
			'router' => $router
		]);

		$auth->expects($this->once())
			->method('canCurrentUserEditForm')
			->with($form);

		$bouncer->redirectUnlessCanEditForm($form);
	}

	public function testRedirectUnlessCanEditFormInvokesRedirectIfUserCantEditForm()
	{
		$auth = $this->mock([
			'class' => 'FormsAuth',
			'methods' => [
				'currentIsAuthorized' => true,
				'canCurrentUserEditForm' => false
			]
		]);
		$form = $this->mock(['class' => 'Form']);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'permitter' => $auth,
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectUnlessCanEditForm($form);
	}

	public function testRedirectIfFormDisabledChecksIfFormIsDisabled()
	{
		$form = $this->mock(['class' => 'Form', 'methods' => ['get']]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$form->expects($this->once())
			->method('get')
			->with('disabled');

		$bouncer->redirectIfFormDisabled($form);
	}

	public function testRedirectIfFormDisabledRedirectsIfFormIsDisabled()
	{
		$form = $this->mock([
			'class' => 'Form', 'methods' => ['get' => true]
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectIfFormDisabled($form);
	}

	public function testRedirectIfFormNotOpenChecksIfFormIsOpen()
	{
		$form = $this->mock([
			'class' => 'Form',
			'methods' => ['get' => false, 'isOpen' => true]
		]);
		$router = $this->mock(['class' => 'Router']);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$form->expects($this->once())
			->method('isOpen');

		$bouncer->redirectIfFormNotOpen($form);
	}

	public function testRedirectIfFormNotOpenChecksIfFormIsDisabled()
	{
		$form = $this->mock(['class' => 'Form',
			'methods' => ['get', 'isOpen' => 'false']
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$form->expects($this->once())
			->method('get')
			->with('disabled');

		$bouncer->redirectIfFormNotOpen($form);
	}

	public function testRedirectIfFormNotOpenRedirectsIfFormNotOpen()
	{
		$form = $this->mock(['class' => 'Form',
			'methods' => ['get', 'isOpen' => false]
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectIfFormNotOpen($form);
	}

	public function testRedirectIfFormNotOpenRedirectsIfFormDisabled()
	{
		$form = $this->mock(['class' => 'Form',
			'methods' => ['get' => true, 'isOpen' => true]
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'component' => 'com_forms',
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectIfFormNotOpen($form);
	}

	public function testRedirectUnlessCanViewResponseRedirectsIfUserCannotViewResponse()
	{
		$permitter = $this->mock([
			'class' => 'Permitter', 'methods' => ['canCurrentUserViewResponse' => false]
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'permitter' => $permitter,
			'router' => $router
		]);

		$router->expects($this->once())
			->method('redirect');

		$bouncer->redirectUnlessCanViewResponse(null);
	}

	public function testRedirectUnlessCanViewResponseDoesNothingIfUserCanViewResponse()
	{
		$permitter = $this->mock([
			'class' => 'Permitter', 'methods' => ['canCurrentUserViewResponse' => true]
		]);
		$router = $this->mock(['class' => 'Router', 'methods' => ['redirect']]);
		$bouncer = new PageBouncer([
			'permitter' => $permitter,
			'router' => $router
		]);

		$router->expects($this->never())
			->method('redirect');

		$bouncer->redirectUnlessCanViewResponse(null);
	}

}
