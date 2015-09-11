<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Http;

use InvalidArgumentException;
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
	 * The request instance.
	 *
	 * @var  object
	 */
	protected $request;

	/**
	 * The session store implementation.
	 *
	 * @var  object
	 */
	protected $session;

	/**
	 * Set a header on the Response.
	 *
	 * @param   string  $key
	 * @param   string  $value
	 * @param   bool    $replace
	 * @return  object
	 */
	public function header($key, $value, $replace = true)
	{
		$this->headers->set($key, $value, $replace);

		return $this;
	}

	/**
	 * Flash a piece of data to the session.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function with($key, $value = null)
	{
		$key = is_array($key) ? $key : [$key => $value];

		foreach ($key as $k => $v)
		{
			$this->session->set($k, $v);
		}

		return $this;
	}

	/**
	 * Get the request instance.
	 *
	 * @return  object
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the request instance.
	 *
	 * @param   object  $request
	 * @return  void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return  object
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the session store implementation.
	 *
	 * @param   object  $session
	 * @return  void
	 */
	public function setSession(SessionStore $session)
	{
		$this->session = $session;
	}

	/**
	 * Prepares the Response before it is sent to the client.
	 *
	 * This method tweaks the Response to ensure that it is
	 * compliant with RFC 2616. Most of the changes are based on
	 * the Request that is "associated" with this Response.
	 *
	 * @param   object  $request  A Request instance
	 * @return  object  The current response.
	 */
	public function send()
	{
		if ($this->request)
		{
			$url = $this->getContent();

			// Check for relative internal links.
			if (preg_match('/^index2?\.php/', $url))
			{
				$url = $this->request->base() . $url;
			}

			// Strip out any line breaks.
			$url = preg_split("/[\r\n]/", $url);
			$url = $url[0];

			// If we don't start with a http we need to fix this before we proceed.
			// We could validly start with something else (e.g. ftp), though this would
			// be unlikely and isn't supported by this API.
			if (!preg_match('/^http/i', $url))
			{
				$prefix = $this->request->scheme() . $this->request->getUserInfo() . $this->request->host();

				if ($url[0] == '/')
				{
					// We just need the prefix since we have a path relative to the root.
					$url = $prefix . $url;
				}
				else
				{
					// It's relative to where we are now, so lets add that.
					$parts = explode('/', $this->request->path());
					array_pop($parts);
					$path = implode('/', $parts) . '/';
					$url = $prefix . $path . $url;
				}
			}

			$this->setContent($url);
		}

		return parent::send(); //prepare($request);
	}
}
