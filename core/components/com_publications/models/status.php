<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication status model class
 *
 * @property  string|null  $label  The element's label, set by curation for history tracking
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
     * @var  int
     */
    public $status;

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
