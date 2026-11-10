<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Domainrestriction\Tests;

use Hubzero\Config\Registry;
use Plugins\User\Domainrestriction\Domainrestriction;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/domainrestriction.php';

/**
 * The plugin reads its allowed and refused domains, top-level domains and
 * addresses from its settings into lists before checking a new account's
 * email address against them.
 */
class ListsTest extends TestCase
{
    /**
     * @return  void
     */
    public function testSettingsBecomeTheLists(): void
    {
        $plugin = new \ReflectionClass(Domainrestriction::class)->newInstanceWithoutConstructor();
        $plugin->params = new Registry([
            'tld'       => 'edu, org.',
            'domain'    => 'example.edu',
            'email'     => 'boss@example.edu',
            'badtld'    => 'xyz',
            'baddomain' => 'spam.example, junk.example',
            'bademail'  => 'bad@example.edu',
        ]);

        $sort = new \ReflectionMethod($plugin, 'sortParams');
        $sort->setAccessible(true);
        $sort->invoke($plugin);

        $list = function ($name) use ($plugin) {
            $property = new \ReflectionProperty($plugin, $name);
            $property->setAccessible(true);
            return $property->getValue($plugin);
        };

        $this->assertSame(['edu', 'org'], $list('tlds'));
        $this->assertSame(['example.edu'], $list('domains'));
        $this->assertSame(['boss@example.edu'], $list('emails'));
        $this->assertSame(['xyz'], $list('badtlds'));
        $this->assertSame(['spam.example', 'junk.example'], $list('baddomains'));
        $this->assertSame(['bad@example.edu'], $list('bademails'));
    }

    /**
     * An empty setting, or one with empty entries, gives a list without them
     *
     * @return  void
     */
    public function testEmptyEntriesAreDropped(): void
    {
        $plugin = new \ReflectionClass(Domainrestriction::class)->newInstanceWithoutConstructor();
        $plugin->params = new Registry(['tld' => 'edu,, org', 'domain' => '']);

        $sort = new \ReflectionMethod($plugin, 'sortParams');
        $sort->setAccessible(true);
        $sort->invoke($plugin);

        $tlds = new \ReflectionProperty($plugin, 'tlds');
        $tlds->setAccessible(true);
        $domains = new \ReflectionProperty($plugin, 'domains');
        $domains->setAccessible(true);

        $this->assertSame(['edu', 'org'], $tlds->getValue($plugin));
        $this->assertSame([], $domains->getValue($plugin));
    }
}
