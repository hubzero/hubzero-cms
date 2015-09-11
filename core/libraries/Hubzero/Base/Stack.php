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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Base;

use Hubzero\Http\Request;

/**
 * Request stack
 *
 * Inspired, in part, by Laravel
 * http://laravel.com
 */
class Stack
{
	/**
	 * Current position in stack
	 * 
	 * @var  integer
	 */
	protected $position;

	/**
	 * Layers to run though
	 * 
	 * @var  array
	 */
	protected $layers;

	/**
	 * Object to pass through layers
	 * 
	 * @var  object
	 */
	protected $request;

	/**
	 * Create stack with first layer as core
	 * 
	 * @param   object  $core  Core service
	 * @return  void
	 */
	public function __construct($core)
	{
		$this->position = 0;
		$this->layers   = array($core);
	}

	/**
	 * Send request through stack
	 * 
	 * @param   object  $request  Request object
	 * @return  object
	 */
	public function send(Request $request)
	{
		$this->request = $request;

		return $this;
	}

	/**
	 * Set layers on stack
	 * 
	 * @param   array   $layers  Array of services
	 * @return  object
	 */
	public function through($layers)
	{
		// Merge existing layers (core)
		$this->layers = array_merge(
			$this->layers, $layers
		);

		// Put the layers in reverse
		$this->layers = array_values(array_reverse($this->layers));

		return $this;
	}

	/**
	 * Add something to the stack
	 * 
	 * @param   mixed   $layer
	 * @return  object
	 */
	public function push($layer)
	{
		array_push($this->layers, $layer);

		return $this;
	}

	/**
	 * Final callback
	 * 
	 * @param   object  $callback  Callback after stack is run
	 * @return  void    Result of callback
	 */
	public function then(\Closure $callback)
	{
		$response = $this->layers[0]->handle($this->request);

		return call_user_func($callback, $response);
	}

	/**
	 * Call next layer in stack
	 * 
	 * @param   object  $request  Request object
	 * @return  object
	 */
	public function next(Request $request)
	{
		// Update the stack position
		$this->position++;

		// Get the next layer
		$layer = $this->layers[$this->position];

		// Call handle on next layer
		return $layer->handle($request);
	}
}