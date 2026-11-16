<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam;

/**
 * Spam result.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class Result
{
    /**
     * @var  bool
     */
    protected $isSpam = false;

    /**
     * @var  array<mixed>
     */
    protected $messages = [];

    /**
     * Constructor
     *
     * @param   bool   $isSpam    Result from spam detectors
     * @param   array<mixed>  $messages  Messages to pass along
     * @return  void
     */
    public function __construct($isSpam, array $messages = [])
    {
        $this->isSpam   = $isSpam;
        $this->messages = $messages;
    }

    /**
     * Alias of SpamResult::failed();
     *
     * @return  bool
     */
    public function isSpam()
    {
        return $this->failed();
    }

    /**
     * Did the content pass?
     *
     * @return  bool
     */
    public function passed()
    {
        return $this->isSpam == false;
    }

    /**
     * Did the content fail?
     *
     * @return  bool
     */
    public function failed()
    {
        return !$this->passed();
    }

    /**
     * Get the list of messages
     *
     * @return  array<mixed>
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
