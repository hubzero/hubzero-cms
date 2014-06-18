<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document;

/**
 * Feed class, provides an easy interface to parse and display any feed document
 */
class Feed extends \JDocument
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
	 * @public array
	 */
	public $items = array();

	/* iTunes specific tags */

	/**
	 * Description for 'itunes_summary'
	 *
	 * @public string
	 */
	public $itunes_summary = '';

	/**
	 * Description for 'itunes_category'
	 *
	 * @public string
	 */
	public $itunes_category = '';

	/**
	 * Description for 'itunes_subcategories'
	 *
	 * @public unknown
	 */
	public $itunes_subcategories = null;

	/**
	 * Description for 'itunes_owner'
	 *
	 * @public unknown
	 */
	public $itunes_owner = null;

	/**
	 * Description for 'itunes_explicit'
	 *
	 * @public string
	 */
	public $itunes_explicit = 'no';

	/**
	 * Description for 'itunes_keywords'
	 *
	 * @public string
	 */
	public $itunes_keywords = '';

	/**
	 * Description for 'itunes_author'
	 *
	 * @public string
	 */
	public $itunes_author = '';

	/**
	 * Description for 'itunes_image'
	 *
	 * @public unknown
	 */
	public $itunes_image = null;

	/**
	 * Class constructor
	 *
	 * @param  array $options Associative array of options
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
	 * @param   boolean $cache  If true, cache the output
	 * @param   array   $params Associative array of attributes
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		$option = \JRequest::getCmd('option');

		// Get the feed type
		$type = \JRequest::getCmd('type', 'Rss');

		// Cache TODO In later release
		$cache      = 0;
		$cache_time = 3600;
		$cache_path = JPATH_BASE . DS . 'cache';

		// set filename for rss feeds
		$file = strtolower(str_replace('.', '', $type));
		$file = $cache_path . DS . $file . '_' . $option . '.xml';

		// Instantiate feed renderer and set the mime encoding
		$renderer = $this->loadXRenderer(($type) ? $type : 'Rss');
		if (!($renderer instanceof \JDocumentRenderer))
		{
			\JError::raiseError(404, \JText::_('Resource Not Found'));
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
	 * Load a renderer
	 *
	 * @param   string The renderer type
	 * @return  object
	 * @throws  InvalidArgumentException
	 */
	public function &loadXRenderer($type)
	{
		$null  = null;
		$class = __NAMESPACE__ . '\\Renderer\\' . ucfirst(strtolower($type));

		if (!class_exists($class))
		{
			$path = __DIR__ . DS . 'Renderer' . DS . ucfirst(strtolower($type)) . '.php';
			if (file_exists($path))
			{
				require_once($path);
			}
			else
			{
				throw new \InvalidArgumentException(\JText::_('Unable to load renderer class'), 500);
			}
		}

		if (!class_exists($class))
		{
			return $null;
		}

		$instance = new $class($this);
		return $instance;
	}

	/**
	 * Adds an Item to the feed.
	 *
	 * @param   object Item $item The feeditem to add to the feed.
	 * @return  void
	 */
	public function addItem(&$item)
	{
		$item->source = $this->link;
		$this->items[] = $item;
	}
}

