<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers\Type;

use Components\Cart\Lib\Handlers\TypeHandler;

class CourseTypeHandler extends TypeHandler
{
    /**
     * Constructor
     *
     * @param   object   $item
     * @param   integer  $crtId
     * @return  void
     */
    public function __construct($item, $crtId)
    {
        parent::__construct($item, $crtId);
    }

    public function handle()
    {
        $ms = new \Components\Storefront\Models\Memberships();

        // Get current registration
        $membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
        $expiration = $membership['crtmExpires'];

        // Get course ID
        $courseId = $this->item['meta']['courseId'];

        // Get user ID for the cart
        $userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

        // Load courses model and register
        $course = \Components\Courses\Models\Course::getInstance($this->item['meta']['courseId']);

        if (!$course->offerings()->count()) {
            // error enrolling
        } else {
            // Get to the first and probably the only offering
            $offering = $course->offering($this->item['meta']['offeringId']);
            $offering->add($userId);
        }
    }
}
