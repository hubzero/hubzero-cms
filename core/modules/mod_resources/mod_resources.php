<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Resources;

use Hubzero\Module\Module;
use Hubzero\Facades\App;

/**
 * Module class for com_resources data
 */
class Resources extends Module
{
    /**
     * @var  mixed
     */
    public $draftInternal;
    /**
     * @var  mixed
     */
    public $draftUser;
    /**
     * @var  mixed
     */
    public $pending;
    /**
     * @var  mixed
     */
    public $published;
    /**
     * @var  mixed
     */
    public $removed;
    /**
     * @var  mixed
     */
    public $unpublished;
    /**
     * @var  array<mixed>|float|int|null
     */
    public $draft;

    /**
     * Display module contents
     *
     * @return     void
     */
    public function display()
    {
        if (!App::isAdmin()) {
            return;
        }

        $database = App::get('db');

        $queries = [
            'unpublished'   => 0,
            'published'     => 1,
            'draftUser'     => 2,
            'pending'       => 3,
            'removed'       => 4,
            'draftInternal' => 5,
        ];

        foreach ($queries as $key => $state) {
            $database->setQuery("SELECT count(*) FROM `#__resources` WHERE published=$state AND standalone=1");
            $this->$key = $database->loadResult();
        }

        // Get the view
        parent::display();
    }
}
