<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Tests;

use Plugins\Wiki\Parserdefault\Macros\Macrolist;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/wikiparser.php';
require_once dirname(__DIR__) . '/macros/macrolist.php';

/**
 * The macro list documents every macro it finds among the macro files.
 */
class MacroListTest extends TestCase
{
    /**
     * @return  void
     */
    public function testEveryMacroFileIsFound(): void
    {
        $list = new \ReflectionClass(Macrolist::class)->newInstanceWithoutConstructor();
        $files = new \ReflectionMethod($list, 'files');
        $files->setAccessible(true);

        $found = $files->invoke($list, dirname(__DIR__) . '/macros');

        $this->assertArrayHasKey('anchor', $found);
        $this->assertArrayHasKey('macrolist', $found);
        $this->assertArrayNotHasKey('helloworld', $found);
    }
}
