<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Announcements;

use Hubzero\Module\Module;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Date;

/**
 * Module class for displaying announcements
 */
class Helper extends Module
{
    protected $cid;
    protected $container;
    protected $content;

    /**
     * Get a list of content pages
     *
     * @return  array
     */
    private function getList()
    {
        $db = \Hubzero\Facades\App::get('db');

        $catid   = (int) $this->params->get('catid', 0);
        $limit   = (int) $this->params->get('numitems', 0);

        $now = Date::toSql();

        $nullDate = $db->getNullDate();

        // Build slug expressions using database-agnostic helpers
        $articleSlug = 'CASE WHEN ' . $db->sqlLength('a.alias') . ' THEN ' .
            $db->sqlConcatWs(':', ['a.id', 'a.alias']) . ' ELSE a.id END';
        $catSlug = 'CASE WHEN ' . $db->sqlLength('cc.alias') . ' THEN ' .
            $db->sqlConcatWs(':', ['cc.id', 'cc.alias']) . ' ELSE cc.id END';

        $query = $db->getQuery()
            ->select('a.*')
            ->select('cc.alias', 'catname')
            ->select('cc.path', 'catpath')
            ->select($articleSlug, 'slug')
            ->select($catSlug, 'catslug')
            ->from('#__content', 'a')
            ->joinRaw('#__categories AS cc', 'cc.id = a.catid', 'inner')
            ->whereEquals('a.state', 1)
            ->whereRaw(
                '(a.publish_up IS NULL OR a.publish_up = ' . $db->quote($nullDate) .
                ' OR a.publish_up <= ' . $db->quote($now) . ')'
            )
            ->whereRaw(
                '(a.publish_down IS NULL OR a.publish_down = ' . $db->quote($nullDate) .
                ' OR a.publish_down >= ' . $db->quote($now) . ')'
            )
            ->whereEquals('cc.id', (int) $catid)
            ->whereEquals('cc.published', 1)
            ->order('a.publish_up', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->fetch();

        return $rows;
    }

    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        //check if cache diretory is writable as cache files will be created for the announcements
        if ($this->params->get('cache', 1) && !is_writable(PATH_APP . DS . 'cache')) {
            echo '<p class="warning">' . Lang::txt('MOD_ANNOUNCEMENTS_ERROR_CACHE_DIR_WRITEABLE') . '</p>';
            return;
        }

        //check if category has been set
        if (!intval($this->params->get('catid', 0))) {
            echo '<p class="warning">' . Lang::txt('MOD_ANNOUNCEMENTS_ERROR_NO_CATEGORY') . '</p>';
            return;
        }

        // Push some CSS to the template
        $this->css();

        $this->content   = $this->getList();
        $this->cid       = (int) $this->params->get('catid', 0);
        $this->container = $this->params->get('container', 'block-announcements');

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
