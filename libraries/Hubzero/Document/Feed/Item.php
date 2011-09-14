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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Hubzero_Document_Feed_Item is an internal class that stores feed item information
 *
 * @author Johan Janssens <johan.janssens@joomla.org>
 * @author Shawn Rice <zooley@purdue.edu>
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

	/**
	 * Description for 'itunes_summary'
	 * 
	 * @var string
	 */
	var $itunes_summary = "";

	/**
	 * Description for 'itunes_explicit'
	 * 
	 * @var string
	 */
	var $itunes_explicit = "no";

	/**
	 * Description for 'itunes_keywords'
	 * 
	 * @var string
	 */
	var $itunes_keywords = "";

	/**
	 * Description for 'itunes_author'
	 * 
	 * @var string
	 */
	var $itunes_author = "";

	/**
	 * Description for 'itunes_image'
	 * 
	 * @var string
	 */
	var $itunes_image = "";

	/**
	 * Description for 'itunes_duration'
	 * 
	 * @var string
	 */
	var $itunes_duration = "";

	/**
	 * Description for 'itunes_category'
	 * 
	 * @var string
	 */
	var $itunes_category = "";

	/**
	 * Description for 'itunes_subcategories'
	 * 
	 * @var unknown
	 */
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

