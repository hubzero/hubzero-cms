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
 * Install command class - fresh Hubzero installation
 **/
class Install extends Base implements CommandInterface
{
    /**
     * Default (required) command - runs the full installation
     *
     * @museDescription  Run the full Hubzero installation process
     *
     * Pass --config=<file>, or --config=- to read it from standard input, to
     * install without being asked anything. The file is JSON, with a "site",
     * "database" and "admin" object holding the same values the prompts
     * collect, and an optional "sample" key saying whether to load sample
     * data. Pass --env to read the same answers from HUB_*
     * environment variables instead, as a container is configured.
     *
     * Without being asked anything, an install of a hub that is already
     * installed only brings its database up to date, so the same command can
     * start a container every time. --wait=<seconds> waits that long for
     * the database server to answer first, as one starting beside it may not
     * yet.
     *
     * @return  void
     **/
    public function execute()
    {
        $ansi = $this->output->isColored();

        if (!$this->loadAnswers()) {
            return;
        }

        $unattended = Install\Answers::isUnattended();

        if ($wait = (int) $this->arguments->getOpt('wait')) {
            $this->waitForDatabase($wait);
        }

        if ($unattended) {
            $installed = Install\AdminUser::exists(PATH_APP);

            if ($installed === null) {
                $this->output->error('The hub is configured, but its database cannot be reached.');
            }

            if ($installed) {
                $this->output->addLine('The hub is already installed. Bringing its database up to date.');

                if (!Install\Migrations::run($ansi, PATH_APP, PATH_CORE, PATH_ROOT)) {
                    $this->output->error('Some migrations failed. You can retry with: muse install migrations');
                }
                return;
            }
        }

        // Run preflight checks
        if (!Install\Preflight::check($ansi, PATH_ROOT)) {
            $this->output->error('Pre-flight checks failed.');
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
        if ($settingsResult === null && $unattended) {
            $this->output->error('Site settings failed.');
        }
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
        $this->say("\n");
        $this->say("\033[33mReady to Load Database Schema\033[39m\n");
        $this->say("-----------------------------\n");
        $this->say("\n");
        $this->say("This will create 381 database tables and load essential system data.\n");
        $this->say("This step is required for Hubzero to function.\n");
        $this->say("\n");

        if (!$unattended && !$this->promptContinue("Continue with schema loading?")) {
            $this->say("\n");
            $this->say("Installation cancelled.\n");
            $this->say("\n");
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
        $this->say("\n");
        $this->say("\033[33mSample Data (Optional)\033[39m\n");
        $this->say("-----------------------\n");
        $this->say("\n");
        $this->say("Sample data includes demo content to help you get started.\n");
        $this->say("This is optional and can be skipped for production installations.\n");
        $this->say("\n");

        $loadSample = $unattended
            ? Install\Answers::wantsSampleData()
            : $this->promptYesNo("Load sample data?", false);

        if ($loadSample) {
            if (!Install\Schema::loadSampleData($ansi, PATH_APP, PATH_CORE)) {
                $this->output->error('Sample data loading failed.');
                // Continue anyway - sample data is optional
            }
        } else {
            $this->say("\n");
            $this->say("Skipping sample data.\n");
        }

        // Run database migrations
        $this->say("\n");
        $this->say("\033[33mDatabase Migrations\033[39m\n");
        $this->say("-------------------\n");
        $this->say("\n");
        $this->say("Migrations apply incremental updates to bring the database schema up to date.\n");
        $this->say("\n");

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
        if (!$this->loadAnswers()) {
            return;
        }

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
        if (!$this->loadAnswers()) {
            return;
        }

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
        if (!$this->loadAnswers()) {
            return;
        }

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
        if (!$this->loadAnswers()) {
            return;
        }

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
        if (!$this->loadAnswers()) {
            return;
        }

        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        // Prompt before loading schema
        $this->say("\n");
        $this->say("\033[33mReady to Load Database Schema\033[39m\n");
        $this->say("-----------------------------\n");
        $this->say("\n");
        $this->say("This will create 381 database tables and load essential system data.\n");
        $this->say("This step is required for Hubzero to function.\n");
        $this->say("\n");

        if (!Install\Answers::isUnattended() && !$this->promptContinue("Continue with schema loading?")) {
            $this->say("\n");
            $this->say("Schema loading cancelled.\n");
            $this->say("\n");
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

        $this->say("\n");
        $this->say("\033[32mSchema and base data loaded successfully.\033[39m\n");
        $this->say("\n");
        $this->say("Next steps:\n");
        $this->say("  - Run migrations:           muse install migrations\n");
        $this->say("  - Load sample data (opt):   muse install sample\n");
        $this->say("\n");
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
        if (!$this->loadAnswers()) {
            return;
        }

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
        if (!$this->loadAnswers()) {
            return;
        }

        $ansi = $this->output->isColored();

        // Check if database config exists
        $configPath = PATH_APP . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->output->error('Database configuration not found at ' . $configPath);
            $this->output->addLine('Run "muse install database" first to configure the database connection.');
            return;
        }

        $this->say("\n");
        $this->say("\033[33mSample Data\033[39m\n");
        $this->say("-----------\n");
        $this->say("\n");
        $this->say("Sample data includes demo content to help you get started.\n");
        $this->say("This is optional and can be skipped for production installations.\n");
        $this->say("\n");

        if (!Install\Answers::isUnattended() && !$this->promptContinue("Load sample data?")) {
            $this->say("\n");
            $this->say("Sample data loading cancelled.\n");
            $this->say("\n");
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
        if (!$this->loadAnswers()) {
            return;
        }

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

        $this->say("\n");
        $this->say("\033[33mDatabase Migrations\033[39m\n");
        $this->say("-------------------\n");
        $this->say("\n");
        $this->say("This will run all pending database migrations to update the schema.\n");
        $this->say("Migrations apply incremental changes to bring the database up to date.\n");
        $this->say("\n");

        if (!Install\Answers::isUnattended() && !$this->promptContinue("Run database migrations?")) {
            $this->say("\n");
            $this->say("Migration cancelled.\n");
            $this->say("\n");
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
        if (!$this->loadAnswers()) {
            return;
        }

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
     * Wait for the database server to accept a connection
     *
     * Tries the configuration the hub already has, or else the answers'
     * database section, every two seconds. Giving up is not an error: the
     * install then fails on the connection itself, and says why.
     *
     * @param   int  $seconds  How long to keep trying
     * @return  void
     **/
    private function waitForDatabase($seconds)
    {
        $config = (array) (Install\Database::readConfig(PATH_APP) ?? []);

        if ($config) {
            $config['username'] = $config['user'] ?? '';
            $config['database'] = $config['db'] ?? '';
        } else {
            $config = Install\Answers::section('database');
        }

        if (empty($config['username'])) {
            return;
        }

        $dsn = 'mysql:';
        if (!empty($config['socket'])) {
            $dsn .= 'unix_socket=' . $config['socket'];
        } else {
            $dsn .= 'host=' . ($config['host'] ?? 'localhost');
            if (!empty($config['port'])) {
                $dsn .= ';port=' . $config['port'];
            }
        }

        $deadline = time() + $seconds;
        $said     = false;

        while (true) {
            try {
                new \PDO($dsn, $config['username'], $config['password'] ?? '', [\PDO::ATTR_TIMEOUT => 5]);
                return;
            } catch (\PDOException $e) {
                if (time() >= $deadline) {
                    return;
                }
            }

            if (!$said) {
                $this->output->addLine('Waiting for the database server...');
                $said = true;
            }

            sleep(2);
        }
    }

    /**
     * Print a line of the installer's own output
     *
     * Its color codes are written into the text, so they come out here when
     * the output is not colored, as in a container's log.
     *
     * @param   string  $text
     * @return  void
     **/
    private function say($text)
    {
        if (!$this->output->isColored()) {
            $text = preg_replace("/\e\[[0-9;]*m/", '', $text);
        }

        echo $text;
    }

    /**
     * Check an installed hub
     *
     * @museDescription  Check that an installed hub is served, keeps its secrets and runs as it should
     *
     * Looks at the hub as a visitor would, over its own address, and at
     * what it needs behind that: the scheduled tasks, the database server's
     * settings, mail, and who owns what it writes. Each check says what to
     * do when it does not pass.
     *
     * @return  void
     **/
    public function verify()
    {
        $this->say("\n\033[33mChecking the Hub\033[39m\n");
        $this->say("----------------\n");

        $live = rtrim((string) \Config::get('live_site'), '/');

        if ($live === '') {
            $passed = $this->report(
                'fail',
                'live_site is not set in app/config/app.php, so the scheduled tasks cannot reach the hub'
            );
        } else {
            $passed = $this->checkServed($live);
        }

        $this->checkScheduledTasks();
        $this->checkDatabaseServer();
        $this->checkMail();
        $this->checkOwnership();

        $this->say("\n");

        if (!$passed) {
            $this->output->error('The hub failed a check above.');
        }

        $this->output->addLine('No check failed.', 'success');
    }

    /**
     * Report the result of one check
     *
     * @param   string  $status  ok, warn or fail
     * @param   string  $text
     * @return  bool    False for a failure
     **/
    private function report($status, $text)
    {
        $labels = [
            'ok'   => "\033[32m[OK]\033[39m  ",
            'warn' => "\033[33m[WARN]\033[39m",
            'fail' => "\033[31m[FAIL]\033[39m",
        ];

        $this->say('  ' . $labels[$status] . ' ' . $text . "\n");

        return $status !== 'fail';
    }

    /**
     * Request one of the hub's own addresses
     *
     * @param   string  $url
     * @param   bool    $verify  Whether to require a certificate this server trusts
     * @return  array   The status code, and curl's error if there was one
     **/
    private function fetch($url, $verify = true)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY         => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => $verify,
            CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_USERAGENT      => 'hubzero-cms verify',
        ]);

        curl_exec($ch);
        $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch) ? curl_error($ch) : '';
        curl_close($ch);

        return [$code, $error];
    }

    /**
     * Check that the hub answers over HTTPS and refuses its private files
     *
     * @param   string  $live  The hub's address
     * @return  bool    False if a check failed
     **/
    private function checkServed($live)
    {
        if (stripos($live, 'https://') !== 0) {
            return $this->report('fail', "live_site is {$live}; a hub has to be served over https://");
        }

        list($code, $error) = $this->fetch($live . '/');

        if ($error !== '') {
            list($code, $insecure) = $this->fetch($live . '/', false);

            if ($insecure !== '') {
                return $this->report('fail', "{$live} does not answer from this server: {$error}");
            }

            $this->report('warn', "{$live} answers, but with a certificate this server does not trust: {$error}");
        }

        if ($code !== 200) {
            return $this->report('fail', "{$live}/ answered HTTP {$code}, not 200");
        }

        if ($error === '') {
            $this->report('ok', "{$live} answers over HTTPS");
        }

        list($code) = $this->fetch($live . '/app/config/database.php', false);

        if ($code === 403 || $code === 404) {
            return $this->report('ok', "Its configuration is refused to visitors (HTTP {$code})");
        }

        return $this->report(
            'fail',
            "{$live}/app/config/database.php answered HTTP {$code}: "
            . "the web server's rules for private paths are missing"
        );
    }

    /**
     * Check that the server ticks the scheduled tasks
     *
     * @return  void
     **/
    private function checkScheduledTasks()
    {
        try {
            $db = \App::get('db');
            $db->setQuery("SELECT MAX(`last_run`) FROM `#__cron_jobs` WHERE `state` = 1");
            $last = $db->loadResult();
        } catch (\Exception $e) {
            $this->report('warn', 'Could not read when the scheduled tasks last ran: ' . $e->getMessage());
            return;
        }

        $minutes = $last && $last !== '0000-00-00 00:00:00'
            ? (int) floor((time() - strtotime($last . ' UTC')) / 60)
            : null;

        if ($minutes === null) {
            $this->report('warn', 'The scheduled tasks have never run. Add the cron line that ticks the hub');
        } elseif ($minutes > 15) {
            $this->report('warn', "The scheduled tasks last ran {$minutes} minutes ago. Is the server ticking them?");
        } else {
            $this->report('ok', 'The scheduled tasks are running');
        }
    }

    /**
     * Check the database server's settings the hub depends on
     *
     * @return  void
     **/
    private function checkDatabaseServer()
    {
        $db = \App::get('db');

        try {
            $db->setQuery('SELECT @@GLOBAL.sql_mode');
            $mode = (string) $db->loadResult();

            if (strpos($mode, 'STRICT_TRANS_TABLES') === false) {
                $this->report(
                    'warn',
                    "MariaDB's SQL mode is not strict ('{$mode}'). Leave it at the server's default"
                );
            } else {
                $this->report('ok', "MariaDB's SQL mode is strict");
            }

            $db->setQuery("SELECT CONVERT_TZ('2000-01-01 00:00:00', 'UTC', 'America/New_York')");

            if ($db->loadResult() === null) {
                $this->report(
                    'warn',
                    "MariaDB's time zone tables are empty. Load them: "
                    . 'mariadb-tzinfo-to-sql /usr/share/zoneinfo | sudo mariadb mysql'
                );
            } else {
                $this->report('ok', "MariaDB's time zone tables are loaded");
            }
        } catch (\Exception $e) {
            $this->report('warn', 'Could not read the database server\'s settings: ' . $e->getMessage());
        }

        $usage = \Component::params('com_usage');

        if (!$usage->get('statsDBDatabase')) {
            $this->report(
                'warn',
                'The usage statistics have no database. Create ' . \Config::get('db') . '_metrics'
                . ' for the hub\'s account, then run: muse migration run -f'
            );
            return;
        }

        try {
            \Hubzero\Database\Driver::getInstance([
                'driver'   => 'pdo',
                'host'     => $usage->get('statsDBHost'),
                'port'     => $usage->get('statsDBPort'),
                'user'     => $usage->get('statsDBUsername'),
                'password' => $usage->get('statsDBPassword'),
                'database' => $usage->get('statsDBDatabase'),
            ]);
            $this->report('ok', 'The usage statistics reach their database');
        } catch (\Exception $e) {
            $this->report('warn', 'The usage statistics cannot reach their database: ' . $e->getMessage());
        }
    }

    /**
     * Check that the hub has somewhere to send mail
     *
     * @return  void
     **/
    private function checkMail()
    {
        $mailer = (string) \Config::get('mailer', 'mail');

        if ($mailer === 'smtp') {
            $host = (string) \Config::get('smtphost', 'localhost');
            $port = (int) \Config::get('smtpport', 25);

            $socket = @fsockopen($host, $port, $errno, $errstr, 5);

            if ($socket) {
                fclose($socket);
                $this->report('ok', "Mail goes to the SMTP server at {$host}:{$port}");
            } else {
                $this->report('warn', "Mail goes to {$host}:{$port}, which does not answer: {$errstr}");
            }
            return;
        }

        $command = $mailer === 'sendmail'
            ? (string) \Config::get('sendmail', '/usr/sbin/sendmail')
            : (string) ini_get('sendmail_path');
        $program = strtok($command, ' ');

        if ($program && is_executable($program)) {
            $this->report('ok', "Mail goes to {$program}");
        } else {
            $this->report(
                'warn',
                'Mail goes to ' . ($program ?: 'nothing') . ', which is not there. '
                . 'Give the hub an SMTP server in its Mail Settings'
            );
        }
    }

    /**
     * Check that everything the hub writes belongs to the account that owns it
     *
     * @return  void
     **/
    private function checkOwnership()
    {
        $owner = fileowner(PATH_ROOT);
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(PATH_APP, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        try {
            foreach ($files as $file) {
                if ($file->getOwner() !== $owner) {
                    $this->report(
                        'warn',
                        $file->getPathname() . ' belongs to another account, so the hub may not be able to write it. '
                        . 'Give it back: sudo chown -R --reference=' . PATH_ROOT . ' ' . PATH_ROOT
                    );
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->report('warn', 'Could not look through ' . PATH_APP . ': ' . $e->getMessage());
            return;
        }

        $this->report('ok', 'Everything in app/ belongs to the account that owns the hub');
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
             ->addOverview('Hubzero Fresh Installation')
             ->addTasks($this)
             ->render();
    }

    /**
     * Read the answers, from a file or the environment, if either was named
     *
     * Every task takes --config or --env, so that a step can be repeated on its own
     * without being asked anything, the same way the whole install can.
     *
     * @return  bool  False if a file was named and could not be read
     **/
    private function loadAnswers()
    {
        if ($this->arguments->getOpt('env')) {
            Install\Answers::fromEnvironment();
            $this->output->addLine('Reading the answers from HUB_* environment variables');
            return true;
        }

        $answerFile = $this->arguments->getOpt('config');

        if (!$answerFile) {
            return true;
        }

        $error = null;

        if (!Install\Answers::load($answerFile, $error)) {
            $this->output->error($error);
            return false;
        }

        $this->output->addLine('Reading the answers in ' . ($answerFile === '-' ? 'standard input' : $answerFile));

        return true;
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

        $this->say("\n");
        $this->say("\033[33mExisting Configuration Found\033[39m\n");
        $this->say("----------------------------\n");
        $this->say("\n");

        if (!empty($siteConfig)) {
            if (!empty($siteConfig['sitename'])) {
                $this->say("  Site name:    \033[32m{$siteConfig['sitename']}\033[39m\n");
            }
            if (!empty($siteConfig['mailfrom'])) {
                $this->say("  Admin email:  \033[32m{$siteConfig['mailfrom']}\033[39m\n");
            }
            if (!empty($siteConfig['offset'])) {
                $this->say("  Timezone:     \033[32m{$siteConfig['offset']}\033[39m\n");
            }
        }

        if (!empty($dbConfig)) {
            if (!empty($dbConfig['database'])) {
                $this->say("  Database:     \033[32m{$dbConfig['database']}\033[39m\n");
            }
            if (!empty($dbConfig['username'])) {
                $this->say("  DB User:      \033[32m{$dbConfig['username']}\033[39m\n");
            }
            $host = $dbConfig['host'] ?? 'localhost';
            $this->say("  DB Host:      \033[32m{$host}\033[39m\n");
        }

        $this->say("\n");
        $this->say("\033[90mExisting values will be shown as defaults. Press Enter to keep them.\033[39m\n");
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
            $this->say("{$prompt} [Y/n] ");
            $response = strtolower(trim(fgets(STDIN)));

            if ($response === '' || $response === 'y' || $response === 'yes') {
                return true;
            }

            if ($response === 'n' || $response === 'no') {
                return false;
            }

            $this->say("Please enter y/yes or n/no.\n\n");
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
            $this->say("{$prompt} {$hint} ");
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

            $this->say("Please enter y/yes or n/no.\n\n");
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
        $this->say("\n");
        $this->say("\033[32m" . str_repeat("=", 50) . "\033[39m\n");
        $this->say("\033[32m  Installation Complete!\033[39m\n");
        $this->say("\033[32m" . str_repeat("=", 50) . "\033[39m\n");
        $this->say("\n");

        $this->say("Your Hubzero site has been successfully installed.\n");
        $this->say("\n");

        $this->say("\033[33mSite Information:\033[39m\n");
        $this->say("  Site Name:  \033[32m{$settingsResult['sitename']}\033[39m\n");
        if (!empty($settingsResult['live_site'])) {
            $this->say("  Site URL:   \033[32m{$settingsResult['live_site']}\033[39m\n");
        }
        $this->say("\n");

        $this->say("\033[33mAdmin Login:\033[39m\n");
        $this->say("  Username:   \033[32m{$adminResult['username']}\033[39m\n");
        $this->say("  Email:      \033[32m{$adminResult['email']}\033[39m\n");
        $this->say("\n");

        $this->say("\033[33mNext Steps:\033[39m\n");
        $this->say("  1. Configure your web server to point to: \033[36m" . PATH_ROOT . "\033[39m\n");
        $this->say("  2. Access your site in a web browser\n");
        $this->say("  3. Log in at \033[36m/administrator\033[39m with the admin credentials above\n");
        $this->say("  4. Configure additional settings in the admin panel\n");
        $this->say("\n");

        $this->say("\033[33mUseful Commands:\033[39m\n");
        $this->say("  \033[36mmuse help\033[39m              - Show available muse commands\n");
        $this->say("  \033[36mmuse migration run\033[39m    - Run database migrations\n");
        $this->say("  \033[36mmuse cache clear\033[39m      - Clear the cache\n");
        $this->say("\n");

        $this->say("\033[90mFor documentation, visit: https://help.hubzero.org\033[39m\n");
        $this->say("\n");
    }
}
