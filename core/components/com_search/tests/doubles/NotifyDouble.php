<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the message notifier in RecordProcessingHelper tests
 *
 * Hubzero\Message\Notify reaches success() and error() through __call,
 * which PHPUnit cannot stub, so the tests mock these declared methods.
 */
class NotifyDouble
{
    /**
     * Stubbed by the tests to observe success messages
     *
     * @param   mixed  $message
     * @return  void
     */
    public function success($message) {}

    /**
     * Stubbed by the tests to observe error messages
     *
     * @param   mixed  $message
     * @return  void
     */
    public function error($message) {}
}
