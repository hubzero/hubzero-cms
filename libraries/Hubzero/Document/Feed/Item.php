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
 * Hubzero_Document_Feed_Item is an internal class that stores feed item information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class Hubzero_Document_Feed_Item extends JObject
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
	 * Set the Hubzero_Document_Feed_Enclosure for this item
	 *
	 * @access public
	 * @param object $enclosure The Hubzero_Document_Feed_Item to add to the feed.
	*/
	public function setEnclosure($enclosure)
	{
		$this->enclosure = $enclosure;
	}
}
