<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility\Tests;

use Hubzero\Test\Basic;

/**
 * Sanitize utility test
 */
class SanitizeTest extends Basic
{
    /**
     * A video keeps its controls and its source
     *
     * @return  void
     */
    public function testVideoKeepsControlsAndSource()
    {
        $html = SanitizeWhitelist::purify(
            '<video controls width="640"><source src="/site/media/a.mp4" type="video/mp4"></video>',
        );

        $this->assertStringContainsString('<video controls="controls" width="640">', $html);
        $this->assertStringContainsString('<source src="/site/media/a.mp4" type="video/mp4" />', $html);
    }

    /**
     * A video written inside a paragraph stays there
     *
     * @return  void
     */
    public function testVideoStaysInsideItsParagraph()
    {
        $html = SanitizeWhitelist::purify('<p>Intro <video controls src="/a.mp4"></video></p>');

        $this->assertSame('<p>Intro <video controls="controls" src="/a.mp4"></video></p>', $html);
    }

    /**
     * Captions survive
     *
     * @return  void
     */
    public function testTrackIsKept()
    {
        $html = SanitizeWhitelist::purify(
            '<video controls src="/a.mp4"><track kind="captions" src="/a.vtt" srclang="en" label="English" '
                . 'default></video>',
        );

        $this->assertStringContainsString(
            '<track kind="captions" src="/a.vtt" srclang="en" label="English" default="default" />',
            $html,
        );
    }

    /**
     * Media does not start by itself
     *
     * @return  void
     */
    public function testAutoplayIsRemoved()
    {
        $html = SanitizeWhitelist::purify('<video autoplay controls src="/a.mp4"></video>');

        $this->assertStringNotContainsString('autoplay', $html);
        $this->assertStringContainsString('controls="controls"', $html);
    }

    /**
     * Scripts get in through neither the addresses nor the handlers
     *
     * @return  void
     */
    public function testScriptsAreStripped()
    {
        $html = SanitizeWhitelist::purify(
            '<video src="javascript:alert(1)" poster="javascript:alert(2)" onerror="alert(3)">'
            . '<source src="javascript:alert(4)"><track src="javascript:alert(5)"></video>',
        );

        $this->assertStringNotContainsString('javascript', $html);
        $this->assertStringNotContainsString('onerror', $html);
        $this->assertStringNotContainsString('alert', $html);
    }

    /**
     * The string filter decodes numeric character references, hexadecimal
     * ones included
     *
     * @return  void
     */
    public function testStringFilterDecodesNumericEntities()
    {
        $this->assertSame('ABC', \Hubzero\Utility\Sanitize::filter('&#65;&#x42;&#X43;', 'string'));
    }

    /**
     * Numeric references above ASCII decode to UTF-8 characters (they used to give single raw bytes); a reference
     * to no character stays as it was
     *
     * @return  void
     */
    public function testStringFilterDecodesNumericEntitiesToUtf8()
    {
        $this->assertSame('0é€😀', \Hubzero\Utility\Sanitize::filter('&#48;&#233;&#x20AC;&#x1F600;', 'string'));
        $this->assertSame('&#xD800;', \Hubzero\Utility\Sanitize::filter('&#xD800;', 'string'));
        $this->assertSame('café', \Hubzero\Utility\Sanitize::filter('caf&eacute;', 'string'));
    }
}
