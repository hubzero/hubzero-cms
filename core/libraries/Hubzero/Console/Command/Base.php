<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * Base command class
 **/
class Base
{
    /**
     * Output object, implements the Output interface
     *
     * @var  \Hubzero\Console\Output
     **/
    protected $output;

    /**
     * Arguments object, implements the Argument interface
     *
     * @var  \Hubzero\Console\Arguments
     **/
    protected $arguments;

    /**
     * Constructor - sets output mechanism and arguments for use by command
     *
     * @param   \Hubzero\Console\Output    $output     The ouput renderer
     * @param   \Hubzero\Console\Arguments $arguments  The command arguments
     * @return  void
     **/
    public function __construct(Output $output, Arguments $arguments)
    {
        $this->output    = $output;
        $this->arguments = $arguments;
    }

    /**
     * The output as a help renderer
     *
     * A help task is handed one already. A help() reached some other way
     * is given one here, in place of the plain output it was built with.
     *
     * @return  \Hubzero\Console\Output\Help
     **/
    protected function helpOutput()
    {
        if (!$this->output instanceof Output\Help) {
            $this->output = $this->output->getHelpOutput();
        }

        return $this->output;
    }
}
