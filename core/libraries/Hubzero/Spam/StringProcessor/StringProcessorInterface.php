<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\StringProcessor;

/**
 * Interface for spam string processors.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
interface StringProcessorInterface
{
    /**
     * Prepare a string
     *
     * @param   string  $string
     * @return  string
     */
    public function prepare($string);
}
