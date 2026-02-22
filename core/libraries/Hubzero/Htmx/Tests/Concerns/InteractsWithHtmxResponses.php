<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests\Concerns;

use Hubzero\Tests\Concerns\InteractsWithDebugPanelMarkup;
use Hubzero\Tests\Concerns\InteractsWithDebugHeaders;

/**
 * Assertion helpers for HTMX-oriented response testing.
 */
trait InteractsWithHtmxResponses
{
    use InteractsWithDebugPanelMarkup;
    use InteractsWithDebugHeaders;

    /**
     * @param   object  $response
     * @param   string  $header
     * @return  string
     */
    protected function getHtmxHeaderValue(object $response, string $header): string
    {
        $headers = isset($response->headers->headers) && is_array($response->headers->headers)
            ? $response->headers->headers
            : array();

        $this->assertArrayHasKey($header, $headers, 'Expected HTMX header missing: ' . $header);

        return (string) $headers[$header];
    }

    /**
     * @param   object  $response
     * @param   string  $header
     * @param   string  $expected
     * @return  void
     */
    protected function assertHtmxHeader(object $response, string $header, string $expected): void
    {
        $actual = $this->getHtmxHeaderValue($response, $header);
        $this->assertSame($expected, $actual, sprintf('Unexpected %s value.', $header));
    }

    /**
     * @param   object  $response
     * @param   string  $url
     * @return  void
     */
    protected function assertHtmxRedirect(object $response, string $url): void
    {
        $this->assertHtmxHeader($response, 'HX-Redirect', $url);
    }

    /**
     * @param   string  $html
     * @return  void
     */
    protected function assertFragmentResponse(string $html): void
    {
        $trimmed = trim($html);
        $this->assertNotSame('', $trimmed, 'Fragment response should not be empty.');
        $this->assertStringNotContainsString(
            '<!DOCTYPE html>',
            $trimmed,
            'Fragment should not include full document doctype.'
        );
        $this->assertStringNotContainsString(
            '<html',
            strtolower($trimmed),
            'Fragment should not include <html> root element.'
        );
        $this->assertStringNotContainsString(
            '<body',
            strtolower($trimmed),
            'Fragment should not include <body> element.'
        );
    }

    /**
     * @param   string  $html
     * @return  void
     */
    protected function assertNoLayoutInFragment(string $html): void
    {
        $this->assertFragmentResponse($html);
    }

    /**
     * @param   object  $response
     * @return  void
     */
    protected function assertHtmxVaryHeader(object $response): void
    {
        $value = strtolower($this->getHtmxHeaderValue($response, 'Vary'));
        $this->assertStringContainsString('hx-request', $value);
    }

    /**
     * @param   object  $response
     * @param   int     $expected
     * @return  void
     */
    protected function assertHtmxStatus(object $response, int $expected): void
    {
        $actual = isset($response->statusCode) ? (int) $response->statusCode : 0;
        $this->assertSame($expected, $actual, 'Unexpected response status code.');
    }
}
