<?php

/**
 * Site Settings Step
 *
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Install\Web\Steps;

class Settings implements StepInterface
{
    private $installer;
    private $errors = [];
    private $existingConfig = [];

    /**
     * Common timezones for dropdown
     */
    private $timezones = [
        'UTC' => 'UTC (Coordinated Universal Time)',
        'America/New_York' => 'Eastern Time (US & Canada)',
        'America/Chicago' => 'Central Time (US & Canada)',
        'America/Denver' => 'Mountain Time (US & Canada)',
        'America/Los_Angeles' => 'Pacific Time (US & Canada)',
        'America/Anchorage' => 'Alaska',
        'Pacific/Honolulu' => 'Hawaii',
        'Europe/London' => 'London',
        'Europe/Paris' => 'Paris',
        'Europe/Berlin' => 'Berlin',
        'Asia/Tokyo' => 'Tokyo',
        'Asia/Shanghai' => 'Shanghai',
        'Asia/Kolkata' => 'Mumbai, Kolkata',
        'Australia/Sydney' => 'Sydney',
    ];

    public function __construct($installer)
    {
        $this->installer = $installer;
        $this->loadExistingConfig();
    }

    public function getTitle()
    {
        return 'Site Settings';
    }

    public function render()
    {
        $installer = $this->installer;
        $config = $this->existingConfig;
        $errors = $this->errors;
        $timezones = $this->timezones;
        ob_start();
        include __DIR__ . '/../views/steps/settings.php';
        return ob_get_clean();
    }

    public function process($data)
    {
        // Validate CSRF token
        if (!$this->installer->validateCsrf($data)) {
            return false;
        }

        // Validate required fields
        if (empty($data['sitename'])) {
            $this->errors['sitename'] = 'Site name is required';
        }
        if (empty($data['mailfrom'])) {
            $this->errors['mailfrom'] = 'Admin email is required';
        } elseif (
            !filter_var($data['mailfrom'], FILTER_VALIDATE_EMAIL)
            && !preg_match('/^[^@]+@[^@]+$/', $data['mailfrom'])
        ) {
            $this->errors['mailfrom'] = 'Invalid email format';
        }

        if (!empty($this->errors)) {
            return false;
        }

        // Build settings arrays
        $appSettings = [
            'sitename' => trim($data['sitename']),
            'offset' => $data['timezone'] ?? 'UTC',
            'live_site' => trim($data['live_site'] ?? ''),
        ];

        $mailSettings = [
            'mailfrom' => trim($data['mailfrom']),
            'fromname' => trim($data['sitename']),
        ];

        $metaSettings = [
            'MetaDesc' => trim($data['metadesc'] ?? ''),
        ];

        // Save configurations
        if (!$this->saveAppConfig($appSettings)) {
            $this->installer->addMessage('Failed to save application configuration.', 'error');
            return false;
        }

        if (!$this->saveMailConfig($mailSettings)) {
            $this->installer->addMessage('Failed to save mail configuration.', 'error');
            return false;
        }

        if (!$this->saveMetaConfig($metaSettings)) {
            $this->installer->addMessage('Failed to save meta configuration.', 'error');
            return false;
        }

        // Store in session for later
        $this->installer->setSessionData('settings', array_merge($appSettings, $mailSettings));
        $this->installer->markStepComplete('settings');

        return true;
    }

    /**
     * Load existing configuration
     */
    private function loadExistingConfig()
    {
        // Load from app.php
        $appConfig = PATH_APP . '/config/app.php';
        if (file_exists($appConfig)) {
            $config = include $appConfig;
            if (is_array($config)) {
                $this->existingConfig['sitename'] = $config['sitename'] ?? '';
                $this->existingConfig['timezone'] = $config['offset'] ?? 'UTC';
                $this->existingConfig['live_site'] = $config['live_site'] ?? '';
            }
        }

        // Load from mail.php
        $mailConfig = PATH_APP . '/config/mail.php';
        if (file_exists($mailConfig)) {
            $config = include $mailConfig;
            if (is_array($config)) {
                $this->existingConfig['mailfrom'] = $config['mailfrom'] ?? '';
            }
        }

        // Load from meta.php
        $metaConfig = PATH_APP . '/config/meta.php';
        if (file_exists($metaConfig)) {
            $config = include $metaConfig;
            if (is_array($config)) {
                $this->existingConfig['metadesc'] = $config['MetaDesc'] ?? '';
            }
        }

        // Defaults
        if (empty($this->existingConfig['sitename'])) {
            $this->existingConfig['sitename'] = 'My Hub';
        }
        if (empty($this->existingConfig['timezone'])) {
            $this->existingConfig['timezone'] = 'UTC';
        }
    }

    /**
     * Save application config
     */
    private function saveAppConfig($settings)
    {
        $configDir = PATH_APP . '/config';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $secret = bin2hex(random_bytes(32));

        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Application configuration\n";
        $content .= " * Generated by HUBzero installer on " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n\n";
        $content .= "return array(\n";
        $content .= "    'secret'              => '{$secret}',\n";
        $content .= "    'application_env'     => 'production',\n";
        $content .= "    'error_reporting'     => 'default',\n";
        $content .= "    'debug'               => '0',\n";
        $content .= "    'debug_lang'          => '0',\n";
        $content .= "    'profile'             => '0',\n";
        $content .= "    'editor'              => 'ckeditor',\n";
        $content .= "    'list_limit'          => '25',\n";
        $content .= "    'feed_limit'          => '10',\n";
        $content .= "    'feed_email'          => 'author',\n";
        $content .= "    'gzip'                => '0',\n";
        $content .= "    'log_path'            => " . var_export(PATH_APP . '/logs', true) . ",\n";
        $content .= "    'tmp_path'            => " . var_export(PATH_APP . '/tmp', true) . ",\n";
        $content .= "    'force_ssl'           => '0',\n";
        $content .= "    'offset'              => " . var_export($settings['offset'], true) . ",\n";
        $content .= "    'sitename'            => " . var_export($settings['sitename'], true) . ",\n";
        $content .= "    'captcha'             => 'image',\n";
        $content .= "    'access'              => '1',\n";
        $content .= "    'log_post_data'       => '0',\n";
        $content .= "    'live_site'           => " . var_export($settings['live_site'], true) . ",\n";
        $content .= "    'xmlrpc_server'       => '0',\n";
        $content .= "    'helpurl'             => '',\n";
        $content .= ");\n";

        return file_put_contents($configDir . '/app.php', $content) !== false;
    }

    /**
     * Save mail config
     */
    private function saveMailConfig($settings)
    {
        $configDir = PATH_APP . '/config';

        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Mail configuration\n";
        $content .= " * Generated by HUBzero installer on " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n\n";
        $content .= "return array(\n";
        $content .= "    'mailer' => 'mail',\n";
        $content .= "    'mailfrom' => " . var_export($settings['mailfrom'], true) . ",\n";
        $content .= "    'fromname' => " . var_export($settings['fromname'], true) . ",\n";
        $content .= "    'smtpauth' => '0',\n";
        $content .= "    'smtphost' => 'localhost',\n";
        $content .= "    'smtpport' => '25',\n";
        $content .= "    'smtpuser' => '',\n";
        $content .= "    'smtppass' => '',\n";
        $content .= "    'smtpsecure' => 'none',\n";
        $content .= "    'sendmail' => '/usr/sbin/sendmail',\n";
        $content .= ");\n";

        return file_put_contents($configDir . '/mail.php', $content) !== false;
    }

    /**
     * Save meta config
     */
    private function saveMetaConfig($settings)
    {
        $configDir = PATH_APP . '/config';
        $sitename = $this->installer->getSessionData('settings')['sitename'] ?? 'My Hub';
        $metadesc = $settings['MetaDesc'] ?: $sitename . ' - A HUBzero-powered hub for research and collaboration.';

        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Meta configuration\n";
        $content .= " * Generated by HUBzero installer on " . date('Y-m-d H:i:s') . "\n";
        $content .= " */\n\n";
        $content .= "return array(\n";
        $content .= "    'MetaAuthor' => '1',\n";
        $content .= "    'MetaTitle' => '0',\n";
        $content .= "    'MetaDesc' => " . var_export($metadesc, true) . ",\n";
        $content .= "    'MetaKeys' => 'hub, hubzero, research, collaboration',\n";
        $content .= "    'MetaVersion' => '0',\n";
        $content .= "    'MetaRights' => '',\n";
        $content .= "    'robots' => '',\n";
        $content .= ");\n";

        return file_put_contents($configDir . '/meta.php', $content) !== false;
    }
}
