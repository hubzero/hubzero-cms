<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Api\Controllers;

use Hubzero\Component\ApiController;
use Component;
use Exception;
use stdClass;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\User;

/**
 * API controller class for resources
 */
class Entriesv1r0 extends ApiController
{
    /**
     * Get a list of resources
     *
     * @apiMethod GET
     * @apiUri    /content/list
     * @apiParameter {
     *      "name":          "limit",
     *      "description":   "Number of result to return.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       25
     * }
     * @apiParameter {
     *      "name":          "limitstart",
     *      "description":   "Number of where to start returning results.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       0
     * }
     * @return    bool|null
     */
    public function listTask()
    {
        // Incoming
        $filters = [
            'limit'  => Request::getInt('limit', Config::get('list_limit')),
            'start'  => Request::getInt('limitstart', 0),
        ];

        $filters['limit'] = \Hubzero\Utility\Sanitize::paranoid($filters['limit']);
        $filters['start'] = \Hubzero\Utility\Sanitize::paranoid($filters['start']);

        if (User::authorise('core.admin', 'com_content')) {
            $db = App::get('db');
            $totalQuery = 'SELECT COUNT(*) FROM `#__content`;';
            $db->setQuery($totalQuery);
            $total = $db->loadResult();

            $query = 'SELECT * FROM `#__content` LIMIT ' . $filters['start'] . ', ' . $filters['limit'] . ';';
            $db->setQuery($query);
            $pages = $db->loadObjectList();

            // The listing has answered false, and nothing else, for as long as
            // this file has had history.
            return false;
        } else {
            $response = new stdClass();
            $response->success = false;
            $this->send($response);
        }

        return null;
    }
}
