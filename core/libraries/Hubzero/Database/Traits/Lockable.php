<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Lockable trait for Relational models (checkout/checkin functionality)
 *
 * This trait provides row locking functionality for models, allowing records
 * to be "checked out" by a user to prevent concurrent editing. This is useful
 * for content management scenarios where you want to prevent two users from
 * editing the same record simultaneously.
 *
 * ## Usage
 *
 * Add the trait to your model and ensure your table has the required columns:
 *
 * ```php
 * class Article extends Relational
 * {
 *     use \Hubzero\Database\Traits\Lockable;
 *
 *     protected $table = '#__articles';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Your table needs two columns:
 * - `checked_out` (INT or NULL) - User ID who has the record locked
 * - `checked_out_time` (DATETIME or NULL) - When the lock was acquired
 *
 * ```sql
 * ALTER TABLE #__articles
 *     ADD COLUMN checked_out INT UNSIGNED DEFAULT NULL,
 *     ADD COLUMN checked_out_time DATETIME DEFAULT NULL;
 * ```
 *
 * Or using schema builder in a migration:
 *
 * ```php
 * $this->schema()->alterTable('#__articles')
 *     ->addColumn('checked_out', 'integer', ['unsigned' => true, 'nullable' => true])
 *     ->addColumn('checked_out_time', 'datetime', ['nullable' => true])
 *     ->execute();
 * ```
 *
 * ### Custom Column Names
 *
 * Override column names in your model if needed:
 *
 * ```php
 * protected $checkedOutColumn = 'locked_by';
 * protected $checkedOutTimeColumn = 'locked_at';
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // Check out a record for editing
 * $article->checkout($userId);
 *
 * // Check if record is locked by another user
 * if ($article->isCheckedOut($currentUserId)) {
 *     echo "This article is being edited by another user";
 * }
 *
 * // Get info about who has it locked
 * $lockedBy = $article->getCheckedOutUser();
 *
 * // Check in a record (release the lock)
 * $article->checkin();
 *
 * // Force checkin (admin override)
 * $article->checkin(true);
 * ```
 *
 * ## Automatic Timeout
 *
 * By default, locks expire after 2 hours. Configure via:
 *
 * ```php
 * protected $lockTimeout = 7200; // seconds (default: 2 hours)
 * ```
 *
 * Or disable timeout:
 *
 * ```php
 * protected $lockTimeout = 0; // Never expires
 * ```
 */
trait Lockable
{
    /**
     * Get the name of the "checked out" column
     *
     * @return  string
     */
    public function getCheckedOutColumn(): string
    {
        return property_exists($this, 'checkedOutColumn')
            ? $this->checkedOutColumn
            : 'checked_out';
    }

    /**
     * Get the name of the "checked out time" column
     *
     * @return  string
     */
    public function getCheckedOutTimeColumn(): string
    {
        return property_exists($this, 'checkedOutTimeColumn')
            ? $this->checkedOutTimeColumn
            : 'checked_out_time';
    }

    /**
     * Get the lock timeout in seconds
     *
     * @return  int  Timeout in seconds (0 = no timeout)
     */
    public function getLockTimeout(): int
    {
        return property_exists($this, 'lockTimeout')
            ? (int) $this->lockTimeout
            : 7200; // 2 hours default
    }

    /**
     * Check out (lock) this record for editing
     *
     * @param   int|null  $userId  The user ID to lock the record for
     * @return  bool  True on success
     */
    public function checkout($userId = null)
    {
        // Can't checkout if no user
        $userId = (int) $userId;
        if ($userId <= 0) {
            return false;
        }

        // Can't checkout if already checked out by another user
        if ($this->isCheckedOut($userId)) {
            return false;
        }

        $checkedOutCol = $this->getCheckedOutColumn();
        $checkedOutTimeCol = $this->getCheckedOutTimeColumn();

        // Get current timestamp
        $now = $this->getCurrentTimestamp();

        // Update the record
        $this->set($checkedOutCol, $userId);
        $this->set($checkedOutTimeCol, $now);

        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([
                $checkedOutCol => $userId,
                $checkedOutTimeCol => $now,
            ])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        if ($result) {
            $this->syncOriginalAttribute($checkedOutCol);
            $this->syncOriginalAttribute($checkedOutTimeCol);
        }

        return (bool) $result;
    }

    /**
     * Check in (unlock) this record
     *
     * @return  bool  True on success
     */
    public function checkin()
    {
        $checkedOutCol = $this->getCheckedOutColumn();
        $checkedOutTimeCol = $this->getCheckedOutTimeColumn();

        // If not checked out, nothing to do
        $currentLocker = $this->get($checkedOutCol);
        if (empty($currentLocker)) {
            return true;
        }

        // Update the record
        $this->set($checkedOutCol, null);
        $this->set($checkedOutTimeCol, null);

        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([
                $checkedOutCol => null,
                $checkedOutTimeCol => null,
            ])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        if ($result) {
            $this->syncOriginalAttribute($checkedOutCol);
            $this->syncOriginalAttribute($checkedOutTimeCol);
        }

        return (bool) $result;
    }

    /**
     * Force check in (unlock) this record
     *
     * This is an alias for checkin() that makes intent clear
     * when used for admin override scenarios.
     *
     * @return  bool  True on success
     */
    public function forceCheckin(): bool
    {
        return (bool) $this->checkin();
    }

    /**
     * Check if the record is checked out by another user
     *
     * Returns true if:
     * - The record is locked by a different user
     * - The lock has not expired
     *
     * Returns false if:
     * - The record is not locked
     * - The record is locked by the given user
     * - The lock has expired
     *
     * @param   int   $userId  The current user ID to check against
     * @return  bool  True if locked by another user
     */
    public function isCheckedOut(int $userId = 0): bool
    {
        $checkedOutCol = $this->getCheckedOutColumn();
        $checkedOutTimeCol = $this->getCheckedOutTimeColumn();

        $lockedBy = (int) $this->get($checkedOutCol);

        // Not checked out
        if ($lockedBy <= 0) {
            return false;
        }

        // Checked out by the same user
        if ($lockedBy === $userId) {
            return false;
        }

        // Check if lock has expired
        if ($this->isLockExpired()) {
            // Auto-release expired lock
            $this->checkin();
            return false;
        }

        // Locked by another user and not expired
        return true;
    }

    /**
     * Check if the current lock has expired
     *
     * @return  bool  True if expired
     */
    public function isLockExpired(): bool
    {
        $timeout = $this->getLockTimeout();

        // No timeout configured
        if ($timeout <= 0) {
            return false;
        }

        $checkedOutTimeCol = $this->getCheckedOutTimeColumn();
        $lockTime = $this->get($checkedOutTimeCol);

        if (empty($lockTime)) {
            return true;
        }

        $lockTimestamp = strtotime($lockTime);
        $expiresAt = $lockTimestamp + $timeout;

        return time() > $expiresAt;
    }

    /**
     * Get the user ID who has this record checked out
     *
     * @return  int|null  User ID or null if not checked out
     */
    public function getCheckedOutUserId(): ?int
    {
        $checkedOutCol = $this->getCheckedOutColumn();
        $userId = $this->get($checkedOutCol);

        return $userId ? (int) $userId : null;
    }

    /**
     * Get the User model who has this record checked out
     *
     * @return  object|null  User object or null if not checked out
     */
    public function getCheckedOutUser()
    {
        $userId = $this->getCheckedOutUserId();

        if (!$userId) {
            return null;
        }

        return static::resolveUser($userId);
    }

    /**
     * Get when the record was checked out
     *
     * @return  string|null  Datetime string or null
     */
    public function getCheckedOutTime(): ?string
    {
        $checkedOutTimeCol = $this->getCheckedOutTimeColumn();
        return $this->get($checkedOutTimeCol);
    }

    /**
     * Check in all records locked by a specific user
     *
     * Useful when a user logs out or for admin cleanup.
     *
     * @param   int   $userId  The user ID
     * @return  int   Number of records checked in
     */
    public static function checkinByUser(int $userId): int
    {
        $instance = new static();
        $checkedOutCol = $instance->getCheckedOutColumn();
        $checkedOutTimeCol = $instance->getCheckedOutTimeColumn();

        $result = $instance->getQuery()
            ->update($instance->getTableName())
            ->set([
                $checkedOutCol => null,
                $checkedOutTimeCol => null,
            ])
            ->whereEquals($checkedOutCol, $userId)
            ->execute();

        return (int) $result;
    }

    /**
     * Check in all expired locks in this table
     *
     * @return  int  Number of records checked in
     */
    public static function checkinExpired(): int
    {
        $instance = new static();
        $timeout = $instance->getLockTimeout();

        // No timeout = no expiration
        if ($timeout <= 0) {
            return 0;
        }

        $checkedOutCol = $instance->getCheckedOutColumn();
        $checkedOutTimeCol = $instance->getCheckedOutTimeColumn();

        // Calculate expiration cutoff
        $cutoff = date('Y-m-d H:i:s', time() - $timeout);

        $result = $instance->getQuery()
            ->update($instance->getTableName())
            ->set([
                $checkedOutCol => null,
                $checkedOutTimeCol => null,
            ])
            ->whereIsNotNull($checkedOutCol)
            ->where($checkedOutTimeCol, '<', $cutoff)
            ->execute();

        return (int) $result;
    }

    /**
     * Get current timestamp
     *
     * @return  string
     */
    protected function getCurrentTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
