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

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;

/**
 * JSON document class for parsing and displaying JSON data
 *
 * Inspired by Joomla's JDocumentJson class
 */
class Json extends Base
{
	/**
	 * Document name
	 *
	 * @var  string
	 */
	protected $name = 'hubzero';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set mime type
		$this->mime = 'application/json';

		// Set document type
		$this->type = 'json';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  object   The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		\App::get('response')->headers->set('Cache-Control', 'no-cache', false);
		\App::get('response')->headers->set('Pragma', 'no-cache');
		\App::get('response')->headers->set('Content-disposition', 'attachment; filename="' . $this->getName() . '.json"', true);

		parent::render();

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 * @return  object  instance of $this to allow chaining
	 */
	public function setName($name)
	{
		$this->name = (string) $name;

		return $this;
	}
}
