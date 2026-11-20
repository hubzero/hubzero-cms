<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use Hubzero\Facades\App;

/**
 * Support ticket criteria
 */
class Criterion extends Relational
{
    /**
     * Table name
     *
     * @var  string
     */
    protected $table = '#__support_criteria';

    /**
     * Execute a condition and return results
     *
     * @return  array<mixed>
     */
    public function getViolations()
    {
        $violations = [];

        if ($query = $this->get('query')) {
            $db = App::get('db');

            $violations = $db
                ->setQuery($query)
                ->execute()
                ->loadRowList();
        }

        return $violations ?? [];
    }
}
