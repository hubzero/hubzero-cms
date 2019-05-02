<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
	 * @return	void
	 */
	public function __construct($record=null)
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
	 * @return	void
	 */
	public function setRecord($record)
	{
		if (is_string($record))
		{
			$this->record = json_decode($record);
		}
		else if (is_object($record))
		{
			$this->record = $record;
		}
		else
		{
			throw new InvalidArgumentException(Lang::txt(__METHOD__ . '; Record must be JSON encoded string or object.'), 500);
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
		$conditions = new stdClass;
		$conditions->owner = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false),
				$this->_operator('LIKE \'%$1%\'', 'contains', false),
				$this->_operator('LIKE \'$1%\'', 'starts with', false),
				$this->_operator('LIKE \'%$1\'', 'ends with', false),
				$this->_operator('NOT LIKE \'%$1%\'', 'does not contain', false),
				$this->_operator('NOT LIKE \'$1%\'', 'does not start with', false),
				$this->_operator('NOT LIKE \'%$1\'', 'does not end with', false)
			),
			'text'
		);

		// Groups
		$items = array(
			$this->_value('*', Lang::txt('(any of mine)'), true)
		);

		if ($xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'members'))
		{
			foreach ($xgroups as $xgroup)
			{
				$xgroup->description = trim($xgroup->description) ?: $xgroup->cn;
				$items[] = $this->_value($xgroup->cn, stripslashes($this->escape($xgroup->description)), false);
			}
		}
		$conditions->group = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false),
				$this->_operator('LIKE \'%$1%\'', 'contains', false),
				$this->_operator('LIKE \'$1%\'', 'starts with', false),
				$this->_operator('LIKE \'%$1\'', 'ends with', false),
				$this->_operator('NOT LIKE \'%$1%\'', 'does not contain', false),
				$this->_operator('NOT LIKE \'$1%\'', 'does not start with', false),
				$this->_operator('NOT LIKE \'%$1\'', 'does not end with', false)
			),
			$items
		);
		$conditions->login = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false),
				$this->_operator('LIKE \'%$1%\'', 'contains', false),
				$this->_operator('LIKE \'$1%\'', 'starts with', false),
				$this->_operator('LIKE \'%$1\'', 'ends with', false),
				$this->_operator('NOT LIKE \'%$1%\'', 'does not contain', false),
				$this->_operator('NOT LIKE \'$1%\'', 'does not start with', false),
				$this->_operator('NOT LIKE \'%$1\'', 'does not end with', false)
			),
			'text'
		);
		$conditions->id = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false),
				$this->_operator('lt', 'less than', false),
				$this->_operator('gt', 'grater than', false),
				$this->_operator('=lt', 'less than or equal to', false),
				$this->_operator('gt=', 'greater than or equal to', false)
			),
			'text'
		);
		$conditions->report = $this->_expression(
			array(
				$this->_operator('=', 'is', false),
				$this->_operator('!=', 'is not', false),
				$this->_operator('LIKE \'%$1%\'', 'contains', true),
				$this->_operator('LIKE \'$1%\'', 'starts with', false),
				$this->_operator('LIKE \'%$1\'', 'ends with', false),
				$this->_operator('NOT LIKE \'%$1%\'', 'does not contain', false),
				$this->_operator('NOT LIKE \'$1%\'', 'does not start with', false),
				$this->_operator('NOT LIKE \'%$1\'', 'does not end with', false)
			),
			'text'
		);
		$conditions->open = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			array(
				$this->_value('1', 'open', true),
				$this->_value('0', 'closed', false)
			)
		);

		$status = Status::all()
			->order('open', 'desc')
			->rows();

		$items = array();
		$items[] = $this->_value(0, $this->escape('open: New'), true);
		if (count($status) > 0)
		{
			$switched = false;
			foreach ($status as $anode)
			{
				if (!$anode->open && !$switched)
				{
					$items[] = $this->_value(-1, $this->escape('closed: No resolution'), false);
					$switched = true;
				}
				$items[] = $this->_value($anode->id, $this->escape(($anode->open ? 'open: ' : 'closed: ') . stripslashes($anode->title)), false);
			}
		}
		$conditions->status = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			$items
		);
		$conditions->created = $this->_expression(
			array(
				$this->_operator('=', 'on', true),
				$this->_operator('lt', 'before', false),
				$this->_operator('gt', 'after', false)
			),
			'text'
		);
		$conditions->closed = $this->_expression(
			array(
				$this->_operator('=', 'on', true),
				$this->_operator('lt', 'before', false),
				$this->_operator('gt', 'after', false)
			),
			'text'
		);
		$conditions->tag = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			'text'
		);
		$conditions->type = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			array(
				$this->_value('0', 'user submitted', true),
				$this->_value('1', 'automatic', false),
				$this->_value('3', 'tool', false)
			)
		);

		$severities = Utilities::getSeverities($this->config->get('severities'));
		$items = 'text';
		if (isset($severities) && is_array($severities))
		{
			$items = array();
			foreach ($severities as $severity)
			{
				$sel = false;
				if ($severity == 'normal')
				{
					$sel = true;
				}
				$items[] = $this->_value($severity, Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . $severity), $sel);
			}
		}
		$conditions->severity = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			$items
		);

		$categories = Category::all()->rows();
		$items = 'text';
		if (count($categories) > 0)
		{
			$items = array();
			foreach ($categories as $anode)
			{
				$sel = false;
				$items[] = $this->_value($this->escape($anode->alias), $this->escape(stripslashes($anode->title)), $sel);
			}
		}
		$conditions->category = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
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
	private function _expression($operators, $values)
	{
		$obj = new stdClass;
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
	private function _operator($val='=', $label='is', $sel=false)
	{
		$obj = new stdClass;
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
	private function _value($val='=', $label='is', $sel=false)
	{
		return $this->_operator($val, $label, $sel);
	}
}
