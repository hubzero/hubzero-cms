<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Schema\ColumnDefinitionTrait;

/**
 * A class with the column definition trait, its protected helpers made
 * public, for testing the trait on its own
 */
class ColumnDefinitionTester
{
    use ColumnDefinitionTrait {
        hasAbstractType as public;
        compileAbstractTypeToSql as public;
    }
}
