<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * DocumentFeed class, provides an easy interface to parse and display any feed document
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */

class XDocumentFeed extends JDocument
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
	function __construct($options = array())
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
	function render( $cache = false, $params = array())
	{
		global $option;

		// Get the feed type
		$type = JRequest::getCmd('type', 'rss');

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
		$renderer =& $this->loadXRenderer(($type) ? $type : 'rss');
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
	function &loadXRenderer( $type )
	{
		$null	= null;
		$class	= 'XDocumentRenderer'.$type;

		if( !class_exists( $class ) )
		{
			$path = dirname(__FILE__).DS.'x'.$type.'.php';
			if(file_exists($path)) {
				require_once($path);
			} else {
				JError::raiseError(500,JText::_('Unable to load renderer class'));
			}
		}

		if ( !class_exists( $class ) ) {
			return $null;
		}

		$instance = new $class($this);
		return $instance;
	}

	/**
	 * Adds an XFeedItem to the feed.
	 *
	 * @param object XFeedItem $item The feeditem to add to the feed.
	 * @access public
	 */
	function addItem( &$item )
	{
		$item->source = $this->link;
		$this->items[] = $item;
	}
}

/**
 * XFeedItem is an internal class that stores feed item information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class XFeedItem extends JObject
{
	/**
	 * Title item element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	var $title;

	/**
	 * Link item element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	var $link;

	/**
	 * Description item element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $description;

	/**
	 * Author item element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $author;

	 /**
	 * Author email element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $authorEmail;


	/**
	 * Category element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $category;

	 /**
	 * Comments element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $comments;

	 /**
	 * Enclosure element
	 *
	 * @var		object
	 * @access	public
	 */
	 var $enclosure =  null;

	 /**
	 * Guid element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $guid;

	/**
	 * Published date
	 *
	 * optional
	 *
	 *  May be in one of the following formats:
	 *
	 *	RFC 822:
	 *	"Mon, 20 Jan 03 18:05:41 +0400"
	 *	"20 Jan 03 18:05:41 +0000"
	 *
	 *	ISO 8601:
	 *	"2003-01-20T18:05:41+04:00"
	 *
	 *	Unix:
	 *	1043082341
	 *
	 * @var		string
	 * @access	public
	 */
	 var $pubDate;

	 /**
	 * Source element
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $source;

	/* iTunes specific tags */
	var $itunes_summary = "";
	var $itunes_explicit = "no";
	var $itunes_keywords = "";
	var $itunes_author = "";
	var $itunes_image = "";
	var $itunes_duration = "";
	var $itunes_category = "";
	var $itunes_subcategories = null;

	 /**
	 * Set the XFeedEnclosure for this item
	 *
	 * @access public
	 * @param object $enclosure The XFeedItem to add to the feed.
	 */
	 function setEnclosure($enclosure)	{
		 $this->enclosure = $enclosure;
	 }
}

/**
 * XFeedEnclosure is an internal class that stores feed enclosure information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class XFeedEnclosure extends JObject
{
	/**
	 * URL enclosure element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $url = "";

	/**
	 * Lenght enclosure element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $length = "";

	 /**
	 * Type enclosure element
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $type = "";
}

/**
 * XFeedImage is an internal class that stores feed image information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class XFeedImage extends JObject
{
	/**
	 * Title image attribute
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $title = "";

	 /**
	 * URL image attribute
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	var $url = "";

	/**
	 * Link image attribute
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $link = "";

	 /**
	 * Image width attribute
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $width;

	 /**
	 * Image height attribute
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $height;

	 /**
	 * Image description attribute
	 *
	 * optional
	 *
	 * @var		string
	 * @access	public
	 */
	 var $description;
}

/**
 * XFeedImage is an internal class that stores feed image information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class XFeedItunesOwner extends JObject
{
	/**
	 * Email attribute
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	 var $email = "";

	 /**
	 * Name attribute
	 *
	 * required
	 *
	 * @var		string
	 * @access	public
	 */
	var $name = "";
}
