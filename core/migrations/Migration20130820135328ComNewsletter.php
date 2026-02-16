<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing newletter character set
 *
*/
class Migration20130820135328ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletters')) {
            $schema->convertToCharset('#__newsletters', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_templates')) {
            $schema->convertToCharset('#__newsletter_templates', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_secondary_story')) {
            $schema->convertToCharset('#__newsletter_secondary_story', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_primary_story')) {
            $schema->convertToCharset('#__newsletter_primary_story', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailings')) {
            $schema->convertToCharset('#__newsletter_mailings', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailinglists')) {
            $schema->convertToCharset('#__newsletter_mailinglists', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailinglist_unsubscribes')) {
            $schema->convertToCharset('#__newsletter_mailinglist_unsubscribes', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailinglist_emails')) {
            $schema->convertToCharset('#__newsletter_mailinglist_emails', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailing_recipients')) {
            $schema->convertToCharset('#__newsletter_mailing_recipients', 'utf8');
        }
        if ($schema->tableExists('#__newsletter_mailing_recipient_actions')) {
            $schema->convertToCharset('#__newsletter_mailing_recipient_actions', 'utf8');
        }
    }
}
