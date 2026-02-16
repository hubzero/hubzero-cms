<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for separating html & plain parts for newsletter emails.
 *
*/
class Migration20140521141237ComNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // rename content to html content
        if ($schema->hasColumn('#__newsletters', 'content')) {
            $schema->renameColumn('#__newsletters', 'content', 'html_content')->mediumText();
        }

        // add plain text col
        if (!$schema->hasColumn('#__newsletters', 'plain_content')) {
            $schema->addColumn('#__newsletters', 'plain_content')->mediumText()->after('html_content');
        }

        // rename content to html content
        if ($schema->hasColumn('#__newsletter_mailings', 'body')) {
            $schema->renameColumn('#__newsletter_mailings', 'body', 'html_body')->longText();
        }

        // add plain text col
        if (!$schema->hasColumn('#__newsletter_mailings', 'plain_body')) {
            $schema->addColumn('#__newsletter_mailings', 'plain_body')->longText()->after('html_body');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // rename html content to content
        if ($schema->hasColumn('#__newsletters', 'html_content')) {
            $schema->renameColumn('#__newsletters', 'html_content', 'content')->mediumText();
        }

        // remove plain text col
        if ($schema->hasColumn('#__newsletters', 'plain_content')) {
            $schema->dropColumn('#__newsletters', 'plain_content');
        }

        // rename html content to content
        if ($schema->hasColumn('#__newsletter_mailings', 'html_body')) {
            $schema->renameColumn('#__newsletter_mailings', 'html_body', 'body')->longText();
        }

        // remove plain text col
        if ($schema->hasColumn('#__newsletter_mailings', 'plain_body')) {
            $schema->dropColumn('#__newsletter_mailings', 'plain_body');
        }
    }
}
