<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Tests\Mock\Alternative\Cli\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;

/**
 * Alternative command class
 *
 * @codeCoverageIgnore
 **/
class Subcommand extends Base implements CommandInterface
{
    /**
     * Default (required) command
     *
     * @return  void
     **/
    public function execute() {}

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help() {}
}
