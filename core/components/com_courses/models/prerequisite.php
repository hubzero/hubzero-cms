<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;

/**
 * Courses model class for prerequisites
 */
class Prerequisite extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Courses\\Tables\\Prerequisites';

    /**
     * Object scope
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_scope = 'prerequisite';

    /**
     * Track the member id for whom the prereqs apply
     *
     * @var int
     **/
    protected $member_id = null;

    /**
     * Store the prereqs themselves
     *
     * @var array
     **/
    protected $prerequisites = array();

    /**
     * Unit progress
     *
     * @var array
     **/
    protected $progress = null;

    /**
     * Asset views
     *
     * @var array
     **/
    protected $views = null;

    /**
     * Grades
     *
     * @var array
     **/
    protected $grades = null;

    /**
     * Constructor
     *
     * @param  (int) $section_id
     * @param  (obj) $gradebook
     * @param  (int) $member_id
     * @return void
     */
    public function __construct($section_id, $gradebook, $member_id)
    {
        $this->_db = \App::get('db');

        $this->_tbl = new $this->_tbl_name($this->_db);

        // Set vars
        $this->member_id = $member_id;
        $prerequisites   = $this->_tbl->loadAllBySectionId($section_id);

        $this->prerequisites = array();
        foreach ($prerequisites as $prerequisite) {
            $key = $prerequisite->item_scope . '.' . $prerequisite->item_id;
            $this->prerequisites[$key][] = array(
                'scope'    => $prerequisite->requisite_scope,
                'scope_id' => $prerequisite->requisite_id
            );
        }

        if (!isset($member_id)) {
            return false;
        }

        $this->progress = $gradebook->progress($member_id);
        $this->views    = $gradebook->views($member_id);
        $filters = array('member_id' => $member_id, 'scope' => 'asset');
        $grades = $gradebook->_tbl->find($filters);

        if ($grades && count($grades) > 0) {
            $this->grades = array();
            foreach ($grades as $grade) {
                $hasOverride = !is_null($grade->override);
                $this->grades[$grade->scope_id] = $hasOverride
                    ? $grade->override
                    : $grade->score;
            }
        }
    }

    /**
     * Get prerequisites
     *
     * @return array
     **/
    public function get($scope, $scope_id = null)
    {
        $key = $scope . '.' . $scope_id;
        return isset($this->prerequisites[$key])
            ? $this->prerequisites[$key]
            : false;
    }

    /**
     * See if item prerequisite has been fulfilled
     *
     * @TODO: For now, we're going to place all of the logic here for checking
     * whether or not different types of items have been fulfilled.
     * Eventually this should be abstracted out elsewhere.
     *
     * @return bool
     **/
    public function hasMet($scope, $scope_id)
    {
        $return = true;

        switch ($scope) {
            case 'unit':
                $key = $scope . '.' . $scope_id;

                $hasPrereqs = isset($this->prerequisites[$key])
                    && count($this->prerequisites[$key]) > 0;
                if ($hasPrereqs) {
                    foreach ($this->prerequisites[$key] as $prerequisite) {
                        $memberId = $this->member_id;
                        $scopeId = $prerequisite['scope_id'];
                        $memberProgress = $this->progress[$memberId] ?? [];
                        $scopeProgress = $memberProgress[$scopeId] ?? null;
                        $pct = $scopeProgress['percentage_complete'] ?? 0;
                        $progress = $this->progress;
                        $noMemberProgress = !isset($progress[$memberId]);
                        $noScopeProgress = !isset($progress[$memberId][$scopeId]);
                        if ($noMemberProgress || $noScopeProgress || $pct != 100) {
                            $return = false;
                            continue;
                        }
                    }
                }

                break;

            case 'asset':
                $key = $scope . '.' . $scope_id;

                $hasPrereqs = isset($this->prerequisites[$key])
                    && count($this->prerequisites[$key]) > 0;
                if ($hasPrereqs) {
                    foreach ($this->prerequisites[$key] as $prerequisite) {
                        $scopeId = $prerequisite['scope_id'];
                        $asset = new Asset($scopeId);

                        if ($asset->get('type') == 'form') {
                            if (!isset($this->grades[$scopeId])) {
                                $return = false;
                                continue;
                            }
                        } else {
                            $memberId = $this->member_id;
                            $views = $this->views[$memberId] ?? null;
                            $noViews = !isset($this->views[$memberId]);
                            $notArray = !is_array($views);
                            $notViewed = !in_array($scopeId, $views ?? []);
                            if ($noViews || $notArray || $notViewed) {
                                $return = false;
                                continue;
                            }
                        }
                    }
                }
                break;
        }

        return $return;
    }
}
