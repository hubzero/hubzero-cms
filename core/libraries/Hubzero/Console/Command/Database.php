<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Facades\App;
use Hubzero\Utility\Date;
use Hubzero\Config\Registry;
use Hubzero\Content\Migration\Base as Migration;
use Hubzero\Console\Command\Database\SchemaConverter;
use Hubzero\Database\DatabaseManager;
use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Plugin;

/**
 * Database class
 **/
class Database extends Base implements CommandInterface
{
    /**
     * Default (required) command - just executes run
     *
     * @return  void
     **/
    public function execute()
    {
        $this->output = $this->output->getHelpOutput();
        $this->help();
        $this->output->render();
    }

    /**
     * Dump the database
     *
     * @museDescription  Dumps the hub's database into a file
     *
     * Give the file to write as the argument, or leave it out to write one
     * in the home directory. Tables without the hub's prefix are left out
     * unless --all-tables is given. The dump is consistent without locking
     * the hub, and only its owner can read it.
     *
     * @return  void
     **/
    public function dump()
    {
        $db       = App::get('db');
        $tables   = $db->getTableList();
        $prefix   = $db->getPrefix();
        $excludes = [];
        $includes = (array) $this->arguments->getOpt('include-table', []);

        if (!$this->arguments->getOpt('all-tables')) {
            $this->output->addLine('Dumping database with all prefixed tables included');
            foreach ($tables as $table) {
                if (strpos($table, $prefix) !== 0 && !in_array(str_replace('#__', $prefix, $table), $includes)) {
                    $excludes[] = '--ignore-table=' . escapeshellarg(Config::get('db') . '.' . $table);
                } elseif (in_array(str_replace('#__', $prefix, $table), $includes)) {
                    $this->output->addLine('Also including `' . $table . '`');
                }
            }
        } else {
            $this->output->addLine('Dumping database with all tables included');
        }

        if (!$filename = $this->file()) {
            $now      = new Date();
            $filename = tempnam(getenv('HOME'), gethostname() . '.mysql.dump.' . $now->format('Y.m.d') . '.sql.');
        }

        // Made first, so that the dump is never readable by anyone else
        if (!touch($filename) || !chmod($filename, 0600)) {
            $this->output->error('Cannot write ' . $filename);
        }

        $client      = $this->client(['mariadb-dump', 'mysqldump']);
        $credentials = $this->credentials();

        $cmd = escapeshellarg($client)
            . ' --defaults-extra-file=' . escapeshellarg($credentials)
            . ' --single-transaction --no-tablespaces --routines --triggers '
            . implode(' ', $excludes) . ' '
            . escapeshellarg(Config::get('db'))
            . ' > ' . escapeshellarg($filename);

        exec($cmd, $output, $status);
        unlink($credentials);

        if ($status !== 0) {
            $this->output->error('The dump failed; ' . $filename . ' is incomplete.');
        }

        $this->output->addLine('File saved to: ' . $filename, 'success');
    }

    /**
     * Load a database dump
     *
     * @museDescription  Loads the provided database into the hubs currently configured database
     *
     * Give the dump to load as the argument, or "-" to read it from standard
     * input, as from a file the hub's account cannot read itself.
     *
     * By default the settings of a few extensions that differ between hubs,
     * such as the paths and services of a development hub, are kept across
     * the load. Pass --exact to restore a hub's own dump just as it was, and
     * --drop-all-tables to remove tables the dump does not mention.
     *
     * @return  void
     **/
    public function load()
    {
        if (!$infile = $this->file()) {
            $this->output->error('Please provide an input file');
        } elseif ($infile !== '-' && !is_file($infile)) {
            $this->output->error("'{$infile}' does not appear to be a valid file");
        }

        $exact = (bool) $this->arguments->getOpt('exact');
        $db    = App::get('db');

        // First, set some things aside that we need to reapply after the update
        $params = [];
        if (!$exact) {
            $params['com_system']             = Component::params('com_system');
            $params['com_tools']              = Component::params('com_tools');
            $params['com_usage']              = Component::params('com_usage');
            $params['com_members']            = Component::params('com_members');
            $params['plg_projects_databases'] = Plugin::params('projects', 'databases');
        }

        // See if we should drop all tables first
        if ($this->arguments->getOpt('drop-all-tables')) {
            $this->output->addLine('Dropping all tables...');

            $db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
            $db->query();

            foreach ($db->getTableList() as $table) {
                $db->dropTable($table);
            }

            $db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
            $db->query();
        }

        $client      = $this->client(['mariadb', 'mysql']);
        $credentials = $this->credentials();

        // A file of "-" is standard input, which the client inherits
        $cmd = escapeshellarg($client)
            . ' --defaults-extra-file=' . escapeshellarg($credentials)
            . ' ' . escapeshellarg(Config::get('db'))
            . ($infile === '-' ? '' : ' < ' . escapeshellarg($infile));

        $this->output->addLine('Loading data from ' . ($infile === '-' ? 'standard input' : $infile) . '...');

        // Now push the big red button
        exec($cmd, $output, $status);
        unlink($credentials);

        if ($status !== 0) {
            $this->output->error('The load failed.');
        }

        $migration = new Migration($db);

        // Now load some things back up
        foreach ($params as $k => $v) {
            $migration->saveParams($k, $v);
        }

        $this->output->addLine('Load complete!', 'success');
    }

    /**
     * Convert database schema from one database type to another
     *
     * @museDescription  Converts schema.sql from one database type to another (e.g., mysql to sqlite)
     *
     * @return  void
     **/
    public function schema()
    {
        $converter = new SchemaConverter($this->output, $this->arguments);
        $converter->convert();
    }

    /**
     * Launch an interactive database CLI session
     *
     * @museDescription  Opens the native database client (mysql/psql/sqlite3) with auto-configured credentials
     *
     * @return  void
     **/
    public function cli()
    {
        $driver = strtolower(Config::get('driver', 'mysql'));

        // Build the command based on database driver
        switch ($driver) {
            case 'mysql':
            case 'mysqli':
            case 'mariadb':
            case 'percona':
                $cmd = 'mysql';
                $cmd .= ' -u ' . escapeshellarg(Config::get('user'));

                $password = Config::get('password');
                if (!empty($password)) {
                    $cmd .= ' -p' . escapeshellarg($password);
                }

                $host = Config::get('host', 'localhost');
                if (!empty($host) && $host !== 'localhost') {
                    $cmd .= ' -h ' . escapeshellarg($host);
                }

                $port = Config::get('port');
                if (!empty($port) && $port != 3306) {
                    $cmd .= ' -P ' . escapeshellarg($port);
                }

                $cmd .= ' ' . escapeshellarg(Config::get('db'));
                break;

            case 'pgsql':
            case 'postgresql':
                // PostgreSQL uses PGPASSWORD environment variable
                $password = Config::get('password');
                $envPrefix = !empty($password) ? 'PGPASSWORD=' . escapeshellarg($password) . ' ' : '';

                $cmd = $envPrefix . 'psql';
                $cmd .= ' -U ' . escapeshellarg(Config::get('user'));

                $host = Config::get('host', 'localhost');
                if (!empty($host)) {
                    $cmd .= ' -h ' . escapeshellarg($host);
                }

                $port = Config::get('port');
                if (!empty($port) && $port != 5432) {
                    $cmd .= ' -p ' . escapeshellarg($port);
                }

                $cmd .= ' ' . escapeshellarg(Config::get('db'));
                break;

            case 'sqlite':
            case 'sqlite3':
                $dbPath = Config::get('database', Config::get('db'));
                if (strpos($dbPath, '/') !== 0) {
                    // Relative path - prepend app root
                    $dbPath = PATH_ROOT . '/' . $dbPath;
                }
                $cmd = 'sqlite3 ' . escapeshellarg($dbPath);
                break;

            default:
                $this->output->error("Unsupported database driver: {$driver}");
        }

        $this->output->addLine("Launching {$driver} client...");
        $this->output->addLine("Type 'exit' or 'quit' to return to the shell.");
        $this->output->addLine('');

        // Use passthru to hand over terminal control to the database client
        passthru($cmd);
    }

    /**
     * Execute a single SQL query
     *
     * @museDescription  Runs a single SQL query against the database with automatic table prefix replacement
     *
     * @return  void
     **/
    public function query()
    {
        $sql = $this->arguments->getOpt('sql');

        // If no --sql option, check for positional argument after 'query'
        if (empty($sql)) {
            $sql = $this->arguments->getOpt(3);
        }

        if (empty($sql)) {
            $this->output->error('Please provide a SQL query using --sql="YOUR QUERY" or as the third argument');
        }

        // Replace #__ with the actual table prefix
        $prefix = App::get('db')->getPrefix();
        $sql = str_replace('#__', $prefix, $sql);

        $jsonOutput = $this->arguments->getOpt('json');

        try {
            $db = App::get('db');
            $db->setQuery($sql);

            // Determine if this is a SELECT query (returns results)
            $trimmedSql = ltrim(strtoupper($sql));
            $isSelect = strpos($trimmedSql, 'SELECT') === 0
                || strpos($trimmedSql, 'SHOW') === 0
                || strpos($trimmedSql, 'DESCRIBE') === 0
                || strpos($trimmedSql, 'DESC') === 0
                || strpos($trimmedSql, 'EXPLAIN') === 0;

            if ($isSelect) {
                $results = $db->loadAssocList();

                if ($jsonOutput) {
                    $this->output->addLine(json_encode($results, JSON_PRETTY_PRINT));
                } else {
                    if (empty($results)) {
                        $this->output->addLine('No results returned.', 'info');
                    } else {
                        // Get column headers from first row
                        $headers = array_keys($results[0]);

                        // Calculate column widths
                        $widths = [];
                        foreach ($headers as $header) {
                            $widths[$header] = strlen($header);
                        }
                        foreach ($results as $row) {
                            foreach ($row as $key => $value) {
                                $len = strlen((string) $value);
                                if ($len > $widths[$key]) {
                                    $widths[$key] = min($len, 50); // Cap at 50 chars
                                }
                            }
                        }

                        // Print header row
                        $headerLine = '| ';
                        $separator = '+-';
                        foreach ($headers as $header) {
                            $headerLine .= str_pad($header, $widths[$header]) . ' | ';
                            $separator .= str_repeat('-', $widths[$header]) . '-+-';
                        }
                        $separator = rtrim($separator, '-+') . '+';

                        $this->output->addLine($separator);
                        $this->output->addLine(rtrim($headerLine));
                        $this->output->addLine($separator);

                        // Print data rows
                        foreach ($results as $row) {
                            $line = '| ';
                            foreach ($headers as $header) {
                                $value = (string) $row[$header];
                                if (strlen($value) > 50) {
                                    $value = substr($value, 0, 47) . '...';
                                }
                                $line .= str_pad($value, $widths[$header]) . ' | ';
                            }
                            $this->output->addLine(rtrim($line));
                        }

                        $this->output->addLine($separator);
                        $this->output->addLine(count($results) . ' row(s) returned.', 'info');
                    }
                }
            } else {
                // Execute non-SELECT query
                $result = $db->execute();

                if ($result) {
                    $affected = $db->getAffectedRows();
                    if ($jsonOutput) {
                        $this->output->addLine(json_encode([
                            'success' => true,
                            'affected_rows' => $affected,
                        ], JSON_PRETTY_PRINT));
                    } else {
                        $this->output->addLine("Query executed successfully. {$affected} row(s) affected.", 'success');
                    }
                } else {
                    if ($jsonOutput) {
                        $this->output->addLine(json_encode([
                            'success' => false,
                            'error' => $db->getErrorMsg(),
                        ], JSON_PRETTY_PRINT));
                    } else {
                        $this->output->error('Query failed: ' . $db->getErrorMsg());
                    }
                }
            }
        } catch (\Exception $e) {
            if ($jsonOutput) {
                $this->output->addLine(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], JSON_PRETTY_PRINT));
            } else {
                $this->output->error('Query error: ' . $e->getMessage());
            }
        }
    }

    /**
     * List available database drivers
     *
     * @museDescription  Lists all supported database drivers and their PHP extension availability
     *
     * @return  void
     **/
    public function drivers()
    {
        $manager = new DatabaseManager();
        $availability = $manager->getDriverAvailability();

        $this->output->addLine('');
        $this->output->addLine('Database Drivers', ['format' => 'bold']);
        $this->output->addLine(str_repeat('=', 68));
        $this->output->addLine('');

        $this->output->addLine(
            sprintf('  %-12s %-36s %s', 'Name', 'Class', 'Status'),
            ['format' => 'underline'],
        );

        foreach ($availability as $name => $info) {
            if ($info['custom']) {
                $class  = 'custom resolver';
                $status = 'registered';
                $style  = 'info';
            } elseif ($info['class']) {
                $class  = $info['class'];
                $status = $info['available'] ? 'available' : 'not available';
                $style  = $info['available'] ? 'success' : ['color' => 'red'];
            } else {
                $class  = '—';
                $status = 'unknown';
                $style  = 'warning';
            }

            $this->output->addString(sprintf('  %-12s %-36s ', $name, $class));
            $this->output->addLine($status, $style);
        }

        $this->output->addLine('');
        $this->output->addLine(
            sprintf(
                '  %d driver(s) total, %d available',
                count($availability),
                count(array_filter($availability, function ($d) {
                    return $d['available'] === true;
                })),
            ),
            'info',
        );
        $this->output->addLine('');
    }

    /**
     * The file named on the command line, before or after its options, or
     * "-" for standard input
     *
     * @return  string|null
     **/
    private function file()
    {
        $opts = (array) $this->arguments->getOpts();

        foreach ($opts as $key => $value) {
            if (is_int($key) && $key >= 3) {
                return $value;
            }
        }

        // A lone "-" reads as an option with no name
        return array_key_exists('', $opts) ? '-' : null;
    }

    /**
     * Find the first of the named database clients that is installed
     *
     * MariaDB's own names first; servers before 10.5 have only MySQL's.
     *
     * @param   array   $names
     * @return  string
     **/
    private function client(array $names)
    {
        foreach ($names as $name) {
            $path = trim((string) shell_exec('command -v ' . escapeshellarg($name) . ' 2>/dev/null'));

            if ($path !== '') {
                return $path;
            }
        }

        $this->output->error('Neither ' . implode(' nor ', $names) . ' is installed. Install the MariaDB client.');
    }

    /**
     * Write the hub's database credentials where only this account can read them
     *
     * The clients read them from the file, so the password is never on a
     * command line, where anyone on the server could see it.
     *
     * @return  string  The file's path; remove it when done
     **/
    private function credentials()
    {
        $file = tempnam(sys_get_temp_dir(), 'muse-db-');
        chmod($file, 0600);

        $options = [
            'user'     => Config::get('user'),
            'password' => Config::get('password'),
        ];

        if (Config::get('socket')) {
            $options['socket'] = Config::get('socket');
        } else {
            $options['host'] = Config::get('host', 'localhost');

            if (Config::get('port')) {
                $options['port'] = Config::get('port');
            }
        }

        $content = "[client]\n";
        foreach ($options as $key => $value) {
            $content .= $key . '="' . addcslashes((string) $value, "\\\"") . "\"\n";
        }

        file_put_contents($file, $content);

        return $file;
    }

    /**
     * Output help documentation
     *
     * @return  void
     **/
    public function help()
    {
        $this
            ->helpOutput()
            ->addOverview(
                'Database utility functions for migrating and restoring database backups.
				The necessity for this function arose primarily from the need to copy
				a production database down to development environments without overwriting
				certain development configuration with inappropriate production values.',
            )
            ->addTasks($this)
            ->addArgument(
                '--include-table: Include a specific table',
                'Specify a given table to be included in the dump. This primarily
				would be used to include a given table from the non-prefixed namespace.',
                'Example: --include-table=migration',
            )
            ->addArgument(
                '--all-tables: Include all tables',
                'By default, the database dump does not include non-prefixed tables
				(example: host, display, etc...). This option can be used to include
				these tables. Use with caution when planning to eventually load this
				data into another host (ex: dev) as it rarely makes sense to reload
				tool sessions into another environment.',
            )
            ->addArgument(
                '--exact: Load the dump just as it was',
                'When loading in a database dump, keep none of the current settings
				that a load otherwise carries over. Use it to restore a hub from its
				own backup.',
            )
            ->addArgument(
                '--drop-all-tables: Drop all tables',
                'When loading in a database dump, this option will drop all tables
				prior to loading in the given dump. This is often helpful when the
				applied dump is divergent in schema from the current database being
				overwritten.',
            )
            ->addArgument(
                '--from: Source database type (for schema command)',
                'Specify the source database type for schema conversion.
				Currently supported: mysql',
                'Example: --from=mysql',
            )
            ->addArgument(
                '--to: Target database type (for schema command)',
                'Specify the target database type for schema conversion.
				Currently supported: sqlite',
                'Example: --to=sqlite',
            )
            ->addArgument(
                '--input: Input schema file (for schema command)',
                'Path to the input schema file. Defaults to Install/sql/{from}/schema.sql',
                'Example: --input=/path/to/schema.sql',
            )
            ->addArgument(
                '--output: Output schema file (for schema command)',
                'Path to the output schema file. Defaults to Install/sql/{to}/schema.sql',
                'Example: --output=/path/to/schema.sql',
            )
            ->addArgument(
                '--sql: SQL query to execute (for query command)',
                'The SQL query to run. Table prefix #__ will be replaced automatically.
				Can also be provided as the third positional argument.',
                'Example: --sql="SELECT * FROM #__users LIMIT 5"',
            )
            ->addArgument(
                '--json: Output results as JSON (for query command)',
                'When used with the query command, outputs results in JSON format
				instead of a formatted table. Useful for scripting.',
            );
    }
}
