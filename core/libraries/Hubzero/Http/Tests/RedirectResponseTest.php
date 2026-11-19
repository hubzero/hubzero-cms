<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Http\Tests;

use Hubzero\Test\Basic;
use Hubzero\Http\Request;
use Hubzero\Http\RedirectResponse;

/**
 * RedirectResponse test
 */
class RedirectResponseTest extends Basic
{
    /**
     * Send a redirect for a request to a page and return what it printed
     *
     * @param   RedirectResponse  $redirect
     * @param   string            $script    The entry point the request came in through
     * @param   string            $uri       The page the request was for
     * @return  string
     */
    private function sendFrom(RedirectResponse $redirect, $script, $uri)
    {
        $request = Request::create('https://hub.example.org' . $uri, 'GET', [], [], [], [
            'SCRIPT_FILENAME' => '/var/www/hub' . $script,
            'SCRIPT_NAME'     => $script,
            'PHP_SELF'        => $script,
        ]);
        $redirect->setRequest($request);

        ob_start();
        $redirect->send();

        return (string) ob_get_clean();
    }

    /**
     * A bare index.php target points at the entry point, not the routed page
     *
     * @return  void
     */
    public function testIndexTargetIsRelativeToEntryPoint()
    {
        $redirect = new RedirectResponse('index.php?option=com_members');
        $this->sendFrom($redirect, '/index.php', '/members/1003/profile');

        $this->assertEquals('/index.php?option=com_members', $redirect->getTargetUrl());
        $this->assertEquals('/index.php?option=com_members', $redirect->headers->get('Location'));

        $redirect = new RedirectResponse('index.php?option=com_members');
        $this->sendFrom($redirect, '/administrator/index.php', '/administrator/index.php');

        $this->assertEquals('/administrator/index.php?option=com_members', $redirect->getTargetUrl());
    }

    /**
     * The body stays the page Symfony builds from the target, and other
     * targets are left as they are
     *
     * @return  void
     */
    public function testBodyIsTheRedirectPage()
    {
        $redirect = new RedirectResponse('/groups');
        $body = $this->sendFrom($redirect, '/index.php', '/groups/new');

        $this->assertEquals('/groups', $redirect->getTargetUrl());
        $this->assertStringStartsWith('<!DOCTYPE html>', $body);
        $this->assertStringContainsString('/groups', $body);

        $redirect = new RedirectResponse('/wishlist/publication/1/wish/16?active=wishlist&amp;saved=3');
        $this->sendFrom($redirect, '/index.php', '/wishlist/4/add');

        $this->assertEquals('/wishlist/publication/1/wish/16?active=wishlist&saved=3', $redirect->getTargetUrl());

        $redirect = new RedirectResponse("https://example.org/a\r\nX-Injected: 1");
        $this->sendFrom($redirect, '/index.php', '/');

        $this->assertEquals('https://example.org/a', $redirect->getTargetUrl());
    }
}
