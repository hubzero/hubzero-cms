<?php

/**
 * Ownership Verification Step
 *
 * Requires proof of file system ownership before allowing installation.
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

use Bootstrap\Install\Web\SecurityGuard;

class Verify implements StepInterface
{
    /**
     * @var \Bootstrap\Install\Web\Installer
     */
    private $installer;

    /**
     * @var SecurityGuard
     */
    private $security;

    /**
     * Constructor
     *
     * @param  \Bootstrap\Install\Web\Installer  $installer
     */
    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->security = new SecurityGuard(defined('PATH_ROOT') ? PATH_ROOT : null);
    }

    /**
     * Get the step title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Verify Ownership';
    }

    /**
     * Render the step content
     *
     * @return string
     */
    public function render()
    {
        $security = $this->security;
        $installer = $this->installer;

        ob_start();
        include __DIR__ . '/../views/steps/verify.php';
        return ob_get_clean();
    }

    /**
     * Process form submission
     *
     * @param  array  $data  POST data
     * @return bool|string
     */
    public function process($data)
    {
        // Validate CSRF token
        if (!$this->security->validateCsrfToken($data['csrf_token'] ?? null)) {
            $this->security->logSecurityEvent('CSRF_FAIL', 'Invalid CSRF token on verify step');
            $this->installer->addMessage('Security validation failed. Please try again.', 'error');
            return false;
        }

        // Check if locked out
        if ($this->security->isLockedOut()) {
            $remaining = ceil($this->security->getLockoutRemaining() / 60);
            $this->security->logSecurityEvent('LOCKOUT_ATTEMPT', "Attempt during lockout");
            $this->installer->addMessage(
                "Too many failed attempts. Please wait {$remaining} minute(s).",
                'error'
            );
            return false;
        }

        // Verify the ownership file
        $result = $this->security->verifyOwnershipFile();

        if (!$result['valid']) {
            $this->security->logSecurityEvent('VERIFY_FAIL', $result['error']);
            $this->installer->addMessage($result['error'], 'error');
            return false;
        }

        // File verified - bind this client to the session
        if (!$this->security->bindClient()) {
            $this->security->logSecurityEvent('BIND_FAIL', 'Failed to bind client');
            $this->installer->addMessage(
                'Failed to complete verification. Please check file permissions.',
                'error'
            );
            return false;
        }

        // Log successful verification
        $this->security->logSecurityEvent(
            'VERIFY_SUCCESS',
            'Installation authorized from IP: ' . $this->security->getClientIP()
        );

        $this->installer->markStepComplete('verify');
        return true;
    }

    /**
     * Get the SecurityGuard instance (for view access)
     *
     * @return SecurityGuard
     */
    public function getSecurityGuard(): SecurityGuard
    {
        return $this->security;
    }
}
