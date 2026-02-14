<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Drivers\Mariadb;

/**
 * Database MariaDB query syntax class
 *
 * MariaDB is a community-developed fork of MySQL that maintains compatibility
 * with MySQL SQL syntax while adding its own extensions.
 *
 * MariaDB-specific SQL features include:
 * - RETURNING clause for INSERT/UPDATE/DELETE (10.5+)
 * - System versioning temporal queries (AS OF, BETWEEN)
 * - Sequences (NEXT VALUE FOR, SETVAL, etc.)
 * - Invisible columns
 * - CHECK constraints (enforced)
 * - Common Table Expressions with RECURSIVE (earlier support than MySQL)
 * - Window functions (earlier support than MySQL)
 *
 * The base SQL syntax for standard queries (SELECT, INSERT, UPDATE, DELETE)
 * is identical to MySQL, so this class extends the MySQL syntax class.
 *
 */
class MariadbSyntax extends \Hubzero\Database\Drivers\Mysql\MysqlSyntax
{
    /**
     * Build a function expression with MariaDB-specific overrides
     *
     * @param   object  $expression  The expression object
     * @return  string
     */
    protected function buildFunctionExpression($expression): string
    {
        $function = $expression->getFunction();
        $args = $expression->getArguments();

        switch ($function) {
            // Sequence functions (native MariaDB 10.3+ syntax)
            case 'NEXTVAL':
                if ($this->connection->supportsSequences()) {
                    return 'NEXTVAL(' . $this->connection->quoteName($args[0]) . ')';
                }
                // Fall back to table-based emulation via parent
                return parent::buildFunctionExpression($expression);

            case 'CURRVAL':
                if ($this->connection->supportsSequences()) {
                    return 'LASTVAL(' . $this->connection->quoteName($args[0]) . ')';
                }
                return parent::buildFunctionExpression($expression);

            default:
                return parent::buildFunctionExpression($expression);
        }
    }
}
