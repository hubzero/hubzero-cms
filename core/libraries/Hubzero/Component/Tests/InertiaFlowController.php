<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\SiteController;

class InertiaFlowController extends SiteController
{
    public function __construct() {}

    public function renderTask(): ?array
    {
        return $this->inertiaRender('Test/Page', ['ok' => true]);
    }

    public function mutateTask(): void
    {
        $this->inertiaRedirect('/target', 302);
    }
}
