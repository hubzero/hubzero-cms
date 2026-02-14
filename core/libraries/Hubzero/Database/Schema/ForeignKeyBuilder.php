<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Fluent Foreign Key Builder
 *
 * Provides a fluent interface for defining foreign key constraints.
 *
 * Usage:
 * ```php
 * // Full fluent syntax
 * $table->foreign('user_id')
 *     ->references('id')
 *     ->on('users')
 *     ->onDelete('cascade')
 *     ->onUpdate('cascade');
 *
 * // Shorthand with constrained() - auto-references table.id
 * $table->foreign('user_id')->constrained();  // References users.id
 * $table->foreign('author_id')->constrained('users');  // References users.id
 *
 * // Via foreignId() convenience method
 * $table->foreignId('user_id')->constrained();  // Creates column + FK
 * ```
 */
class ForeignKeyBuilder
{
    /**
     * The parent table builder
     *
     * @var TableBuilder|AlterTableBuilder
     */
    protected $tableBuilder;

    /**
     * Local column name
     *
     * @var string
     */
    protected $column;

    /**
     * Referenced table name
     *
     * @var string|null
     */
    protected $referencedTable;

    /**
     * Referenced column name
     *
     * @var string
     */
    protected $referencedColumn = 'id';

    /**
     * ON DELETE action
     *
     * @var string
     */
    protected $onDeleteAction = 'NO ACTION';

    /**
     * ON UPDATE action
     *
     * @var string
     */
    protected $onUpdateAction = 'NO ACTION';

    /**
     * Constraint name (auto-generated if not set)
     *
     * @var string|null
     */
    protected $constraintName;

    /**
     * Whether this FK has been finalized
     *
     * @var bool
     */
    protected $finalized = false;

    /**
     * Create a new foreign key builder
     *
     * @param TableBuilder|AlterTableBuilder $tableBuilder Parent builder
     * @param string                         $column       Local column name
     */
    public function __construct($tableBuilder, string $column)
    {
        $this->tableBuilder = $tableBuilder;
        $this->column = $column;
    }

    /**
     * Set the referenced column
     *
     * @param  string $column Referenced column name
     * @return $this
     */
    public function references(string $column): self
    {
        $this->referencedColumn = $column;
        return $this;
    }

    /**
     * Set the referenced table
     *
     * @param  string $table Referenced table name
     * @return $this
     */
    public function on(string $table): self
    {
        $this->referencedTable = $table;
        $this->finalize();
        return $this;
    }

    /**
     * Auto-configure the foreign key based on conventions
     *
     * If no table is provided, it will be derived from the column name:
     * - user_id -> users
     * - author_id -> authors (via 'users' if second param)
     *
     * @param  string|null $table    Optional table name (defaults to derived from column)
     * @param  string      $column   Referenced column (defaults to 'id')
     * @return $this
     */
    public function constrained(?string $table = null, string $column = 'id'): self
    {
        if ($table === null) {
            // Derive table from column name: user_id -> users
            $table = $this->deriveTableFromColumn($this->column);
        }

        $this->referencedTable = $table;
        $this->referencedColumn = $column;
        $this->finalize();
        return $this;
    }

    /**
     * Set the ON DELETE action
     *
     * @param  string $action DELETE action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * @return $this
     */
    public function onDelete(string $action): self
    {
        $this->onDeleteAction = strtoupper($action);
        $this->updateConstraint();
        return $this;
    }

    /**
     * Set the ON UPDATE action
     *
     * @param  string $action UPDATE action (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * @return $this
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdateAction = strtoupper($action);
        $this->updateConstraint();
        return $this;
    }

    /**
     * Set ON DELETE CASCADE
     *
     * @return $this
     */
    public function cascadeOnDelete(): self
    {
        return $this->onDelete('CASCADE');
    }

    /**
     * Set ON UPDATE CASCADE
     *
     * @return $this
     */
    public function cascadeOnUpdate(): self
    {
        return $this->onUpdate('CASCADE');
    }

    /**
     * Set ON DELETE SET NULL
     *
     * @return $this
     */
    public function nullOnDelete(): self
    {
        return $this->onDelete('SET NULL');
    }

    /**
     * Set ON DELETE RESTRICT
     *
     * @return $this
     */
    public function restrictOnDelete(): self
    {
        return $this->onDelete('RESTRICT');
    }

    /**
     * Set ON UPDATE RESTRICT
     *
     * @return $this
     */
    public function restrictOnUpdate(): self
    {
        return $this->onUpdate('RESTRICT');
    }

    /**
     * Set a custom constraint name
     *
     * @param  string $name Constraint name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->constraintName = $name;
        $this->updateConstraint();
        return $this;
    }

    /**
     * Get the foreign key definition as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'column' => $this->column,
            'referencedTable' => $this->referencedTable,
            'referencedColumn' => $this->referencedColumn,
            'onDelete' => $this->onDeleteAction,
            'onUpdate' => $this->onUpdateAction,
            'name' => $this->constraintName,
        ];
    }

    /**
     * Get the local column name
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the referenced table
     *
     * @return string|null
     */
    public function getReferencedTable(): ?string
    {
        return $this->referencedTable;
    }

    /**
     * Get the referenced column
     *
     * @return string
     */
    public function getReferencedColumn(): string
    {
        return $this->referencedColumn;
    }

    /**
     * Get the ON DELETE action
     *
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDeleteAction;
    }

    /**
     * Get the ON UPDATE action
     *
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdateAction;
    }

    /**
     * Get the constraint name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->constraintName;
    }

    /**
     * Derive table name from column name using conventions
     *
     * user_id -> users
     * author_id -> authors
     * category_id -> categories (y -> ies)
     *
     * @param  string $column Column name
     * @return string Derived table name
     */
    protected function deriveTableFromColumn(string $column): string
    {
        // Remove _id suffix
        $base = preg_replace('/_id$/i', '', $column);

        // Simple pluralization
        return $this->pluralize($base);
    }

    /**
     * Simple pluralization for table name derivation
     *
     * @param  string $word Singular word
     * @return string Plural word
     */
    protected function pluralize(string $word): string
    {
        // Common irregular plurals
        $irregulars = [
            'person' => 'people',
            'man' => 'men',
            'woman' => 'women',
            'child' => 'children',
            'foot' => 'feet',
            'tooth' => 'teeth',
            'goose' => 'geese',
            'mouse' => 'mice',
        ];

        $lower = strtolower($word);
        if (isset($irregulars[$lower])) {
            return $irregulars[$lower];
        }

        // Words ending in y (preceded by consonant) -> ies
        if (preg_match('/[bcdfghjklmnpqrstvwxz]y$/i', $word)) {
            return substr($word, 0, -1) . 'ies';
        }

        // Words ending in s, x, z, ch, sh -> es
        if (preg_match('/(s|x|z|ch|sh)$/i', $word)) {
            return $word . 'es';
        }

        // Default: add s
        return $word . 's';
    }

    /**
     * Finalize the foreign key and register it with the parent builder
     *
     * @return void
     */
    protected function finalize(): void
    {
        if (!$this->finalized && $this->referencedTable !== null) {
            $this->tableBuilder->registerForeignKey($this);
            $this->finalized = true;
        }
    }

    /**
     * Update the constraint in the parent builder after modifications
     *
     * @return void
     */
    protected function updateConstraint(): void
    {
        if ($this->finalized) {
            $this->tableBuilder->updateForeignKey($this);
        }
    }

    /**
     * Allow method chaining back to the table builder
     *
     * @param  string $method Method name
     * @param  array  $args   Arguments
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        // First finalize if we have enough info
        if (!$this->finalized && $this->referencedTable !== null) {
            $this->finalize();
        }

        // Delegate to table builder
        return $this->tableBuilder->$method(...$args);
    }
}
