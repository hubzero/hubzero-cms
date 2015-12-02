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

namespace Components\Storefront\Models;

require_once(__DIR__ . DS . 'Sku.php');

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