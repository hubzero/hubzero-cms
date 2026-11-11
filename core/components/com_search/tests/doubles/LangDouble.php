<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the language service in BoostDocumentTypeMap tests
 *
 * The Lang facade reaches txt() through __callStatic, which PHPUnit cannot
 * stub, so the tests mock this declared method instead.
 */
class LangDouble
{
    /**
     * Stubbed by the tests to return translations
     *
     * @param   mixed  $key
     * @return  mixed
     */
    public function txt($key)
    {
        return null;
    }
}
