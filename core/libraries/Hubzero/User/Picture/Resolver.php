<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Picture;

/**
 * User picture resolver
 */
interface Resolver
{
    /**
     * Get a path or URL to a user pciture
     *
     * @param   integer  $id
     * @param   string   $name
     * @param   string   $email
     * @param   bool     $thumbnail
     * @return  string
     */
    public function picture($id, $name, $email, $thumbnail = true);
}
