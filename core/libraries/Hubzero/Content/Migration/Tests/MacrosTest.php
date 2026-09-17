<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * The migration macros call methods that are there
 *
 * A macro runs during an install or a migration, which is the worst place to
 * find out that a method it calls on itself was moved somewhere else: the
 * schema is half built and the fatal comes out of a migration, not a test.
 * AddComponentEntry called a rebuildMenu() for a day after the rebuild moved
 * into Hubzero\Menu\ComponentRoute, and every fresh install died on it while
 * every existing hub - which never runs that migration again - was fine.
 */
class MacrosTest extends TestCase
{
    /**
     * Where the macros live
     *
     * @var  string
     */
    private const MACROS = __DIR__ . '/../Macros';

    /**
     * What PHP gives a class without anyone declaring it
     *
     * @var  array
     */
    private const MAGIC = array('__construct', '__call', '__get', '__set', '__invoke', '__toString');

    /**
     * Every macro file, as a class name
     *
     * @return  array
     */
    public static function macroProvider(): array
    {
        $macros = array();

        foreach (glob(self::MACROS . '/*.php') ?: array() as $file) {
            $name = basename($file, '.php');
            $macros[$name] = array('Hubzero\\Content\\Migration\\Macros\\' . $name);
        }

        return $macros;
    }

    /**
     * Every $this->method() a macro calls is a method it has
     *
     * @param   string  $class
     * @return  void
     */
    #[Test]
    #[DataProvider('macroProvider')]
    public function testEveryCallOnSelfResolves($class)
    {
        $this->assertTrue(class_exists($class), $class . ' should exist');

        $reflection = new ReflectionClass($class);
        $source     = file_get_contents($reflection->getFileName());

        preg_match_all('/\$this->([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $source, $matches);

        $missing = array();

        foreach (array_unique($matches[1]) as $method) {
            if (in_array($method, self::MAGIC) || $reflection->hasMethod($method)) {
                continue;
            }

            // A property holding a closure is called the same way
            if ($reflection->hasProperty($method)) {
                continue;
            }

            $missing[] = $method . '()';
        }

        $this->assertSame(
            array(),
            $missing,
            $reflection->getShortName() . ' calls ' . implode(', ', $missing) . ' on itself, which it does not have'
        );
    }

    /**
     * Every macro can be invoked - it is what a migration calls
     *
     * @param   string  $class
     * @return  void
     */
    #[Test]
    #[DataProvider('macroProvider')]
    public function testEveryMacroIsInvokable($class)
    {
        $reflection = new ReflectionClass($class);

        $this->assertTrue(
            $reflection->hasMethod('__invoke'),
            $reflection->getShortName() . ' should have an __invoke() for a migration to call'
        );
    }
}
