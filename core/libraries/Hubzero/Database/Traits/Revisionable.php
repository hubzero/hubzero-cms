<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Revisionable trait for Relational models
 *
 * This trait provides version history functionality for models, allowing records
 * to maintain a history of changes with the ability to view and restore previous
 * versions.
 *
 * ## Usage
 *
 * Add the trait to your model and create a revisions table:
 *
 * ```php
 * class Article extends Relational
 * {
 *     use \Hubzero\Database\Traits\Revisionable;
 *
 *     protected $table = '#__articles';
 * }
 * ```
 *
 * ### Database Migration
 *
 * Create a revisions table following the naming convention `{table}_revisions`:
 *
 * ```sql
 * CREATE TABLE #__articles_revisions (
 *     id INT AUTO_INCREMENT PRIMARY KEY,
 *     article_id INT NOT NULL,
 *     revision_number INT NOT NULL,
 *     data JSON NOT NULL,
 *     created DATETIME NOT NULL,
 *     created_by INT NOT NULL,
 *     log TEXT,
 *     INDEX idx_article_revision (article_id, revision_number)
 * );
 * ```
 *
 * Or using schema builder:
 *
 * ```php
 * $this->schema()->createTable('#__articles_revisions')
 *     ->addColumn('id', 'integer', ['autoIncrement' => true, 'primary' => true])
 *     ->addColumn('article_id', 'integer', ['nullable' => false])
 *     ->addColumn('revision_number', 'integer', ['nullable' => false])
 *     ->addColumn('data', 'text', ['nullable' => false])
 *     ->addColumn('created', 'datetime', ['nullable' => false])
 *     ->addColumn('created_by', 'integer', ['nullable' => false])
 *     ->addColumn('log', 'text', ['nullable' => true])
 *     ->addIndex('idx_article_revision', ['article_id', 'revision_number'])
 *     ->execute();
 * ```
 *
 * ### Configuration
 *
 * Override these properties in your model as needed:
 *
 * ```php
 * // Custom revision table name
 * protected $revisionTable = '#__article_history';
 *
 * // Custom foreign key column name
 * protected $revisionForeignKey = 'article_id';
 *
 * // Limit which fields are tracked (default: all non-pk fields)
 * protected $revisionable = ['title', 'content', 'state'];
 *
 * // Maximum revisions to keep (0 = unlimited)
 * protected $maxRevisions = 50;
 *
 * // Auto-create revision on update (default: false)
 * protected $autoRevision = true;
 * ```
 *
 * ## Basic Operations
 *
 * ```php
 * // Create a revision manually
 * $article->createRevision('Updated content section');
 *
 * // Get all revisions
 * $revisions = $article->revisions();
 *
 * // Get specific revision
 * $revision = $article->findRevision(3);
 *
 * // Get latest revision
 * $latest = $article->latestRevision();
 *
 * // Restore from revision
 * $article->restoreRevision(5);
 *
 * // Compare revisions
 * $diff = $article->diffRevisions(3, 5);
 *
 * // Get revision count
 * $count = $article->getRevisionCount();
 *
 * // Prune old revisions (keep last 10)
 * Article::pruneRevisions(10);
 * ```
 */
trait Revisionable
{
    /**
     * Get the revision table name
     *
     * Defaults to {table}_revisions
     *
     * @return  string
     */
    public function getRevisionTable(): string
    {
        if (property_exists($this, 'revisionTable')) {
            return $this->revisionTable;
        }

        return $this->getTableName() . '_revisions';
    }

    /**
     * Get the foreign key column name in the revision table
     *
     * @return  string
     */
    public function getRevisionForeignKey(): string
    {
        if (property_exists($this, 'revisionForeignKey')) {
            return $this->revisionForeignKey;
        }

        // Default: primary key name from the model
        // Most tables use 'id', so foreign key becomes '{singular_table}_id'
        // e.g., articles -> article_id
        $pk = $this->getPrimaryKey();

        // If PK is just 'id', derive from table name
        if ($pk === 'id') {
            $table = $this->getTableName();
            // Remove common prefixes
            $table = preg_replace('/^(#__|jos_|[a-z]+_)/', '', $table);
            // Singularize (simple: remove trailing 's')
            $table = rtrim($table, 's');
            return $table . '_id';
        }

        return $pk;
    }

    /**
     * Get the fields that should be tracked in revisions
     *
     * @return  array
     */
    public function getRevisionableFields(): array
    {
        if (property_exists($this, 'revisionable')) {
            return (array) $this->revisionable;
        }

        // Default: all fields except primary key
        $fields = array_keys($this->getAttributes());
        $pk = $this->getPrimaryKey();

        return array_values(array_diff($fields, [$pk]));
    }

    /**
     * Get maximum revisions to keep (0 = unlimited)
     *
     * @return  int
     */
    public function getMaxRevisions(): int
    {
        return property_exists($this, 'maxRevisions')
            ? (int) $this->maxRevisions
            : 0;
    }

    /**
     * Check if auto-revision on update is enabled
     *
     * @return  bool
     */
    public function isAutoRevisionEnabled(): bool
    {
        return property_exists($this, 'autoRevision')
            ? (bool) $this->autoRevision
            : false;
    }

    /**
     * Create a revision snapshot of the current state
     *
     * @param   string|null  $log  Optional log message describing the change
     * @return  bool  True on success
     */
    public function createRevision(?string $log = null): bool
    {
        // Can't create revision for unsaved record
        if (!$this->getPkValue()) {
            return false;
        }

        // Gather field data
        $data = [];
        foreach ($this->getRevisionableFields() as $field) {
            $data[$field] = $this->get($field);
        }

        $revisionNumber = $this->getNextRevisionNumber();

        $result = $this->getQuery()
            ->push($this->getRevisionTable(), [
                $this->getRevisionForeignKey() => $this->getPkValue(),
                'revision_number' => $revisionNumber,
                'data' => json_encode($data),
                'created' => $this->getCurrentRevisionTimestamp(),
                'created_by' => $this->getCurrentRevisionUserId(),
                'log' => $log,
            ]);

        // Clear query cache so subsequent operations see the new revision
        \Hubzero\Database\Query::purgeCache();

        // Auto-prune if max revisions configured
        if ($result && $this->getMaxRevisions() > 0) {
            $this->pruneOldRevisions();
        }

        return (bool) $result;
    }

    /**
     * Get all revisions for this record
     *
     * @param   string  $order  Sort order ('desc' or 'asc')
     * @return  array
     */
    public function revisions(string $order = 'desc'): array
    {
        if (!$this->getPkValue()) {
            return [];
        }

        $rows = $this->getQuery()
            ->select('*')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->order('revision_number', $order)
            ->fetch('rows');

        // Decode JSON data
        $result = [];
        foreach ($rows as $row) {
            if (isset($row->data) && is_string($row->data)) {
                $row->data = json_decode($row->data, true);
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Find a specific revision by number
     *
     * Note: Default parameter of -1 is used to handle relationship discovery
     * in Relational which invokes methods with no arguments.
     *
     * @param   int  $number  The revision number (-1 returns null)
     * @return  object|null  Revision object or null if not found
     */
    public function findRevision(int $number = -1): ?object
    {
        if ($number < 0 || !$this->getPkValue()) {
            return null;
        }

        $revision = $this->getQuery()
            ->select('*')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->whereEquals('revision_number', $number)
            ->fetch('row');

        if ($revision && isset($revision->data) && is_string($revision->data)) {
            $revision->data = json_decode($revision->data, true);
        }

        return $revision ?: null;
    }

    /**
     * Get the latest (most recent) revision
     *
     * @return  object|null  Revision object or null if none exist
     */
    public function latestRevision(): ?object
    {
        if (!$this->getPkValue()) {
            return null;
        }

        $revision = $this->getQuery()
            ->select('*')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->order('revision_number', 'desc')
            ->limit(1)
            ->fetch('row');

        if ($revision && isset($revision->data) && is_string($revision->data)) {
            $revision->data = json_decode($revision->data, true);
        }

        return $revision ?: null;
    }

    /**
     * Restore the model to a previous revision
     *
     * Note: Default parameter of -1 handles relationship discovery.
     *
     * @param   int   $number             The revision number to restore (-1 returns false)
     * @param   bool  $createNewRevision  Create a revision before restoring (default: true)
     * @return  bool  True on success
     */
    public function restoreRevision(int $number = -1, bool $createNewRevision = true): bool
    {
        if ($number < 0) {
            return false;
        }

        $revision = $this->findRevision($number);

        if (!$revision || empty($revision->data)) {
            return false;
        }

        // Optionally save current state first
        if ($createNewRevision) {
            $this->createRevision('Before restore from revision ' . $number);
        }

        // Apply revision data to model and save
        foreach ($revision->data as $field => $value) {
            $this->set($field, $value);
        }

        $result = $this->save();
        return $result !== false;
    }

    /**
     * Get the number of revisions for this record
     *
     * @return  int
     */
    public function getRevisionCount(): int
    {
        if (!$this->getPkValue()) {
            return 0;
        }

        $result = $this->getQuery()
            ->select('COUNT(*) as cnt')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->fetch('row');

        return $result ? (int) $result->cnt : 0;
    }

    /**
     * Get the next revision number for this record
     *
     * @return  int
     */
    protected function getNextRevisionNumber(): int
    {
        $result = $this->getQuery()
            ->select('MAX(revision_number) as max_num')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->fetch('row');

        return $result && $result->max_num ? (int) $result->max_num + 1 : 1;
    }

    /**
     * Compare two revisions and return the differences
     *
     * Note: Default parameters handle relationship discovery.
     *
     * @param   int  $fromRevision  The older revision number (-1 returns empty)
     * @param   int  $toRevision    The newer revision number (-1 returns empty)
     * @return  array  Array of field => ['old' => value, 'new' => value]
     */
    public function diffRevisions(int $fromRevision = -1, int $toRevision = -1): array
    {
        if ($fromRevision < 0 || $toRevision < 0) {
            return [];
        }

        $fromRev = $this->findRevision($fromRevision);
        $toRev = $this->findRevision($toRevision);

        if (!$fromRev || !$toRev) {
            return [];
        }

        $diff = [];

        // Get all fields from both revisions
        $allFields = array_unique(array_merge(
            array_keys($fromRev->data ?? []),
            array_keys($toRev->data ?? [])
        ));

        foreach ($allFields as $field) {
            $oldVal = $fromRev->data[$field] ?? null;
            $newVal = $toRev->data[$field] ?? null;

            if ($oldVal !== $newVal) {
                $diff[$field] = [
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        return $diff;
    }

    /**
     * Compare a revision to the current state
     *
     * Note: Default parameter handles relationship discovery.
     *
     * @param   int  $revisionNumber  The revision number to compare (-1 returns empty)
     * @return  array  Array of field => ['old' => value, 'new' => value]
     */
    public function diffFromRevision(int $revisionNumber = -1): array
    {
        if ($revisionNumber < 0) {
            return [];
        }

        $revision = $this->findRevision($revisionNumber);

        if (!$revision) {
            return [];
        }

        $diff = [];
        $revisionableFields = $this->getRevisionableFields();

        foreach ($revisionableFields as $field) {
            $oldVal = $revision->data[$field] ?? null;
            $newVal = $this->get($field);

            if ($oldVal !== $newVal) {
                $diff[$field] = [
                    'old' => $oldVal,
                    'new' => $newVal,
                ];
            }
        }

        return $diff;
    }

    /**
     * Check if current state differs from the latest revision
     *
     * @return  bool
     */
    public function hasChangedSinceLastRevision(): bool
    {
        $latest = $this->latestRevision();

        if (!$latest) {
            return true; // No revisions exist
        }

        foreach ($this->getRevisionableFields() as $field) {
            $revisionVal = $latest->data[$field] ?? null;
            $currentVal = $this->get($field);

            // Use string comparison to handle PDO drivers that return all values
            // as strings (e.g., PDO_INFORMIX) vs JSON-decoded typed values
            if ((string) $revisionVal !== (string) $currentVal) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete all revisions for this record
     *
     * @return  bool  True on success
     */
    public function deleteRevisions(): bool
    {
        if (!$this->getPkValue()) {
            return false;
        }

        $result = $this->getQuery()
            ->delete($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->execute();

        return (bool) $result;
    }

    /**
     * Prune old revisions for this record, keeping the most recent N
     *
     * @param   int|null  $keep  Number to keep (uses maxRevisions if null)
     * @return  int  Number of revisions deleted
     */
    public function pruneOldRevisions(?int $keep = null): int
    {
        $keep = $keep ?? $this->getMaxRevisions();

        if ($keep <= 0 || !$this->getPkValue()) {
            return 0;
        }

        $count = $this->getRevisionCount();

        if ($count <= $keep) {
            return 0;
        }

        // Get revision IDs to delete (oldest first, limit to count - keep)
        $toDelete = $count - $keep;

        $revisions = $this->getQuery()
            ->select('id')
            ->from($this->getRevisionTable())
            ->whereEquals($this->getRevisionForeignKey(), $this->getPkValue())
            ->order('revision_number', 'asc')
            ->limit($toDelete)
            ->fetch('rows');

        if (empty($revisions)) {
            return 0;
        }

        $ids = [];
        foreach ($revisions as $rev) {
            $ids[] = (int) $rev->id;
        }

        // Delete revisions by IDs one at a time using Query's remove method
        $table = $this->getRevisionTable();
        foreach ($ids as $id) {
            $this->getQuery()->remove($table, 'id', $id);
        }

        // Clear query cache so subsequent counts reflect the deletion
        \Hubzero\Database\Query::purgeCache();

        return count($ids);
    }

    /**
     * Prune revisions across all records, keeping the most recent N per record
     *
     * @param   int    $keep   Number of revisions to keep per record
     * @param   array  $where  Optional WHERE conditions
     * @return  int    Number of revisions deleted
     */
    public static function pruneRevisions(int $keep = 10, array $where = []): int
    {
        if ($keep <= 0) {
            return 0;
        }

        $instance = new static();
        $revisionTable = $instance->getRevisionTable();
        $fk = $instance->getRevisionForeignKey();

        // Get all distinct foreign keys
        $query = $instance->getQuery()
            ->select($fk, 'fk_id')
            ->distinct()
            ->from($revisionTable);

        foreach ($where as $col => $val) {
            $query->whereEquals($col, $val);
        }

        $records = $query->fetch('rows');

        if (empty($records)) {
            return 0;
        }

        $totalDeleted = 0;

        foreach ($records as $record) {
            // Count revisions for this record
            $countResult = $instance->getQuery()
                ->select('COUNT(*) as cnt')
                ->from($revisionTable)
                ->whereEquals($fk, $record->fk_id)
                ->fetch('row');

            $count = $countResult ? (int) $countResult->cnt : 0;

            if ($count <= $keep) {
                continue;
            }

            // Get oldest revision IDs to delete
            $toDelete = $count - $keep;

            $revisions = $instance->getQuery()
                ->select('id')
                ->from($revisionTable)
                ->whereEquals($fk, $record->fk_id)
                ->order('revision_number', 'asc')
                ->limit($toDelete)
                ->fetch('rows');

            if (empty($revisions)) {
                continue;
            }

            $ids = [];
            foreach ($revisions as $rev) {
                $ids[] = (int) $rev->id;
            }

            // Delete revisions by IDs one at a time using Query's remove method
            foreach ($ids as $id) {
                $instance->getQuery()->remove($revisionTable, 'id', $id);
            }

            // Clear query cache
            \Hubzero\Database\Query::purgeCache();

            $totalDeleted += count($ids);
        }

        return $totalDeleted;
    }

    /**
     * Boot the revisionable trait
     *
     * If autoRevision is enabled, auto-create revision on update.
     *
     * @return  void
     */
    protected static function bootRevisionable(): void
    {
        // Auto-create revision on update if enabled
        static::updating(function ($model) {
            if ($model->isAutoRevisionEnabled()) {
                $revisionableFields = $model->getRevisionableFields();

                // Only create revision if revisionable fields changed
                if ($model->isDirty($revisionableFields)) {
                    // Create revision of current state before update
                    $model->createRevision('Auto-revision before update');
                }
            }
        });

        // Delete revisions when model is deleted
        static::deleted(function ($model) {
            $model->deleteRevisions();
        });
    }

    /**
     * Get current timestamp for revision
     *
     * @return  string
     */
    protected function getCurrentRevisionTimestamp(): string
    {
        if (class_exists('\\Date')) {
            return \Date::toSql();
        }
        return date('Y-m-d H:i:s');
    }

    /**
     * Get current user ID for revision
     *
     * @return  int
     */
    protected function getCurrentRevisionUserId(): int
    {
        if (class_exists('\\User')) {
            return (int) \User::get('id', 0);
        }
        return 0;
    }
}
