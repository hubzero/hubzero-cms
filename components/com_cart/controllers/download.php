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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product viewing controller class
 */
class CartControllerDownload extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$this->warehouse = new StorefrontModelWarehouse();

		parent::execute();
	}

	/**
	 * Serve the file
	 *
	 * @param		$pId
	 * @return     	void
	 */
	public function displayTask()
	{
		// Get the transaction ID
		$tId  = JRequest::getCmd('task', '');

		// Get the SKU ID
		$sId = JRequest::getVar('p0');

		print_r($tId);
		echo ' - ';
		print_r($sId); die;

		// Check if the transaction is complete and belongs to the user and is active

		// Check if the product is valid and downloadable; find the file

		// Path and file name
		$dir = JPATH_ROOT . DS . 'media' . DS . 'software';
		$file = $dir . DS . 'download1.txt';

		// Serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($file);
		$xserver->serve_attachment($file); // Firefox and Chrome fail if served inline
		exit;
	}
}

