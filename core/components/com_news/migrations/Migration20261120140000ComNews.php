<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\News\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Register com_news, so its API answers
 *
 * com_news is an API-only component (no site or admin side) and never had
 * a #__extensions row, so /api/news/... was "Component not found". No admin
 * menu item or site route: there is nothing there to open.
 **/
class Migration20261120140000ComNews extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addComponentEntry('news', null, 1, '', false, false);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteComponentEntry('news');
    }
}
