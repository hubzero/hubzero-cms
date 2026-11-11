<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Migrations\Data\UsageMetricsSeed;
use Hubzero\Content\Migration\SkipMigrationException;
use Hubzero\Facades\Config;

/**
 * Migration script for usage setup
 *
*/
class Migration20150904235936ComUsage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        require_once __DIR__ . '/data/UsageMetricsSeed.php';

        // Get stats DB object
        $config = $this->getParams('com_usage');

        $options['driver']   = $config->get('statsDBDriver');
        $options['host']     = $config->get('statsDBHost');
        $options['port']     = $config->get('statsDBPort');
        $options['user']     = $config->get('statsDBUsername');
        $options['password'] = $config->get('statsDBPassword');
        $options['database'] = $config->get('statsDBDatabase');

        if (empty($options['driver'])) {
            $options['driver'] = Config::get('dbtype');
        }
        if (empty($options['host'])) {
            $options['host'] = Config::get('host');
        }
        // Carried through as well as the host. A hub reached on a port other
        // than the default, or over TCP because its socket is not where PHP
        // expects one, cannot reach its metrics database without it.
        if (empty($options['port'])) {
            $options['port'] = Config::get('port');
        }
        if (empty($options['user'])) {
            $options['user'] = Config::get('user');
        }
        if (empty($options['password'])) {
            $options['password'] = Config::get('password');
        }
        if (empty($options['database'])) {
            $options['database'] = Config::get('db') . '_metrics';
        }

        $originalDriver    = $options['driver'];
        $options['driver'] = 'pdo';

        $connected = false;

        try {
            $statsDb   = \Hubzero\Database\Driver::getInstance($options);
            // getInstance() builds a lazy connection, so ask for the connection
            // itself; otherwise a bad host or missing grant is not discovered
            // until the first query, long past the checks below.
            $statsDb->connect();
            $connected = true;
        } catch (\Hubzero\Database\Exception\ConnectionFailedException $e) {
            $connected = false;
        }

        if (!$connected) {
            // Check if we can get root access to set up the metrics database
            if (!$this->runAsRoot()) {
                // No root access and no metrics database - skip this migration
                // It can be run later when metrics infrastructure is set up
                throw new SkipMigrationException('Metrics database not available (run with sudo to auto-configure)');
            }

            $query = $this->db
                ->getQuery(true)
                ->select(['Host', 'Db', 'User'])
                ->from('mysql.db')
                ->where('User', '=', $options['user'])
                ->where('Db', '=', $options['database']);
            $results = $query->loadObjectList();

            if (count($results) > 0) {
                // User has grants but still can't connect - likely database doesn't exist
                // Skip gracefully since metrics is optional
                throw new SkipMigrationException('Metrics database not available');
            } else {
                // Identifiers and values, not raw interpolation - main's
                // 982bc08fd1, kept through the query-builder modernisation
                $this->db->setQuery(
                    'GRANT ALL PRIVILEGES ON ' . $this->db->quoteName($options['database'])
                    . '.* TO ' . $this->db->quote($options['user'])
                    . '@' . $this->db->quote('localhost'),
                )->execute();
            }

            try {
                $statsDb = \Hubzero\Database\Driver::getInstance($options);
                $statsDb->connect();
            } catch (\Hubzero\Database\Exception\ConnectionFailedException $e) {
                // Still can't connect after granting - database probably doesn't exist
                // Skip gracefully since metrics is optional
                throw new SkipMigrationException('Metrics database not available');
            }
        }

        $params = $this->getParams('com_usage')->toArray();

        $params['statsDBDriver']   = $originalDriver;
        $params['statsDBHost']     = $options['host'];
        $params['statsDBPort']     = $options['port'];
        $params['statsDBUsername'] = $options['user'];
        $params['statsDBPassword'] = $options['password'];
        $params['statsDBDatabase'] = $options['database'];

        $this->saveParams('com_usage', $params);

        if (!$statsDb->schema()->tableExists('countries')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('countries')
                    ->string('code', 4)
                    ->string('name', 128)->default('')
                    ->primaryKey('code')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'countries',
                        UsageMetricsSeed::rows('countries'),
                        UsageMetricsSeed::ignore('countries'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('sessionlog_metrics')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('sessionlog_metrics')
                    ->unsignedBigInteger('id', ['autoIncrement' => true])
                    ->unsignedBigInteger('sessnum')
                    ->string('user', 150)->default('')
                    ->string('ip', 15)->default('')
                    ->dateTime('start')->default('0000-00-00 00:00:00')
                    ->string('appname', 150)->default('')
                    ->tinyText('host')
                    ->tinyText('domain')
                    ->tinyText('orgtype')
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->char('ipcountry', 2)->nullable()
                    ->primaryKey('sessnum')
                    ->uniqueIndex('idx_id', 'id')
                    ->index('idx_user', 'user')
                    ->index('idx_start', 'start')
                    ->index('idx_appname', 'appname')
                    ->index('idx_countryresident', 'countryresident')
                    ->index('idx_countrycitizen', 'countrycitizen')
                    ->index('idx_orgtype', 'orgtype')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('#__xprofiles_metrics')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('#__xprofiles_metrics')
                    ->integer('uidNumber')
                    ->string('name', 255)->default('')
                    ->string('username', 150)->default('')
                    ->string('email', 100)->default('')
                    ->dateTime('registerDate')->default('0000-00-00 00:00:00')
                    ->string('gidNumber', 11)->default('')
                    ->string('homeDirectory', 255)->default('')
                    ->string('loginShell', 255)->default('')
                    ->string('ftpShell', 255)->default('')
                    ->string('userPassword', 255)->default('')
                    ->string('gid', 255)->default('')
                    ->string('orgtype', 255)->default('')
                    ->string('organization', 255)->default('')
                    ->char('countryresident', 2)->default('')
                    ->char('countryorigin', 2)->default('')
                    ->string('gender', 255)->default('')
                    ->string('url', 255)->default('')
                    ->text('reason')
                    ->integer('mailPreferenceOption')->default(0)
                    ->integer('usageAgreement')->default(0)
                    ->integer('jobsAllowed')->default(0)
                    ->dateTime('modifiedDate')->default('0000-00-00 00:00:00')
                    ->integer('emailConfirmed')->default(0)
                    ->string('regIP', 255)->default('')
                    ->string('regHost', 255)->default('')
                    ->string('nativeTribe', 255)->default('')
                    ->string('phone', 255)->default('')
                    ->string('proxyPassword', 255)->default('')
                    ->string('proxyUidNumber', 255)->default('')
                    ->string('givenName', 255)->default('')
                    ->string('middleName', 255)->default('')
                    ->string('surname', 255)->default('')
                    ->string('picture', 255)->default('')
                    ->integer('vip')->default(0)
                    ->tinyInteger('public')->default(0)
                    ->text('params')
                    ->text('note')
                    ->integer('shadowExpire')->nullable()
                    ->primaryKey('uidNumber')
                    ->index('idx_username', 'username')
                    ->index('idx_orgtype', 'orgtype')
                    ->index('idx_countryresident', 'countryresident')
                    ->index('idx_countryorigin', 'countryorigin')
                    ->index('idx_registerDate', 'registerDate')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_andmore_vals')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_andmore_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_misc_vals')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_misc_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->string('value', 200)->default('')
                    ->tinyInteger('valfmt')->default(0)
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_simusage_vals')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_simusage_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('toolstart')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('toolstart')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->bigInteger('sessionid')->nullable()
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyText('orgtype')
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->tinyInteger('success')->default(0)
                    ->char('ipcountry', 2)->nullable()
                    ->string('ip', 15)->default('')
                    ->tinyText('host')
                    ->string('user', 150)->nullable()
                    ->tinyText('tool')
                    ->integer('pid')->nullable()
                    ->tinyText('domain')
                    ->tinyText('filesystem')
                    ->tinyText('execunit')
                    ->float('walltime')->unsigned()->default(0)
                    ->float('cputime')->unsigned()->default(0)
                    ->tinyText('error')
                    ->primaryKey('id')
                    ->index('idx_datetime', 'datetime')
                    ->index('idx_success', 'success')
                    ->index('idx_sessionid', 'sessionid')
                    ->index('idx_ipcountry', 'ipcountry')
                    ->index('idx_countrycitizen', 'countrycitizen')
                    ->index('idx_countryresident', 'countryresident')
                    ->index('idx_orgtype', 'orgtype')
                    ->index('idx_ip', 'ip')
                    ->index('idx_user', 'user')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('userlogin')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('userlogin')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->string('user', 255)->default('-')
                    ->bigInteger('uidNumber')->default(0)
                    ->string('ip', 15)->default('')
                    ->string('action', 40)->default('')
                    ->primaryKey('id')
                    ->uniqueIndex('userlogin', ['datetime', 'user', 'uidNumber', 'ip', 'action'])
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('web')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('web')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->bigInteger('elementid')->nullable()
                    ->bigInteger('sessionid')->nullable()
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->nullable()
                    ->tinyText('content')
                    ->tinyText('referrer')
                    ->tinyText('useragent')
                    ->string('ip', 15)->default('')
                    ->tinyText('host')
                    ->tinyText('domain')
                    ->integer('uidNumber')->nullable()
                    ->string('apache_pid', 120)->default('')
                    ->string('joomla_sessionid', 120)->default('')
                    ->string('site_cookie', 120)->default('')
                    ->string('auth_type', 120)->default('')
                    ->string('component_name', 120)->default('')
                    ->string('view_name', 120)->default('')
                    ->string('task_name', 120)->default('')
                    ->string('action_name', 120)->default('')
                    ->string('item_name', 120)->default('')
                    ->tinyInteger('dnload')->nullable()
                    ->primaryKey('id')
                    ->index('idx_datetime', 'datetime')
                    ->index('idx_sessionid', 'sessionid')
                    ->index('idx_elementid', 'elementid')
                    ->index('idx_ipcountry', 'ipcountry')
                    ->index('idx_ip', 'ip')
                    ->index('idx_content', 'content')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('webhits')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('webhits')
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->bigInteger('hits')->default(0)
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('websessions')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('websessions')
                    ->bigInteger('id')->default(0)
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->default('')
                    ->string('ip', 15)->default('')
                    ->tinyText('host')
                    ->tinyText('domain')
                    ->bigInteger('duration')->default(0)
                    ->tinyInteger('jobs')->default(0)
                    ->bigInteger('webevents')->default(0)
                    ->primaryKey('id')
                    ->index('idx_datetime', 'datetime')
                    ->index('idx_ipcountry', 'ipcountry')
                    ->index('idx_ip', 'ip')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('domainclass')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('domainclass')
                    ->string('domain', 64)->default('')
                    ->tinyInteger('class')->default(0)
                    ->string('country', 4)->default('')
                    ->string('state', 4)->default('')
                    ->tinyText('name')
                    ->primaryKey('domain')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'domainclass',
                        UsageMetricsSeed::rows('domainclass'),
                        UsageMetricsSeed::ignore('domainclass'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('domainclasses')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('domainclasses')
                    ->tinyInteger('class')
                    ->string('name', 64)->default('')
                    ->primaryKey('class')
                    ->uniqueIndex('idx_class_name', ['class', 'name'])
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'domainclasses',
                        UsageMetricsSeed::rows('domainclasses'),
                        UsageMetricsSeed::ignore('domainclasses'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_user')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('idx_label', 'label')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_user',
                        UsageMetricsSeed::rows('summary_user'),
                        UsageMetricsSeed::ignore('summary_user'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user_vals')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_user_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->dateTime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_andmore')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_andmore')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('idx_label', 'label')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_andmore',
                        UsageMetricsSeed::rows('summary_andmore'),
                        UsageMetricsSeed::ignore('summary_andmore'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_misc')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_misc')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('idx_label', 'label')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_misc',
                        UsageMetricsSeed::rows('summary_misc'),
                        UsageMetricsSeed::ignore('summary_misc'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_simusage')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_simusage')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('idx_label', 'label')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_simusage',
                        UsageMetricsSeed::rows('summary_simusage'),
                        UsageMetricsSeed::ignore('summary_simusage'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('continents')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('continents')
                    ->char('continentSHORT', 2)->default('')
                    ->string('continentLONG', 45)->default('')
                    ->uniqueIndex('idx_continent', ['continentSHORT', 'continentLONG'])
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'continents',
                        UsageMetricsSeed::rows('continents'),
                        UsageMetricsSeed::ignore('continents'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('country_continent')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('country_continent')
                    ->char('country', 2)->default('')
                    ->char('continent', 2)->default('')
                    ->uniqueIndex('idx_country_continent', ['country', 'continent'])
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'country_continent',
                        UsageMetricsSeed::rows('country_continent'),
                        UsageMetricsSeed::ignore('country_continent'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('exclude_list')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('exclude_list')
                    ->string('filter', 65)
                    ->string('type', 65)->default('domain')
                    ->string('notes', 120)->nullable()
                    ->uniqueIndex('filter_type', ['filter', 'type'])
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'exclude_list',
                        UsageMetricsSeed::rows('exclude_list'),
                        UsageMetricsSeed::ignore('exclude_list'),
                    );
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('bot_useragents')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('bot_useragents')
                    ->tinyText('useragent')
                    ->primaryKey('useragent')
                    ->execute();
            } catch (\Hubzero\Database\Exception\QueryFailedException $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }
    }
}
