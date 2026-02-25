<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\QuickTips;

use Hubzero\Module\Module;
use Hubzero\Database\Expression;
use Cache;
use Hubzero\Facades\Date;

/**
 * Module class for displaying tips
 */
class Helper extends Module
{
    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        if ($content = $this->getCacheContent()) {
            echo $content;
            return;
        }

        $this->run();
    }

    /**
     * Build module content
     *
     * @return  void
     */
    public function run()
    {
        $database = \Hubzero\Facades\App::get('db');

        $catid  = trim($this->params->get('catid', ''));
        $secid  = trim($this->params->get('secid', ''));
        $method = trim($this->params->get('method', ''));

        $now = Date::toSql();

        $query = $database->getQuery()
            ->select('a.id')
            ->select('a.title')
            ->select('a.introtext')
            ->select('a.created')
            ->from('#__content', 'a')
            ->whereEquals('a.state', 1)
            ->whereEquals('a.checked_out', 0)
            ->where('a.sectionid', '>', 0)
            ->whereRaw('(a.publish_up IS NULL OR a.publish_up <= ' . $database->quote($now) . ')')
            ->whereRaw('(a.publish_down IS NULL OR a.publish_down >= ' . $database->quote($now) . ')');

        if ($catid) {
            $query->whereIn('a.catid', array_map('intval', explode(',', $catid)));
        }

        if ($secid) {
            $query->whereIn('a.sectionid', array_map('intval', explode(',', $secid)));
        }

        if ($method == 'random') {
            $query->order(Expression::randomOrder(), 'asc');
        } elseif ($method == 'ordering') {
            $query->order('a.ordering', 'asc');
        } else {
            $query->order('a.publish_up', 'desc');
        }

        $this->rows = $query
            ->limit(1)
            ->fetch();

        require $this->getLayoutPath();
    }
}
