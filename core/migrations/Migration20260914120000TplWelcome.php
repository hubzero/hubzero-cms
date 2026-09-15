<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Take the welcome template out
 *
 * It was the page a new hub landed on: a monitor, two laptops, a band of cloud
 * and a button that switched the hub to kimera and was never seen again. It
 * had no navigation on it, its instructions covered getting an admin password
 * out of an Amazon EC2 system log, and the template it handed over to is two
 * generations old. Its pictures are meridian's front page now.
 *
 * The files are gone, so a hub still set to it would have nothing to render.
 * This puts such a hub on meridian and takes the entry away.
 *
 * Meridian is named rather than left to chance: deleting a template entry that
 * happens to be the home one makes some other style home, choosing by highest
 * id, and on a hub with several installed that is a coin toss.
 *
 * Content is left alone. The tour at /congratulations and the "your new hub is
 * up and running" block belong to whoever has been running the hub since, and
 * a hub that never opens them is no worse off than it was yesterday. New hubs
 * do not get them - that is starter.sql's business, not this.
 **/
class Migration20260914120000TplWelcome extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__template_styles')) {
            return;
        }

        // Only a hub still sitting on the welcome template is moved. One that
        // has chosen a template has chosen it, and this is not the migration
        // to overrule that.
        $this->db->setQuery(
            'SELECT `template` FROM `#__template_styles`'
            . ' WHERE `client_id` = 0 AND `home` = 1 LIMIT 1'
        );

        if ($this->db->loadResult() == 'welcome') {
            $this->db->setQuery(
                'SELECT `id` FROM `#__template_styles`'
                . " WHERE `client_id` = 0 AND `template` = 'meridian'"
                . ' ORDER BY `id` ASC LIMIT 1'
            );

            if ($id = $this->db->loadResult()) {
                $this->db->setQuery(
                    'UPDATE `#__template_styles` SET `home` = 0 WHERE `client_id` = 0'
                );
                $this->db->query();

                $this->db->setQuery(
                    'UPDATE `#__template_styles` SET `home` = 1 WHERE `id` = ' . (int) $id
                );
                $this->db->query();

                $this->log('Moved this hub from the welcome template to meridian (style ' . $id . ')');
            } else {
                $this->log(
                    'This hub is on the welcome template and has no meridian to move to;'
                    . ' the next enabled style will be used instead',
                    'warning'
                );
            }
        }

        $this->deleteTemplateEntry('welcome', 0);
    }

    /**
     * Down
     *
     * The entry comes back, but core/templates/welcome does not - it is not in
     * the repository any more. A hub that rolls this back gets a row naming a
     * template with no files, which is why it is not made the default one.
     **/
    public function down()
    {
        $this->addTemplateEntry('welcome', 'Welcome', 0, 0, 0, null, 0);
    }
}
