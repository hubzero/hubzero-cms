<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Site\Controllers;

use Components\Media\Admin\Controllers\Media as Manager;

/**
 * The media manager on the site: the administrator's, with its views, for
 * the editors the site's entry point lets in
 */
class Media extends Manager
{
    /**
     * Constructor
     *
     * @param   array<mixed>  $config  Optional configurations
     * @return  void
     */
    public function __construct($config = [])
    {
        // The manager's views and language live with the administrator's
        if (!isset($config['base_path'])) {
            $config['base_path'] = dirname(__DIR__, 2) . '/admin';
        }

        parent::__construct($config);
    }
}
