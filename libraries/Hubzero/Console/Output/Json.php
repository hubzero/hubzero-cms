<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

/**
 * Json output class for rendering content to command line (usually for ingestion to browser)
 **/
class Json extends Output
{
	/**
	 * Constructor - set mode
	 *
	 * @return void
	 **/
	public function __construct()
	{
		// Assume minimal mode and non-interactivity
		$this->setMode('minimal');
		$this->makeNonInteractive();
	}

	/**
	 * Render out stored output to command line
	 *
	 * @param  (bool) $newLine - whether or not to include new line with each response (really only applies to interactive output)
	 * @return void
	 **/
	public function render($newLine=true)
	{
		// Make sure there is something there
		if (isset($this->response) && count($this->response) > 0)
		{
			echo json_encode($this->response);

			// Reset response
			$this->response = array();
		}
	}

	/**
	 * Add a new line to the output buffer (not actually a real php output buffer)
	 *
	 * @param  (string) $message - text of line
	 * @param  (mixed)  $styles  - array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @param  (bool)   $newLine - whether or not line should end with a new line
	 * @return (object) $this    - for method chaining
	 **/
	public function addLine($message, $styles=null, $newLine=true)
	{
		$styles  = null;
		$newLine = true;
		if (is_array($message))
		{
			$this->response[key($message)] = current($message);
		}
		else
		{
			$this->response[] = $message;
		}

		return $this;
	}
}