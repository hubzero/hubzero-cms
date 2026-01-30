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
 * Install command class - fresh HUBzero installation
 **/
class Install extends Base implements CommandInterface
{
    /**
     * Default (required) command - runs the full installation
     *
     * @museDescription  Run the full HUBzero installation process
     *
     * @return  void
     **/
    public function execute()
    {
        $ansi = $this->output->isColored();

        // Run preflight checks
        if (!Install\Preflight::check($ansi, PATH_ROOT)) {
            return;
        }

        // Create app directory structure
        if (!Install\AppDirectory::create($ansi, PATH_APP)) {
            $this->output->error('App directory creation failed.');
            return;
        }

        // Load and display existing configuration status
        $this->showExistingConfigStatus($ansi);

        // Site settings
        $settingsResult = Install\SiteSettings::configure($ansi, PATH_APP);
        if ($settingsResult === null) {
            return; // User chose to exit
        }

        // Database setup
        $dbResult = Install\Database::configure($ansi, PATH_APP);
        if ($dbResult === null) {
            return; // User chose to exit
        }
        if ($dbResult === false) {
            $this->output->error('Database configuration failed.');
            return;
        }

        // Prompt before loading schema
        echo "\n";
        echo "\033[33mReady to Load Database Schema\033[39m\n";
        echo "-----------------------------\n";
        echo "\n";
        echo "This will create 381 database tables and load essential system data.\n";
        echo "This step is required for HUBzero to function.\n";
        echo "\n";

        if (!$this->promptContinue("Continue with schema loading?")) {
            echo "\n";
            echo "Installation cancelled.\n";
            echo "\n";
            return;
        }

        // Load database schema
        if (!Install\Schema::load($ansi, PATH_APP, PATH_CORE)) {
            $this->output->error('Schema loading failed.');
            return;
        }

        // Load base data
        if (!Install\Schema::loadData($ansi, PATH_APP, PATH_CORE)) {
            $this->output->error('Base data loading failed.');
            return;
        }

        // Ask about sample data
        echo "\n";
        echo "\033[33mSample Data (Optional)\033[39m\n";
        echo "-----------------------\n";
        echo "\n";
        echo "Sample data includes demo content to help you get started.\n";
        echo "This is optional and can be skipped for production installations.\n";
        echo "\n";

        if ($this->promptYesNo("Load sample data?", false)) {
            if (!Install\Schema::loadSampleData($ansi, PATH_APP, PATH_CORE)) {
                $this->output->error('Sample data loading failed.');
                // Continue anyway - sample data is optional
            }
        } else {
            echo "\n";
            echo "Skipping sample data.\n";
        }

        // Run database migrations
        echo "\n";
        echo "\033[33mDatabase Migrations\033[39m\n";
        echo "-------------------\n";
        echo "\n";
        echo "Migrations apply incremental updates to bring the database schema up to date.\n";
        echo "\n";

        if (!Install\Migrations::run($ansi, PATH_APP, PATH_CORE, PATH_ROOT)) {
            $this->output->error('Some migrations may have failed. You can retry with: muse install migrations');
            // Continue anyway - some migrations may have succeeded
        }

        // Admin user creation
        // Pass the admin email from site settings as default
        $adminDefaults = [];
        if (!empty($settingsResult['mailfrom'])) {
            $adminDefaults['email'] = $settingsResult['mailfrom'];
        }

        $adminResult = Install\AdminUser::configure($ansi, PATH_APP, $adminDefaults);
        if ($adminResult === null) {
            $this->output->error('Admin user creation cancelled or failed.');
            return;
        }

        // Final verification and next steps
        $this->showInstallationComplete($ansi, $settingsResult, $adminResult);
    }

    /**
     * Install composer vendor dependencies
     *
     * @museDescription  Install composer dependencies only
     *
     * @return  void
     **/
    public function vendor()
    {
        $ansi = $this->output->isColored();

        if (!Install\Vendor::install($ansi, false, PATH_CORE)) {
            $this->output->error('Vendor installation failed.');
        }
    }

    /**
     * Run pre-flight checks only
     *
     * @museDescription  Run pre-flight checks without installing
     *
     * @return  void
     **/
    public function check()
    {
        $ansi = $this->output->isColored();

        $passed = Install\Preflight::check($ansi, PATH_ROOT);

        if ($passed) {
            $this->output->addLine('All pre-flight checks passed!', 'success');
        } else {
            $this->output->addLine('Some pre-flight checks failed.', 'error');
        }
    }

    /**
     * Create app directory structure only
     *
     * @museDescription  Create the app directory structure only
     *
     * @return  void
     **/
    public function appdir()
    {
        $ansi = $this->output->isColored();

        if (!Install\AppDirectory::create($ansi, PATH_APP)) {
            $this->output->error('App directory creation failed.');
        }
    }

    /**
     * Configure database only
     *
     * @museDescription  Configure database connection only
     *
     * @return  void
     **/
    public function database()
    {
        $ansi = $this->output->isColored();

        $result = Install\Database::configure($ansi, PATH_APP);
        if ($result === false) {
            $this->output->error('Database configuration failed.');
        }
        // null means user chose to exit - no error message needed
    }

    /**
     * Load database schema and base data
     *
     * @museDescription  Load database schema and essential data
     *
     * @return  void
     **/
    public function schema()
    {
        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        // Prompt before loading schema
        echo "\n";
        echo "\033[33mReady to Load Database Schema\033[39m\n";
        echo "-----------------------------\n";
        echo "\n";
        echo "This will create 381 database tables and load essential system data.\n";
        echo "This step is required for HUBzero to function.\n";
        echo "\n";

        if (!$this->promptContinue("Continue with schema loading?")) {
            echo "\n";
            echo "Schema loading cancelled.\n";
            echo "\n";
            return;
        }

        // Load database schema
        if (!Install\Schema::load($ansi, PATH_APP, PATH_CORE)) {
            $this->output->error('Schema loading failed.');
            return;
        }

        // Load base data
        if (!Install\Schema::loadData($ansi, PATH_APP, PATH_CORE)) {
            $this->output->error('Base data loading failed.');
            return;
        }

        echo "\n";
        echo "\033[32mSchema and base data loaded successfully.\033[39m\n";
        echo "\n";
        echo "Next steps:\n";
        echo "  - Run migrations:           muse install migrations\n";
        echo "  - Load sample data (opt):   muse install sample\n";
        echo "\n";
    }

    /**
     * Configure site settings only
     *
     * @museDescription  Configure site settings and generate config files
     *
     * @return  void
     **/
    public function settings()
    {
        $ansi = $this->output->isColored();

        $result = Install\SiteSettings::configure($ansi, PATH_APP);
        if ($result === null) {
            // User cancelled - no error message needed
            return;
        }
    }

    /**
     * Load sample data (optional)
     *
     * @museDescription  Load sample/demo data into database
     *
     * @return  void
     **/
    public function sample()
    {
        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        echo "\n";
        echo "\033[33mSample Data\033[39m\n";
        echo "-----------\n";
        echo "\n";
        echo "Sample data includes demo content to help you get started.\n";
        echo "This is optional and can be skipped for production installations.\n";
        echo "\n";

        if (!$this->promptContinue("Load sample data?")) {
            echo "\n";
            echo "Sample data loading cancelled.\n";
            echo "\n";
            return;
        }

        if (!Install\Schema::loadSampleData($ansi, PATH_APP, PATH_CORE)) {
            $this->output->error('Sample data loading failed.');
        }
    }

    /**
     * Run database migrations
     *
     * @museDescription  Run database migrations to update schema
     *
     * @return  void
     **/
    public function migrations()
    {
        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        // Check if schema is loaded
        $dbConfig = include $configPath;
        $prefix = $dbConfig['dbprefix'] ?? 'jos_';

        try {
            $pdo = new \PDO(
                'mysql:host=' . ($dbConfig['host'] ?? 'localhost') . ';dbname=' . $dbConfig['db'] . ';charset=utf8mb4',
                $dbConfig['user'],
                $dbConfig['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $stmt = $pdo->query(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '{$prefix}%'"
            );
            $tableCount = (int) $stmt->fetchColumn();

            if ($tableCount === 0) {
                $this->output->error('Database schema not loaded.');
                $this->output->addLine('Run "muse install schema" first to load the database schema.');
                return;
            }
        } catch (\PDOException $e) {
            $this->output->error('Could not connect to database: ' . $e->getMessage());
            return;
        }

        echo "\n";
        echo "\033[33mDatabase Migrations\033[39m\n";
        echo "-------------------\n";
        echo "\n";
        echo "This will run all pending database migrations to update the schema.\n";
        echo "Migrations apply incremental changes to bring the database up to date.\n";
        echo "\n";

        if (!$this->promptContinue("Run database migrations?")) {
            echo "\n";
            echo "Migration cancelled.\n";
            echo "\n";
            return;
        }

        if (!Install\Migrations::run($ansi, PATH_APP, PATH_CORE, PATH_ROOT)) {
            $this->output->error('Some migrations may have failed.');
        }
    }

    /**
     * Create or verify admin user
     *
     * @museDescription  Create the initial admin user account
     *
     * @return  void
     **/
    public function admin()
    {
        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        // Load existing site settings for email default
        $siteConfig = Install\SiteSettings::loadExistingConfig(PATH_APP);
        $defaults = [];
        if (!empty($siteConfig['mailfrom'])) {
            $defaults['email'] = $siteConfig['mailfrom'];
        }

        $result = Install\AdminUser::configure($ansi, PATH_APP, $defaults);
        if ($result === null) {
            // User cancelled or it failed - error already displayed
            return;
        }
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
             ->addOverview('HUBzero Fresh Installation')
             ->addTasks($this)
             ->render();
    }

    /**
     * Show existing configuration status
     *
     * @param   bool  $ansi  Whether to use ANSI colors
     * @return  void
     **/
    private function showExistingConfigStatus($ansi)
    {
        $siteConfig = Install\SiteSettings::loadExistingConfig(PATH_APP);
        $dbConfig = Install\Database::loadExistingConfig(PATH_APP);

        $hasConfig = !empty($siteConfig) || !empty($dbConfig);

        if (!$hasConfig) {
            return;
        }

        echo "\n";
        echo "\033[33mExisting Configuration Found\033[39m\n";
        echo "----------------------------\n";
        echo "\n";

        if (!empty($siteConfig)) {
            if (!empty($siteConfig['sitename'])) {
                echo "  Site name:    \033[32m{$siteConfig['sitename']}\033[39m\n";
            }
            if (!empty($siteConfig['mailfrom'])) {
                echo "  Admin email:  \033[32m{$siteConfig['mailfrom']}\033[39m\n";
            }
            if (!empty($siteConfig['offset'])) {
                echo "  Timezone:     \033[32m{$siteConfig['offset']}\033[39m\n";
            }
        }

        if (!empty($dbConfig)) {
            if (!empty($dbConfig['database'])) {
                echo "  Database:     \033[32m{$dbConfig['database']}\033[39m\n";
            }
            if (!empty($dbConfig['username'])) {
                echo "  DB User:      \033[32m{$dbConfig['username']}\033[39m\n";
            }
            $host = $dbConfig['host'] ?? 'localhost';
            echo "  DB Host:      \033[32m{$host}\033[39m\n";
        }

        echo "\n";
        echo "\033[90mExisting values will be shown as defaults. Press Enter to keep them.\033[39m\n";
    }

    /**
     * Prompt user to continue (Y/n, default yes)
     *
     * @param   string  $prompt  The prompt message
     * @return  bool    True to continue, false to abort
     **/
    private function promptContinue($prompt)
    {
        while (true) {
            echo "{$prompt} [Y/n] ";
            $response = strtolower(trim(fgets(STDIN)));

            if ($response === '' || $response === 'y' || $response === 'yes') {
                return true;
            }

            if ($response === 'n' || $response === 'no') {
                return false;
            }

            echo "Please enter y/yes or n/no.\n\n";
        }
    }

    /**
     * Prompt user yes/no question
     *
     * @param   string  $prompt   The prompt message
     * @param   bool    $default  Default value (true=yes, false=no)
     * @return  bool    True for yes, false for no
     **/
    private function promptYesNo($prompt, $default = false)
    {
        $hint = $default ? '[Y/n]' : '[y/N]';

        while (true) {
            echo "{$prompt} {$hint} ";
            $response = strtolower(trim(fgets(STDIN)));

            if ($response === '') {
                return $default;
            }

            if ($response === 'y' || $response === 'yes') {
                return true;
            }

            if ($response === 'n' || $response === 'no') {
                return false;
            }

            echo "Please enter y/yes or n/no.\n\n";
        }
    }

    /**
     * Show installation complete message and next steps
     *
     * @param   bool   $ansi           Whether to use ANSI colors
     * @param   array  $settingsResult Site settings configuration
     * @param   array  $adminResult    Admin user configuration
     * @return  void
     **/
    private function showInstallationComplete($ansi, $settingsResult, $adminResult)
    {
        echo "\n";
        echo "\033[32m" . str_repeat("=", 50) . "\033[39m\n";
        echo "\033[32m  Installation Complete!\033[39m\n";
        echo "\033[32m" . str_repeat("=", 50) . "\033[39m\n";
        echo "\n";

        echo "Your HUBzero site has been successfully installed.\n";
        echo "\n";

        echo "\033[33mSite Information:\033[39m\n";
        echo "  Site Name:  \033[32m{$settingsResult['sitename']}\033[39m\n";
        if (!empty($settingsResult['live_site'])) {
            echo "  Site URL:   \033[32m{$settingsResult['live_site']}\033[39m\n";
        }
        echo "\n";

        echo "\033[33mAdmin Login:\033[39m\n";
        echo "  Username:   \033[32m{$adminResult['username']}\033[39m\n";
        echo "  Email:      \033[32m{$adminResult['email']}\033[39m\n";
        echo "\n";

        echo "\033[33mNext Steps:\033[39m\n";
        echo "  1. Configure your web server to point to: \033[36m" . PATH_ROOT . "\033[39m\n";
        echo "  2. Access your site in a web browser\n";
        echo "  3. Log in at \033[36m/administrator\033[39m with the admin credentials above\n";
        echo "  4. Configure additional settings in the admin panel\n";
        echo "\n";

        echo "\033[33mUseful Commands:\033[39m\n";
        echo "  \033[36mmuse help\033[39m              - Show available muse commands\n";
        echo "  \033[36mmuse migration run\033[39m    - Run database migrations\n";
        echo "  \033[36mmuse cache clear\033[39m      - Clear the cache\n";
        echo "\n";

        echo "\033[90mFor documentation, visit: https://help.hubzero.org\033[39m\n";
        echo "\n";
    }
}
