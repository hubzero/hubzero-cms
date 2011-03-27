<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Document'.DS.'Feed'.DS.'Enclosure.php');
include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Document'.DS.'Feed'.DS.'Image.php');
include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Document'.DS.'Feed'.DS.'Item.php');
include_once(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Document'.DS.'Feed'.DS.'ItunesOwner.php');

/**
 * Hubzero_Document_Feed class, provides an easy interface to parse and display any feed document
 *
 * @author      Johan Janssens <johan.janssens@joomla.org>
 * @author      Shawn Rice <zooley@purdue.edu>
 */

class Hubzero_Document_Feed extends JDocument
{
	/**
	 * Syndication URL feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $syndicationURL = "";

	 /**
	 * Image feed element
	 *
	 * optional
	 *
	 * @var		object
	 * @access	public
	 */
	 var $image = null;

	/**
	 * Copyright feed elememnt
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $copyright = "";

	 /**
	 * Published date feed element
	 *
	 *  optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $pubDate = "";

	 /**
	 * Lastbuild date feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $lastBuildDate = "";

	 /**
	 * Editor feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $editor = "";

	/**
	 * Docs feed element
	 *
	 * @var		string
	 * @access	public
	 */
	 var $docs = "";

	 /**
	 * Editor email feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $editorEmail = "";

	/**
	 * Webmaster email feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $webmaster = "";

	/**
	 * Category feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $category = "";

	/**
	 * TTL feed attribute
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $ttl = "";

	/**
	 * Rating feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $rating = "";

	/**
	 * Skiphours feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $skipHours = "";

	/**
	 * Skipdays feed element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $skipDays = "";

	/**
	 * The feed items collection
	 *
	 * @var array
	 * @access public
	 */
	var $items = array();

	/* iTunes specific tags */
	var $itunes_summary = "";
	var $itunes_category = "";
	var $itunes_subcategories = null;
	var $itunes_owner = null;
	var $itunes_explicit = "no";
	var $itunes_keywords = "";
	var $itunes_author = "";
	var $itunes_image = null;

	/**
	 * Class constructor
	 *
	 * @access protected
	 * @param	array	$options Associative array of options
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set document type
		$this->_type = 'feed';
	}

	/**
	 * Render the document
	 *
	 * @access public
	 * @param boolean 	$cache		If true, cache the output
	 * @param array		$params		Associative array of attributes
	 * @return 	The rendered data
	 */
	public function render( $cache = false, $params = array())
	{
		global $option;

		// Get the feed type
		$type = JRequest::getCmd('type', 'Rss');

		/*
		 * Cache TODO In later release
		 */
		$cache		= 0;
		$cache_time = 3600;
		$cache_path = JPATH_BASE.DS.'cache';

		// set filename for rss feeds
		$file = strtolower( str_replace( '.', '', $type ) );
		$file = $cache_path.DS.$file.'_'.$option.'.xml';


		// Instantiate feed renderer and set the mime encoding
		$renderer =& $this->loadXRenderer(($type) ? $type : 'Rss');
		if (!is_a($renderer, 'JDocumentRenderer')) {
			JError::raiseError(404, JText::_('Resource Not Found'));
		}
		$this->setMimeEncoding($renderer->getContentType());

		//output
		// Generate prolog
		$data  = "<?xml version=\"1.0\" encoding=\"".$this->_charset."\"?>\n";
		$data .= "<!-- generator=\"".$this->getGenerator()."\" -->\n";

		 // Generate stylesheet links
		foreach ($this->_styleSheets as $src => $attr ) {
			$data .= "<?xml-stylesheet href=\"$src\" type=\"".$attr['mime']."\"?>\n";
		}

		// Render the feed
		$data .= $renderer->render();

		parent::render();
		return $data;
	}

	/**
	* Load a renderer
	*
	* @access	public
	* @param	string	The renderer type
	* @return	object
	* @since 1.5
	*/
	public function &loadXRenderer( $type )
	{
		$null	= null;
		$class	= 'Hubzero_Document_Renderer_'.ucfirst(strtolower($type));

		if (!class_exists( $class )) {
			$path = dirname(__FILE__).DS.'Renderer'.DS.$type.'.php';
			if (file_exists($path)) {
				require_once($path);
			} else {
				JError::raiseError(500,JText::_('Unable to load renderer class'));
			}
		}

		if (!class_exists( $class )) {
			return $null;
		}

		$instance = new $class($this);
		return $instance;
	}

	/**
	 * Adds a Hubzero_Document_Feed_Item to the feed.
	 *
	 * @param object Hubzero_Document_Feed_Item $item The feeditem to add to the feed.
	 * @access public
	 */
	public function addItem( &$item )
	{
		$item->source = $this->link;
		$this->items[] = $item;
	}
}

