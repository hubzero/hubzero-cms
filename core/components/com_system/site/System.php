<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;

/**
 * Component entry point
 */
class System extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        App::abort(404);
    }
}
