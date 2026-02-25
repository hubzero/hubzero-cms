<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Hittable trait for Relational models (hit/view counter)
 *
 * This trait provides hit counting functionality for models, allowing you to
 * track how many times a record has been viewed. Useful for articles, products,
 * or any content where view counts matter.
 *
 * ## Usage
 *
 * Add the trait to your model and ensure your table has a hits column:
 *
 * ```php
 * class Article extends Relational
 * {
 *     use \Hubzero\Database\Traits\Hittable;
 *
 *     protected $table = '#__articles';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Your table needs a hits column (INT):
 *
 * ```sql
 * ALTER TABLE #__articles ADD COLUMN hits INT UNSIGNED NOT NULL DEFAULT 0;
 * ```
 *
 * Or using schema builder:
 *
 * ```php
 * $this->schema()->alterTable('#__articles')
 *     ->addColumn('hits', 'integer', ['unsigned' => true, 'default' => 0])
 *     ->execute();
 * ```
 *
 * ### Custom Column Name
 *
 * Override the column name if needed:
 *
 * ```php
 * protected $hitsColumn = 'view_count';
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // Increment hit counter
 * $article->hit();
 *
 * // Increment by specific amount
 * $article->hit(5);
 *
 * // Get current hit count
 * $count = $article->getHits();
 *
 * // Reset hit counter
 * $article->resetHits();
 *
 * // Set specific hit count
 * $article->setHits(100);
 * ```
 *
 * ## Session-Based Deduplication
 *
 * To prevent counting multiple views from the same user session:
 *
 * ```php
 * // Only counts once per session
 * $article->hitOnce();
 *
 * // With custom session key prefix
 * $article->hitOnce('article_views');
 *
 * // Check if already hit this session
 * if (!$article->hasHitSession()) {
 *     $article->hit();
 * }
 * ```
 */
trait Hittable
{
    /**
     * Get the name of the hits column
     *
     * @return  string
     */
    public function getHitsColumn(): string
    {
        return property_exists($this, 'hitsColumn')
            ? $this->hitsColumn
            : 'hits';
    }

    /**
     * Get the current hit count
     *
     * @return  int
     */
    public function getHits(): int
    {
        return (int) $this->get($this->getHitsColumn(), 0);
    }

    /**
     * Increment the hit counter
     *
     * @param   int   $amount  Amount to increment (default: 1)
     * @return  bool  True on success
     */
    public function hit(int $amount = 1): bool
    {
        if ($amount <= 0) {
            return true;
        }

        $hitsCol = $this->getHitsColumn();
        $currentHits = $this->getHits();
        $newHits = $currentHits + $amount;

        // Use atomic increment in database
        $result = $this->getQuery()
            ->update($this->getTableName())
            ->setRaw($hitsCol, $hitsCol . ' + ' . (int) $amount)
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        if ($result) {
            // Update local model
            $this->set($hitsCol, $newHits);
            $this->syncOriginalAttribute($hitsCol);
        }

        return (bool) $result;
    }

    /**
     * Increment hit counter only once per session
     *
     * This prevents counting multiple page views from the same user
     * in a single session.
     *
     * @param   string  $sessionPrefix  Optional prefix for session key
     * @return  bool    True if hit was counted, false if already counted this session
     */
    public function hitOnce(string $sessionPrefix = 'content_hits'): bool
    {
        if ($this->hasHitSession($sessionPrefix)) {
            return false;
        }

        // Mark as hit in session
        $this->markHitSession($sessionPrefix);

        // Increment counter
        return $this->hit();
    }

    /**
     * Check if this record has been hit in the current session
     *
     * @param   string  $sessionPrefix  Prefix for session key
     * @return  bool
     */
    public function hasHitSession(string $sessionPrefix = 'content_hits'): bool
    {
        $sessionKey = $this->getHitSessionKey($sessionPrefix);

        // Try HubZero session
        if (class_exists('\\App') && method_exists('\\App', 'get')) {
            $session = \Hubzero\Facades\App::get('session');
            if ($session) {
                $hits = $session->get($sessionKey, []);
                return in_array($this->getPkValue(), $hits);
            }
        }

        // Fallback to native PHP session
        if (session_status() === PHP_SESSION_ACTIVE) {
            $hits = $_SESSION[$sessionKey] ?? [];
            return in_array($this->getPkValue(), $hits);
        }

        return false;
    }

    /**
     * Mark this record as hit in the current session
     *
     * @param   string  $sessionPrefix  Prefix for session key
     * @return  void
     */
    protected function markHitSession(string $sessionPrefix = 'content_hits'): void
    {
        $sessionKey = $this->getHitSessionKey($sessionPrefix);
        $pk = $this->getPkValue();

        // Try HubZero session
        if (class_exists('\\App') && method_exists('\\App', 'get')) {
            $session = \Hubzero\Facades\App::get('session');
            if ($session) {
                $hits = $session->get($sessionKey, []);
                $hits[] = $pk;
                $session->set($sessionKey, array_unique($hits));
                return;
            }
        }

        // Fallback to native PHP session
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (!isset($_SESSION[$sessionKey])) {
                $_SESSION[$sessionKey] = [];
            }
            $_SESSION[$sessionKey][] = $pk;
            $_SESSION[$sessionKey] = array_unique($_SESSION[$sessionKey]);
        }
    }

    /**
     * Get the session key for hit tracking
     *
     * @param   string  $prefix  Session key prefix
     * @return  string
     */
    protected function getHitSessionKey(string $prefix): string
    {
        return $prefix . '_' . $this->getTableName();
    }

    /**
     * Reset the hit counter to zero
     *
     * @return  bool
     */
    public function resetHits(): bool
    {
        return $this->setHits(0);
    }

    /**
     * Set the hit counter to a specific value
     *
     * @param   int   $hits  The hit count to set
     * @return  bool
     */
    public function setHits(int $hits): bool
    {
        if ($hits < 0) {
            $hits = 0;
        }

        $hitsCol = $this->getHitsColumn();

        $result = $this->getQuery()
            ->update($this->getTableName())
            ->set([$hitsCol => $hits])
            ->whereEquals($this->getPrimaryKey(), $this->getPkValue())
            ->execute();

        if ($result) {
            $this->set($hitsCol, $hits);
            $this->syncOriginalAttribute($hitsCol);
        }

        return (bool) $result;
    }

    /**
     * Static method to increment hits for a record by ID
     *
     * @param   mixed  $id      Primary key value
     * @param   int    $amount  Amount to increment
     * @return  bool
     */
    public static function incrementHits($id, int $amount = 1): bool
    {
        $instance = new static();
        $hitsCol = $instance->getHitsColumn();

        return (bool) $instance->getQuery()
            ->update($instance->getTableName())
            ->setRaw($hitsCol, $hitsCol . ' + ' . (int) $amount)
            ->whereEquals($instance->getPrimaryKey(), $id)
            ->execute();
    }

    /**
     * Get the most viewed records
     *
     * @param   int    $limit  Number of records to return
     * @param   array  $where  Additional WHERE conditions
     * @return  \Hubzero\Database\Rows
     */
    public static function mostViewed(int $limit = 10, array $where = [])
    {
        $instance = new static();
        $hitsCol = $instance->getHitsColumn();

        $query = $instance->order($hitsCol, 'desc')->limit($limit);

        foreach ($where as $col => $val) {
            $query->whereEquals($col, $val);
        }

        return $query->rows();
    }

    /**
     * Get total hits across all records
     *
     * @param   array  $where  Optional WHERE conditions
     * @return  int
     */
    public static function totalHits(array $where = []): int
    {
        $instance = new static();
        $hitsCol = $instance->getHitsColumn();

        $query = $instance->getQuery()
            ->select('SUM(' . $hitsCol . ') as total_hits')
            ->from($instance->getTableName());

        foreach ($where as $col => $val) {
            $query->whereEquals($col, $val);
        }

        $result = $query->fetch('row');

        return $result ? (int) $result->total_hits : 0;
    }
}
