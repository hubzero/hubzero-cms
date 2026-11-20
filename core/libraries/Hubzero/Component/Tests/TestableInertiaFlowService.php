<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Inertia\InertiaService;

class TestableInertiaFlowService extends InertiaService
{
    public ?int $status = null;
    /**
     * @var  array<string, string>
     */
    public array $headers = [];
    public ?string $body = null;
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

        $response = \App::get('response');
        if ($response) {
            $response->setStatusCode($status);
            foreach ($headers as $name => $value) {
                $response->headers->set((string) $name, (string) $value);
            }
            $response->headers->set('Vary', 'X-Inertia');
        }
    }
}
