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
defined('_JEXEC') or die('Restricted access');

/**
 * Model for a handler editor
 */
class PublicationsModelEditor extends \Hubzero\Base\Object
{
	/**
	 * Handler object
	 *
	 * @var object
	 */
	public $handler = null;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = null;

	/**
	 * Configs
	 *
	 * @var object
	 */
	public $_configs = null;

	/**
	 * Constructor
	 *
	 * @param   string $scope
	 * @return  void
	 */
	public function __construct($handler, $configs)
	{
		$this->_db = JFactory::getDBO();

		$this->handler 	= $handler;
		$this->configs 	= $configs;
	}

	/**
	 * Draw status
	 *
	 * @return  string
	 */
	public function drawStatus()
	{
		return $this->handler->drawStatus($this);
	}

	/**
	 * Draw editor content
	 *
	 * @return  string
	 */
	public function drawEditor()
	{
		return $this->handler->drawEditor($this);
	}
}

