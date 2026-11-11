<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Migrations\Data\UsageMetricsSeed;
use Hubzero\Facades\App;

/**
 * Migration script for usage setup
**/
class Migration20131112130740ComUsage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        require_once __DIR__ . '/data/UsageMetricsSeed.php';

        $schema = $this->db->schema();

        // Get stats DB object
        $config     = $this->getParams('com_usage');
        $siteConfig = App::get('config');

        $options['driver']   = $config->get('statsDBDriver');
        $options['host']     = $config->get('statsDBHost');
        $options['user']     = $config->get('statsDBUsername');
        $options['password'] = $config->get('statsDBPassword');
        $options['database'] = $config->get('statsDBDatabase');

        if (empty($options['driver'])) {
            $options['driver']   = $siteConfig->get('dbtype');
        }
        if (empty($options['host'])) {
            $options['host']     = $siteConfig->get('host');
        }
        if (empty($options['user'])) {
            $options['user']     = $siteConfig->get('user');
        }
        if (empty($options['password'])) {
            $options['password'] = $siteConfig->get('password');
        }
        if (empty($options['database'])) {
            $options['database'] = $siteConfig->get('db') . '_metrics';
        }

        $originalDriver    = $options['driver'];
        $options['driver'] = 'pdo';

        try {
            $statsDb = \Hubzero\Database\Driver::getInstance($options);
        } catch (\Exception $e) {
            // Fail silently
            return true;
        }

        $options['driver'] = $originalDriver;

        if ($schema->tableExists('#__extensions')) {
            $result = $this->db
                ->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = (array) json_decode($result);
        } else {
            $result = $this->db
                ->getQuery(true)
                ->select('params')
                ->from('#__plugins')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = [];

            if (!empty($result)) {
                $ar = explode("\n", $result);

                foreach ($ar as $a) {
                    $a = trim($a);
                    if (empty($a)) {
                        continue;
                    }

                    $ar2 = explode("=", $a, 2);
                    $params[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                }
            }
        }

        $params['statsDBDriver']   = $options['driver'];
        $params['statsDBHost']     = $options['host'];
        $params['statsDBUsername'] = $options['user'];
        $params['statsDBPassword'] = $options['password'];
        $params['statsDBDatabase'] = $options['database'];

        if ($schema->tableExists('#__extensions')) {
            $params = json_encode($params);

            $this->db
                ->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $params])
                ->where('element', '=', 'com_usage')
                ->execute();
        } else {
            $p = '';
            foreach ($params as $k => $v) {
                $p .= "{$k}={$v}\n";
            }

            $params = $p;

            $this->db
                ->getQuery(true)
                ->update('#__plugins')
                ->set(['params' => $params])
                ->where('element', '=', 'com_usage')
                ->execute();
        }

        if (!$statsDb->schema()->tableExists('countries')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('countries')
                    ->string('code', 4)->default('')
                    ->string('name', 128)->default('')
                    ->primaryKey('code')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'countries',
                        UsageMetricsSeed::rows('countries'),
                        UsageMetricsSeed::ignore('countries'),
                    );
            } catch (\Exception $e) {
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
                    ->datetime('start')->default('0000-00-00 00:00:00')
                    ->string('appname', 150)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
                    ->tinyText('orgtype')->nullable()
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->char('ipcountry', 2)->nullable()
                    ->primaryKey('sessnum')
                    ->uniqueIndex('id', 'id')
                    ->index('user', 'user')
                    ->index('start', 'start')
                    ->index('appname', 'appname')
                    ->index('countryresident', 'countryresident')
                    ->index('countrycitizen', 'countrycitizen')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('registerDate')->default('0000-00-00 00:00:00')
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
                    ->datetime('modifiedDate')->default('0000-00-00 00:00:00')
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
                    ->index('username', 'username')
                    ->index('orgtype', 'orgtype')
                    ->index('countryresident', 'countryresident')
                    ->index('countryorigin', 'countryorigin')
                    ->index('registerDate', 'registerDate')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->string('value', 200)->default('')
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyText('orgtype')->nullable()
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->tinyInteger('success')->default(0)
                    ->char('ipcountry', 2)->nullable()
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->string('user', 150)->nullable()
                    ->tinyText('tool')
                    ->integer('pid')->nullable()
                    ->tinyText('domain')->nullable()
                    ->tinyText('filesystem')->nullable()
                    ->tinyText('execunit')->nullable()
                    ->float('walltime')->default(0)
                    ->float('cputime')->default(0)
                    ->tinyText('error')->nullable()
                    ->primaryKey('id')
                    ->index('datetime', 'datetime')
                    ->index('success', 'success')
                    ->index('sessionid', 'sessionid')
                    ->index('ipcountry', 'ipcountry')
                    ->index('countrycitizen', 'countrycitizen')
                    ->index('countryresident', 'countryresident')
                    ->index('ip', 'ip')
                    ->index('user', 'user')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->string('user', 255)->default('-')
                    ->bigInteger('uidNumber')->default(0)
                    ->string('ip', 15)->default('')
                    ->string('action', 40)->default('')
                    ->primaryKey('id')
                    ->uniqueIndex('userlogin', ['datetime', 'user', 'uidNumber', 'ip', 'action'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->nullable()
                    ->tinyText('content')
                    ->tinyText('referrer')->nullable()
                    ->tinyText('useragent')->nullable()
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
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
                    ->index('datetime', 'datetime')
                    ->index('sessionid', 'sessionid')
                    ->index('elementid', 'elementid')
                    ->index('ipcountry', 'ipcountry')
                    ->index('ip', 'ip')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->bigInteger('hits')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->default('')
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
                    ->bigInteger('duration')->default(0)
                    ->tinyInteger('jobs')->default(0)
                    ->bigInteger('webevents')->default(0)
                    ->primaryKey('id')
                    ->index('datetime', 'datetime')
                    ->index('ipcountry', 'ipcountry')
                    ->index('ip', 'ip')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->index('class', 'class')
                    ->index('domain_class', ['domain', 'class'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'domainclass',
                        UsageMetricsSeed::rows('domainclass'),
                        UsageMetricsSeed::ignore('domainclass'),
                    );
            } catch (\Exception $e) {
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
                    ->tinyInteger('class')->default(0)
                    ->string('name', 64)->default('')
                    ->primaryKey('class')
                    ->uniqueIndex('class_name', ['class', 'name'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'domainclasses',
                        UsageMetricsSeed::rows('domainclasses'),
                        UsageMetricsSeed::ignore('domainclasses'),
                    );
            } catch (\Exception $e) {
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
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_user',
                        UsageMetricsSeed::rows('summary_user'),
                        UsageMetricsSeed::ignore('summary_user'),
                    );
            } catch (\Exception $e) {
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
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
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
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_andmore',
                        UsageMetricsSeed::rows('summary_andmore'),
                        UsageMetricsSeed::ignore('summary_andmore'),
                    );
            } catch (\Exception $e) {
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
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_misc',
                        UsageMetricsSeed::rows('summary_misc'),
                        UsageMetricsSeed::ignore('summary_misc'),
                    );
            } catch (\Exception $e) {
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
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'summary_simusage',
                        UsageMetricsSeed::rows('summary_simusage'),
                        UsageMetricsSeed::ignore('summary_simusage'),
                    );
            } catch (\Exception $e) {
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
                    ->uniqueIndex('continentSHORT_continentLONG', ['continentSHORT', 'continentLONG'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'continents',
                        UsageMetricsSeed::rows('continents'),
                        UsageMetricsSeed::ignore('continents'),
                    );
            } catch (\Exception $e) {
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
                    ->primaryKey(['country', 'continent'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'country_continent',
                        UsageMetricsSeed::rows('country_continent'),
                        UsageMetricsSeed::ignore('country_continent'),
                    );
            } catch (\Exception $e) {
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
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $statsDb
                    ->getQuery(true)
                    ->insertMany(
                        'exclude_list',
                        UsageMetricsSeed::rows('exclude_list'),
                        UsageMetricsSeed::ignore('exclude_list'),
                    );
            } catch (\Exception $e) {
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
                    ->string('useragent', 255)
                    ->primaryKey('useragent')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }
    }
}
