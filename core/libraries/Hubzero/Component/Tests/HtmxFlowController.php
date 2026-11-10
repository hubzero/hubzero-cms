<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\SiteController;

class HtmxFlowController extends SiteController
{
    public function __construct()
    {
    }

    public function fragmentTask(): void
    {
        $this->htmxVaryOnRequest();
        $this->htmxFragment('<li>ok</li>');
    }

    public function noContentTask(): void
    {
        $this->htmxVaryOnRequest();
        $this->htmxNoContent();
    }

    public function stopPollingTask(): void
    {
        $this->htmxVaryOnRequest();
        $this->htmxStopPolling();
    }
}
