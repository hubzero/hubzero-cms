<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

require_once __DIR__ . DS . 'SingleSkuProduct.php';
require_once __DIR__ . DS . 'CourseOffering.php';
require_once __DIR__ . DS . 'Warehouse.php';

/**
 *
 * Storefront course product class
 *
 */
class Course extends SingleSkuProduct
{
	/**
	 * Contructor
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($pId = false)
	{
		parent::__construct($pId);

		if (!$pId)
		{
			// Set type course
			$this->setType('course');

			// Override SKU
			$this->setSku(new CourseOffering());
		}
	}

	/**
	 * Set the course ID associated with product
	 *
	 * @param  string		courseId (alias)
	 * @return bool			true on success
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
	 * @return bool			true on success
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
	 * @return bool			true on success
	 */
	public function verify($action = null)
	{
		// If action is 'add', make sure that course id/alias is unique
		if ($action == 'add')
		{
			$warehouse = new Warehouse();
			$courseIdExists = $warehouse->getCourseByAlias($this->getCourseId());

			if ($courseIdExists)
			{
				throw new \Exception(Lang::txt('Course with this alias already exists.'));
			}
		}

		parent::verify($action);
	}

}
