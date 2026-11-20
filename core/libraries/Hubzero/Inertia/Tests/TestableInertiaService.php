<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia\Tests;

use Hubzero\Inertia\InertiaService;

class TestableInertiaService extends InertiaService
{
    public ?int $status = null;
    /**
     * @var  array<string, string>
     */
    public array $headers = [];
    /**
     * @var  array<mixed>|null
     */
    public ?array $jsonPayload = null;
    public ?string $body = null;
    public ?string $locationTarget = null;
    public ?string $redirectTarget = null;
    public bool $terminated = false;

    protected function addVaryInertia(): void
    {
        $this->headers['Vary'] = 'X-Inertia';
    }

    protected function sendAndTerminate(int $status, array $headers = [], ?string $body = null): void
    {
        if ($this->terminated) {
            return;
        }

        $this->terminated = true;
        $this->status = $status;
        foreach ($headers as $name => $value) {
            $this->headers[(string) $name] = (string) $value;
        }
        $this->addVaryInertia();
        $this->body = $body;

        if (isset($this->headers['X-Inertia-Location'])) {
            $this->locationTarget = $this->headers['X-Inertia-Location'];
        }
        if (isset($this->headers['Location'])) {
            $this->redirectTarget = $this->headers['Location'];
        }

        if (
            is_string($body)
            && isset($this->headers['Content-Type'])
            && str_contains(strtolower($this->headers['Content-Type']), 'application/json')
        ) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->jsonPayload = $decoded;
            }
        }
    }
}
