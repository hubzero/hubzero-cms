<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/emailFactory.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\EmailFactory;

class EmailFactoryTest extends Basic
{

	public function testOneReturnsMessageInstance()
	{
		$factory = new EmailFactory();

		$email = $factory->one();

		$this->assertEquals('Hubzero\Mail\Message', get_class($email));
	}

}
