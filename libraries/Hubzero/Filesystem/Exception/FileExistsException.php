<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Filesystem\Exception;

/**
 * File already exists exception
 */
class FileExistsException extends \Exception
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * Constructor.
	 *
	 * @param  string  $path
	 * @param  int     $code
	 * @param  object  $previous  \Exception
	 */
	public function __construct($path, $code = 0, \Exception $previous = null)
	{
		$this->path = $path;

		parent::__construct('File already exists at path: ' . $this->getPath(), $code, $previous);
	}

	/**
	 * Get the path which was not found.
	 *
	 * @return  string
	 */
	public function getPath()
	{
		return $this->path;
	}
}
