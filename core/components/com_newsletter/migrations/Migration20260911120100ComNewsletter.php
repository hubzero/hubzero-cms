<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Put the hub's own name at the top of its newsletter
 *
 * The shipped HTML template carried "HUB Campaign Template" as the heading of
 * every issue, hard-coded, with no placeholder for the site's name to put
 * there. Every hub that had not edited its template sent its newsletter out
 * under that name.
 *
 * The template now ships using {{SITENAME}}, which the builder replaces. This
 * is for the hubs that already have the literal in the row.
 *
 * A template somebody has edited is theirs, so this only touches the heading
 * itself, and only where the shipped literal is still sitting in it.
 **/
class Migration20260911120100ComNewsletter extends Base
{
    /**
     * The heading as it shipped
     *
     * @var  string
     */
    const SHIPPED = 'HUB Campaign Template';

    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__newsletter_templates')) {
            return;
        }

        $this->db->setQuery(
            "SELECT `id`, `template` FROM `#__newsletter_templates`"
            . " WHERE `template` LIKE '%" . self::SHIPPED . "%'"
        );

        $templates = $this->db->loadObjectList();

        if (!$templates) {
            return;
        }

        foreach ($templates as $template) {
            // Only the heading, and only that string. Anything else in the
            // row is whoever edited it talking, and stays as they left it.
            $replaced = str_replace(self::SHIPPED, '{{SITENAME}}', $template->template);

            $this->db->setQuery(
                "UPDATE `#__newsletter_templates` SET `template` = "
                . $this->db->quote($replaced) . " WHERE `id` = " . (int) $template->id
            );
            $this->db->query();
        }

        $this->log(
            'Newsletter templates now head each issue with the hub\'s own name: '
            . count($templates) . ' updated.'
        );
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__newsletter_templates')) {
            return;
        }

        $this->db->setQuery(
            "SELECT `id`, `template` FROM `#__newsletter_templates`"
            . " WHERE `template` LIKE '%{{SITENAME}}%'"
        );

        foreach ((array) $this->db->loadObjectList() as $template) {
            $replaced = str_replace('{{SITENAME}}', self::SHIPPED, $template->template);

            $this->db->setQuery(
                "UPDATE `#__newsletter_templates` SET `template` = "
                . $this->db->quote($replaced) . " WHERE `id` = " . (int) $template->id
            );
            $this->db->query();
        }
    }
}
