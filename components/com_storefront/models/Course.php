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
 * @author    Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'SingleSkuProduct.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'CourseOffering.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');

/**
 *
 * Storefront course product class
 *
 */
class StorefrontModelCourse extends StorefrontModelSingleSkuProduct
{
	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		// Set type course
		$this->setType('course');

		// Override SKU
		$this->setSku(new StorefrontModelCourseOffering());
	}

	/**
	 * Set the course ID associated with product
	 *
	 * @param  string		courseId (alias)
	 * @return bool			true on sucess
	 */
	public function setCourseId($courseId)
	{
		$this->getSku()->setCourseId($courseId);
		return true;
	}

	/**
	 * Set the course ID associated with product
	 *
	 * @param  string		offeringId (alias)
	 * @return bool			true on sucess
	 */
	public function setOfferingId($offeringId)
	{
		$this->getSku()->setOfferingId($offeringId);
		return true;
	}

	/**
	 * Get the course ID associated with product
	 *
	 * @param	void
	 * @return 	string			course id/alias
	 */
	public function getCourseId()
	{
		return $this->getSku()->getCourseId();
	}

	/**
	 * Verify course
	 *
	 * @param  string		action (optional)
	 * @return bool			true on sucess
	 */
	public function verify($action = NULL)
	{
		// If action is 'add', make sure that course id/alias is unique
		if ($action == 'add')
		{
			$warehouse = new StorefrontModelWarehouse();
			$courseIdExists = $warehouse->getCourseByAlias($this->getCourseId());

			if ($courseIdExists)
			{
				throw new Exception(JText::_('Course with this alias already exists.'));
			}
		}

		parent::verify($action);
	}

}