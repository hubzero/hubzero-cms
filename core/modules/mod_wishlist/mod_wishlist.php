<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Wishlist;

use Hubzero\Module\Module;
use Components\Wishlist\Models\Wishlist as WishlistModel;
use Hubzero\Facades\Component;

/**
 * Module class for com_wishlist data
 */
class Wishlist extends Module
{
    protected $accepted;
    protected $granted;
    protected $pending;
    protected $rejected;
    protected $removed;
    protected $wishlist;
    protected $withdrawn;

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

        $wishlist = intval($this->params->get('wishlist', 0));
        if (!$wishlist) {
            $model = WishlistModel::oneByReference(1, 'general');
            if (!$model->get('id')) {
                return false;
            }
            $wishlist = $model->get('id');
        }
        $this->wishlist = $wishlist;

        $queries = array(
            'granted'   => 1,
            'pending'   => "0 AND accepted=0",
            'accepted'  => "0 AND accepted=1",
            'rejected'  => 3,
            'withdrawn' => 4,
            'removed'   => 2
        );

        $database = \Hubzero\Facades\App::get('db');

        foreach ($queries as $key => $state) {
            $database->setQuery(
                "SELECT COUNT(*) FROM `#__wishlist_item` WHERE wishlist=" .
                $database->quote($wishlist) . " AND status=" . $state
            );
            $this->$key = $database->loadResult();
        }

        // Get the view
        parent::display();
    }
}
