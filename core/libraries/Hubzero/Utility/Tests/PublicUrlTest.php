<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility\Tests;

use Hubzero\Utility\PublicUrl;
use Hubzero\Test\Basic;

/**
 * Tests for URLs the server may fetch
 *
 * Address literals only, so that no test depends on DNS.
 */
class PublicUrlTest extends Basic
{
    /**
     * Internal addresses, however written, are refused
     *
     * @return  void
     */
    public function testInternalAddressesAreRefused()
    {
        foreach ([
            'http://127.0.0.1/',
            'http://[::1]/',
            'http://[::ffff:127.0.0.1]/',
            'http://[::ffff:169.254.169.254]/',
            'http://[2002:7f00:1::]/',
            'http://[64:ff9b::a00:1]/',
            'http://10.0.0.1/',
            'http://100.100.100.200/',
            'http://[fd00::1]/',
        ] as $url)
        {
            $this->assertFalse(PublicUrl::check($url), $url);
        }
    }

    /**
     * What parse_url() and curl could read as different hosts is refused
     *
     * @return  void
     */
    public function testConfusableUrlsAreRefused()
    {
        foreach ([
            'http://127.0.0.1:80\\@1.1.1.1/',
            'http://1.1.1.1@127.0.0.1/',
            'http://user:pass@1.1.1.1/',
            'http://1.1.1.1\\x/',
        ] as $url)
        {
            $this->assertFalse(PublicUrl::check($url), $url);
        }
    }

    /**
     * Only the schemes allowed
     *
     * @return  void
     */
    public function testSchemes()
    {
        $this->assertFalse(PublicUrl::check('ftp://1.1.1.1/'));
        $this->assertFalse(PublicUrl::check('file:///etc/passwd'));
        $this->assertFalse(PublicUrl::check('http://1.1.1.1/', ['https']));
        $this->assertNotFalse(PublicUrl::check('https://1.1.1.1/', ['https']));
    }

    /**
     * A public literal is fetched from itself, from a URL rebuilt from the
     * parts checked, with no pin (there is no name to re-point)
     *
     * @return  void
     */
    public function testPublicLiterals()
    {
        $target = PublicUrl::check('https://1.1.1.1/a/b?c=d#frag');
        $this->assertEquals('https://1.1.1.1/a/b?c=d', $target['url']);
        $this->assertEquals(['1.1.1.1'], $target['addresses']);
        $this->assertEquals([], $target['pins']);
        $this->assertEquals(443, $target['port']);

        $target = PublicUrl::check('http://[2606:4700:4700::1111]:8080');
        $this->assertEquals('http://[2606:4700:4700::1111]:8080/', $target['url']);
        $this->assertEquals(['2606:4700:4700::1111'], $target['addresses']);
    }
}
