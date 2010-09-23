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
 * Hubzero_Document_Feed_Image is an internal class that stores feed image information
 *
 * @author		Johan Janssens <johan.janssens@joomla.org>
 * @author		Shawn Rice <zooley@purdue.edu>
 */
class Hubzero_Document_Feed_Image extends JObject
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
