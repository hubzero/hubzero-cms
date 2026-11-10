<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Tests;

require_once dirname(__DIR__) . '/helpers/anchor.php';

use Components\Wiki\Helpers\Anchor;
use Hubzero\Test\Basic;

/**
 * Tests for the anchors wiki headings are linked to
 *
 * Every expected value here is what the MediaWiki sanitizer this replaced
 * made of the same heading, so links to existing sections keep working.
 */
class AnchorTest extends Basic
{
    /**
     * Spaces, punctuation and non-ASCII text come out as they always have
     *
     * @return  void
     */
    public function testHeadingsKeepTheirAnchors()
    {
        $this->assertSame('Getting_Started', Anchor::fromHeading('Getting Started'));
        $this->assertSame('Caf.C3.A9_au_lait', Anchor::fromHeading('Café au lait'));
        $this->assertSame('100.25_sure:_yes.3F', Anchor::fromHeading('100% sure: yes?'));
        $this->assertSame('a.2Fb_c.23d', Anchor::fromHeading('a/b c#d'));
    }

    /**
     * Named and numeric references decode to the characters they name
     *
     * @return  void
     */
    public function testReferencesAreDecoded()
    {
        $this->assertSame('Caf.C3.A9_.26_Bar', Anchor::fromHeading('Caf&eacute; &amp; Bar'));
        $this->assertSame('Tom_.26_Jerry', Anchor::fromHeading('Tom &#38; Jerry'));
        $this->assertSame('x_.E2.98.83_y', Anchor::fromHeading('x &#x2603; y'));
        $this->assertSame('A', Anchor::fromHeading('&#X41;'));
    }

    /**
     * Only HTML 4's entities are known, with HTML 4's meanings
     *
     * @return  void
     */
    public function testEntitiesAreHtml4s()
    {
        // HTML 5 gives these two other characters
        $this->assertSame("\u{2329}\u{232A}", Anchor::decodeReferences('&lang;&rang;'));
        // and knows these, which HTML 4 does not
        $this->assertSame('&apos; &NewLine; &bogus;', Anchor::decodeReferences('&apos; &NewLine; &bogus;'));
        $this->assertSame("\u{200F}", Anchor::decodeReferences("&\u{05E8}\u{05DC}\u{05DE};"));
    }

    /**
     * A number that is not a character a document may hold becomes U+FFFD
     *
     * The sanitizer this replaced meant to do the same, and stopped the page
     * with an undefined constant instead.
     *
     * @return  void
     */
    public function testInvalidNumbersBecomeTheReplacementCharacter()
    {
        $this->assertSame('bad_.EF.BF.BD_ref', Anchor::fromHeading('bad &#1; ref'));
        $this->assertSame("\u{FFFD}", Anchor::decodeReferences('&#xD800;'));
        $this->assertSame("\u{FFFD}", Anchor::decodeReferences('&#99999999999;'));
    }
}
