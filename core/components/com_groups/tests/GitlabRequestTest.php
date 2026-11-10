<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tests;

use Components\Groups\Helpers\Gitlab;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/helpers/gitlab.php';
require_once __DIR__ . '/GitlabClient.php';

/**
 * The super group GitLab helper sends each kind of request through its
 * HTTP client.
 */
class GitlabRequestTest extends TestCase
{
    /**
     * @return  void
     */
    public function testRequestsReachTheClient(): void
    {
        $gitlab = (new \ReflectionClass(Gitlab::class))->newInstanceWithoutConstructor();
        $client = new GitlabClient();
        foreach (['client' => $client, 'url' => 'https://git.example/api/v4', 'options' => []] as $name => $value) {
            $property = new \ReflectionProperty($gitlab, $name);
            $property->setAccessible(true);
            $property->setValue($gitlab, $value);
        }

        $this->assertSame([], $gitlab->request('GET', 'groups', 'team'));
        $this->assertSame([], $gitlab->request('POST', 'projects', ['name' => 'site']));
        $this->assertSame([], $gitlab->request('PUT', 'projects/1', []));

        $this->assertSame(['GET', 'POST', 'PUT'], array_column($client->requests, 0));
    }
}
