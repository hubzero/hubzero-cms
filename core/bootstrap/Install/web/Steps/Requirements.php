<?php

/**
 * Requirements Check Step
 *
 * Based on the CLI preflight checks from Hubzero\Console\Command\Install\Preflight
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

use Hubzero\System\Requirements as SystemRequirements;

class Requirements implements StepInterface
{
    private $installer;
    private $requirements = [];
    private $passed = true;

    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->checkRequirements();
    }

    public function getTitle()
    {
        return 'System Requirements';
    }

    public function render()
    {
        $installer = $this->installer;
        $requirements = $this->requirements;
        $passed = $this->passed;
        ob_start();
        include __DIR__ . '/../views/steps/tmpl/requirements.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        if (!$this->passed) {
            $this->installer->addMessage('Please fix the failed requirements before continuing.', 'error');
            return false;
        }

        $this->installer->markStepComplete('requirements');
        return true;
    }

    /**
     * Check all requirements (mirrors CLI Preflight::check())
     */
    private function checkRequirements()
    {
        // Note: Operating system check is handled earlier in WizardServiceProvider
        // before any installer code runs (Windows users see a dedicated error page)

        // Check system commands (unzip)
        $this->checkSystemCommands();

        // Check vendor dependencies
        $this->checkVendor();

        // Check PHP version
        $this->checkPhpVersion();

        // Check required extensions
        $this->checkExtensions();

        // Check write permissions
        $this->checkWritePermissions();

        // Check overall pass status
        foreach ($this->requirements as $req) {
            if (!$req['passed']) {
                $this->passed = false;
                break;
            }
        }
    }

    /**
     * Check PHP version meets minimum requirement
     */
    private function checkPhpVersion()
    {
        $this->requirements['php_version'] = SystemRequirements::checkPhpVersion();
    }

    /**
     * Check all required PHP extensions are loaded
     */
    private function checkExtensions()
    {
        $this->requirements['php_extensions'] = SystemRequirements::checkExtensions();
    }

    /**
     * Check required system commands are available
     */
    private function checkSystemCommands()
    {
        $this->requirements['cmd_unzip'] = SystemRequirements::checkUnzip();
    }

    /**
     * Check composer vendor dependencies are installed
     */
    private function checkVendor()
    {
        $this->requirements['vendor'] = SystemRequirements::checkVendor(PATH_CORE);
    }

    /**
     * Check required directories are writable
     */
    private function checkWritePermissions()
    {
        $appPath = PATH_APP;
        $appParent = dirname($appPath);

        if (is_dir($appPath)) {
            $writable = is_writable($appPath);
            $checkPath = $appPath;
        } else {
            $writable = is_writable($appParent);
            $checkPath = $appParent;
        }

        $this->requirements['writable_app'] = [
            'name' => 'Document Root writable',
            'required' => 'Writable',
            'current' => $writable ? 'Writable' : 'Not writable',
            'passed' => $writable,
            'description' => $writable ? $checkPath : $checkPath . ' must be writable',
        ];
    }
}
