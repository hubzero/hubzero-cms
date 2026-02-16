<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for adding nested pages & comments
 *
*/
class Migration20140908193005ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // add parent
        if (!$schema->hasColumn('#__xgroups_pages', 'parent')) {
            $schema->addColumn('#__xgroups_pages', 'parent')->integer()->default(0)->after('gidNumber')->execute();
        }

        // add depth
        if (!$schema->hasColumn('#__xgroups_pages', 'depth')) {
            $schema->addColumn('#__xgroups_pages', 'depth')->integer()->default(1)->after('parent')->execute();
        }

        // add left
        if (!$schema->hasColumn('#__xgroups_pages', 'lft')) {
            $schema->addColumn('#__xgroups_pages', 'lft')->integer()->after('parent')->execute();
        }

        // add right
        if (!$schema->hasColumn('#__xgroups_pages', 'rgt')) {
            $schema->addColumn('#__xgroups_pages', 'rgt')->integer()->after('lft')->execute();
        }

        // drop ordering
        if ($schema->hasColumn('#__xgroups_pages', 'ordering')) {
            // get a list of all hub & super groups
            // get a list of all hub & super groups
            $groups = $this->db->getQuery(true)
                ->select(['gidNumber', 'cn', 'description'])
                ->from('#__xgroups')
                ->where('type', 'IN', [1, 3])
                ->loadObjectList();

            // default home page content
            $defaultHomePageContent = "<!-- {FORMAT:HTML} -->\n<p>[[Group.DefaultHomePage()]]</p>";

            // run through each group
            // loading all of their pages
            // create a default home page if one does not exist
            foreach ($groups as $group) {
                $pages = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__xgroups_pages')
                    ->where('gidNumber', '=', $group->gidNumber)
                    ->order('ordering', 'ASC')
                    ->loadObjectList();

                // locate the home page
                $homePage = null;
                foreach ($pages as $k => $page) {
                    if ($page->home == 1) {
                        $homePage = $page;
                        unset($pages[$k]);
                        break;
                    }
                }

                // if we dont ahve a home page we need one
                if ($homePage == null) {
                    // create page
                    $this->db->getQuery(true)
                        ->insert('#__xgroups_pages')
                        ->columns(['gidNumber', 'parent', 'depth', 'lft', 'alias', 'title', 'state', 'privacy', 'home'])
                        ->values([$group->gidNumber, 0, 0, 1, 'overview', 'Overview', 1, 'default', 1])
                        ->execute();

                    $homePageId = $this->db->insertid();

                    // create page version
                    $this->db->getQuery(true)
                        ->insert('#__xgroups_pages_versions')
                        ->columns(['pageid', 'version', 'content', 'created', 'created_by'])
                        ->values([$homePageId, 1, $defaultHomePageContent, new Expression('NOW()'), 1000])
                        ->execute();
                } else {
                    // update the home page
                    $this->db->getQuery(true)
                        ->update('#__xgroups_pages')
                        ->set(['parent' => 0, 'depth' => 0, 'lft' => 1, 'alias' => 'overview', 'title' => 'Overview'])
                        ->where('id', '=', $homePage->id)
                        ->execute();

                    $homePageId = $homePage->id;
                }

                // loop through other pages
                $left = 2;
                foreach ($pages as $page) {
                    // left is home's left plus 1
                    $right = $left + 1;

                    // update the left, right, parent, & depth
                    $this->db->getQuery(true)
                        ->update('#__xgroups_pages')
                        ->set(['parent' => $homePageId, 'lft' => $left, 'rgt' => $right, 'depth' => 1])
                        ->where('id', '=', $page->id)
                        ->execute();

                    // add 2 to left for next iteration
                    $left += 2;
                }

                // update the home page after weve computed all the left & rights
                $this->db->getQuery(true)
                    ->update('#__xgroups_pages')
                    ->set(['rgt' => $left])
                    ->where('id', '=', $homePageId)
                    ->execute();
            }

            // drop ordering column
            $schema->dropColumn('#__xgroups_pages', 'ordering');
        }

        // add comments
        if (!$schema->hasColumn('#__xgroups_pages', 'comments')) {
            $schema->addColumn('#__xgroups_pages', 'comments')->tinyInteger()->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // drop parent
        if ($schema->hasColumn('#__xgroups_pages', 'parent')) {
            $schema->dropColumn('#__xgroups_pages', 'parent');
        }

        // drop depth
        if ($schema->hasColumn('#__xgroups_pages', 'depth')) {
            $schema->dropColumn('#__xgroups_pages', 'depth');
        }

        // drop left
        if ($schema->hasColumn('#__xgroups_pages', 'lft')) {
            $schema->dropColumn('#__xgroups_pages', 'lft');
        }

        // drop right
        if ($schema->hasColumn('#__xgroups_pages', 'rgt')) {
            $schema->dropColumn('#__xgroups_pages', 'rgt');
        }

        // add ordering
        if (!$schema->hasColumn('#__xgroups_pages', 'ordering')) {
            $schema->addColumn('#__xgroups_pages', 'ordering')->integer()->after('title')->execute();
        }

        //remove comments
        if ($schema->hasColumn('#__xgroups_pages', 'comments')) {
            $schema->dropColumn('#__xgroups_pages', 'comments');
        }
    }
}
