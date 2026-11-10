<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests;

use Components\Search\Helpers\ConfigHelper;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/helpers/confighelper.php';

/**
 * A search path's [[Macro(argument)]] markers are filled in from the record
 * being indexed. Hubs write the macro names in their configuration, so a
 * name keeps working however the method behind it is spelled.
 */
class ConfigHelperMacroTest extends TestCase
{
    /**
     * @return  void
     */
    public function testMacrosAreFilledInFromTheRecord(): void
    {
        $row = (object) ['alias' => 'my-page', 'scope_id' => 42];

        $path = (new ConfigHelper())->parse('/members/[[Member_id()]]/[[Field(alias)]]', $row);

        $this->assertSame("\n/members/42/my-page", $path);
    }

    /**
     * @return  void
     */
    public function testMacroNamesAreCaseInsensitive(): void
    {
        $row = (object) ['alias' => 'my-page', 'scope_id' => 42];

        $path = (new ConfigHelper())->parse('/members/[[member_id()]]/[[field(alias)]]', $row);

        $this->assertSame("\n/members/42/my-page", $path);
    }
}
