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

use Hubzero\Document\Type\Opensearch\Image;
use Hubzero\Document\Type\Opensearch\Url;
use Hubzero\Document\Base;
use Request;
use Route;
use App;

/**
 * OpenSearch document class for parsing and displaying an OpenSearch page
 *
 * Inspired by Joomla's JDocumentOpenSearch class
 */
class Opensearch extends Base
{
	/**
	 * ShortName element
	 *
	 * required
	 *
	 * @var  string
	 */
	private $shortName = '';

	/**
	 * Images collection
	 *
	 * optional
	 *
	 * @var  object
	 */
	private $images = array();

	/**
	 * The url collection
	 *
	 * @var  array
	 */
	private $urls = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set document type
		$this->type = 'opensearch';

		// Set mime type
		$this->mime = 'application/opensearchdescription+xml';

		// Add the URL for self updating
		$update = new Url;
		$update->type     = 'application/opensearchdescription+xml';
		$update->rel      = 'self';
		$update->template = Route::url(\Request::current());
		$this->addUrl($update);

		// Add the favicon as the default image
		// Try to find a favicon by checking the template and root folder
		$dirs = array(App::get('template')->path, PATH_ROOT);

		foreach ($dirs as $dir)
		{
			if (file_exists($dir . DS . 'favicon.ico'))
			{
				$path = str_replace(PATH_ROOT . DS, '', $dir);
				$path = str_replace('\\', '/', $path);

				$favicon = new Image;
				$favicon->data   = \Request::root() . $path . '/favicon.ico';
				$favicon->height = '16';
				$favicon->width  = '16';
				$favicon->type   = 'image/vnd.microsoft.icon';

				$this->addImage($favicon);

				break;
			}
		}
	}

	/**
	 * Render the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		$xml = new \DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;

		// The OpenSearch Namespace
		$osns = 'http://a9.com/-/spec/opensearch/1.1/';

		// Create the root element
		$elOs = $xml->createElementNS($osns, 'OpenSearchDescription');

		$elShortName = $xml->createElementNS($osns, 'ShortName');
		$elShortName->appendChild($xml->createTextNode(htmlspecialchars($this->shortName)));
		$elOs->appendChild($elShortName);

		$elDescription = $xml->createElementNS($osns, 'Description');
		$elDescription->appendChild($xml->createTextNode(htmlspecialchars($this->description)));
		$elOs->appendChild($elDescription);

		// Always set the accepted input encoding to UTF-8
		$elInputEncoding = $xml->createElementNS($osns, 'InputEncoding');
		$elInputEncoding->appendChild($xml->createTextNode('UTF-8'));
		$elOs->appendChild($elInputEncoding);

		foreach ($this->images as $image)
		{
			$elImage = $xml->createElementNS($osns, 'Image');
			$elImage->setAttribute('type', $image->type);
			$elImage->setAttribute('width', $image->width);
			$elImage->setAttribute('height', $image->height);
			$elImage->appendChild($xml->createTextNode(htmlspecialchars($image->data)));

			$elOs->appendChild($elImage);
		}

		foreach ($this->urls as $url)
		{
			$elUrl = $xml->createElementNS($osns, 'Url');
			$elUrl->setAttribute('type', $url->type);
			// Results is the defualt value so we don't need to add it
			if ($url->rel != 'results')
			{
				$elUrl->setAttribute('rel', $url->rel);
			}
			$elUrl->setAttribute('template', $url->template);

			$elOs->appendChild($elUrl);
		}

		$xml->appendChild($elOs);

		parent::render();

		return $xml->saveXML();
	}

	/**
	 * Sets the short name
	 *
	 * @param   string  $name  The name.
	 * @return  object  Supports chaining
	 */
	public function setShortName($name)
	{
		$this->shortName = $name;

		return $this;
	}

	/**
	 * Adds an URL to the OpenSearch description.
	 *
	 * @param   object  $url  The url to add to the description.
	 * @return  object  Supports chaining
	 */
	public function addUrl(Url $url)
	{
		$this->urls[] = $url;

		return $this;
	}

	/**
	 * Adds an image to the OpenSearch description.
	 *
	 * @param   object  $image  The image to add to the description.
	 * @return  object  Supports chaining
	 */
	public function addImage(Image $image)
	{
		$this->images[] = $image;

		return $this;
	}
}