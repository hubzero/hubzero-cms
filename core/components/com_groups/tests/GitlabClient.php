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
     * @var  array<mixed>
     */
    public $requests = [];

    /**
     * @param   mixed  $method
     * @param   mixed  $url
     * @param   array<mixed>  $options
     * @return  object
     */
    public function request($method, $url, $options = [])
    {
        $this->requests[] = [$method, $url];

        return new class {
            /**
             * @return  string
             */
            public function getBody()
            {
                return '{}';
            }
        };
    }
}
