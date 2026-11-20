<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification;

/**
 * Message store
 */
interface MessageStore
{
    /**
     * Store a message
     *
     * @param   array<mixed>   $data
     * @param   string|null  $domain
     * @return  void
     */
    public function store($data, $domain);

    /**
     * Return a list of messages
     *
     * @param   string|null  $domain
     * @return  array<mixed>
     */
    public function retrieve($domain);

    /**
     * Clear all messages
     *
     * @param   string|null  $domain
     * @return  void
     */
    public function clear($domain);

    /**
     * Return a count of messages
     *
     * @param   string|null  $domain
     * @return  integer
     */
    public function total($domain);
}
