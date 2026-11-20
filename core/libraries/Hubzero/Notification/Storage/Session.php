<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;

/**
 * Session storage handler.
 */
class Session implements MessageStore
{
    /**
     * Session handler
     *
     * @var  \Hubzero\Session\Manager
     */
    private $session;

    /**
     * Constructor
     *
     * @param   \Hubzero\Session\Manager  $session
     * @return  void
     */
    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * Store a message
     *
     * @param   array<mixed>   $data
     * @param   string|null  $domain
     * @return  void
     */
    public function store($data, $domain)
    {
        $messages   = (array) $this->retrieve($domain);
        $messages[] = $data;

        $this->session->set($this->key($domain), $messages);
    }

    /**
     * Return a list of messages
     *
     * @param   string|null  $domain
     * @return  array<mixed>
     */
    public function retrieve($domain)
    {
        $messages = $this->session->get($this->key($domain), []);

        if (count($messages)) {
            $this->clear($domain);
        }

        return $messages;
    }

    /**
     * Clear all messages
     *
     * @param   string|null  $domain
     * @return  void
     */
    public function clear($domain)
    {
        $this->session->set($this->key($domain), null);
    }

    /**
     * Return a count of messages
     *
     * @param   string|null  $domain
     * @return  integer
     */
    public function total($domain)
    {
        $messages = $this->session->get($this->key($domain), []);

        return count($messages);
    }

    /**
     * Get the storage key
     *
     * @param   string|null  $domain
     * @return  string
     */
    private function key($domain)
    {
        $domain = (!$domain ? '' : $domain . '.');

        return $domain . 'application.queue';
    }
}
