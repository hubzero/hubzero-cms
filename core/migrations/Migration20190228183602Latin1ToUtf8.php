<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for setting character set on tables to UTF8
 *
*/
class Migration20190228183602Latin1ToUtf8 extends Base
{
    /**
     * List of tables
     *
     * @var  array
     **/
    public static $tables = array(
        'hg_update_queue',
        '#__audit_results',
        '#__developer_access_tokens',
        '#__developer_applications',
        '#__developer_authorization_codes',
        '#__developer_refresh_tokens',
        '#__geosearch_markers',
        '#__kb_comments',
        '#__media_tracking',
        '#__media_tracking_detailed',
        '#__resource_import_hooks',
        '#__search_blacklist',
        '#__shibboleth_sessions',
        '#__solr_search_searchcomponents',
        '#__storefront_skus',
        '#__support_criteria'
    );

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        foreach (self::$tables as $tbl) {
            if ($schema->tableExists($tbl)) {
                if (strtolower($schema->getCharacterSet($tbl) ?? '') != 'utf8') {
                    $schema->convertToCharset($tbl, 'utf8');
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        foreach (self::$tables as $tbl) {
            if ($schema->tableExists($tbl)) {
                if (strtolower($schema->getCharacterSet($tbl) ?? '') != 'latin1') {
                    $schema->convertToCharset($tbl, 'latin1');
                }
            }
        }
    }
}
