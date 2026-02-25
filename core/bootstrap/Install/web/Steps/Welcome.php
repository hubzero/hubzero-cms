<?php

/**
 * Welcome Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

class Welcome implements StepInterface
{
    private $installer;

    public function __construct($installer)
    {
        $this->installer = $installer;
    }

    public function getTitle()
    {
        return 'Welcome';
    }

    public function render()
    {
        $installer = $this->installer;
        ob_start();
        include __DIR__ . '/../views/steps/tmpl/welcome.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        // Accept license agreement
        if (empty($data['accept_license'])) {
            $this->installer->addMessage('You must accept the license agreement to continue.', 'error');
            return false;
        }

        $this->installer->markStepComplete('welcome');
        return true;
    }
}
