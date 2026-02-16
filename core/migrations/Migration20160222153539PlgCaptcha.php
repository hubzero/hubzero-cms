<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to move existing captcha plugins to captcha plugin group
**/
class Migration20160222153539PlgCaptcha extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $params = '';

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'captcha',
                    'element' => 'math',
                    'name'    => 'plg_captcha_math'
                ])
                ->where('folder', '=', 'hubzero')
                ->where('element', '=', 'mathcaptcha')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'captcha',
                    'element' => 'image',
                    'name'    => 'plg_captcha_image'
                ])
                ->where('folder', '=', 'hubzero')
                ->where('element', '=', 'imagecaptcha')
                ->execute();

            // Remove the old recaptcha and move the hubzero recaptcha.
            // This will preserve existing settings.
            $this->deletePluginEntry('captcha', 'recaptcha');
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'captcha',
                    'element' => 'recaptcha',
                    'name'    => 'plg_captcha_recaptcha'
                ])
                ->where('folder', '=', 'hubzero')
                ->where('element', '=', 'recaptcha')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'hubzero',
                    'element' => 'mathcaptcha',
                    'name'    => 'plg_hubzero_mathcaptcha'
                ])
                ->where('folder', '=', 'captcha')
                ->where('element', '=', 'math')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'hubzero',
                    'element' => 'imagecaptcha',
                    'name'    => 'plg_hubzero_imagecaptcha'
                ])
                ->where('folder', '=', 'captcha')
                ->where('element', '=', 'image')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'folder'  => 'hubzero',
                    'element' => 'recaptcha',
                    'name'    => 'plg_hubzero_recaptcha'
                ])
                ->where('folder', '=', 'captcha')
                ->where('element', '=', 'recaptcha')
                ->execute();
            $this->addPluginEntry('captcha', 'recaptcha');
        }
    }
}
