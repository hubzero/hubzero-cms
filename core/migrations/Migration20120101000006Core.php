<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for 2011/12 content
  *
**/
class Migration20120101000006Core extends Base
{
    public function up()
    {
        // Component entries
        $this->addComponentEntry('Forum');
        $this->addComponentEntry('Register');
        $this->addComponentEntry('System');

        // @FIXME: Save params for geodb and ldap?

        // Plugins
        $this->addPluginEntry('groups', 'calendar');
        $this->addPluginEntry('groups', 'memberoptions');
        $this->addPluginEntry('groups', 'userenrollment');
        $this->addPluginEntry('support', 'captcha');
        $this->addPluginEntry('support', 'kb');
        $this->addPluginEntry('resources', 'about');
        $this->addPluginEntry('resources', 'abouttool');
        $this->addPluginEntry('resources', 'sponsors');
        $this->addPluginEntry('members', 'profile');
        $this->addPluginEntry('members', 'dashboard');
        $this->addPluginEntry('members', 'account');
        $this->addPluginEntry('ysearch', 'citations');
        $this->addPluginEntry('ysearch', 'forum');
        $this->addPluginEntry('tags', 'forum');
        $this->addPluginEntry('hubzero', 'wikieditorwykiwyg');
        $this->addPluginEntry('hubzero', 'comments');
        $this->addPluginEntry('hubzero', 'recaptcha');
        $this->addPluginEntry('system', 'jquery', 1, array(
            "jquery"             => "1",
            "jquerycdnpath"      => "\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/1.7.2\/jquery.min.js",
            "jqueryui"           => "1",
            "jqueryuicdnpath"    => "\/\/ajax.googleapis.com\/ajax\/libs\/jqueryui\/1.8.6\/jquery-ui.min.js",
            "jqueryuicss"        => "1",
            "jqueryuicsspath"    => "\/media\/system\/css\/jquery.ui.css",
            "jquerytools"        => "1",
            "jquerytoolscdnpath" => "http:\/\/cdn.jquerytools.org\/1.2.5\/all\/jquery.tools.min.js",
            "jqueryfb"           => "1",
            "jqueryfbcdnpath"    => "\/\/fancyapps.com\/fancybox\/",
            "jqueryfbcss"        => "1",
            "jqueryfbcsspath"    => "\/media\/system\/css\/jquery.fancybox.css",
            "activateSite"       => "1",
            "noconflictSite"     => "0",
            "activateAdmin"      => "0",
            "noconflictAdmin"    => "0"
        ));
        $this->addPluginEntry('authentication', 'hubzero');
        $this->addPluginEntry('authentication', 'facebook');
        $this->addPluginEntry('authentication', 'google');
        $this->addPluginEntry('authentication', 'linkedin');
        $this->addPluginEntry('user', 'ldap');

        // Modules
        $this->installModule('search', 'search', false, array(
            'label' => 'Search',
            'width' => '20',
            'text'  => 'Search'
        ));

        // Deletes
        $this->deleteComponentEntry('userpoints');
        $this->deleteComponentEntry('xpoll');
        $this->deleteComponentEntry('sef');
        $this->deleteComponentEntry('ldap');
        $this->deleteComponentEntry('geodb');
        $this->deleteComponentEntry('apc');
        $this->deleteComponentEntry('ximport');
        $this->deleteComponentEntry('myaccount');
        $this->deleteComponentEntry('xflash');
        $this->deleteComponentEntry('xsearch');
        $this->deleteComponentEntry('whois');
        $this->deleteComponentEntry('myhub');
        $this->deletePluginEntry('authentication', 'xauth');
        $this->deletePluginEntry('xauthentication');
        $this->deletePluginEntry('xsearch');
        $this->deletePluginEntry('xhub', 'xlibrary');
        $this->deletePluginEntry('user', 'breeze');
        $this->deleteModuleEntry('mod_myprofile');
        $this->disablePlugin('authentication', 'joomla');

        // Update faq text
        // Update faq text
        $updates = [
            ['/change_password', '/members/{{userid}}/changepassword'],
            ['/members/{{userid}}/changepassword', '/members/myaccount/changepassword'],
            ['/mynanohub/account/', '/members/{{userid}}/'],
            ['/members/{{userid}}/', '/members/myaccount/'],
            ['/lostpassword', '/login/reset'],
            ['/lostusername', '/login/remind'],
            ['"/report_problems/', '"/feedback/report_problems']
        ];

        foreach ($updates as $update) {
            $query = $this->db->getQuery(true);
            $query->update('#__faq')
                ->set(['fulltxt' => $query->replace('fulltxt', $update[0], $update[1])])
                ->execute();
        }

        // Insert resource licenses
        $licenses = [
            [
                'name' => 'cc25-by-nc-sa',
                'title' => 'Creative Commons BY-NC-SA 2.5',
                'url' => 'http://creativecommons.org/licenses/by-nc-sa/2.5/',
                'ordering' => 6,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n"
                    . "Noncommercial — You may not use this work for commercial purposes.\r\n\r\n"
                    . "Share Alike — If you alter, transform, or build upon this work, you may "
                        . "distribute the resulting "
                    . "work only under the same or similar license to this one.\r\n\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights.\r\n\r\n"
                    . "Notice — For any reuse or distribution, you must make clear to others the "
                        . "license terms of this "
                    . "work. The best way to do this is with a link to this web page."
            ],
            [
                'name' => 'cc30-by-nc-sa',
                'title' => 'Creative Commons BY-NC-SA 3.0',
                'url' => 'http://creativecommons.org/licenses/by-nc-sa/3.0/',
                'ordering' => 7,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n\r\n"
                    . "Noncommercial — You may not use this work for commercial purposes.\r\n\r\n"
                    . "Share Alike — If you alter, transform, or build upon this work, you may "
                        . "distribute the resulting "
                    . "work only under the same or similar license to this one.\r\n\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights."
            ],
             [
                'name' => 'cc',
                'title' => 'Creative Commons',
                'url' => 'http://creativecommons.org/licenses/by-nc-sa/2.5/',
                'ordering' => 1,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n"
                    . "Noncommercial — You may not use this work for commercial purposes.\r\n\r\n"
                    . "Share Alike — If you alter, transform, or build upon this work, you may "
                        . "distribute the resulting "
                    . "work only under the same or similar license to this one.\r\n\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights.\r\n\r\n"
                    . "Notice — For any reuse or distribution, you must make clear to others the "
                        . "license terms of this "
                    . "work. The best way to do this is with a link to this web page."
            ],
             [
                'name' => 'cc30-by-nc-nd',
                'title' => 'Creative Commons BY-NC-ND 3.0',
                'url' => 'http://creativecommons.org/licenses/by-nc-nd/3.0/',
                'ordering' => 8,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n\r\n"
                    . "Noncommercial — You may not use this work for commercial purposes.\r\n\r\n"
                    . "No Derivative Works — You may not alter, transform, or build upon this work.\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights."
            ],
             [
                'name' => 'cc30-by',
                'title' => 'Creative Commons BY 3.0',
                'url' => 'http://creativecommons.org/licenses/by/3.0/',
                'ordering' => 2,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n"
                    . "to make commercial use of the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights."
            ],
             [
                'name' => 'cc30-by-sa',
                'title' => 'Creative Commons BY-SA 3.0',
                'url' => 'http://creativecommons.org/licenses/by-sa/3.0/',
                'ordering' => 3,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n"
                    . "to make commercial use of the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n\r\n"
                    . "Share Alike — If you alter, transform, or build upon this work, you may "
                        . "distribute the resulting "
                    . "work only under the same or similar license to this one.\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights."
            ],
             [
                'name' => 'cc30-by-nc',
                'title' => 'Creative Commons BY-NC 3.0',
                'url' => 'http://creativecommons.org/licenses/by-nc/3.0/',
                'ordering' => 5,
                'text' => "You are free:\r\n\r\n"
                    . "to Share — to copy, distribute and transmit the work\r\n"
                    . "to Remix — to adapt the work\r\n\r\n"
                    . "Under the following conditions:\r\n\r\n"
                    . "Attribution — You must attribute the work in the manner specified by the author or licensor "
                    . "(but not in any way that suggests that they endorse you or your use of the work).\r\n\r\n"
                    . "Noncommercial — You may not use this work for commercial purposes.\r\n\r\n"
                    . "With the understanding that:\r\n\r\n"
                    . "Waiver — Any of the above conditions can be waived if you get permission from the copyright "
                    . "holder.\r\n\r\n"
                    . "Public Domain — Where the work or any of its elements is in the public domain "
                        . "under applicable "
                    . "law, that status is in no way affected by the license.\r\n\r\n"
                    . "Other Rights — In no way are any of the following rights affected by the license:\r\n"
                    . "- Your fair dealing or fair use rights, or other applicable copyright exceptions and "
                    . "limitations;\r\n"
                    . "- The author\'s moral rights;\r\n"
                    . "- Rights other persons may have either in the work itself or in how the work is used, such as "
                    . "publicity or privacy rights."
            ]
        ];

        foreach ($licenses as $license) {
            $query = $this->db->getQuery(true)
                ->select('name')
                ->from('#__resource_licenses')
                ->where('name', '=', $license['name']);

            if ($query->doesntExist()) {
                $this->db->getQuery(true)
                    ->insertOrIgnore('#__resource_licenses')
                    ->values([
                        'name'      => $license['name'],
                        'text'      => $license['text'],
                        'title'     => $license['title'],
                        'ordering'  => (int)$license['ordering'],
                        'apps_only' => 0,
                        'main'      => null,
                        'icon'      => null,
                        'url'       => $license['url'],
                        'agreement' => 0,
                        'info'      => null
                    ])
                    ->execute();
            }
        }

        // Update timezones
        $timezones = [
            'est' => -5,
            'edt' => -4,
            'cst' => -6,
            'cdt' => -5,
            'mst' => -7,
            'mdt' => -6,
            'pst' => -8,
            'pdt' => -7
        ];

        foreach ($timezones as $tz_name => $tz_offset) {
            $this->db->getQuery(true)
                ->update('#__events')
                ->set(['time_zone' => $tz_offset])
                ->where('time_zone', '=', $tz_name)
                ->execute();
        }

        // Initial population of users password table
        $select = $this->db->getQuery(true)
            ->select('uidNumber')
            ->select('userPassword')
            ->from('#__xprofiles');

        $this->db->getQuery(true)
            ->insertIgnore('#__users_password')
            ->columns(['user_id', 'passhash'])
            ->fromSelect($select)
            ->execute();

        // Update support tickets to use new open field
        $this->db->getQuery(true)
            ->update('#__support_tickets')
            ->set(['open' => 0])
            ->where('status', '=', 2)
            ->execute();

        $this->db->getQuery(true)
            ->update('#__support_tickets')
            ->set(['status' => 2, 'open' => 1])
            ->where('status', '=', 1)
            ->execute();

        $this->db->getQuery(true)
            ->update('#__support_tickets')
            ->set(['status' => 1])
            ->where('owner', '!=', '')
            ->whereNotNull('owner')
            ->where('open', '=', 1)
            ->where('status', '=', 0)
            ->execute();

        // Change xpoll module entries to use poll module
        $this->db->getQuery(true)
            ->update('#__modules')
            ->set(['module' => 'mod_poll'])
            ->where('module', '=', 'mod_xpoll')
            ->execute();

        // Change to use hub menu module
        $this->db->getQuery(true)
            ->update('#__modules')
            ->set(['module' => 'mod_hubmenu'])
            ->where('module', '=', 'mod_menu')
            ->where('client_id', '=', 1)
            ->execute();

        // Update login redirect url
        $query = $this->db->getQuery(true);
        $query->update('#__menu')
            ->set(['params' => $query->replace('params', 'login=/myhub', 'login=/members/myaccount/')])
            ->where('alias', '=', 'login')
            ->execute();
    }
}
