<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tests;

/**
 * An HTTP client that records each request and answers with an empty
 * JSON object
 */
class GitlabClient
{
    /**
     * @var  array
     */
    public $requests = [];

    public function request($method, $url, $options = [])
    {
        $this->requests[] = [$method, $url];

        return new class {
            public function getBody()
            {
                return '{}';
            }
        };
    }
}
