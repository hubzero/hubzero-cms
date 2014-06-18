<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Publication DOI metadata base class
 */
class PublicationsMetadata extends JObject
{
	/**
	 *  Object title
	 */
	var $title						= NULL;
		
	/**
	 * Object abstract
	 */
	var $abstract					= NULL;
	
	/**
	 * Object description
	 */
	var $description				= NULL;
	
	/**
	 * Object dc type
	 */
	var $type						= NULL;
	
	/**
	 * URL to resource
	 */
	var $url						= NULL;
	
	/**
	 * URL to resource
	 */
	var $doi						= NULL;
	
	/**
	 * Publisher
	 */
	var $publisher					= NULL;
	
	/**
	 * Journal
	 */
	var $journal					= NULL;
	
	/**
	 * Subject
	 */
	var $subject					= NULL;
	
	/**
	 * Language
	 */
	var $language					= NULL;
	
	/**
	 * Format
	 */
	var $format						= NULL;
	
	/**
	 * Date
	 */
	var $date						= NULL;
	
	/**
	 * Date
	 */
	var $issued						= NULL;
	
	/**
	 * Volume
	 */
	var $volume						= NULL;
	
	/**
	 * Issue
	 */
	var $issue						= NULL;
	
	/**
	 * Page
	 */
	var $page						= NULL;
		
	/**
	 * ISBN
	 */
	var $isbn						= NULL;
	
	/**
	 * Author
	 */
	var $author						= NULL;
		
}