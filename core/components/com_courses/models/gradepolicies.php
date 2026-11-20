<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Facades\Component;
use Hubzero\Facades\App;

/**
 * Courses model class for grade book
 */
class GradePolicies extends Base
{
    /**
     * Table class name
     *
     * @var class-string<\Components\Courses\Tables\GradePolicies>
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\GradePolicies';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Courses\Tables\GradePolicies
     */
    protected $table;

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'gradepolicies';

    /**
     * Constructor
     *
     * @param      integer|mixed $oid  Resource ID
     * @param      integer|null $sid Section ID
     * @return     void
     */
    public function __construct($oid, $sid = null)
    {
        $this->db = App::get('db');

        $this->table = new $this->tableClass($this->db);

        if (is_numeric($oid)) {
            // Check if this is the default section
            if (!is_null($sid)) {
                $section = new Section($sid);

                if (!$section->get('is_default')) {
                    $config  = Component::params('com_courses');
                    $canEdit = $config->get('section_grade_policy', true);

                    if (!$canEdit) {
                        // We need to find the default section and use that grade policy
                        $offering = new Offering($section->get('offering_id'));
                        $default  = $offering->section('!!default!!');
                        $oid      = $default->get('grade_policy_id');
                    }
                }
            }
            $this->table->load($oid);
        }
    }
}
