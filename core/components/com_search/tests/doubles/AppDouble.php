<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the application in RecordProcessingHelper tests
 *
 * The App facade reaches redirect() through __callStatic, which PHPUnit
 * cannot stub, so the tests mock this declared method instead.
 */
class AppDouble
{
    /**
     * Stubbed by the tests to observe redirects
     *
     * @param   mixed  $url
     * @return  void
     */
    public function redirect($url)
    {
    }
}
