<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Base class for "replacers", objects used in preg_replace_callback() and
 * StringUtils::delimiterReplaceCallback()
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
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
        return array(&$this, 'replace');
    }
}
