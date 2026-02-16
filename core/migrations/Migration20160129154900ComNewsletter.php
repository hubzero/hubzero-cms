<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to Newsletter tables
 *
*/
class Migration20160129154900ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletter_mailing_recipient_actions')) {
            $schema->addIndex('#__newsletter_mailing_recipient_actions', 'idx_mailingid', 'mailingid');

            $schema->addIndex('#__newsletter_mailing_recipient_actions', 'idx_action', 'action');
        }

        if ($schema->tableExists('#__newsletter_mailing_recipients')) {
            $schema->addIndex('#__newsletter_mailing_recipients', 'idx_mid', 'mid');

            $schema->addIndex('#__newsletter_mailing_recipients', 'idx_status', 'status');
        }

        if ($schema->tableExists('#__newsletter_mailinglist_emails')) {
            $schema->addIndex('#__newsletter_mailinglist_emails', 'idx_mid', 'mid');

            $schema->addIndex('#__newsletter_mailinglist_emails', 'idx_status', 'status');
        }

        if ($schema->tableExists('#__newsletter_mailinglist_unsubscribes')) {
            $schema->addIndex('#__newsletter_mailinglist_unsubscribes', 'idx_mid', 'mid');
        }

        if ($schema->tableExists('#__newsletter_mailinglists')) {
            $schema->addIndex('#__newsletter_mailinglists', 'idx_private', 'private');

            $schema->addIndex('#__newsletter_mailinglists', 'idx_deleted', 'deleted');
        }

        if ($schema->tableExists('#__newsletter_mailings')) {
            $schema->addIndex('#__newsletter_mailings', 'idx_nid', 'nid');

            $schema->addIndex('#__newsletter_mailings', 'idx_lid', 'lid');

            $schema->addIndex('#__newsletter_mailings', 'idx_deleted', 'deleted');
        }

        if ($schema->tableExists('#__newsletter_primary_story')) {
            $schema->addIndex('#__newsletter_primary_story', 'idx_nid', 'nid');

            $schema->addIndex('#__newsletter_primary_story', 'idx_deleted', 'deleted');
        }

        if ($schema->tableExists('#__newsletter_secondary_story')) {
            $schema->addIndex('#__newsletter_secondary_story', 'idx_nid', 'nid');

            $schema->addIndex('#__newsletter_secondary_story', 'idx_deleted', 'deleted');
        }

        if ($schema->tableExists('#__newsletters')) {
            $schema->addIndex('#__newsletters', 'idx_published', 'published');

            $schema->addIndex('#__newsletters', 'idx_sent', 'sent');

            $schema->addIndex('#__newsletters', 'idx_deleted', 'deleted');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletter_mailing_recipient_actions')) {
            $schema->dropIndex('#__newsletter_mailing_recipient_actions', 'idx_mailingid');

            $schema->dropIndex('#__newsletter_mailing_recipient_actions', 'idx_action');
        }

        if ($schema->tableExists('#__newsletter_mailing_recipients')) {
            $schema->dropIndex('#__newsletter_mailing_recipients', 'idx_mid');

            $schema->dropIndex('#__newsletter_mailing_recipients', 'idx_status');
        }

        if ($schema->tableExists('#__newsletter_mailinglist_emails')) {
            $schema->dropIndex('#__newsletter_mailinglist_emails', 'idx_mid');

            $schema->dropIndex('#__newsletter_mailinglist_emails', 'idx_status');
        }

        if ($schema->tableExists('#__newsletter_mailinglist_unsubscribes')) {
            $schema->dropIndex('#__newsletter_mailinglist_unsubscribes', 'idx_mid');
        }

        if ($schema->tableExists('#__newsletter_mailinglists')) {
            $schema->dropIndex('#__newsletter_mailinglists', 'idx_private');

            $schema->dropIndex('#__newsletter_mailinglists', 'idx_deleted');
        }

        if ($schema->tableExists('#__newsletter_mailings')) {
            $schema->dropIndex('#__newsletter_mailings', 'idx_nid');

            $schema->dropIndex('#__newsletter_mailings', 'idx_lid');

            $schema->dropIndex('#__newsletter_mailings', 'idx_deleted');
        }

        if ($schema->tableExists('#__newsletter_primary_story')) {
            $schema->dropIndex('#__newsletter_primary_story', 'idx_nid');

            $schema->dropIndex('#__newsletter_primary_story', 'idx_deleted');
        }

        if ($schema->tableExists('#__newsletter_secondary_story')) {
            $schema->dropIndex('#__newsletter_secondary_story', 'idx_nid');

            $schema->dropIndex('#__newsletter_secondary_story', 'idx_deleted');
        }

        if ($schema->tableExists('#__newsletters')) {
            $schema->dropIndex('#__newsletters', 'idx_published');

            $schema->dropIndex('#__newsletters', 'idx_sent');

            $schema->dropIndex('#__newsletters', 'idx_deleted');
        }
    }
}
