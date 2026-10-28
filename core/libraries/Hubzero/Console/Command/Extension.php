<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Content\Migration\Base as Migration;

/**
 * Extension class
 **/
class Extension extends Base implements CommandInterface
{
    /**
     * Default (required) command - just executes run
     *
     * @return  void
     **/
    public function execute()
    {
        if ($this->output->isInteractive()) {
            $actions = array('add', 'delete', 'install', 'enable', 'disable');
            $action  = $this->output->getResponse('What do you want to do? [' . implode("|", $actions) . ']');

            if (in_array($action, $actions)) {
                $this->$action();
            } else {
                $this->output->error('Sorry, I don\'t know how to do that.');
            }
        } else {
            $this->output = $this->output->getHelpOutput();
            $this->help();
            $this->output->render();
            return;
        }
    }

    /**
     * Add an entry to the extension table
     *
     * @museDescription  Adds a new extension to the extensions table
     *
     * @return  void
     **/
    public function add()
    {
        $this->alter('add');
    }

    /**
     * Delete an entry from the extension table
     *
     * @museDescription  Deletes an existing entry from the extensions table (for a template, its styles too)
     *
     * @return  void
     **/
    public function delete()
    {
        $this->alter('delete');
    }

    /**
     * Install an extension
     *
     * @museDescription  Installs an extension, adding it if it hasn't been already
     *
     * @return  void
     **/
    public function install()
    {
        $this->alter('install');
    }

    /**
     * Enable an extension
     *
     * @museDescription  Enables an existing extension
     *
     * @return  void
     **/
    public function enable()
    {
        $this->alter('enable');
    }

    /**
     * Disable an extension
     *
     * @museDescription  Disables an existing extension
     *
     * @return  void
     **/
    public function disable()
    {
        $this->alter('disable');
    }

    /**
     * Alter extension
     *
     * @param   string  $method  The method name
     * @return  void
     **/
    private function alter($method)
    {
        // The macros report through $migration->log(), which goes to a
        // "migration" callback; without one (the previous state here) their
        // messages, including why a helper refused, were simply dropped.
        $output = $this->output;
        $logger = new class ($output)
        {
            private $output;

            public function __construct($output)
            {
                $this->output = $output;
            }

            public function log($message, $type = 'info')
            {
                $this->output->addLine($message, $type ?: 'info');
            }
        };

        $migration = new Migration(\App::get('db'), array('migration' => $logger));

        $name = null;
        if ($this->arguments->getOpt('name')) {
            $name = $this->arguments->getOpt('name');
        }

        if (!isset($name)) {
            if ($this->output->isInteractive()) {
                $name = $this->output->getResponse("What extension were you wanting to {$method}?");
            } else {
                $this->output = $this->output->getHelpOutput();
                $this->help();
                $this->output->render();
                return;
            }
        }

        $extensionType = substr($name, 0, 3);

        switch ($extensionType) {
            case 'com':
                if ($method == 'add' || $method == 'delete') {
                    $extensionName = ucfirst(substr($name, 4));
                    $mthd          = $method . 'ComponentEntry';
                } else {
                    $extensionName = $name;
                    $mthd          = $method . 'Component';
                }

                $this->callMigration($migration, 'components', $mthd, array($extensionName));
                break;

            case 'mod':
                $client = $this->arguments->getOpt('client', 'site');

                if ($method == 'install') {
                    $mthd     = $method . 'Module';
                    $position = $this->arguments->getOpt('position', null);

                    if (!isset($position)) {
                        if ($this->output->isInteractive()) {
                            $position = $this->output->getResponse("Where should the module be positioned?");
                        } else {
                            $this->output->addLine('Please provide a position for the module', 'warning');
                            return;
                        }
                    }

                    $this->callMigration($migration, 'modules', $mthd, array(str_replace('mod_', '', $name), $position, true, '', (($client == 'admin') ? 1 : 0)));
                } else {
                    $mthd = $method . 'Module' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

                    $this->callMigration($migration, 'modules', $mthd, array($name, 1, '', (($client == 'admin') ? 1 : 0)));
                }
                break;

            case 'tpl':
                // The template macros are addTemplateEntry, installTemplateEntry,
                // deleteTemplateEntry, enableTemplate and disableTemplate
                $mthd = $method . 'Template' . (($method == 'add' || $method == 'delete' || $method == 'install') ? 'Entry' : '');

                $element = $name = substr($name, 4);
                $name    = ucwords($name);
                $client  = $this->arguments->getOpt('client', 'site');
                $client  = ($client == 'admin') ? 1 : 0;
                $isCore  = $this->arguments->getOpt('core', false) ? 1 : 0;

                if ($method == 'delete') {
                    $args = array($element, $client);
                } else if ($method == 'add') {
                    $args = array($element, $name, $client, 1, 0, null, $isCore);
                } else if ($method == 'install') {
                    $args = array($element, $name, $client, null, $isCore);
                } else {
                    $args = array($element, $client);
                }

                $this->callMigration($migration, 'templates', $mthd, $args);
                break;

            case 'plg':
                preg_match('/plg_([[:alnum:]]+)_([[:alnum:]]*)/', $name, $matches);

                if (!isset($matches[1]) || !isset($matches[2])) {
                    $this->output->error('This does not appear to be a valid extension name.');
                }

                $folder  = $matches[1];
                $element = $matches[2];
                $mthd    = $method . 'Plugin' . (($method == 'add' || $method == 'delete') ? 'Entry' : '');

                $this->callMigration($migration, 'plugins', $mthd, array($folder, $element));
                break;

            default:
                $this->output->error('This does not appear to be a valid extension name.');
                break;
        }

        $this->output->addLine("Successfully {$method}" . ((substr($method, -1) == 'e') ? 'd' : 'ed') . " {$name}!", 'success');
    }

    /**
     * Run one of the migration helpers
     *
     * The helpers (addModuleEntry, deleteTemplateEntry, ...) are macros that the
     * migration base resolves in __call(), so method_exists() never sees them;
     * the unknown-helper case surfaces as a BadMethodCallException instead.
     *
     * @param   object  $migration  The migration base
     * @param   string  $type       Plural extension type, for the error message
     * @param   string  $method     Helper name
     * @param   array   $args       Helper arguments
     * @return  mixed
     **/
    private function callMigration($migration, $type, $method, array $args)
    {
        try {
            $result = call_user_func_array(array($migration, $method), $args);
        } catch (\BadMethodCallException $e) {
            $this->output->error('Sorry, ' . $type . ' do not currently support the ' . $method . ' method');
        }

        if ($result === false) {
            $this->output->error('The ' . $method . ' helper reported failure (does the extension exist?)');
        }

        return $result;
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this
            ->output
            ->addOverview(
                'Extension management utility functions. Extensions are named as on disk: ' .
                'com_name, mod_name, plg_folder_element or tpl_name. Options: --name=..., ' .
                '--client=site|admin (modules and templates), --position=... (module install), --core (templates).'
            )
            ->addTasks($this);
    }
}
