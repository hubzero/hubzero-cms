<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests;

use Hubzero\Template\Positions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * What a template declares, and what it therefore renders
 *
 * The declarations are the contract between a module and a template: a module
 * sits in a position, the template asks for that position by name, and a name
 * the template never asks for renders nowhere. Reading them wrongly is not
 * visible on a page - the module simply is not there - so the reading is
 * tested rather than trusted.
 */
class PositionsTest extends TestCase
{
    /**
     * Start each test with nothing remembered
     *
     * @return  void
     */
    protected function setUp(): void
    {
        Positions::forget();
    }

    /**
     * A shipped template's declarations are read
     *
     * @return  void
     */
    #[Test]
    public function testAShippedTemplateDeclaresPositions()
    {
        $positions = Positions::declared('lucent');

        $this->assertNotEmpty($positions);
        $this->assertContains('footer', $positions);
        $this->assertContains('user3', $positions);
    }

    /**
     * Two templates are not assumed to declare the same thing
     *
     * This is the bug the whole class is for: mesozoic declares the four home
     * positions its front page modules sit in and lucent does not, so a hub
     * wearing lucent renders those modules nowhere.
     *
     * @return  void
     */
    #[Test]
    public function testTemplatesDifferInWhatTheyDeclare()
    {
        $this->assertTrue(Positions::declares('mesozoic', 'home-1'));
        $this->assertFalse(Positions::declares('lucent', 'home-1'));

        // And neither declares the position Joomla's own sample data uses
        $this->assertFalse(Positions::declares('mesozoic', 'position-7'));
        $this->assertFalse(Positions::declares('lucent', 'position-7'));
    }

    /**
     * A name that is not a template declares nothing, and reaches nothing
     *
     * @return  void
     */
    #[Test]
    public function testWhatIsNotATemplateDeclaresNothing()
    {
        $this->assertSame(array(), Positions::declared('nosuchtemplate'));
        $this->assertSame(array(), Positions::declared(''));

        // A position is a name, not a path: without the guard these would
        // read a file from outside the templates directory.
        $this->assertSame('', Positions::file('../components'));
        $this->assertSame(array(), Positions::declared('../../etc'));
    }

    /**
     * An undeclared position is not declared by accident
     *
     * @return  void
     */
    #[Test]
    public function testAPositionNoTemplateAsksForIsNotDeclared()
    {
        $this->assertFalse(Positions::declares('lucent', 'no-such-position'));
        $this->assertFalse(Positions::declares('lucent', ''));
    }

    /**
     * The declarations keep the order the template wrote them in
     *
     * The picker shows them in this order, and a template author orders them
     * to say something about the page.
     *
     * @return  void
     */
    #[Test]
    public function testDeclarationsKeepTheirOrder()
    {
        $positions = Positions::declared('mesozoic');

        $this->assertSame('footer', $positions[0]);
        $this->assertLessThan(
            array_search('home-2', $positions),
            array_search('home-1', $positions)
        );
    }

    /**
     * A template's own file answers before the shipped one is looked for
     *
     * @return  void
     */
    #[Test]
    public function testTheFileFoundIsTheTemplatesOwn()
    {
        $path = Positions::file('lucent');

        $this->assertNotEmpty($path);
        $this->assertTrue(is_file($path));
        $this->assertStringEndsWith('/templates/lucent/templateDetails.xml', str_replace('\\', '/', $path));
    }
}
