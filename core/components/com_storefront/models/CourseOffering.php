<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models;

require_once __DIR__ . DS . 'Sku.php';

class CourseOffering extends Sku
{

	public function __construct()
	{
		parent::__construct();

		//$this->setAllowMultiple(0);
		$this->setTrackInventory(0);
	}

	public function setCourseId($courseId)
	{
		$this->data->courseId = $courseId;
		$this->data->meta['courseId'] = $courseId;
	}

	public function setOfferingId($offeringId)
	{
		$this->data->offeringId = $offeringId;
		$this->data->meta['offeringId'] = $offeringId;
	}

	public function getCourseId()
	{
		return $this->data->meta['courseId'];
	}

	public function verify()
	{
		parent::verify();

		// Each course has to have a course ID
		if (empty($this->data->courseId))
		{
			throw new \Exception(Lang::txt('No course id'));
		}
	}

}
