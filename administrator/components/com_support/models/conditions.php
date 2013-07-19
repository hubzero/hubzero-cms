<?php
/**
 * @package	 hubzero-cms
 * @author	  Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license	 http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* 
 * Support model class for query conditions
 */
class SupportModelConditions extends JObject
{
	/**
	 * Callback for escaping.
	 *
	 * @var string
	 */
	private $_escape = 'htmlspecialchars';

	 /**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 */
	private $_charset = 'UTF-8';

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	public $database;

	/**
	 * JParameter
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
		$this->database = JFactory::getDBO();
		$this->config = JComponentHelper::getParams('com_support');
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
				$this->_operator('!=', 'is', false),
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
		ximport('Hubzero_User_Helper');
		$items = array(
			$this->_value('*', JText::_('(any of mine)'), true)
		);
		$juser = JFactory::getUser();
		if (($xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'members'))) 
		{
			foreach ($xgroups as $xgroup)
			{
				$items[] = $this->_value($xgroup->cn, ' &nbsp; ' . stripslashes($this->escape($xgroup->description)), false);
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
		$conditions->status = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			array(
				$this->_value('0', 'new', false),
				$this->_value('1', 'open', true),
				$this->_value('2', 'waiting', false)
			)
		);
		$conditions->created = $this->_expression(
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

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
		$severities = SupportUtilities::getSeverities($this->config->get('severities'));
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
				$items[] = $this->_value($severity, $severity, $sel);
			}
		}
		$conditions->severity = $this->_expression(
			array(
				$this->_operator('=', 'is', true),
				$this->_operator('!=', 'is not', false)
			),
			$items
		);

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'resolution.php');
		$sr = new SupportResolution($this->database);
		$resolutions = $sr->getResolutions();
		$items = 'text';
		if (isset($resolutions) && is_array($resolutions)) 
		{
			$items = array();
			foreach ($resolutions as $anode) 
			{
				$sel = false;
				if ($anode->alias == 'fixed')
				{
					$sel = true;
				}
				$items[] = $this->_value($this->escape($anode->alias), $this->escape(stripslashes($anode->title)), $sel);
			}
		}
		$conditions->resolved = $this->_expression(
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

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) 
		{
			return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		}

		return call_user_func($this->_escape, $var);
	}
}
