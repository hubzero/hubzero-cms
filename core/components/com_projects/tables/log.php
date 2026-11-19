<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project logs
 *
 * @property  mixed  $action
 * @property  mixed  $ajax
 * @property  mixed  $ip
 * @property  mixed  $layout
 * @property  mixed  $owner
 * @property  mixed  $projectid
 * @property  mixed  $request_uri
 * @property  mixed  $section
 * @property  mixed  $time
 * @property  mixed  $userid
 */
class Log extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
     * @return  void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__project_logs', 'id', $db);
    }
}
