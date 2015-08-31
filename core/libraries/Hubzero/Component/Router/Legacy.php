<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
