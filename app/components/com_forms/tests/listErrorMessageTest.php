<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/listErrorMessage.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\ListErrorMessage;

class ListErrorMessageTest extends Basic
{

	public function testWithoutIntroToStringReturnsCorrectString()
	{
		$errors = ['a', 'b', 'c'];

		$errorMessage = new ListErrorMessage(['errors' => $errors]);
		$message = $errorMessage->toString();
		$expectedMessage = ' <br/><br/>• a<br/>• b<br/>• c<br/><br/>';

		$this->assertEquals($expectedMessage, $message);
	}

	public function testWithIntroToStringReturnsCorrectString()
	{
		$errorIntro = 'Error intro:';
		$errors = ['a', 'b', 'c'];

		$errorMessage = new ListErrorMessage([
			'errorIntro' => $errorIntro,
			'errors' => $errors
		]);
		$message = $errorMessage->toString();
		$expectedMessage = 'Error intro: <br/><br/>• a<br/>• b<br/>• c<br/><br/>';

		$this->assertEquals($expectedMessage, $message);
	}

}
