<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to make the IP address columns wide enough for IPv6
 *
 * Most were varchar(15), which holds a dotted IPv4 address and nothing
 * longer. Under STRICT_TRANS_TABLES writing an IPv6 address into one fails
 * ("Data too long"): an IPv6 visitor could not sign in, since the #__session
 * insert failing ends the request, and failed sign-ins from IPv6 were never
 * counted towards a ban. Without strict mode the address was cut short.
 *
 * Request::ip() gives an IPv6 address compressed, at most 39 characters;
 * varchar(45) also holds the longest text form, with an embedded IPv4 one.
 * Each column keeps its nullability, default, collation and comment.
 *
 * The usage tables in the metrics database are widened by com_usage's own
 * migration, Migration20261104160100ComUsage.
 **/
class Migration20261104160000CoreIpv6 extends Base
{
    /**
     * IP address columns, by table
     *
     * @var  array
     **/
    protected $columns = [
        '#__session'               => 'ip',
        '#__session_geo'           => 'ip',
        '#__xsession'              => 'ip',
        '#__users'                 => 'registerIP',
        '#__users_log_auth'        => 'ip',
        '#__item_votes'            => 'ip',
        '#__vote_log'              => 'ip',
        '#__answers_log'           => 'ip',
        '#__answers_questions_log' => 'ip',
        '#__faq_helpful_log'       => 'ip',
        '#__kb_votes'              => 'ip',
        '#__courses_asset_views'   => 'ip',
        '#__courses_page_hits'     => 'ip',
        '#__project_logs'          => 'ip',
        '#__xgroups_pages_hits'    => 'ip',
        '#__citations_authors'     => 'ip',
    ];

    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        foreach ($this->columns as $table => $column) {
            $this->widenColumn($table, $column, 45, null, $this->db);
        }
    }

    /**
     * Down
     *
     * Narrowing the columns again would cut short every IPv6 address stored
     * since, so leave them as they are
     *
     * @return  void
     **/
    public function down() {}
}
