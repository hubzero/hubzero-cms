<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

$componentPath = Component::path('com_search');

require_once "$componentPath/helpers/errorMessageHelper.php";

use Components\Search\Helpers\ErrorMessageHelper;
use Hubzero\Test\Basic;

class ErrorMessageHelperTest extends Basic
{

	public function testGenerateErrorMessage()
	{
		$helper = new ErrorMessageHelper();
		$errors = ['a', 'b', 'c'];

		$message = $helper->generateErrorMessage($errors);

		$this->assertEquals('• a<br/><br/>• b<br/><br/>• c', $message);
	}

}
