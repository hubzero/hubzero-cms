<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

/**
 * Finish an upgrade once the hub's new code is in place
 *
 * New code may need other libraries and a newer database, and the cache
 * still holds what the old code made. This does those three things, in
 * order, and stops at the first that fails.
 **/
class Upgrade extends Base implements CommandInterface
{
    /**
     * Bring the libraries, the database and the cache up to the code
     *
     * @museDescription  Install the libraries, run the migrations and clear the cache the code now needs
     *
     * Run it once the code is up to date, after `muse repository update -f`.
     *
     * @return  void
     **/
    public function execute()
    {
        $ansi = $this->output->isColored();

        if (!Install\Vendor::install($ansi, false, PATH_CORE)) {
            $this->output->error(
                'Composer failed, so the database was not changed. Correct the cause and run muse upgrade again.'
            );
        }

        if (!Install\Migrations::run($ansi, PATH_APP, PATH_CORE, PATH_ROOT)) {
            $this->output->error('The migrations failed. Correct the cause and run muse upgrade again.');
        }

        $this->output->addLine('');
        $this->output->addLine('Clearing the cache');
        (new Cache($this->output, $this->arguments))->clear();

        $this->output->addLine('');
        $this->output->addLine(
            'The hub is up to date with its code. Reload PHP so that it serves the new code.',
            'success'
        );
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this->output
             ->getHelpOutput()
             ->addOverview(
                 'Finish an upgrade once the new code is in place: install the
                 libraries it needs with Composer, bring the database up to date
                 with the migrations, and clear the cache. Run it after
                 muse repository update -f.'
             )
             ->render();
    }
}
