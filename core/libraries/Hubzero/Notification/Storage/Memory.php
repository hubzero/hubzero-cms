<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;

/**
 * Memory storage handler.
 */
class Memory implements MessageStore
{
    /**
     * Message bag
     *
     * @var  array
     */
    private $messages = [];

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * Store a message
     *
     * @param   array   $data
     * @param   string  $domain
     * @return  void
     */
    public function store($data, $domain)
    {
        $messages   = (array) $this->retrieve($domain);
        $messages[] = $data;

        $this->messages[$this->key($domain)] = $messages;
    }

    /**
     * Return a list of messages
     *
     * @param   string  $domain
     * @return  array
     */
    public function retrieve($domain)
    {
        $key = $this->key($domain);

        $messages = isset($this->messages[$key]) ? $this->messages[$key] : [];

        if (count($messages)) {
            $this->clear($domain);
        }

        return $messages;
    }

    /**
     * Clear all messages
     *
     * @param   string  $domain
     * @return  void
     */
    public function clear($domain)
    {
        $key = $this->key($domain);

        $this->messages[$key] = [];
    }

    /**
     * Return a count of messages
     *
     * @param   string  $domain
     * @return  integer
     */
    public function total($domain)
    {
        $key = $this->key($domain);

        $messages = isset($this->messages[$key]) ? $this->messages[$key] : [];

        return count($messages);
    }

    /**
     * Get the storage key
     *
     * @param   string  $domain
     * @return  string
     */
    private function key($domain)
    {
        $domain = (!$domain ? '' : $domain . '.');

        return $domain . 'application.queue';
    }
}
