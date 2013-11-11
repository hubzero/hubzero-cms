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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'PaymentGateway'
 * 
 * Long description (if any) ...
 */
class Hubzero_Badges
{
	private $badgesHandler;

	/**
	 * Constructor
	 * 
	 * @param	string 		badgesProivder
	 * @return  void
	 */
	public function __construct($badgesProivder)
	{
		switch ($badgesProivder)
		{
			case "PASSPORT":
				$this->badgesHandler = new Hubzero_Badges_Passport_BadgesProvider();
			break;

			/*
			case "MOZILLA":
				$this->badgesHandler = new Hubzero_Badges_Mozilla_BadgesProvider();
			break;
			*/

			default:
				die('Bad badges provider.');
		}
	}

	/**
	 * Get badges provider instance
	 *
	 */
	public function getProvider()
	{
		return $this->badgesHandler;
	}
}