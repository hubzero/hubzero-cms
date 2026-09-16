<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Repository;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Facades\App;
use Hubzero\Flavor\Applier;
use Hubzero\Flavor\Finder;

/**
 * Shape a hub with a flavor
 *
 * A flavor is a description, in a JSON file, of the levers that shape a hub:
 * components and modules to enable or disable, parameters to set, the
 * default template, the member dashboard's tiles, knowledge base and content
 * articles to publish or not, resource types to open or close. The CMS ships
 * two in core/flavors - default, the CMS without simulation tools, and full,
 * with them - and a hub may add its own in app/flavors or in a directory
 * named with --path or the HUBZERO_FLAVORS environment variable. Flavors
 * may extend one another.
 **/
class Flavor extends Base implements CommandInterface
{
    /**
     * Default (required) command
     *
     * @museDescription  Shape the hub with a flavor; run alone, lists the flavors there are
     *
     * @return  void
     **/
    public function execute()
    {
        $this->list();
    }

    /**
     * The flavors there are, and where they come from
     *
     * @museDescription  List the flavors available, with their descriptions
     * @museArgument     path  A directory of flavor files to read ahead of app/flavors and core/flavors
     *
     * @return  void
     **/
    public function list()
    {
        $finder  = $this->finder();
        $flavors = $this->flavors($finder);

        $this->output->addLine('Flavors from: ' . implode(', ', $finder->directories()));
        $this->output->addSpacer();

        foreach ($flavors as $flavor) {
            $this->output->addLine($flavor->name(), 'success');
            $this->output->addLine('  ' . ($flavor->description() ?: '(no description)'));
        }

        $this->output->addSpacer();
        $this->output->addLine("muse repository:flavor set <name> applies one; status <name> compares the hub with it.");
    }

    /**
     * One flavor's levers, resolved through everything it extends
     *
     * @museDescription  Show a flavor's levers, resolved through what it extends
     * @museArgument     path  A directory of flavor files to read ahead of app/flavors and core/flavors
     *
     * @return  void
     **/
    public function show()
    {
        $flavor = $this->named();

        $this->output->addLine($flavor->name() . ' (' . $flavor->file() . ')', 'success');
        $this->output->addLine(json_encode($flavor->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Set the flavor
     *
     * @museDescription  Pull every lever a flavor names
     * @museArgument     path  A directory of flavor files to read ahead of app/flavors and core/flavors
     *
     * @return  void
     **/
    public function set()
    {
        $flavor  = $this->named();
        $output  = $this->output;
        $applier = new Applier(App::get('db'), function ($message, $type = 'info') use ($output) {
            $output->addLine($message, $type == 'info' ? null : $type);
        });

        $applier->apply($flavor);

        $left = $applier->check($flavor);

        if ($left) {
            $this->output->addLine('Applied, but the hub still differs from the flavor:', 'warning');

            foreach ($left as $difference) {
                $this->output->addLine('  ' . $difference, 'warning');
            }

            return;
        }

        $this->output->addLine("The hub is now the {$flavor->name()} flavor.", 'success');
    }

    /**
     * Compare the hub with a flavor - or, given none, with every flavor
     *
     * @museDescription  Say how the hub differs from a flavor, or which flavor it matches
     * @museArgument     path  A directory of flavor files to read ahead of app/flavors and core/flavors
     *
     * @return  void
     **/
    public function status()
    {
        $applier = new Applier(App::get('db'));
        $name    = $this->arguments->getOpt(3);

        if ($name) {
            $flavor = $this->named();
            $found  = $applier->check($flavor);

            if (!$found) {
                $this->output->addLine("The hub is the {$flavor->name()} flavor.", 'success');
            } else {
                $this->output->addLine("The hub differs from the {$flavor->name()} flavor:", 'warning');

                foreach ($found as $difference) {
                    $this->output->addLine('  ' . $difference);
                }
            }

            $this->notes($applier->notes());

            return;
        }

        $matches = array();
        $nearest = null;
        $notes   = array();

        foreach ($this->flavors($this->finder()) as $flavor) {
            $found = $applier->check($flavor);

            if (!$found) {
                $matches[] = $flavor->name();
                $notes     = array_merge($notes, $applier->notes());
            } elseif ($nearest === null || count($found) < count($nearest[1])) {
                $nearest = array($flavor->name(), $found, $applier->notes());
            }
        }

        if ($matches) {
            $this->output->addLine('The hub is the ' . implode(' and the ', $matches) . ' flavor.', 'success');
            $this->notes(array_unique($notes));
            return;
        }

        if ($nearest) {
            $this->output->addLine("The hub matches no flavor. Nearest is {$nearest[0]}, differing in:", 'warning');

            foreach ($nearest[1] as $difference) {
                $this->output->addLine('  ' . $difference);
            }

            $this->notes($nearest[2]);
        }
    }

    /**
     * Say what a flavor names that the hub does not have - not a difference,
     * but a mistyped path looks exactly like this
     *
     * @param   array  $notes
     * @return  void
     */
    protected function notes(array $notes)
    {
        if (!$notes) {
            return;
        }

        $this->output->addLine('Named by the flavor, not on this hub: ' . implode('; ', $notes) . '.', 'warning');
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
                 'Shape the hub with a flavor: a JSON description of the levers that '
                 . 'make one hub differ from another - components and modules to enable '
                 . 'or disable, parameters to set, the default template, the member '
                 . 'dashboard tiles, knowledge base and content articles to publish or '
                 . 'not, resource types to open or close. Nothing is added or removed; '
                 . 'a flavor is a set of switches, and setting another flavor moves them. '
                 . 'The CMS ships default (the CMS without simulation tools) and full '
                 . '(with them) in core/flavors. A hub adds its own in app/flavors, or in '
                 . 'a directory named with --path or HUBZERO_FLAVORS; a flavor there with '
                 . 'a shipped name replaces it, and any flavor may extend another.'
             )
             ->noArgsSection()
             ->addSection('Usage')
             ->addArgument('muse repository:flavor list [--path=<dir>]')
             ->addArgument('muse repository:flavor show <name> [--path=<dir>]')
             ->addArgument('muse repository:flavor set <name> [--path=<dir>]')
             ->addArgument('muse repository:flavor status [<name>] [--path=<dir>]')
             ->addSpacer()
             ->addSection('Flavor files')
             ->addArgument(
                 '<dir>/*.json, *.yml, *.yaml',
                 'An object of flavor names, each an object of levers: description, extends, '
                 . 'template, components {enable, disable}, modules {enable, disable, params, items}, '
                 . 'plugins {enable, disable, params} (named folder/element), dashboard {tiles}, '
                 . 'kb {categories, articles}, content {articles}, menu {items, by path}, '
                 . 'resource_types. JSON and YAML read alike; YAML can carry a comment.'
             )
             ->render();
    }

    /**
     * The finder over the usual directories, plus --path
     *
     * @return  Finder
     */
    protected function finder()
    {
        return Finder::usual($this->arguments->getOpt('path') ?: null);
    }

    /**
     * Every flavor, or a plain error if the files do not read
     *
     * @param   Finder  $finder
     * @return  array
     */
    protected function flavors(Finder $finder)
    {
        try {
            $flavors = $finder->all();
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }

        if (!$flavors) {
            $this->output->error('No flavors found in: ' . implode(', ', $finder->directories()));
        }

        return $flavors;
    }

    /**
     * The flavor named on the command line, or a plain error
     *
     * @return  \Hubzero\Flavor\Flavor
     */
    protected function named()
    {
        $name = (string) $this->arguments->getOpt(3);

        if ($name === '') {
            $this->output->error("Please name a flavor. 'muse repository:flavor list' shows the ones there are.");
        }

        $finder = $this->finder();
        $flavor = $this->flavors($finder);

        if (!isset($flavor[$name])) {
            $this->output->error(
                "There is no '{$name}' flavor. There is: " . implode(', ', array_keys($flavor))
            );
        }

        return $flavor[$name];
    }
}
