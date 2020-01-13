<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/email.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\Email;
use Components\Forms\Tests\Traits\canMock;

class EmailTest extends Basic
{
	use canMock;

	public function testSendInstantiatesMessage()
	{
		$message = $this->mock([
			'class' => 'Message', 'methods' => ['setSubject', 'addFrom', 'addPart']
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory, 'title' => '', 'reply_to' => [], 'content' => '', 'to' => []
		]);

		$emailFactory->expects($this->once())
			->method('one');

		$email->send();
	}

	public function testSendPopulatesMessage()
	{
		$message = $this->mock([
			'class' => 'Message', 'methods' => ['setSubject', 'addFrom', 'addPart']
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory, 'title' => '', 'reply_to' => [], 'content' => '', 'to' => []
		]);

		$message->expects($this->once())->method('setSubject');
		$message->expects($this->once())->method('addFrom');
		$message->expects($this->once())->method('addPart');

		$email->send();
	}

	public function testSendAddsReplyToEmails()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => ['setSubject', 'addFrom', 'addPart', 'addReplyTo']
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => '',
			'reply_to' => ['a@e','b@e'],
			'content' => '',
		 	'to' => []
		]);

		$message->expects($this->exactly(2))
			->method('addReplyTo')
			->withConsecutive(['a@e'], ['b@e']);

		$email->send();
	}

	public function testSendAddsToEmails()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => ['setSubject', 'addFrom', 'addPart', 'addTo']
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => '',
			'reply_to' => [],
			'content' => '',
		 	'to' => ['a@e','b@e']
		]);

		$message->expects($this->exactly(2))
			->method('addTo')
			->withConsecutive(['a@e'], ['b@e']);

		$email->send();
	}

	public function testSendDoesNotInvokesSendIfInvalid()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => ['setSubject', 'addFrom', 'addPart']
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => '',
			'reply_to' => [],
			'content' => '',
		 	'to' => []
		]);

		$message->expects($this->exactly(0))
			->method('send');

		$email->send();
	}

	public function testSendInvokesSendIfValid()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => [
				'setSubject', 'addFrom', 'addPart', 'addTo', 'addReplyTo', 'send'
			]
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => 'title',
			'reply_to' => ['reply@e'],
			'content' => 'content',
		 	'to' => ['to@e']
		]);

		$message->expects($this->once())
			->method('send');

		$email->send();
	}

	public function testValidIsTrueIfRequiredDataProvided()
	{
		$requiredData = [
			'title' => 'title',
			'reply_to' => ['reply@e'],
			'content' => 'content',
		 	'to' => ['to@e']
		];
		$email = new Email($requiredData);

		$isValid = $email->isValid();

		$this->assertEquals(true, $isValid);
	}

	public function testValidIsFalseIfRequiredDataAbsent()
	{
		$requiredData = [
			'title' => '',
			'reply_to' => [],
			'content' => '',
		 	'to' => []
		];
		$email = new Email($requiredData);

		$isValid = $email->isValid();

		$this->assertEquals(false, $isValid);
	}

	public function testGetErrorsReturnsCorrectErrors()
	{
		$expectedErrors = [
			'title COM_FORMS_EMAIL_NON_EMPTY',
			'reply to COM_FORMS_EMAIL_NON_EMPTY',
			'content COM_FORMS_EMAIL_NON_EMPTY',
			'to COM_FORMS_EMAIL_NON_EMPTY'
		];
		$invalidData = [
			'title' => '', 'reply_to' => [], 'content' => '', 'to' => []
		];
		$email = new Email($invalidData);

		$errors = $email->getErrors();

		$this->assertEquals($expectedErrors, $errors);
	}

	public function testSentSuccessfullyIsTrueIfSentValidAndNoFailures()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => [
				'setSubject', 'addFrom', 'addPart',
				'addTo', 'addReplyTo', 'send', 'getFailures' => []
			]
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => 'title',
			'reply_to' => ['reply@e'],
			'content' => 'content',
		 	'to' => ['to@e']
		]);

		$email->send();
		$sentSuccessfully = $email->sentSuccessfully();

		$this->assertEquals(true, $sentSuccessfully);
	}

	public function testSentSuccessfullyIsFalseIfInvalid()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => [
				'setSubject', 'addFrom', 'addPart',
				'addTo', 'addReplyTo', 'send', 'getFailures' => []
			]
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => '',
			'reply_to' => [],
			'content' => '',
		 	'to' => []
		]);

		$email->send();
		$sentSuccessfully = $email->sentSuccessfully();

		$this->assertEquals(false, $sentSuccessfully);
	}

	public function testSentSuccessfullyIsFalseIfFailures()
	{
		$message = $this->mock([
			'class' => 'Message',
			'methods' => [
				'setSubject', 'addFrom', 'addPart', 'addTo', 'addReplyTo', 'send', 'getFailures' => [true]
			]
		]);
		$emailFactory = $this->mock([
			'class' => 'EmailFactory', 'methods' => ['one' => $message]
		]);
		$email = new Email([
			'factory' => $emailFactory,
			'title' => 'title',
			'reply_to' => ['reply@e'],
			'content' => 'content',
		 	'to' => ['to@e']
		]);

		$sentSuccessfully = $email->sentSuccessfully();

		$this->assertEquals(false, $sentSuccessfully);
	}

}
