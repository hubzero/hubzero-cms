<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\SiteController;

class InertiaFlowController extends SiteController
{
    public function __construct() {}

    /**
     * @return  array<mixed>|null
     */
    public function renderTask(): ?array
    {
        return $this->inertiaRender('Test/Page', ['ok' => true]);
    }

    public function mutateTask(): void
    {
        $this->inertiaRedirect('/target', 302);
    }
}
