<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Obj;

/**
 * Class for storing iTunes owner information
 */
class ItunesOwner extends Obj
{
    /**
     * Email attribute
     *
     * @var  string
     */
    public $email = '';

    /**
     * Name attribute
     *
     * @var  string
     */
    public $name = '';
}
