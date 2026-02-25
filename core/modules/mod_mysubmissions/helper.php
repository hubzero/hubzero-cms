<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MySubmissions;

use Components\Resources\Models\Entry;
use Hubzero\Module\Module;
use Hubzero\Facades\Component;
use Hubzero\Facades\User;
use App;

/**
 * Module class for displaying a user's submissions and their progress
 */
class Helper extends Module
{
    /**
     * Check if the type selection step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepTypeCheck($row)
    {
        return ($row->id ? true : false);
    }

    /**
     * Check if the compose step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepComposeCheck($row)
    {
        return ($row->id ? true : false);
    }

    /**
     * Check if the attach step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepAttachCheck($row)
    {
        if ($row->id) {
            $total = $row->children()->total();
        } else {
            $total = 0;
        }
        return ($total ? true : false);
    }

    /**
     * Check if the authors step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepAuthorsCheck($row)
    {
        if ($row->id) {
            $contributors = $row->authors()->total();
        } else {
            $contributors = 0;
        }

        return ($contributors ? true : false);
    }

    /**
     * Check if the tags step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepTagsCheck($row)
    {
        $tags = $row->tags();

        if (count($tags) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if the review step is completed
     *
     * @param   object   $row  Resource
     * @return  boolean  True if step completed
     */
    public function stepReviewCheck($row)
    {
        return false;
    }

    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        if (User::isGuest()) {
            return false;
        }

        include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

        $this->steps = array(
            'Type',
            'Compose',
            'Attach',
            'Authors',
            'Tags',
            'Review'
        );

        $this->rows = Entry::all()
            ->whereEquals('standalone', 1)
            ->whereEquals('published', 2)
            ->where('type', '!=', 7)
            ->whereEquals('created_by', User::get('id'))
            ->rows();

        require $this->getLayoutPath();
    }
}
