<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing some links in default content
 *
*/
class Migration20130426071400Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $result = $this->db->getQuery(true)
            ->select('introtext')
            ->from('#__content')
            ->where('alias', '=', 'licensing')
            ->where('title', '=', 'Intellectual PropertyConsiderations')
            ->value('introtext');

        $search = '<a href="http://www.hubzero.org/topics/middleware">unique middleware</a>';
        $result = str_replace($search, 'unique middleware', $result);

        $this->db->getQuery(true)
            ->update('#__content')
            ->set(['introtext' => $result])
            ->where('alias', '=', 'licensing')
            ->where('title', '=', 'Intellectual Property Considerations')
            ->execute();

        $introtext = $this->db->getQuery(true)
            ->select('introtext')
            ->from('#__content')
            ->where('alias', '=', 'licensing')
            ->where('title', '=', 'Intellectual Property Considerations')
            ->value('introtext');

        if ($introtext) {
            $introtext = str_replace('/feedback/report_problems/', '/support/ticket/new', $introtext);
            $this->db->getQuery(true)
                ->update('#__content')
                ->set(['introtext' => $introtext])
                ->where('alias', '=', 'licensing')
                ->where('title', '=', 'Intellectual Property Considerations')
                ->execute();
        }
    }
}
