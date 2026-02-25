<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\SlidingPanes;

use Hubzero\Module\Module;
use Hubzero\Database\Expression;
use Hubzero\Facades\Date;

/**
 * Module class for displaying sliding panes of content
 */
class Helper extends Module
{
    /**
     * The number of module instances
     *
     * @var  integer
     */
    protected static $instances = 0;

    /**
     * Constructor
     *
     * @param   object  $params  Registry
     * @param   object  $module  Database row
     * @return  void
     */
    public function __construct($params, $module)
    {
        parent::__construct($params, $module);

        self::$instances++;
    }

    /**
     * Get a list of content articles
     *
     * @return     array
     */
    private function getList()
    {
        $db = \Hubzero\Facades\App::get('db');

        $catid   = (int) $this->params->get('catid', 0);
        $random  = $this->params->get('random', 0);
        $limit   = (int) $this->params->get('limitslides', 0);

        $now  = Date::toSql();

        $nullDate = $db->getNullDate();

        $quotedNullDate = $db->quote($nullDate);
        $quotedNow = $db->quote($now);
        $query = $db->getQuery()
            ->select('a.*')
            ->from('#__content', 'a')
            ->joinRaw('#__categories AS cc', 'cc.id = a.catid', 'inner')
            ->whereEquals('a.state', 1)
            ->whereRaw(
                '(a.publish_up IS NULL OR a.publish_up = ' . $quotedNullDate .
                ' OR a.publish_up <= ' . $quotedNow . ')'
            )
            ->whereRaw(
                '(a.publish_down IS NULL OR a.publish_down = ' . $quotedNullDate .
                ' OR a.publish_down >= ' . $quotedNow . ')'
            )
            ->whereEquals('cc.id', (int) $catid)
            ->whereEquals('cc.published', 1);

        if ($random) {
            $query->order(Expression::randomOrder(), 'asc');
        } else {
            $query->order('a.ordering', 'asc');
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->fetch();
    }

    /**
     * Display module contents
     *
     * @return     void
     */
    public function display()
    {
        $type = $this->params->get('animation', 'slide');

        // Check if we have multiple instances of the module running
        // If so, we only want to push the CSS and JS to the template once
        if (self::$instances <= 1) {
            // Push some CSS to the template
            $this->css($type . '.css')
                 ->js();
        }

        $id = rand();

        $this->content   = $this->getList();
        $this->container = $this->params->get('container', 'pane-sliders');

        $jsCode = "jQuery(document).ready(function($){"
            . " $('#" . $this->container . " .panes-content').jSlidingPanes(); });";
        $this->js($jsCode);

        require $this->getLayoutPath();
    }
}
