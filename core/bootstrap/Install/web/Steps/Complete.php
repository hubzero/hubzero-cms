<?php

/**
 * Installation Complete Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

class Complete implements StepInterface
{
    private $installer;

    public function __construct($installer)
    {
        $this->installer = $installer;
    }

    public function getTitle()
    {
        return 'Installation Complete';
    }

    public function render()
    {
        $settings = $this->installer->getSessionData('settings') ?? [];
        $admin = $this->installer->getSessionData('admin') ?? [];
        $siteUrl = $this->getSiteUrl();

        // Perform security cleanup (remove verification file)
        $this->installer->cleanup();

        ob_start();
        include __DIR__ . '/../views/steps/complete.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Redirect to site (session already cleared by cleanup in render)
        $url = $this->getSiteUrl();
        header('Location: ' . $url);
        exit;
    }

    /**
     * Get the site URL
     */
    private function getSiteUrl()
    {
        $settings = $this->installer->getSessionData('settings') ?? [];

        if (!empty($settings['live_site'])) {
            return $settings['live_site'];
        }

        // Auto-detect
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Remove /core/bootstrap/Install/web from path
        $path = dirname($_SERVER['SCRIPT_NAME']);
        $path = preg_replace('#/core/bootstrap/Install/web$#', '', $path);

        return $protocol . '://' . $host . $path;
    }
}
