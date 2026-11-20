<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Auditor;

/**
 * Auditor test interface
 */
interface Test
{
    /**
     * Run content through test
     *
     * @param   array<mixed>  $data
     * @param   array<mixed>  $options
     * @return  object
     */
    public function examine(array $data, array $options = []);
}
