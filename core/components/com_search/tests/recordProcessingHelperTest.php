<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/recordProcessingHelper.php";
require_once "$componentPath/tests/traits/canMock.php";

use Components\Search\Helpers\RecordProcessingHelper as Helper;
use Components\Search\Tests\Traits\canMock;
use Hubzero\Test\Basic;

class RecordProcessingHelperTest extends Basic
{
	use canMock;

	public function testHandleSaveSuccessInvokesNotifySuccess()
	{
		$message = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
			'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('success')
			->with($message);

		$helper->handleSaveSuccess($message, '');
	}

	public function testHandleSaveSuccessInvokesAppRedirect()
	{
		$url = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
		 	'notify' => $notifyMock
		]);

		$appMock->expects($this->once())
			->method('redirect')
			->with($url);

		$helper->handleSaveSuccess('', $url);
	}

	public function testHandleSaveFailInvokesNotifyError()
	{
		$message = 'test';
		$controllerMock = $this->mock([
			'class' => 'Controller',
		 	'methods' => ['setView', 'newTask']
		]);
		$controllerMock->name = '';
		$errorHelperMock = $this->mock([
			'class' => 'ErrorMessageHelper',
		 	'methods' => ['generateErrorMessage' => $message]
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$recordMock = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors']
		]);
		$helper = new Helper([
			'controller' => $controllerMock,
		 	'error' => $errorHelperMock,
		 	'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('error')
			->with($message);

		$helper->handleSaveFail($recordMock);
	}

	public function testHandleSaveFailInvokesControllerNewTask()
	{
		$name = 'test';
		$controllerMock = $this->mock([
			'class' => 'Controller',
		 	'methods' => ['setView', 'newTask']
		]);
		$controllerMock->name = $name;
		$errorHelperMock = $this->mock([
			'class' => 'ErrorMessageHelper',
		 	'methods' => ['generateErrorMessage']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$recordMock = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors']
		]);
		$helper = new Helper([
			'controller' => $controllerMock,
		 	'error' => $errorHelperMock,
		 	'notify' => $notifyMock
		]);

		$controllerMock->expects($this->once())
			->method('setView')
			->with($name, 'new');

		$controllerMock->expects($this->once())
			->method('newTask')
			->with($recordMock);

		$helper->handleSaveFail($recordMock);
	}

	public function testHandleUpdateSuccessInvokesNotifySuccess()
	{
		$message = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
			'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('success')
			->with($message);

		$helper->handleUpdateSuccess($message, '');
	}

	public function testHandleUpdateSuccessInvokesAppRedirect()
	{
		$url = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
		 	'notify' => $notifyMock
		]);

		$appMock->expects($this->once())
			->method('redirect')
			->with($url);

		$helper->handleUpdateSuccess('', $url);
	}

	public function testHandleUpdateFailInvokesNotifyError()
	{
		$message = 'test';
		$controllerMock = $this->mock([
			'class' => 'Controller',
		 	'methods' => ['setView', 'editTask']
		]);
		$controllerMock->name = '';
		$errorHelperMock = $this->mock([
			'class' => 'ErrorMessageHelper',
		 	'methods' => ['generateErrorMessage' => $message]
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$recordMock = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors']
		]);
		$helper = new Helper([
			'controller' => $controllerMock,
		 	'error' => $errorHelperMock,
		 	'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('error')
			->with($message);

		$helper->handleUpdateFail($recordMock);
	}

	public function testHandleUpdateFailInvokesControllerEditTask()
	{
		$name = 'test';
		$controllerMock = $this->mock([
			'class' => 'Controller',
		 	'methods' => ['setView', 'editTask']
		]);
		$controllerMock->name = $name;
		$errorHelperMock = $this->mock([
			'class' => 'ErrorMessageHelper',
		 	'methods' => ['generateErrorMessage']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$recordMock = $this->mock([
			'class' => 'Relational', 'methods' => ['getErrors']
		]);
		$helper = new Helper([
			'controller' => $controllerMock,
		 	'error' => $errorHelperMock,
		 	'notify' => $notifyMock
		]);

		$controllerMock->expects($this->once())
			->method('setView')
			->with($name, 'edit');

		$controllerMock->expects($this->once())
			->method('editTask')
			->with($recordMock);

		$helper->handleUpdateFail($recordMock);
	}

	public function testHandleDestroySuccessInvokesNotifySuccess()
	{
		$message = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
			'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('success')
			->with($message);

		$helper->handleDestroySuccess($message, '');
	}

	public function testHandleDestroySuccessInvokesAppRedirect()
	{
		$url = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['success']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
		 	'notify' => $notifyMock
		]);

		$appMock->expects($this->once())
			->method('redirect')
			->with($url);

		$helper->handleDestroySuccess('', $url);
	}

	public function testHandleDestroyFailInvokesNotifyError()
	{
		$message = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
			'notify' => $notifyMock
		]);

		$notifyMock->expects($this->once())
			->method('error')
			->with($message);

		$helper->handleDestroyFail($message, '');
	}

	public function testHandleDestroyFailInvokesAppRedirect()
	{
		$url = 'test';
		$appMock = $this->mock([
			'class' => 'App', 'methods' => ['redirect']
		]);
		$notifyMock = $this->mock([
			'class' => 'Notify', 'methods' => ['error']
		]);
		$helper = new Helper([
			'controller' => 0,
			'app' => $appMock,
		 	'notify' => $notifyMock
		]);

		$appMock->expects($this->once())
			->method('redirect')
			->with($url);

		$helper->handleDestroyFail('', $url);
	}

}
