<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Cache;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Facades\Filesystem;

/**
 * Cache command class
 **/
class Css extends Base implements CommandInterface
{
    /**
     * Default (required) command - just executes run
     *
     * @return  void
     **/
    public function execute()
    {
        $this->help();
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
             ->addOverview('Cache Management')
             ->render();
    }

    /**
     * Clear Site.css & Site.less.cache files
     *
     * @return  void
     */
    public function clear()
    {
        // The compiled stylesheet is written per client - app/cache/site/site.css,
        // app/cache/admin/site.css - by Document\Assets::getSystemStylesheet().
        // This looked for app/cache/site.css, which nothing writes, so it
        // reported the file missing and cleared nothing on every hub.
        $cacheDir = PATH_APP . DS . 'cache';
        $files    = array('site.css', 'site.less.cache');
        $removed  = 0;

        foreach ((array) glob($cacheDir . DS . '*', GLOB_ONLYDIR) as $clientDir) {
            foreach ($files as $file) {
                $path = $clientDir . DS . $file;

                if (!is_file($path)) {
                    continue;
                }

                $name = basename($clientDir) . '/' . $file;

                if (!Filesystem::delete($path)) {
                    $this->output->addLine('Unable to delete cache file: ' . $name, 'error');
                } else {
                    $this->output->addLine($name . ' deleted', 'success');
                    $removed++;
                }
            }
        }

        if (!$removed) {
            $this->output->addLine('No compiled CSS to remove', 'warning');
            return;
        }

        // success!
        $this->output->addLine('All CSS cache files removed!', 'success');
    }
}
