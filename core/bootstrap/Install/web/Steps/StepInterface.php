<?php

/**
 * Step Interface for Installation Wizard
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

interface StepInterface
{
    /**
     * Get the step title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Render the step content
     *
     * @return string  HTML content
     */
    public function render();

    /**
     * Process form submission
     *
     * @param  array  $data  POST data
     * @return bool|string  True to continue, string error message on failure
     */
    public function process($data);
}
