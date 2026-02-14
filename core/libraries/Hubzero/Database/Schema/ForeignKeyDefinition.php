<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Schema;

/**
 * Foreign key definition for schema builder
 */
class ForeignKeyDefinition
{
    /**
     * Local column name
     *
     * @var string
     */
    protected $column;

    /**
     * Referenced table
     *
     * @var string
     */
    protected $referencedTable;

    /**
     * Referenced column
     *
     * @var string
     */
    protected $referencedColumn = 'id';

    /**
     * ON DELETE action
     *
     * @var string
     */
    protected $onDelete = 'RESTRICT';

    /**
     * ON UPDATE action
     *
     * @var string
     */
    protected $onUpdate = 'RESTRICT';

    /**
     * Constraint name
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * Create a new foreign key definition
     *
     * @param  string  $column
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * Set the referenced table and column
     *
     * @param  string  $column
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
     * @param  string  $table
     * @return $this
     */
    public function on(string $table): self
    {
        $this->referencedTable = $table;
        return $this;
    }

    /**
     * Set ON DELETE action
     *
     * @param  string  $action  (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * @return $this
     */
    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    /**
     * Set ON UPDATE action
     *
     * @param  string  $action  (CASCADE, SET NULL, RESTRICT, NO ACTION)
     * @return $this
     */
    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    /**
     * Set ON DELETE to CASCADE
     *
     * @return $this
     */
    public function cascadeOnDelete(): self
    {
        return $this->onDelete('CASCADE');
    }

    /**
     * Set ON UPDATE to CASCADE
     *
     * @return $this
     */
    public function cascadeOnUpdate(): self
    {
        return $this->onUpdate('CASCADE');
    }

    /**
     * Set ON DELETE to SET NULL
     *
     * @return $this
     */
    public function nullOnDelete(): self
    {
        return $this->onDelete('SET NULL');
    }

    /**
     * Get local column
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get referenced table
     *
     * @return string
     */
    public function getReferencedTable(): string
    {
        return $this->referencedTable;
    }

    /**
     * Get referenced column
     *
     * @return string
     */
    public function getReferencedColumn(): string
    {
        return $this->referencedColumn;
    }

    /**
     * Get ON DELETE action
     *
     * @return string
     */
    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    /**
     * Get ON UPDATE action
     *
     * @return string
     */
    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }
}
