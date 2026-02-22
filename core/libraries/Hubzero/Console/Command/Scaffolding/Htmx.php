<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Scaffolding;

use Hubzero\Console\Command\Scaffolding;

/**
 * Scaffolding class for HTMX + Alpine component/view boilerplate.
 *
 * @museIgnoreHelp
 */
class Htmx extends Scaffolding
{
    /**
     * Build HTMX scaffold files.
     *
     * @return  void
     */
    public function construct()
    {
        $component = $this->arguments->getOpt('c', $this->arguments->getOpt('component', $this->arguments->getOpt(4)));
        if (!$component) {
            if ($this->output->isInteractive()) {
                $component = $this->output->getResponse('Component name (e.g. com_example)?');
            } else {
                $this->output->error('Error: a component name is required.');
            }
        }

        $component = strtolower(trim((string) $component));
        if (strpos($component, 'com_') === 0) {
            $component = substr($component, 4);
        }

        $view = strtolower(trim((string) $this->arguments->getOpt('v', $this->arguments->getOpt('view', 'items'))));
        $controller = strtolower(trim((string) $this->arguments->getOpt('controller', $view)));
        $withTest = (bool) $this->arguments->getOpt('with-test', false);
        $force = (bool) $this->arguments->getOpt('force', false);

        if ($view === '') {
            $this->output->error('Error: view name cannot be empty.');
        }

        if ($controller === '') {
            $this->output->error('Error: controller name cannot be empty.');
        }

        $installDir = PATH_CORE . DS . 'components';
        if (
            $this->arguments->getOpt('install-dir')
            && strlen(trim((string) $this->arguments->getOpt('install-dir'))) > 0
        ) {
            $installDir = PATH_CORE . DS
                . trim((string) $this->arguments->getOpt('install-dir'), DS)
                . DS . 'components';
        }

        $componentPath = $installDir . DS . 'com_' . $component;
        if (!is_dir($componentPath)) {
            $this->output->error('Error: component path not found: ' . $componentPath);
        }

        $controllerPath = $componentPath . DS . 'site' . DS . 'controllers' . DS . $controller . '.php';
        $defaultViewPath = $componentPath . DS . 'site' . DS . 'views'
            . DS . $view . DS . 'tmpl' . DS . 'default.php';
        $fragmentViewPath = $componentPath . DS . 'site' . DS . 'views'
            . DS . $view . DS . 'tmpl' . DS . 'fragment.php';
        $testPath = $componentPath . DS . 'tests' . DS . 'Htmx' . ucfirst($view) . 'Test.php';

        $this->ensureDirectory(dirname($controllerPath));
        $this->ensureDirectory(dirname($defaultViewPath));
        $this->ensureDirectory(dirname($fragmentViewPath));
        if ($withTest) {
            $this->ensureDirectory(dirname($testPath));
        }

        $targets = array($controllerPath, $defaultViewPath, $fragmentViewPath);
        if ($withTest) {
            $targets[] = $testPath;
        }

        if (!$force) {
            foreach ($targets as $target) {
                if (is_file($target)) {
                    $this->output->error('Error: file exists (use --force to overwrite): ' . $target);
                }
            }
        }

        $this->addReplacement('component_name', $component)
            ->addReplacement('option', 'com_' . $component)
            ->addReplacement('view', $view)
            ->addReplacement('controller', $controller);

        $this->addTemplateFile('htmx.controller.tmpl', $controllerPath)
            ->addTemplateFile('htmx.default.tmpl', $defaultViewPath)
            ->addTemplateFile('htmx.fragment.tmpl', $fragmentViewPath);

        if ($withTest) {
            $this->addTemplateFile('htmx.test.tmpl', $testPath);
        }

        $this->make();

        $this->output->addLine('Created HTMX scaffold for com_' . $component, 'success');
        $this->output->addLine('Controller: ' . $controllerPath);
        $this->output->addLine('View (full): ' . $defaultViewPath);
        $this->output->addLine('View (fragment): ' . $fragmentViewPath);
        if ($withTest) {
            $this->output->addLine('Test: ' . $testPath);
        }
    }

    /**
     * Ensure directory exists before writing scaffold files.
     *
     * @param   string  $path
     * @return  void
     */
    protected function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            $this->output->error('Error: unable to create directory: ' . $path);
        }
    }

    /**
     * Output help documentation.
     *
     * @return  void
     */
    public function help()
    {
        $this->output
            ->addOverview('Create HTMX + Alpine boilerplate inside an existing component.')
            ->addArgument(
                '-c, --component: component name',
                'Target component name (with or without com_ prefix).'
                . ' Can also be provided as first positional argument.',
                'Example: --component=com_projects'
            )
            ->addArgument(
                '-v, --view: view name',
                'Target view folder name under site/views.',
                'Example: --view=items'
            )
            ->addArgument(
                '--controller: controller file/class stem',
                'Controller name under site/controllers. Defaults to --view value.',
                'Example: --controller=items'
            )
            ->addArgument(
                '--with-test: generate baseline test',
                'Also creates a starter test file in component tests folder.'
            )
            ->addArgument(
                '--force: overwrite existing scaffold files',
                'Allow overwriting files if they already exist.'
            );
    }
}
