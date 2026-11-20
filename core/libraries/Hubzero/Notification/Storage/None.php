<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;

/**
 * Null storage handler.
 */
class None implements MessageStore
{
    /**
     * Store a message
     *
     * @param   array<mixed>   $data
     * @param   string|null  $domain
     * @return  void
     */
    public function store($data, $domain)
    {
        return;
    }

    /**
     * Return a list of messages
     *
     * @param   string|null  $domain
     * @return  array<mixed>
     */
    public function retrieve($domain)
    {
        return [];
    }

    /**
     * Clear all messages
     *
     * @param   string|null  $domain
     * @return  void
     */
    public function clear($domain)
    {
        return;
    }

    /**
     * Return a count of messages
     *
     * @param   string|null  $domain
     * @return  integer
     */
    public function total($domain)
    {
        return 0;
    }
}
