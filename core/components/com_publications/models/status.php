<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication status model class
 */
class Status extends Obj
{
    /**
     * Int(1)
     * 1 = requirement satisfied
     * 0 = requirement not satisfied
     * 2 = requirement partially satisfied (incomplete)
     * 3 = not available
     *
     * @var  int|null
     */
    public $status = null;

    /**
     * Status message
     *
     * @var  mixed
     */
    public $message = null;

    /**
     * For nested blocks
     *
     * @var  \stdClass|null
     */
    public $elements = null;

    /**
     * Time of last status update
     *
     * @var  mixed
     */
    public $lastupdate = null;
}
