<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

include_once(__DIR__ . DS . 'SingleSkuProduct.php');
include_once(__DIR__ . DS . 'CourseOffering.php');
include_once(__DIR__ . DS . 'Warehouse.php');

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
				throw new Exception(Lang::txt('Course with this alias already exists.'));
			}
		}

		parent::verify($action);
	}

}