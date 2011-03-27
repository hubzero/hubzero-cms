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

//----------------------------------------------------------
// Support configuration
//----------------------------------------------------------

// Maths constants
define( 'MW_MATH_PNG',    0 );
define( 'MW_MATH_SIMPLE', 1 );
define( 'MW_MATH_HTML',   2 );
define( 'MW_MATH_SOURCE', 3 );
define( 'MW_MATH_MODERN', 4 );
define( 'MW_MATH_MATHML', 5 );

// General Configuration
class WikiConfig
{
	public $filepath   = '/site/wiki';
	public $mathpath   = '/site/wiki/math';
	public $tmppath    = '/site/wiki/tmp';
	public $iconpath   = '';
	public $homepage   = 'MainPage';
	public $max_pagename_length = 100;
	public $subpage_separator = '/';
	public $image_ext  = array('jpg','jpeg','bmp','tif','tiff','png','gif');
	public $file_ext   = array('jpg','jpeg','jpe','bmp','tif','tiff','png','gif','pdf','zip','mpg','mpeg','avi','mov','wmv','asf','asx','ra','rm','txt','rtf','doc','xsl','html','js','wav','mp3','eps','ppt','pps','swf','tar','tex','gz');
	public $maxAllowed = '40000000';
	
	public function __construct( $config=array() )
	{
		if (isset($config['filepath'])) {
			$this->filepath = $config['filepath'];
		}
		if (isset($config['option'])) {
			$this->iconpath = DS.'components'.DS.$config['option'].DS.'images'.DS.'icons';
		}
		if (isset($config['homepage'])) {
			$this->homepage = $config['homepage'];
		}
		if (isset($config['max_pagename_length'])) {
			$this->max_pagename_length = $config['max_pagename_length'];
		}
		if (isset($config['subpage_separator'])) {
			$this->subpage_separator = $config['subpage_separator'];
		}
		if (isset($config['image_ext'])) {
			$this->image_ext = $config['image_ext'];
		}
		if (isset($config['file_ext'])) {
			$this->file_ext = $config['file_ext'];
		}
		if (isset($config['maxAllowed'])) {
			$this->maxAllowed = $config['maxAllowed'];
		}
	}
}

