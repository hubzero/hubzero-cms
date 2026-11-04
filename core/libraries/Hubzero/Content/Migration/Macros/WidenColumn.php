<?php
/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to widen a text column
 **/
class WidenColumn extends Macro
{
	/**
	 * Make a char or varchar column a varchar of the given width
	 *
	 * Does nothing when the table or column is missing, or the column is
	 * not char/varchar or already that wide, so it can run again safely.
	 * The column keeps its nullability, default and comment, and its
	 * collation unless a character set is given.
	 *
	 * Throws \Hubzero\Database\Exception\QueryFailedException when the
	 * ALTER fails.
	 *
	 * @param   string       $table    Table name
	 * @param   string       $column   Column name
	 * @param   integer      $width    New width, in characters
	 * @param   string|null  $charset  Character set to store it in instead, e.g. ascii
	 * @param   object|null  $db       Connection to use, if not the migration's own
	 * @return  bool  Whether the column was widened
	 **/
	public function __invoke($table, $column, $width, $charset = null, $db = null)
	{
		$db = $db ?: $this->db;

		if (!$db->tableExists($table))
		{
			return false;
		}

		$fields = $db->getTableColumns($table, false);

		if (!isset($fields[$column]))
		{
			return false;
		}

		$field = $fields[$column];

		if (!preg_match('/^(?:var)?char\((\d+)\)$/i', $field->Type, $match) || (int) $match[1] >= $width)
		{
			return false;
		}

		$query = 'ALTER TABLE ' . $db->quoteName($table)
			. ' MODIFY ' . $db->quoteName($column) . ' varchar(' . (int) $width . ')';

		if ($charset)
		{
			$query .= ' CHARACTER SET ' . preg_replace('/[^a-z0-9_]/i', '', $charset);
		}
		elseif (!empty($field->Collation))
		{
			$query .= ' COLLATE ' . preg_replace('/[^a-z0-9_]/i', '', $field->Collation);
		}

		$query .= ($field->Null == 'YES') ? ' NULL' : ' NOT NULL';

		if ($field->Default !== null)
		{
			$query .= ' DEFAULT ' . $db->quote($field->Default);
		}
		elseif ($field->Null == 'YES')
		{
			$query .= ' DEFAULT NULL';
		}

		if (!empty($field->Comment))
		{
			$query .= ' COMMENT ' . $db->quote($field->Comment);
		}

		$db->setQuery($query);
		$db->query();

		$this->log(sprintf('Widened %s.%s from %s to varchar(%d)%s', $table, $column, $field->Type, $width, $charset ? ' ' . $charset : ''));

		return true;
	}
}
