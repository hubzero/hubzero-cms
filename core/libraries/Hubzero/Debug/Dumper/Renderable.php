<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Renderable interface to be extended
 */
interface Renderable
{
    /**
     * Returns renderer name
     *
     * @return  string
     */
    public function getName();

    /**
     * Set the list of messages
     *
     * @param   mixed  $messages
     * @return  object
     */
    public function setMessages($messages);

    /**
     * Get the list of messages
     *
     * @return  array<mixed>
     */
    public function getMessages();

    /**
     * Render a list of messages
     *
     * @param   array<mixed>|null  $messages
     * @return  string
     */
    public function render($messages = null);
}
