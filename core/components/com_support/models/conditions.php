<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Components\Support\Helpers\Utilities;
use Hubzero\Base\Traits\Escapable;
use Hubzero\Base\Obj;
use InvalidArgumentException;
use stdClass;
use Component;
use User;
use Lang;

include_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';
include_once __DIR__ . DS . 'status.php';
include_once __DIR__ . DS . 'category.php';

/*
 * Support model class for query conditions
 */
class Conditions extends Obj
{
    use Escapable;

    /**
     * Database
     *
     * @var object
     */
    public $database;

    /**
     * Registry
     *
     * @var object
     */
    public $config;

    /**
     * SupportQuery condition
     *
     * @var object
     */
    public $record;

    /**
     * Display a form for adding/editing a record
     *
     * @return  void
     */
    public function __construct($record = null)
    {
        /*if ($record)
        {
            $this->setRecord($record);
        }*/
        $this->database = \App::get('db');
        $this->config = Component::params('com_support');
    }

    /**
     * Create a new record
     *
     * @return  void
     */
    public function setRecord($record)
    {
        if (is_string($record)) {
            $this->record = json_decode($record);
        } elseif (is_object($record)) {
            $this->record = $record;
        } else {
            $message = Lang::txt(__METHOD__ . '; Record must be JSON encoded string or object.');
            throw new InvalidArgumentException($message, 500);
        }

        return $this;
    }

    /**
     * Create a new record
     *
     * @return    object
     */
    public function getConditions()
    {
        $conditions = new stdClass();
        $conditions->owner = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false),
                $this->operator('LIKE \'%$1%\'', 'contains', false),
                $this->operator('LIKE \'$1%\'', 'starts with', false),
                $this->operator('LIKE \'%$1\'', 'ends with', false),
                $this->operator('NOT LIKE \'%$1%\'', 'does not contain', false),
                $this->operator('NOT LIKE \'$1%\'', 'does not start with', false),
                $this->operator('NOT LIKE \'%$1\'', 'does not end with', false)
            ),
            'text'
        );

        // Groups
        $items = array(
            $this->value('*', Lang::txt('(any of mine)'), true)
        );

        if ($xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members')) {
            foreach ($xgroups as $xgroup) {
                $xgroup->description = trim($xgroup->description) ?: $xgroup->cn;
                $items[] = $this->value($xgroup->cn, stripslashes($this->escape($xgroup->description)), false);
            }
        }
        $conditions->group = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false),
                $this->operator('LIKE \'%$1%\'', 'contains', false),
                $this->operator('LIKE \'$1%\'', 'starts with', false),
                $this->operator('LIKE \'%$1\'', 'ends with', false),
                $this->operator('NOT LIKE \'%$1%\'', 'does not contain', false),
                $this->operator('NOT LIKE \'$1%\'', 'does not start with', false),
                $this->operator('NOT LIKE \'%$1\'', 'does not end with', false)
            ),
            $items
        );
        $conditions->login = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false),
                $this->operator('LIKE \'%$1%\'', 'contains', false),
                $this->operator('LIKE \'$1%\'', 'starts with', false),
                $this->operator('LIKE \'%$1\'', 'ends with', false),
                $this->operator('NOT LIKE \'%$1%\'', 'does not contain', false),
                $this->operator('NOT LIKE \'$1%\'', 'does not start with', false),
                $this->operator('NOT LIKE \'%$1\'', 'does not end with', false)
            ),
            'text'
        );
        $conditions->id = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false),
                $this->operator('lt', 'less than', false),
                $this->operator('gt', 'grater than', false),
                $this->operator('=lt', 'less than or equal to', false),
                $this->operator('gt=', 'greater than or equal to', false)
            ),
            'text'
        );
        $conditions->report = $this->expression(
            array(
                $this->operator('=', 'is', false),
                $this->operator('!=', 'is not', false),
                $this->operator('LIKE \'%$1%\'', 'contains', true),
                $this->operator('LIKE \'$1%\'', 'starts with', false),
                $this->operator('LIKE \'%$1\'', 'ends with', false),
                $this->operator('NOT LIKE \'%$1%\'', 'does not contain', false),
                $this->operator('NOT LIKE \'$1%\'', 'does not start with', false),
                $this->operator('NOT LIKE \'%$1\'', 'does not end with', false)
            ),
            'text'
        );
        $conditions->open = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            array(
                $this->value('1', 'open', true),
                $this->value('0', 'closed', false)
            )
        );

        $status = Status::all()
            ->order('open', 'desc')
            ->rows();

        $items = array();
        $items[] = $this->value(0, $this->escape('open: New'), true);
        if (count($status) > 0) {
            $switched = false;
            foreach ($status as $anode) {
                if (!$anode->open && !$switched) {
                    $items[] = $this->value(-1, $this->escape('closed: No resolution'), false);
                    $switched = true;
                }
                $prefix = ($anode->open ? 'open: ' : 'closed: ');
                $label = $this->escape($prefix . stripslashes($anode->title));
                $items[] = $this->value($anode->id, $label, false);
            }
        }
        $conditions->status = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            $items
        );
        $conditions->created = $this->expression(
            array(
                $this->operator('=', 'on', true),
                $this->operator('lt', 'before', false),
                $this->operator('gt', 'after', false)
            ),
            'text'
        );
        $conditions->closed = $this->expression(
            array(
                $this->operator('=', 'on', true),
                $this->operator('lt', 'before', false),
                $this->operator('gt', 'after', false)
            ),
            'text'
        );
        $conditions->tag = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            'text'
        );
        $conditions->type = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            array(
                $this->value('0', 'user submitted', true),
                $this->value('1', 'automatic', false),
                $this->value('3', 'tool', false)
            )
        );

        $severities = Utilities::getSeverities($this->config->get('severities'));
        $items = 'text';
        if (isset($severities) && is_array($severities)) {
            $items = array();
            foreach ($severities as $severity) {
                $sel = false;
                if ($severity == 'normal') {
                    $sel = true;
                }
                $items[] = $this->value($severity, Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . $severity), $sel);
            }
        }
        $conditions->severity = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            $items
        );

        $categories = Category::all()->rows();
        $items = 'text';
        if (count($categories) > 0) {
            $items = array();
            foreach ($categories as $anode) {
                $sel = false;
                $items[] = $this->value($this->escape($anode->alias), $this->escape(stripslashes($anode->title)), $sel);
            }
        }
        $conditions->category = $this->expression(
            array(
                $this->operator('=', 'is', true),
                $this->operator('!=', 'is not', false)
            ),
            $items
        );
        return $conditions;
    }

    /**
     * Create an expression object
     *
     * @param    array $operators List of operators
     * @param    mixed $values    Either a string or array
     * @return   object
     */
    private function expression($operators, $values)
    {
        $obj = new stdClass();
        $obj->operators = $operators;
        $obj->values    = $values;

        return $obj;
    }

    /**
     * Create an operator object
     *
     * @param    string  $val   Operator value
     * @param    string  $label Operator label
     * @param    boolean $sel   Operator selected?
     * @return   object
     */
    private function operator($val = '=', $label = 'is', $sel = false)
    {
        $obj = new stdClass();
        $obj->val   = $val;
        $obj->label = $label;
        $obj->sel   = $sel;

        return $obj;
    }

    /**
     * Create a value object
     *
     * @param    string  $val   Operator value
     * @param    string  $label Operator label
     * @param    boolean $sel   Operator selected?
     * @return   object
     */
    private function value($val = '=', $label = 'is', $sel = false)
    {
        return $this->operator($val, $label, $sel);
    }
}
