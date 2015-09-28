<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document;

use Hubzero\Base\Object;

/**
 * Abstract document renderer
 *
 * Inspired by Joomla's JDocumentRenderer class
 */
class Renderer extends Object
{
	/**
	 * Reference to the Document object that instantiated the renderer
	 *
	 * @var  object
	 */
	protected $doc = null;

	/**
	 * Renderer mime type
	 *
	 * @var  string
	 */
	protected $mime = 'text/html';

	/**
	 * Class constructor
	 *
	 * @param   object  &$doc  A reference to the Document object that instantiated the renderer
	 * @return  void
	 */
	public function __construct(&$doc)
	{
		$this->doc = &$doc;
	}

	/**
	 * Renders a script and returns the results as a string
	 *
	 * @param   string  $name     The name of the element to render
	 * @param   array   $params   Array of values
	 * @param   string  $content  Override the output of the renderer
	 * @return  string  The output of the script
	 */
	public function render($name, $params = null, $content = null)
	{
		// ...
	}

	/**
	 * Return the content type of the renderer
	 *
	 * @return  string  The contentType
	 */
	public function getContentType()
	{
		return $this->mime;
	}
}
