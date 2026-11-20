<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Helpers\ErrorMessageHelper;
use Hubzero\Test\Basic;

class ErrorMessageHelperTest extends Basic
{
    public function testGenerateErrorMessage(): void
    {
        $helper = new ErrorMessageHelper();
        $errors = ['a', 'b', 'c'];

        $message = $helper->generateErrorMessage($errors);

        $this->assertEquals('• a<br/><br/>• b<br/><br/>• c', $message);
    }
}
