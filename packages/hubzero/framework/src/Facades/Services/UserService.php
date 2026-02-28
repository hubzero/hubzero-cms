<?php

namespace Hubzero\Framework\Facades\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Bridges HubZero's User:: facade to Laravel's Auth system.
 */
class UserService
{
    public function isGuest(): bool
    {
        return Auth::guest();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // Virtual properties that don't map to a DB column
        if ($key === 'guest') {
            return Auth::guest();
        }

        $user = Auth::user();

        if (!$user) {
            // Guest defaults for common keys
            return match ($key) {
                'id' => 0,
                'username', 'name', 'email' => '',
                default => $default,
            };
        }

        return $user->$key ?? $default;
    }

    public function authorise(string $action, ?string $asset = null): bool
    {
        return !Auth::guest();
    }

    public function getInstance(?int $id = null): static
    {
        return $this;
    }

    /**
     * Get authorised view levels for the current user.
     *
     * Returns public (1) for guests, plus registered (2) for logged-in.
     */
    public function getAuthorisedViewLevels(): array
    {
        if (Auth::guest()) {
            return [1]; // public only
        }
        return [1, 2, 3]; // public, registered, special
    }

    public function link(): string
    {
        $id = $this->get('id', 0);
        if (!$id) {
            return '#';
        }
        return '/members/' . $id;
    }

    public function picture(
        int $member = 0,
        int $anonymous = 0,
        bool $thumbit = true
    ): string {
        return '/core/components/com_members/site/assets/img/profile.gif';
    }

    /**
     * Get a user state variable (session-based state tracking).
     */
    public function getState(string $key, mixed $default = null): mixed
    {
        return session($key, $default);
    }

    /**
     * Set a user state variable.
     */
    public function setState(string $key, mixed $value): mixed
    {
        session([$key => $value]);
        return $value;
    }
}
