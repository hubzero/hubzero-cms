<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Careerplans\Models;

use Hubzero\Database\Relational;
use Components\Careerplans\Models\Goal\Strategy;
use Component;
use Lang;
use User;
use Date;

require_once __DIR__ . DS . 'answer.php';
require_once __DIR__ . DS . 'goal.php';

/**
 * Model class for a career plan
 */
class Careerplan extends Relational
{
	/**
	 * Accepted state
	 *
	 * @var integer
	 */
	const STATE_ACCEPTED = 3;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'user_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Is this in the submitted state?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Mark the record as trashed
	 *
	 * @return  boolean
	 */
	public function markAsTrashed()
	{
		$this->set('state', self::STATE_DELETED);

		return $this->save();
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get the modifier of this entry
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Get a list of answers
	 *
	 * @return  object
	 */
	public function answers()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Answer', 'careerplan_id');
	}

	/**
	 * Get a list of answers
	 *
	 * @return  object
	 */
	public function comments()
	{
		$query = \Hubzero\Activity\Log::all()
			->whereEquals('scope', 'careerplan' . $this->get('id'));

		return $query;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('created');

		if (strtolower($as) == 'date')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if (strtolower($as) == 'time')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			$dt = Date::of($dt)->toLocal($as);
		}

		return $dt;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function modified($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('modified');

		if (strtolower($as) == 'date')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if (strtolower($as) == 'time')
		{
			$dt = Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			$dt = Date::of($dt)->toLocal($as);
		}

		return $dt;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove comments
		foreach ($this->answers()->rows() as $answer)
		{
			if (!$answer->destroy())
			{
				$this->addError($answer->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Save profile data
	 *
	 * @param   array   $answers
	 * @return  boolean
	 */
	public function saveAnswers($answers, $field_ids = array())
	{
		$answers = (array)$answers;

		// Fire the onUserBeforeSaveProfile event
		$application = $this->toArray();
		$result = Event::trigger('careerplans.onCareerplanBeforeSave', array($application, $answers));

		if (in_array(false, $result, true))
		{
			// Plugin will have to raise its own error or throw an exception.
			return false;
		}

		$keep = array();

		if (!empty($field_ids))
		{
			$prev = $this->answers()
				->whereIn('field_id', $field_ids)
				->ordered()
				->rows();
		}
		else
		{
			$prev = $this->answers;
		}

		foreach ($prev as $answer)
		{
			// Remove any entries not in the incoming data
			if (!isset($answers[$answer->field->get('name')]))
			{
				if (!$answer->destroy())
				{
					$this->addError($answer->getError());
					return false;
				}

				continue;
			}

			// Push to the list of fields we want to keep
			if (!isset($keep[$answer->field->get('name')]))
			{
				$keep[$answer->field->get('name')] = $answer;
			}
			else
			{
				// Multi-value field
				$values = $keep[$answer->field->get('name')];
				$values = is_array($values) ? $values : array($values->get('value') => $values);
				$values[$answer->get('value')] = $answer;

				$keep[$answer->field->get('name')] = $values;
			}
		}

		$i = 1;

		foreach ($answers as $key => $data)
		{
			$field = Field::oneByName($key);

			if ($field->get('type') == 'goals')
			{
				$answer = null;

				if (isset($keep[$key]))
				{
					$answer = $keep[$key];
				}

				if (!($answer instanceof Answer))
				{
					$answer = Answer::blank();
				}

				// First, we need an answer object representing
				$answer->set(array(
					'careerplan_id' => $this->get('id'),
					'field_id' => $field->get('id'),
					'value'    => '',
					'ordering' => $i
				));

				if (!$answer->save())
				{
					$this->addError($answer->getError());
					continue;
				}

				$oldgoals = Goal::all()
					->whereEquals('careerplan_id', $answer->get('careerplan_id'))
					->whereEquals('field_id', $field->get('id'))
					->rows();
				$gls = array();

				foreach ($data as $tmpgoal)
				{
					if (!isset($tmpgoal['id']))
					{
						$tmpgoal['id'] = 0;
					}
					$goal = Goal::oneOrNew($tmpgoal['id']);
					$goal->set(array(
						'careerplan_id' => $this->get('id'),
						'field_id' => $field->get('id'),
						'goal' => $tmpgoal['goal']
					));
					if (isset($tmpgoal['skills_needed']))
					{
						$goal->set('skills_needed', $tmpgoal['skills_needed']);
					}
					if (isset($tmpgoal['skill']) && is_array($tmpgoal['skill']))
					{
						$goal->set('skills_level', json_encode($tmpgoal['skill']));
					}
					if (!$goal->save())
					{
						$this->addError($goal->getError());
						continue;
					}

					$gls[] = $goal->get('id');

					$oldstrats = $goal->strategies;
					$strts = array();

					if (isset($tmpgoal['strategy']))
					{
						foreach ($tmpgoal['strategy'] as $strat)
						{
							if (!isset($strat['id']))
							{
								$strat['id'] = 0;
							}
							$strategy = Strategy::oneOrNew($strat['id']);
							$strategy->set(array(
								'goal_id' => $goal->get('id'),
								'content' => $strat['content'],
								'badge'   => (isset($strat['badge']) && $strat['badge'] ? 1 : 0)
							));
							if (isset($strat['completed']) && $strat['completed'])
							{
								$strategy->set('completed', $strat['completed']);
							}
							if (!$strategy->save())
							{
								$this->addError($strategy->getError());
								continue;
							}
							$strts[] = $strategy->get('id');
						}
					}

					// Remove any values not already found
					foreach ($oldstrats as $oldstrat)
					{
						if (!in_array($oldstrat->get('id'), $strts))
						{
							if (!$oldstrat->destroy())
							{
								$this->addError($oldstrat->getError());
								continue;
							}
						}
					}
				}

				foreach ($oldgoals as $oldgoal)
				{
					if (!in_array($oldgoal->get('id'), $gls))
					{
						if (!$oldgoal->destroy())
						{
							$this->addError($oldgoal->getError());
							continue;
						}
					}
				}

				continue;
			}

			/*if (!$field->saveData($data, $this->get('id'), $i))
			{
				$this->addError($field->getError());
				return false;
			}
			*/

			// Is it a multi-value field?
			if (is_array($data))
			{
				if (empty($data))
				{
					continue;
				}

				foreach ($data as $val)
				{
					if (is_array($val) || is_object($val))
					{
						$val = json_encode($val);
					}

					$val = trim($val);

					// Skip empty values
					if (!$val)
					{
						continue;
					}

					$field = null;

					// Try to find an existing entry
					if (isset($keep[$key]))
					{
						if (is_array($keep[$key]))
						{
							if (isset($keep[$key][$val]))
							{
								$field = $keep[$key][$val];
								unset($keep[$key][$val]);
							}
						}
						else
						{
							$field = $keep[$key];
							unset($keep[$key]);
						}
					}

					if (!($field instanceof Answer))
					{
						$field = Answer::blank();
					}

					$field->set(array(
						'careerplan_id' => $this->get('id'),
						'field_id' => Field::oneByName($key)->get('id'),
						'value'    => $val,
						'ordering' => $i
					));

					if (!$field->save())
					{
						$this->addError($field->getError());
						return false;
					}
				}

				// Remove any values not already found
				if (isset($keep[$key]) && is_array($keep[$key]))
				{
					foreach ($keep[$key] as $f)
					{
						if (!$f->destroy())
						{
							$this->addError($f->getError());
							return false;
						}
					}
				}
			}
			else
			{
				$val = trim($data);

				$field = null;

				if (isset($keep[$key]))
				{
					$field = $keep[$key];
				}

				if (!($field instanceof Answer))
				{
					$field = Answer::blank();
				}

				// If value is empty
				if (!$val)
				{
					// If an existing field, remove it
					if ($field->get('id'))
					{
						if (!$field->destroy())
						{
							$this->addError($field->getError());
							return false;
						}
					}

					// Move along. Nothing to see here.
					continue;
				}

				$field->set(array(
					'careerplan_id' => $this->get('id'),
					'field_id' => Field::oneByName($key)->get('id'),
					'value'    => $val,
					'ordering' => $i
				));

				if (!$field->save())
				{
					$this->addError($field->getError());
					return false;
				}
			}

			$i++;
		}

		// Fire the onApplicationAfterSave event
		Event::trigger('careerplans.onCareerplanAfterSave', array($application, $answers));

		return true;
	}

	/**
	 * Return a Database\Row object containing fieldsets and
	 * fields with values set from the application's answers
	 *
	 * @return  object
	 */
	public function summary()
	{
		$entries = $this->answers();

		$p = $entries->getTableName();
		$f = Field::blank()->getTableName();
		$o = Option::blank()->getTableName();

		$answers = $entries
			->select($p . '.*,' . $o . '.label')
			->join($f, $f . '.id', $p . '.field_id', 'inner')
			->joinRaw($o, $o . '.field_id=' . $f . '.id AND ' . $o . '.value=' . $p . '.value', 'left')
			->ordered()
			->rows();

		$fieldsets = Fieldset::all()
			->ordered()
			->rows();

		foreach ($fieldsets as $fieldset)
		{
			$ffields = $fieldset->fields()
				->including(['options', function ($option){
					$option
						->select('*');
				}])
				->ordered()
				->rows();

			$fields = array();
			foreach ($answers as $answer)
			{
				if (isset($fields[$answer->get('field_id')]))
				{
					$values = $fields[$answer->get('field_id')]->get('value');
					if (!is_array($values))
					{
						$values = array($fields[$answer->get('field_id')]->get('label', $values));
					}
					$values[] = $answer->get('label', $answer->get('value'));

					$fields[$answer->get('field_id')]->set('value', $values);
				}
				else
				{
					$fields[$answer->get('field_id')] = $answer;
				}
			}

			$data = array();

			foreach ($ffields as $field)
			{
				if ($field->get('type') == 'paragraph')
				{
					continue;
				}
				if (!isset($fields[$field->get('id')]))
				{
					continue;
				}
				$answer = $fields[$field->get('id')];

				$value = $answer->get('value');
				if (!is_array($value))
				{
					$value = $answer->get('label', $value);
				}
				$value = $value ?: $field->get('default_value');

				$field->set('value', $value);

				$data[] = $field;
			}

			$fieldset->set('fields', $data);
		}

		return $fieldsets;
	}

	/**
	 * Return a record by user_id
	 *
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByUser($user_id)
	{
		$row = self::all()
			->whereEquals('user_id', (int)$user_id)
			->where('state', '!=', self::STATE_DELETED)
			->limit(1)
			->row();

		if (!$row)
		{
			$row = self::blank();
			$row->set('user_id', (int)$user_id);
			$row->set('created_by', (int)$user_id);
		}

		return $row;
	}

	/**
	 * Helper method to collect multi-value fields
	 *
	 * @param   mixed
	 * @return  array
	 */
	public static function collect($data)
	{
		$arr = array();

		foreach ($data as $answer)
		{
			$name = $answer->field->get('name', 'field' . $answer->get('field_id'));

			if (!isset($arr[$name]))
			{
				$arr[$name] = $answer->get('value');
			}
			else
			{
				$values = $arr[$name];
				if (!is_array($values))
				{
					$values = array($values);
				}
				$values[] = $answer->get('value');

				$arr[$name] = $values;
			}
		}

		return $arr;
	}
}
