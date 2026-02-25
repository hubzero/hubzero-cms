<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Supportactivity;

use Hubzero\Module\Module;
use Hubzero\Facades\Request;

/**
 * Module class for an activity feed
 */
class Supportactivity extends Module
{
    protected $feed;
    protected $results;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!\Hubzero\Facades\App::isAdmin()) {
            return;
        }

        $database = \Hubzero\Facades\App::get('db');
        $limit = (int) $this->params->get('limit', 25);
        $query = $database->getQuery()
            ->select('a.*')
            ->fromSub(function ($sub) use ($database) {
                $this->buildActivityUnionQuery($sub, $database);
            }, 'a');

        if ($start = Request::getString('start', '')) {
            $query->where('a.created', '>', $start);
        }

        $this->results = $query
            ->order('a.created', 'desc')
            ->limit($limit)
            ->fetch();

        $this->feed = Request::getInt('feedactivity', 0);

        if ($this->feed == 1) {
            ob_clean();
            foreach ($this->results as $result) {
                require $this->getLayoutPath('default_item');
            }
            exit();
        }

        parent::display();
    }

    /**
     * Build derived activity rows from comments and tickets.
     *
     * Produces:
     * - id
     * - ticket
     * - created
     * - category (comment|change|ticket)
     *
     * @param   object  $query
     * @param   object  $database
     * @return  void
     */
    private function buildActivityUnionQuery($query, $database)
    {
        $commentCategoryExpr = sprintf(
            "(CASE WHEN %s != '' THEN 'comment' ELSE 'change' END)",
            $database->quoteName('c.comment')
        );

        $query->select('c.id')
            ->select('c.ticket')
            ->select('c.created')
            ->select($commentCategoryExpr, 'category')
            ->from('#__support_comments', 'c')
            ->union(function ($union) {
                $union->select("'0'", 'id')
                    ->select('t.id', 'ticket')
                    ->select('t.created')
                    ->select("'ticket'", 'category')
                    ->from('#__support_tickets', 't');
            });
    }
}
