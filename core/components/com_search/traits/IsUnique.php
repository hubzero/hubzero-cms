<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Traits;

/**
 * @phpstan-require-extends \Hubzero\Database\Relational
 */
trait IsUnique
{
    /**
     * Whether no saved record shares this one's values for any of the
     * class's sets of unique keys
     *
     * @return  bool
     */
    public function isUnique()
    {
        $isUnique = true;

        foreach ($this::$uniqueKeys as $fields) {
            if ($this->duplicatesExist($fields)) {
                $isUnique = false;
                break;
            }
        }

        return $isUnique;
    }

    /**
     * @param   array<int, string>  $fields
     * @return  bool
     */
    protected function duplicatesExist($fields)
    {
        $duplicates = $this->findDuplicates($fields);

        return $duplicates->count() > 0;
    }

    /**
     * @param   array<int, string>  $fields
     * @return  static  A query for the saved records with this one's values for the fields
     */
    protected function findDuplicates($fields)
    {
        $savedRecords = self::all();

        foreach ($fields as $field) {
            $savedRecords
                ->whereEquals($field, $this->get($field));
        }

        return $savedRecords;
    }
}
