<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Picture;

use Exception;

/**
 * Gravatar User picture
 */
class Gravatar
{
	/**
	 * Gravatar base url
	 *
	 * @var  string
	 */
	private $publicBaseUrl = 'http://www.gravatar.com/avatar/';

	/**
	 * Gravatar secure base url
	 *
	 * @var  string
	 */
	private $secureBaseUrl = 'https://secure.gravatar.com/avatar/';

	/**
	 * Config values
	 *
	 * @var  array
	 */
	private $config = array(
		'secure'         => false,
		'fallback'       => false,
		'forceExtension' => false,
		'forceDefault'   => false,
		'maximumRating'  => null,
		'size'           => null
	);

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct($config=array())
	{
		foreach ($this->config as $key => $val)
		{
			if (array_key_exists($key, $config))
			{
				if ($key == 'fallback')
				{
					if (filter_var($config['fallback'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
					 || in_array($config['fallback'], array('mm', 'identicon', 'monsterid', 'wavatar', 'retro', 'blank')))
					{
						$this->set('fallback', $config['fallback']);
					}
				}

				$this->set($key, $config[$key]);
			}
		}
	}

	/**
	 * Helper function to set config values
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  object
	 */
	public function set($key, $value)
	{
		$this->config[$key] = $value;

		return $this;
	}

	/**
	 * Helper function to retrieve config values
	 *
	 * @param   string  $value
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function get($value, $default = null)
	{
		return array_key_exists($value, $this->config) ? $this->config[$value] : $default;
	}

	/**
	 * Get a path or URL to a user pciture
	 *
	 * @param   string  $email
	 * @param   bool    $thumbnail
	 * @return  string
	 */
	public function picture($email, $thumbnail = true)
	{
		if ($thumbnail)
		{
			$this->set('size', 300);
		}

		$url  = $this->get('secure') === true ? $this->secureBaseUrl : $this->publicBaseUrl;
		$url .= htmlspecialchars($this->hash($email));
		$url .= $this->extension();
		$url .= $this->parameters();

		return $url;
	}

	/**
	 * Helper function to hash an email address.
	 *
	 * @param   string  $email
	 * @return  string
	 * @throws  InvalidEmailException
	 */
	public function hash($email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new Exception('Please specify a valid email address');
		}

		return md5(strtolower(trim($email)));
	}

	/**
	 * Force file extension
	 *
	 * @return  string
	 */
	public function extension()
	{
		$v = $this->get('forceExtension');

		return $v ? '.' . $v : '';
	}

	/**
	 * Get querystring of parameters
	 *
	 * @return  string
	 */
	public function parameters()
	{
		$build = array();

		foreach (get_class_methods($this) as $method)
		{
			if (substr($method, -strlen('Parameter')) !== 'Parameter')
			{
				continue;
			}

			if ($called = call_user_func(array($this, $method)))
			{
				$build = array_replace($build, $called);
			}
		}

		return '?' . http_build_query($build);
	}

	/**
	 * Get size parameter
	 *
	 * @return  array|null
	 */
	public function sizeParameter()
	{
		if (!$this->get('size') || !is_integer($this->get('size')))
		{
			return null;
		}

		return array('s' => (int)$this->get('size'));
	}

	/**
	 * Get fallback image URL
	 *
	 * @return  array|null
	 */
	public function fallbackParameter()
	{
		$fallback = $this->get('fallback');

		if (!$fallback)
		{
			return null;
		}

		return array('d' => $fallback);
	}

	/**
	 * Get rating
	 *
	 * @return  mixed  array|null
	 */
	public function ratingParameter()
	{
		$rating = $this->get('maximumRating');

		if (!$rating || !in_array($rating, array('g','pg','r','x')))
		{
			return null;
		}

		return array('r' => $rating);
	}

	/**
	 * Force default?
	 *
	 * @return  mixed  array|null
	 */
	public function forceDefaultParameter()
	{
		if ($this->get('forceDefault') === true)
		{
			return array('forcedefault' => 'y');
		}

		return null;
	}
}
