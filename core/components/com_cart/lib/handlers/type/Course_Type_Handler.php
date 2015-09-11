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
 * @package   Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		require_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
		$ms = new StorefrontModelMemberships();

		// Get current registration
		$membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
		$expiration = $membership['crtmExpires'];

		// Get course ID
		$courseId = $this->item['meta']['courseId'];

		// Get user ID for the cart
		require_once(JPATH_BASE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');
		$userId = CartModelCart::getCartUser($this->crtId);

		// Load courses model and register
		// registerForCourse($userId, $courseId, $expiration);

		require_once(JPATH_BASE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

		$course = \Components\Courses\Models\Course::getInstance($this->item['meta']['courseId']);

		if (!$course->offerings()->count())
		{
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
