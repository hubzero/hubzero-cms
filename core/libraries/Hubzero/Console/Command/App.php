<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

/**
 * Migration class
 **/
class App extends Base implements CommandInterface
{
    /**
     * Default (required) command - just executes run
     *
     * @return  void
     **/
    public function execute()
    {
        $this->output = $this->output->getHelpOutput();
        $this->help();
        $this->output->render();
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this
            ->helpOutput()
            ->addOverview(
                'Helper for the app directory',
            )
            ->addTasks($this);
    }
}
