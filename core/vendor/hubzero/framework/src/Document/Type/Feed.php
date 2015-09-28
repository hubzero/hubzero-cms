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

use Hubzero\Document\Type\Feed\Item;
use Hubzero\Document\Renderer;
use Hubzero\Document\Base;

/**
 * Feed document class for parsing and displaying an XML feed
 *
 * Inspired by Joomla's JDocumentFeed class
 */
class Feed extends Base
{
	/**
	 * Syndication URL feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $syndicationURL = '';

	 /**
	 * Image feed element
	 *
	 * optional
	 *
	 * @var  object
	 */
	public $image = null;

	/**
	 * Copyright feed elememnt
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $copyright = '';

	 /**
	 * Published date feed element
	 *
	 *  optional
	 *
	 * @var  string
	 */
	public $pubDate = '';

	 /**
	 * Lastbuild date feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $lastBuildDate = '';

	 /**
	 * Editor feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $editor = '';

	/**
	 * Docs feed element
	 *
	 * @var  string
	 */
	public $docs = '';

	 /**
	 * Editor email feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $editorEmail = '';

	/**
	 * Webmaster email feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $webmaster = '';

	/**
	 * Category feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $category = '';

	/**
	 * TTL feed attribute
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $ttl = '';

	/**
	 * Rating feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $rating = '';

	/**
	 * Skiphours feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $skipHours = '';

	/**
	 * Skipdays feed element
	 *
	 * optional
	 *
	 * @var  string
	 */
	public $skipDays = '';

	/**
	 * The feed items collection
	 *
	 * @var  array
	 */
	public $items = array();

	/**
	 * iTunes summary
	 *
	 * @var  string
	 */
	public $itunes_summary = '';

	/**
	 * iTunes category
	 *
	 * @var  string
	 */
	public $itunes_category = '';

	/**
	 * iTunes subcategories
	 *
	 * @var  string
	 */
	public $itunes_subcategories = null;

	/**
	 * iTunes feed owner
	 *
	 * @var  string
	 */
	public $itunes_owner = null;

	/**
	 * iTunes 'explicit content' flag
	 *
	 * @var  string
	 */
	public $itunes_explicit = 'no';

	/**
	 * iTunes keywords
	 *
	 * @var  string
	 */
	public $itunes_keywords = '';

	/**
	 * iTunes author
	 *
	 * @var  string
	 */
	public $itunes_author = '';

	/**
	 * iTunes image
	 *
	 * @var  string
	 */
	public $itunes_image = null;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// set document type
		$this->_type = 'feed';
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
		// Get the feed type
		$type = \Request::getCmd('type', 'Rss');

		// Instantiate feed renderer and set the mime encoding
		$renderer = $this->loadRenderer(($type) ? $type : 'rss');

		if (!($renderer instanceof Renderer))
		{
			\App::abort(404, \Lang::txt('Resource Not Found'));
		}

		$this->setMimeEncoding($renderer->getContentType());

		// output
		// Generate prolog
		$data  = '<?xml version="1.0" encoding="' . $this->_charset . '"?>' . "\n";
		$data .= '<!-- generator="' . $this->getGenerator() . '" -->' . "\n";

		// Generate stylesheet links
		foreach ($this->_styleSheets as $src => $attr)
		{
			$data .= '<?xml-stylesheet href="' . $src . '" type="' . $attr['mime'] . '"?>' . "\n";
		}

		// Render the feed
		$data .= $renderer->render();

		parent::render();

		return $data;
	}

	/**
	 * Adds an Item to the feed.
	 *
	 * @param   object  &$item  The feeditem to add to the feed.
	 * @return  object  instance of $this to allow chaining
	 */
	public function addItem(Item $item)
	{
		$item->source = $this->link;

		$this->items[] = $item;

		return $this;
	}
}
