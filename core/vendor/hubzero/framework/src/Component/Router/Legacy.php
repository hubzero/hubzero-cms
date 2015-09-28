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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

/**
 * Default routing class for missing or legacy component routers
 */
class Legacy implements RouterInterface
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	protected $component;

	/**
	 * Constructor
	 *
	 * @param   string  $component  Component name without the com_ prefix this router should react upon
	 * @return  void
	 */
	public function __construct($component)
	{
		$this->component = $component;
	}

	/**
	 * Generic preprocess function for missing or legacy component router
	 *
	 * @param   array  $query  An associative array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function preprocess($query)
	{
		return $query;
	}

	/**
	 * Generic build function for missing or legacy component router
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$function = $this->component . 'BuildRoute';

		if (function_exists($function))
		{
			$segments = $function($query);

			if ($this->component == 'com_content')
			{
				$total = count($segments);

				for ($i = 0; $i < $total; $i++)
				{
					$segments[$i] = str_replace(':', '-', $segments[$i]);
				}
			}

			return $segments;
		}

		return array();
	}

	/**
	 * Generic parse function for missing or legacy component router
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$function = $this->component . 'ParseRoute';

		if (function_exists($function))
		{
			if ($this->component == 'com_content')
			{
				$total = count($segments);

				for ($i = 0; $i < $total; $i++)
				{
					$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
				}
			}

			return $function($segments);
		}

		return array();
	}
}
