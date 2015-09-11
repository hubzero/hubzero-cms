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

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;

/**
 * XML document class for parsing and displaying an XML page
 *
 * Inspired by Joomla's JDocumentXml class
 */
class Xml extends Base
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

		// set mime type
		$this->mime = 'application/xml';

		// set document type
		$this->type = 'xml';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		parent::render();

		\App::get('response')->headers->set('Content-disposition', 'inline; filename="' . $this->getName() . '.xml"', true);

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
