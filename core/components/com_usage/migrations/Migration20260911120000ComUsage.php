<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Point the metrics connection at the host the site itself uses
 *
 * com_usage shipped with statsDBHost set to "localhost". For PDO that is not
 * a hostname, it is an instruction to use a unix socket, and the socket PHP
 * is built against is not always the one the server listens on — which is why
 * an installer will write 127.0.0.1 for the site's own database. A hub in
 * that position cannot reach its metrics database and answers 500 on /usage.
 *
 * The shipped default is now empty so that a new hub falls back to the site's
 * host. This is for hubs that already have "localhost" written into their
 * parameters, which the migration that wrote it will not correct: it falls
 * back only when the value is empty, and "localhost" is not.
 *
 * There is deliberately no connection test here. A migration runs on the
 * command line, and the command line and the web server are different PHP
 * builds with different default socket paths: the socket that fails under the
 * web server connects perfectly from a migration, so testing it here would
 * report success and leave the hub broken. What the site is configured with
 * is the better evidence, because it is the connection the web server is
 * known to make.
 **/
class Migration20260911120000ComUsage extends Base
{
    /**
     * The value com_usage shipped with
     *
     * @var  string
     */
    const SHIPPED = 'localhost';

    /**
     * Up
     **/
    public function up()
    {
        $params = $this->getParams('com_usage');

        // Only the shipped default. A host somebody chose is theirs.
        if ($params->get('statsDBHost') !== self::SHIPPED) {
            return;
        }

        // Nothing configured means the helper falls back to the site's own
        // connection anyway, so there is nothing here to repair
        if (!$params->get('statsDBUsername') && !$params->get('statsDBDatabase')) {
            return;
        }

        $host = \Config::get('host');

        // The site is on a socket too, so it has nothing to teach us
        if (empty($host) || $host === self::SHIPPED) {
            return;
        }

        $saved = $params->toArray();

        $saved['statsDBHost'] = $host;

        if (!$params->get('statsDBPort') && \Config::get('port')) {
            $saved['statsDBPort'] = \Config::get('port');
        }

        $this->saveParams('com_usage', $saved);

        $this->log(
            'com_usage now reaches its metrics database at ' . $host
            . ', as the site does, rather than over a socket.'
        );
    }

    /**
     * Down
     **/
    public function down()
    {
        $params = $this->getParams('com_usage');

        if ($params->get('statsDBHost') !== \Config::get('host')) {
            return;
        }

        $saved = $params->toArray();

        $saved['statsDBHost'] = self::SHIPPED;

        $this->saveParams('com_usage', $saved);
    }
}
