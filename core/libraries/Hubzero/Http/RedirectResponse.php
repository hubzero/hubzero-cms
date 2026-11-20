<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Http;

use Hubzero\Session\Manager as SessionStore;
use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;

/**
 * RedirectResponse represents an HTTP response doing a redirect.
 *
 * Inspired, in part, by Laravel
 * http://laravel.com
 */
class RedirectResponse extends BaseRedirectResponse
{
    /**
     * The request instance, once set
     *
     * @var  Request|null
     */
    protected $request = null;

    /**
     * The session store implementation.
     *
     * @var  \Hubzero\Session\Manager
     */
    protected $session;

    /**
     * Set a header on the Response.
     *
     * @param   string  $key
     * @param   string  $value
     * @param   bool    $replace
     * @return  $this
     */
    public function header($key, $value, $replace = true)
    {
        $this->headers->set($key, $value, $replace);

        return $this;
    }

    /**
     * Flash a piece of data to the session.
     *
     * @param   string|array<mixed>  $key
     * @param   mixed   $value
     * @return  $this
     */
    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];

        foreach ($key as $k => $v) {
            $this->session->set($k, $v);
        }

        return $this;
    }

    /**
     * Get the request instance.
     *
     * @return  \Hubzero\Http\Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the request instance.
     *
     * @param   \Hubzero\Http\Request $request
     * @return  void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the session store implementation.
     *
     * @return  \Hubzero\Session\Manager
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the session store implementation.
     *
     * @param   \Hubzero\Session\Manager $session
     * @return  void
     */
    public function setSession(SessionStore $session)
    {
        $this->session = $session;
    }

    /**
     * Send the response, first making a bare "index.php" target point at the
     * client's own entry point
     *
     * The target, not the content: the content is the HTML page Symfony
     * builds from the target, and reading that as the address put a mangled
     * copy of the first line of it in the body while leaving the Location
     * header untouched. Other relative and root-relative targets are left to
     * the browser, which resolves them against the page it is on.
     *
     * @return  $this  The current response.
     */
    public function send()
    {
        $url = $this->getTargetUrl();

        // Only the first line: a line break in a header would end it
        $url = (preg_split("/[\r\n]/", $url) ?: [''])[0];

        // A header is not HTML. Route::url() escapes '&' for HTML unless told
        // not to, and hundreds of redirects pass it straight here; a browser
        // reads '&amp;saved=1' as a parameter named 'amp;saved'.
        $url = str_replace('&amp;', '&', $url);

        // "index.php?..." is relative to the client's entry point, not to the
        // path a routed page happens to be on
        if ($this->request && preg_match('/^index2?\.php/', $url)) {
            $url = rtrim($this->request->base(true), '/') . '/' . $url;
        }

        if ($url !== '' && $url !== $this->getTargetUrl()) {
            $this->setTargetUrl($url);
        }

        return parent::send();
    }
}
