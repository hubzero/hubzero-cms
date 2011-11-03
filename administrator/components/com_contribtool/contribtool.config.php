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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Store configuration
//----------------------------------------------------------

/**
 * Short description for 'ContribtoolConfig'
 * 
 * Long description (if any) ...
 */
class ContribtoolConfig
{

	/**
	 * Description for 'paramaters'
	 * 
	 * @var array
	 */
	public $paramaters = array();

	/**
	 * Description for 'option'
	 * 
	 * @var unknown
	 */
	public $option = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $option )
	{
		$database =& JFactory::getDBO();

		$database->setQuery( "SELECT params FROM #__components WHERE `option`='".$option."' AND parent=0 LIMIT 1" );
		$parameters = $database->loadResult();

		$params = array();
		if ($parameters) {
			$ps = explode(n,$parameters);
			foreach ($ps as $p)
			{
				$m = explode('=',$p);
				if (trim($m[0])) {
					$params[$m[0]] = (isset($m[1])) ? $m[1] : '';
				}
			}
		}

		$this->option = $option;
		$this->parameters = $params;
	}

}
?>