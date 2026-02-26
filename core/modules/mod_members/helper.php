<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Members;

use Hubzero\Module\Module;
use Hubzero\Facades\App;

/**
 * Module class for com_members data
 */
class Helper extends Module
{
    protected $approved;
    protected $confirmed;
    protected $domains;
    protected $pastDay;
    protected $unapproved;
    protected $unconfirmed;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!App::isAdmin()) {
            return;
        }

        $database = App::get('db');

        $this->unconfirmed = $database->getQuery()
            ->select('count(u.id)')
            ->from('#__users', 'u')
            ->whereEquals('u.block', 0)
            ->where('u.activation', '<', 1)
            ->value();

        $this->confirmed = $database->getQuery()
            ->select('count(u.id)')
            ->from('#__users', 'u')
            ->whereEquals('u.block', 0)
            ->where('u.activation', '>=', 1)
            ->value();

        $pastDayDate = gmdate('Y-m-d', (time() - 24 * 3600)) . ' 00:00:00';
        $this->pastDay = $database->getQuery()
            ->select('count(u.id)')
            ->from('#__users', 'u')
            ->whereEquals('u.block', 0)
            ->where('u.registerDate', '>=', $pastDayDate)
            ->value();

        $this->approved = $database->getQuery()
            ->select('count(u.id)')
            ->from('#__users', 'u')
            ->whereEquals('u.block', 0)
            ->where('u.activation', '>', 0)
            ->where('u.approved', '>', 0)
            ->value();

        $this->unapproved = $database->getQuery()
            ->select('count(u.id)')
            ->from('#__users', 'u')
            ->whereEquals('u.block', 0)
            ->where('u.activation', '>', 0)
            ->whereEquals('u.approved', 0)
            ->value();

        $domainExpr = $database->sqlSubstringIndex('email', '@', -1);
        $this->domains = $database->getQuery()
            ->select($domainExpr, 'domain')
            ->select('COUNT(*)', 'email_count')
            ->from('#__users')
            ->whereEquals('block', 0)
            ->group($domainExpr)
            ->order('email_count', 'desc')
            ->fetch();

        // Get the view
        parent::display();
    }
}
