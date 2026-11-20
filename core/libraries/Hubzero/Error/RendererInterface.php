<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error;

/**
 * Error renderer interface
 */
interface RendererInterface
{
    /**
     * Display the given exception to the user.
     *
     * @param  \ErrorException|\Throwable  $error
     * @return void
     */
    public function render($error);
}
