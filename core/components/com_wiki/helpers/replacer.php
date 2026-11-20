<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

/**
 * Base class for "replacers", objects used in preg_replace_callback() and
 * StringUtils::delimiterReplaceCallback()
 */
class Replacer
{
    /**
     * Short description for 'cb'
     *
     * Long description (if any) ...
     *
     * @return     mixed Return description (if any) ...
     */
    public function cb()
    {
        return [&$this, 'replace'];
    }
}
