<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia\Tests\Concerns;

use Hubzero\Tests\Concerns\InteractsWithDebugPanelMarkup;
use Hubzero\Tests\Concerns\InteractsWithDebugHeaders;

/**
 * Assertion helpers for Inertia-oriented response testing.
 */
trait InteractsWithInertiaResponses
{
    use InteractsWithDebugPanelMarkup;
    use InteractsWithDebugHeaders;

    /**
     * @param   array  $page
     * @return  void
     */
    protected function assertInertiaPageShape(array $page): void
    {
        $this->assertArrayHasKey('component', $page);
        $this->assertArrayHasKey('props', $page);
        $this->assertArrayHasKey('url', $page);
        $this->assertArrayHasKey('version', $page);
        $this->assertIsArray($page['props']);
    }

    /**
     * @param   array   $page
     * @param   string  $component
     * @return  void
     */
    protected function assertInertiaComponent(array $page, string $component): void
    {
        $this->assertInertiaPageShape($page);
        $this->assertSame($component, (string) $page['component']);
    }

    /**
     * @param   array   $page
     * @param   string  $key
     * @return  mixed
     */
    protected function assertInertiaProp(array $page, string $key)
    {
        $this->assertInertiaPageShape($page);
        $this->assertArrayHasKey($key, $page['props']);

        return $page['props'][$key];
    }

    /**
     * @param   object  $response
     * @param   string  $header
     * @return  string
     */
    protected function getInertiaHeaderValue(object $response, string $header): string
    {
        $headers = isset($response->headers->headers) && is_array($response->headers->headers)
            ? $response->headers->headers
            : array();

        $this->assertArrayHasKey($header, $headers, 'Expected Inertia header missing: ' . $header);

        return (string) $headers[$header];
    }

    /**
     * @param   object  $response
     * @param   string  $url
     * @return  void
     */
    protected function assertInertiaLocationHeader(object $response, string $url): void
    {
        $this->assertSame($url, $this->getInertiaHeaderValue($response, 'X-Inertia-Location'));
    }

    /**
     * @param   object  $response
     * @return  void
     */
    protected function assertInertiaVaryHeader(object $response): void
    {
        $value = strtolower($this->getInertiaHeaderValue($response, 'Vary'));
        $this->assertStringContainsString('x-inertia', $value);
    }

    /**
     * @param   object  $response
     * @param   int     $expected
     * @return  void
     */
    protected function assertInertiaStatus(object $response, int $expected): void
    {
        $actual = isset($response->statusCode) ? (int) $response->statusCode : 0;
        $this->assertSame($expected, $actual, 'Unexpected response status code.');
    }
}
