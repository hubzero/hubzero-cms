<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Traits;

/**
 * Sequence-based ID trait for Relational models
 *
 * This trait provides automatic ID generation using database sequences.
 * On save, if the primary key is empty, the trait fetches the next value
 * from the configured sequence and assigns it as the primary key.
 *
 * Works on all backends:
 * - Native sequences on PostgreSQL, Firebird, Informix, MariaDB 10.3+, Oracle
 * - Table-based emulation on MySQL, Percona, SQLite
 *
 * ## Usage
 *
 * ```php
 * class Invoice extends Relational
 * {
 *     use \Hubzero\Database\Traits\HasSequenceId;
 *
 *     protected $table = '#__invoices';
 *
 *     // Optional: custom sequence name (default: {table}_id_seq)
 *     protected $sequenceName = 'invoice_number_seq';
 * }
 * ```
 *
 * The sequence must already exist in the database. Create it via:
 * ```php
 * $driver->createSequence('invoice_number_seq', 1000);
 * ```
 */
trait HasSequenceId
{
    /**
     * Boot the HasSequenceId trait for a model
     *
     * Registers a creating event to auto-assign an ID from the
     * configured sequence when the primary key is empty.
     *
     * @return  void
     */
    protected static function bootHasSequenceId()
    {
        static::creating(function ($model) {
            $pk = $model->getPrimaryKey();

            // Only generate if PK is empty (same pattern as HasUuid)
            if (!$model->get($pk)) {
                $sequence = $model->getSequenceName();
                $driver = static::$connection;
                $id = $driver->nextSequenceValue($sequence);
                $model->set($pk, $id);
            }
        });
    }

    /**
     * Get the sequence name for this model
     *
     * Override in model by setting the $sequenceName property.
     * Default: {table}_id_seq
     *
     * @return  string
     */
    public function getSequenceName(): string
    {
        return property_exists($this, 'sequenceName')
            ? $this->sequenceName
            : $this->getTableName() . '_id_seq';
    }
}
