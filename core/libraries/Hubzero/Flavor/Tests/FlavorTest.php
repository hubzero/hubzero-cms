<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Flavor\Tests;

use Hubzero\Flavor\Applier;
use Hubzero\Flavor\Finder;
use Hubzero\Flavor\Flavor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Flavor files: reading, cascading, and saying no to what is not a lever
 */
class FlavorTest extends TestCase
{
    /**
     * The fixture directories
     *
     * @param   string  $name
     * @return  string
     */
    protected function files($name)
    {
        return __DIR__ . '/Files/' . $name;
    }

    /**
     * A finder reads every flavor in a directory
     *
     * @return  void
     */
    public function testFindsEveryFlavorInADirectory()
    {
        $finder = new Finder([$this->files('shipped')]);

        $this->assertSame(['plain', 'tooled'], array_keys($finder->all()));
        $this->assertSame('The base', $finder->find('plain')->description());
        $this->assertNull($finder->find('no-such'));
    }

    /**
     * A child starts from its parent and overrides: scalars replace, an
     * enable removes from the inherited disable, params merge key by key,
     * states merge alias by alias, a layout replaces the layout
     *
     * @return  void
     */
    public function testAChildCascadesOntoItsParent()
    {
        $tooled = new Finder([$this->files('shipped')])->find('tooled');

        $this->assertNull($tooled->parent(), 'the cascade is done once resolved');
        $this->assertSame('The base, with tools', $tooled->description());
        $this->assertSame('meridian', $tooled->get('template'), 'a scalar the child does not set is inherited');

        $this->assertSame(['com_tools'], $tooled->get('components', [], 'enable'));
        $this->assertSame(['com_usage'], $tooled->get('components', [], 'disable'), 'enabling removes from disable');

        $this->assertSame(['mod_mytools'], $tooled->get('modules', [], 'enable'));
        $this->assertArrayNotHasKey('disable', $tooled->get('modules'));
        $this->assertSame(
            ['show_tools' => '1', 'limit' => '5'],
            $tooled->get('modules', [], 'params')['mod_mycontributions'],
            'params merge key by key',
        );

        $this->assertSame(['tools' => 0], $tooled->get('kb', [], 'categories'), 'an untouched state is kept');
        $this->assertSame(['webdav' => 1], $tooled->get('kb', [], 'articles'));
        $this->assertSame(['tools' => ['contributable' => 1]], $tooled->get('resource_types'));
        $this->assertCount(3, $tooled->get('dashboard', [], 'tiles'), 'a layout replaces the layout');
    }

    /**
     * The first directory's definition of a name wins, and a flavor there
     * may extend one defined further down the list
     *
     * @return  void
     */
    public function testAnEarlierDirectoryRedefinesAndExtendsAcrossDirectories()
    {
        $finder = new Finder([$this->files('local'), $this->files('shipped')]);
        $all    = $finder->all();

        $this->assertSame(['mine', 'plain', 'tooled'], array_keys($all));
        $this->assertSame('lucent', $all['plain']->get('template'), 'the local plain replaces the shipped one');
        $this->assertArrayNotHasKey('components', $all['plain']->toArray(), 'replaced, not merged');

        // tooled extends plain - and plain is now the local one
        $this->assertSame('lucent', $all['tooled']->get('template'));

        // mine extends tooled, which is shipped
        $this->assertSame('lucent', $all['mine']->get('template'));
        $this->assertSame(['com_tools'], $all['mine']->get('components', [], 'enable'));
        $this->assertSame(['welcome' => 0], $all['mine']->get('content', [], 'articles'));
    }

    /**
     * A YAML file reads like a JSON one, and both may sit in one directory;
     * a flavor in either may extend one in the other
     *
     * @return  void
     */
    public function testYamlAndJsonReadAlike()
    {
        $finder = new Finder([$this->files('yaml'), $this->files('shipped')]);
        $all    = $finder->all();

        $this->assertSame(['plain', 'quiet', 'quieter', 'tooled'], array_keys($all));

        // quiet (yml) extends plain (json, other directory)
        $quiet = $all['quiet'];
        $this->assertSame('meridian', $quiet->get('template'));
        $this->assertSame(
            ['com_tools', 'com_usage', 'com_poll'],
            $quiet->get('components', [], 'disable'),
        );
        $this->assertSame(['community/poll' => 0], $quiet->get('menu', [], 'items'));
        $this->assertStringEndsWith('extra.yml', $quiet->file());

        // quieter (json, same directory) extends quiet (yml): menu items merge by path
        $this->assertSame(
            ['community/poll' => 0, 'community/poll/archive' => 0],
            $all['quieter']->get('menu', [], 'items'),
        );

        // module instances merge by id or title, alongside the module switches
        $this->assertSame(['Latest Poll' => 0, 92 => 0], $quiet->get('modules', [], 'items'));
        $this->assertSame(['Latest Poll' => 0, 92 => 1], $all['quieter']->get('modules', [], 'items'));
        $this->assertSame(['mod_mytools'], $all['quieter']->get('modules', [], 'disable'), 'switches kept');
        $this->assertSame(['com_tools', 'com_usage', 'com_poll'], $all['quieter']->get('components', [], 'disable'));
    }

    /**
     * A file that does not parse is refused with its name and the reason
     *
     * @return  void
     */
    public function testAFileThatDoesNotParseIsRefused()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('broken.yml does not read as flavors');

        new Finder([$this->files('broken')])->all();
    }

    /**
     * Extending in a circle is refused with the circle named
     *
     * @return  void
     */
    public function testACircleIsRefused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('circle');

        new Finder([$this->files('looped')])->all();
    }

    /**
     * Extending something not defined anywhere is refused
     *
     * @return  void
     */
    public function testAnUnknownParentIsRefused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("extends 'ghost'");

        $orphan = new Flavor('orphan', ['extends' => 'ghost']);
        $this->assertSame('ghost', $orphan->parent());

        $finder = new Finder([$this->files('local'), $this->files('shipped')]);
        // Nothing in the fixtures does this, so resolve one by hand through a
        // finder that only knows the fixtures
        $method = new \ReflectionMethod($finder, 'resolve');
        $method->setAccessible(true);
        $method->invoke($finder, 'orphan', ['orphan' => $orphan], []);
    }

    /**
     * A lever that is not one is said to be, by name, with the list
     *
     * @return  void
     */
    public function testAnUnknownLeverIsRefused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'templates' is not a lever");

        new Flavor('typo', ['templates' => 'meridian']);
    }

    /**
     * A key that is not under its lever is said to be, too
     *
     * @return  void
     */
    public function testAnUnknownKeyUnderALeverIsRefused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'components.params' is not a lever");

        new Flavor('typo', ['components' => ['params' => []]]);
    }

    /**
     * A list that is not a list of names is refused
     *
     * @return  void
     */
    public function testAMisshapenLeverIsRefused()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'components.disable' should be a list of names");

        new Flavor('shape', ['components' => ['disable' => 'com_tools']]);
    }

    /**
     * Tiles are laid out two rows high, in column order, and listed column
     * by column whatever order they were given in
     *
     * @return  void
     */
    public function testTilesAreLaidOutByColumn()
    {
        $laid = Applier::layout([
            ['module' => 37, 'col' => 3],
            ['module' => 44, 'col' => 1],
            ['module' => 41, 'col' => 3],
            ['module' => 35, 'col' => 1],
        ]);

        $this->assertSame([
            ['module' => 44, 'col' => 1, 'row' => 1, 'size_x' => 1, 'size_y' => 2],
            ['module' => 35, 'col' => 1, 'row' => 3, 'size_x' => 1, 'size_y' => 2],
            ['module' => 37, 'col' => 3, 'row' => 1, 'size_x' => 1, 'size_y' => 2],
            ['module' => 41, 'col' => 3, 'row' => 3, 'size_x' => 1, 'size_y' => 2],
        ], $laid);
    }

    /**
     * The shipped flavors read, and full is default with the tools back on
     *
     * @return  void
     */
    public function testTheShippedFlavorsResolve()
    {
        $finder = new Finder([dirname(dirname(dirname(dirname(__DIR__)))) . '/flavors']);
        $all    = $finder->all();

        $this->assertArrayHasKey('default', $all);
        $this->assertArrayHasKey('full', $all);

        $default = $all['default'];
        $full    = $all['full'];

        $this->assertContains('com_tools', $default->get('components', [], 'disable'));
        $this->assertContains('com_tools', $full->get('components', [], 'enable'));
        $this->assertArrayNotHasKey('disable', $full->get('components'), 'full disables nothing default did');
        $this->assertSame('1', $full->get('modules', [], 'params')['mod_mycontributions']['show_tools']);
        $this->assertSame(
            $default->get('plugins'),
            $full->get('plugins'),
            'what full does not mention it inherits',
        );
    }
}
