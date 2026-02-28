<?php

namespace Hubzero\Framework\Facades\Services;

/**
 * Flash notification service bridging HubZero's Notify:: to Laravel sessions.
 */
class NotifyService
{
    private array $messages = [];

    public function warning(string $message, ?string $domain = null): static
    {
        return $this->message($message, 'warning', $domain);
    }

    public function success(string $message, ?string $domain = null): static
    {
        return $this->message($message, 'success', $domain);
    }

    public function error(string $message, ?string $domain = null): static
    {
        return $this->message($message, 'error', $domain);
    }

    public function info(string $message, ?string $domain = null): static
    {
        return $this->message($message, 'info', $domain);
    }

    public function message(string $message, string $type = 'info', ?string $domain = null): static
    {
        $this->messages[] = [
            'message' => $message,
            'type'    => $type,
            'domain'  => $domain,
        ];

        return $this;
    }

    public function messages(?string $domain = null): array
    {
        if ($domain === null) {
            return $this->messages;
        }

        return array_filter($this->messages, fn ($m) => $m['domain'] === $domain);
    }

    public function any(?string $domain = null): bool
    {
        return !empty($this->messages($domain));
    }

    public function clear(?string $domain = null): static
    {
        if ($domain === null) {
            $this->messages = [];
        } else {
            $this->messages = array_filter($this->messages, fn ($m) => $m['domain'] !== $domain);
        }

        return $this;
    }
}
