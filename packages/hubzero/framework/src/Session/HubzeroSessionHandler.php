<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Session;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Session handler that reads/writes the existing HubZero jos_session table.
 *
 * This allows Laravel and legacy HubZero to share session state — a user
 * logged in through one entry point stays logged in through both.
 *
 * Schema: session_id, client_id, guest, time, data, userid, username,
 *         usertype, ip
 */
class HubzeroSessionHandler implements \SessionHandlerInterface
{
    private int $lifetime;

    public function __construct(int $lifetime = 120)
    {
        $this->lifetime = $lifetime;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $session = DB::table('session')
            ->where('session_id', $id)
            ->first();

        if ($session && $session->time >= time() - ($this->lifetime * 60)) {
            return $session->data ?? '';
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        // Extract user info from auth state for denormalized columns
        $user = null;
        try {
            $user = Auth::user();
        } catch (\Throwable $e) {
            // Auth may not be available during early bootstrap
        }

        $userId = $user?->id ?? 0;
        $username = $user?->username ?? '';
        $guest = $user ? 0 : 1;

        $payload = [
            'session_id' => $id,
            'client_id' => 0, // 0 = site
            'guest' => $guest,
            'time' => (string) time(),
            'data' => $data,
            'userid' => $userId,
            'username' => $username,
            'ip' => request()->ip() ?? '',
        ];

        DB::table('session')->updateOrInsert(
            ['session_id' => $id],
            $payload
        );

        return true;
    }

    public function destroy(string $id): bool
    {
        DB::table('session')->where('session_id', $id)->delete();

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        $cutoff = time() - $max_lifetime;

        return DB::table('session')
            ->where('time', '<', (string) $cutoff)
            ->delete();
    }
}
