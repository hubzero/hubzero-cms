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
 * @package   Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

class Course_Type_Handler extends Type_Handler
{
	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId)
	{
		parent::__construct($item, $crtId);
	}

	public function handle()
	{
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php';
		$ms = new \Components\Storefront\Models\Memberships();

		// Get current registration
		$membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
		$expiration = $membership['crtmExpires'];

		// Get course ID
		$courseId = $this->item['meta']['courseId'];

		// Get user ID for the cart
		require_once (dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php');
		$userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

		// Load courses model and register
		// registerForCourse($userId, $courseId, $expiration);

		require_once PATH_CORE . DS. 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php';

		$course = \Components\Courses\Models\Course::getInstance($this->item['meta']['courseId']);

		if (!$course->offerings()->count()) {
			// error enrolling
		}
		else
		{
			// Get to the first and probably the only offering
			//$offering = $course->offerings()->current();
			$offering = $course->offering($this->item['meta']['offeringId']);

			$offering->add($userId);
			//$offering->remove($userId);
		}
	}
}
