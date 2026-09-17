<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests;

use Hubzero\Template\Style;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The guard that decides whether a template is there to be worn
 *
 * makeDefault() will add a style for a template that has none, which is what
 * lets an install name a template installed without one. That only holds
 * because it first asks whether the template exists: without the check, a
 * typo would get a style of its own and a hub would come up wearing a
 * directory that is not there.
 */
class StyleTest extends TestCase
{
    /**
     * A template shipped with the CMS is found
     *
     * @return  void
     */
    #[Test]
    public function testAShippedTemplateExists()
    {
        $this->assertTrue(Style::exists('meridian'));
        $this->assertTrue(Style::exists('kimera'));

        // Site and administrator templates share the directory
        $this->assertTrue(Style::exists('kameleon'));
    }

    /**
     * Something that is not a template is not found
     *
     * @return  void
     */
    #[Test]
    public function testWhatIsNotThereIsNotFound()
    {
        $this->assertFalse(Style::exists('nosuchtemplate'));
        $this->assertFalse(Style::exists(''));

        // Not a directory, and not a way out of the templates directory
        $this->assertFalse(Style::exists('index.html'));
        $this->assertFalse(Style::exists('../components'));
    }
}
