<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Memberships.php';
		$ms = new \Components\Storefront\Models\Memberships();

		// Get current registration
		$membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
		$expiration = $membership['crtmExpires'];

		// Get course ID
		$courseId = $this->item['meta']['courseId'];

		// Get user ID for the cart
		require_once dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php';
		$userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

		// Load courses model and register
		// registerForCourse($userId, $courseId, $expiration);

		require_once \Component::path('com_courses') . DS . 'models' . DS . 'course.php';

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
