<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Sampledata\Pack;
use Hubzero\Sampledata\Runner;

/**
 * Build a hub's sample content
 **/
class Sampledata extends Base implements CommandInterface
{
    /**
     * List the packs there are
     *
     * @museDescription  List the sample data packs available
     *
     * @return  void
     **/
    public function execute()
    {
        $packs = Pack::all();

        if (empty($packs)) {
            $this->output->addLine('There are no sample data packs.');
            return;
        }

        $runner = new Runner(null, null);
        $rows   = [];

        foreach ($packs as $pack) {
            $steps = $pack->steps();
            $done  = $runner->completed($pack->name());

            $rows[] = [
                $pack->name(),
                count($steps) . ' steps',
                count($done) . ' built',
            ];
        }

        $this->output->addTable($rows, ['Pack', 'Holds', 'On this hub']);
    }

    /**
     * Build a pack's content
     *
     * @museDescription  Build the content of a sample data pack
     *
     * Pass --pack=<name> to say which. --again rebuilds steps that have
     * already run, which only makes sense for a step written to tolerate it.
     *
     * @return  void
     **/
    public function run()
    {
        $name = $this->arguments->getOpt('pack');

        if (!$name) {
            $this->output->error('Say which pack to build, with --pack=<name>.');
            return;
        }

        $pack = new Pack($name);

        if (!$pack->exists()) {
            $this->output->error('There is no pack named ' . $name . '.');
            return;
        }

        $this->output->addLine('Building the ' . $pack->name() . ' content');

        $runner = new Runner(null, function ($message, $error = false) {
            $this->output->addLine($message, $error ? 'error' : null);
        });

        if (!$runner->run($pack, (bool) $this->arguments->getOpt('again'))) {
            $this->output->error('The ' . $pack->name() . ' pack did not finish.');
            return;
        }

        $this->output->addLine('Done.', 'success');
    }

    /**
     * Say what a pack has built on this hub
     *
     * @museDescription  Show which steps of a pack have run
     *
     * @return  void
     **/
    public function status()
    {
        $name = $this->arguments->getOpt('pack');

        if (!$name) {
            $this->output->error('Say which pack to look at, with --pack=<name>.');
            return;
        }

        $pack = new Pack($name);

        if (!$pack->exists()) {
            $this->output->error('There is no pack named ' . $name . '.');
            return;
        }

        $runner = new Runner(null, null);
        $done   = $runner->completed($pack->name());
        $rows   = [];

        foreach ($pack->steps() as $step) {
            $rows[] = [
                in_array($step->name(), $done, true) ? 'built' : '-',
                $step->name(),
                $step->describe(),
            ];
        }

        if (empty($rows)) {
            $this->output->addLine('The ' . $pack->name() . ' pack has no steps.');
            return;
        }

        $this->output->addTable($rows, ['', 'Step', 'Builds']);
    }

    /**
     * Take a pack's content back out
     *
     * @museDescription  Remove the content a sample data pack built
     *
     * @return  void
     **/
    public function remove()
    {
        $name = $this->arguments->getOpt('pack');

        if (!$name) {
            $this->output->error('Say which pack to remove, with --pack=<name>.');
            return;
        }

        $pack = new Pack($name);

        if (!$pack->exists()) {
            $this->output->error('There is no pack named ' . $name . '.');
            return;
        }

        $this->output->addLine('Removing the ' . $pack->name() . ' content');

        $runner = new Runner(null, function ($message, $error = false) {
            $this->output->addLine($message, $error ? 'error' : null);
        });

        if (!$runner->remove($pack)) {
            $this->output->error('Not all of the ' . $pack->name() . ' content could be removed.');
            return;
        }

        $this->output->addLine('Done.', 'success');
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
             ->addOverview('Build a hub\'s sample content')
             ->addTasks($this)
             ->render();
    }
}
