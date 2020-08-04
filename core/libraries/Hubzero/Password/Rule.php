<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Password;

use Hubzero\Database\Relational;
use Hubzero\User\User;
use Hubzero\User\Password\History;
use Hubzero\User\Password;

/**
 * Password rule model
 */
class Rule extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'password';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__password_rule';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

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
		'description' => 'notempty',
		'rule'        => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering'
	);

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = (int)$last->get('ordering') + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 * @return  bool     True on success.
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all();

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->ordered()->row();

		// If a row is found, move the item.
		if ($row->get('id'))
		{
			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Insert default content
	 *
	 * @param   integer  $restore  Whether or not to force restoration of default values (even if other values are present)
	 * @return  void
	 */
	public static function defaultContent($restore=0)
	{
		$defaults = array(
			array(
				'class'       => 'alpha',
				'description' => 'Must contain at least 1 letter',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain at least 1 letter',
				'grp'         => 'hub',
				'ordering'    => '1',
				'rule'        => 'minClassCharacters',
				'value'       => '1'
			),
			array(
				'class'       => 'nonalpha',
				'description' => 'Must contain at least 1 number or punctuation mark',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain at least 1 number or punctuation mark',
				'grp'         => 'hub',
				'ordering'    => '2',
				'rule'        => 'minClassCharacters',
				'value'       => '1'
			),
			array(
				'class'       => '',
				'description' => 'Must be at least 8 characters long',
				'enabled'     => '0',
				'failuremsg'  => 'Must be at least 8 characters long',
				'grp'         => 'hub',
				'ordering'    => '3',
				'rule'        => 'minPasswordLength',
				'value'       => '8'
			),
			array(
				'class'       => '',
				'description' => 'Must be no longer than 16 characters',
				'enabled'     => '0',
				'failuremsg'  => 'Must be no longer than 16 characters',
				'grp'         => 'hub',
				'ordering'    => '4',
				'rule'        => 'maxPasswordLength',
				'value'       => '16'
			),
			array(
				'class'       => '',
				'description' => 'Must contain more than 4 unique characters',
				'enabled'     => '0',
				'failuremsg'  => 'Must contain more than 4 unique characters',
				'grp'         => 'hub',
				'ordering'    => '5',
				'rule'        => 'minUniqueCharacters',
				'value'       => '5'
			),
			array(
				'class'       => '',
				'description' => 'Must not contain easily guessed words',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain easily guessed words',
				'grp'         => 'hub',
				'ordering'    => '6',
				'rule'        => 'notBlacklisted',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must not contain your name or parts of your name',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain your name or parts of your name',
				'grp'         => 'hub',
				'ordering'    => '7',
				'rule'        => 'notNameBased',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must not contain your username',
				'enabled'     => '0',
				'failuremsg'  => 'Must not contain your username',
				'grp'         => 'hub',
				'ordering'    => '8',
				'rule'        => 'notUsernameBased',
				'value'       => ''
			),
			array(
				'class'       => '',
				'description' => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
				'enabled'     => '0',
				'failuremsg'  => 'Must be different than the previous password (re-use of the same password will not be allowed for one (1) year)',
				'grp'         => 'hub',
				'ordering'    => '9',
				'rule'        => 'notReused',
				'value'       => '365'
			),
			array(
				'class'       => '',
				'description' => 'Must be changed at least every 120 days',
				'enabled'     => '0',
				'failuremsg'  => 'Must be changed at least every 120 days',
				'grp'         => 'hub',
				'ordering'    => '10',
				'rule'        => 'notStale',
				'value'       => '120'
			)
		);


		if ($restore)
		{
			// Delete current password rules for manual restore
			$rows = self::all()->limit(1000)->rows();

			foreach ($rows as $row)
			{
				if (!$row->destroy())
				{
					return false;
				}
			}
		}

		// Add default rules
		foreach ($defaults as $rule)
		{
			$row = self::blank()->set($rule);

			if (!$row->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Analyze a password
	 *
	 * @param   string  $password
	 * @return  array
	 */
	public static function analyze($password)
	{
		$stats = array();

		$len = strlen($password);

		$stats['count'][0] = $len;
		$stats['uniqueCharacters'] = 0;
		$stats['uniqueClasses']    = 0;

		$classes   = array();
		$histogram = array();

		for ($i = 0; $i < $len; $i++)
		{
			$c = $password[$i];

			$cl = CharacterClass::match($c);

			foreach ($cl as $class)
			{
				if (empty($stats['count'][$class->name]))
				{
					$stats['count'][$class->name] = 1;
					if ($class->flag)
					{
						$stats['uniqueClasses']++;
					}
				}
				else
				{
					$stats['count'][$class->name]++;
				}
			}

			if (empty($histogram[$c]))
			{
				$histogram[$c] = 1;
				$stats['uniqueCharacters']++;
			}
			else
			{
				$histogram[$c]++;
			}
		}

		return $stats;
	}

	/**
	 * Validate a password
	 *
	 * @param   string  $password
	 * @param   array   $rules
	 * @param   mixed   $user
	 * @param   string  $name
	 * @param   bool    $isNew
	 * @return  array
	 */
	public static function verify($password, $rules, $user, $name=null, $isNew=true)
	{
		if (empty($rules))
		{
			return array();
		}

		$fail = array();

		$stats = self::analyze($password);

		foreach ($rules as $rule)
		{
			if ($rule['rule'] == 'minCharacterClasses')
			{
				if ($stats['uniqueClasses'] < $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxCharacterClasses')
			{
				if ($stats['uniqueClasses'] > $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minPasswordLength')
			{
				if ($stats['count'][0] < $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxPasswordLength')
			{
				if ($stats['count'][0] > $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'maxClassCharacters')
			{
				if (empty($rule['class']))
				{
					continue;
				}

				$class = $rule['class'];

				if (empty($stats['count'][$class]))
				{
					$stats['count'][$class] = 0;
				}

				if ($stats['count'][$class] > $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minClassCharacters')
			{
				if (empty($rule['class']))
				{
					continue;
				}

				$class = $rule['class'];

				if (empty($stats['count'][$class]))
				{
					$stats['count'][$class] = 0;
				}

				if ($stats['count'][$class] < $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'minUniqueCharacters')
			{
				if ($stats['uniqueCharacters'] < $rule['value'])
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notBlacklisted')
			{
				if (Blacklist::basedOnBlackList($password))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notNameBased')
			{
				if ($name == null)
				{
					if (is_numeric($user))
					{
						$xuser = User::oneOrNew($user);
					}
					else
					{
						$xuser = User::oneByUsername($user);
					}

					if (!is_object($xuser))
					{
						continue;
					}

					$givenName  = $xuser->get('givenName');
					$middleName = $xuser->get('middleName');
					$surname    = $xuser->get('surname');

					$name = $givenName;

					if (!empty($middleName))
					{
						if (empty($name))
						{
							$name = $middleName;
						}
						else
						{
							$name .= ' ' . $middleName;
						}
					}

					if (!empty($surname))
					{
						if (empty($name))
						{
							$name = $surname;
						}
						else
						{
							$name .= ' ' . $surname;
						}
					}
				}

				if (self::isBasedOnName($password, $name))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notUsernameBased')
			{
				if (is_numeric($user))
				{
					$xuser = User::oneOrNew($user);

					if (!is_object($xuser))
					{
						continue;
					}

					$user = $xuser->get('username');
				}
				if (self::isBasedOnUsername($password, $user))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notReused')
			{
				$date = new \DateTime('now');
				$date->modify("-" . $rule['value'] . "day");

				$phist = History::getInstance($user);
				if (!is_object($phist))
				{
					continue;
				}

				if ($phist->exists($password, $date->format("Y-m-d H:i:s")))
				{
					$fail[] = $rule['failuremsg'];
				}

				$current = Password::getInstance($user);

				// [HUBZERO][#10274] Check the current password too
				if ($isNew && Password::passwordMatches($user, $password, true))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] == 'notRepeat')
			{
				if (Password::passwordMatches($user, $password, true))
				{
					$fail[] = $rule['failuremsg'];
				}
			}
			else if ($rule['rule'] === 'true')
			{
			}
			else if ($rule['rule'] == 'notStale')
			{
			}
			else
			{
				$fail[] = $rule['failuremsg'];
			}
		}

		if (empty($fail))
		{
			$fail = array();
		}

		return $fail;
	}

	/**
	 * Normalize a word
	 *
	 * @param   string  $word
	 * @return  string
	 */
	protected static function normalize($word)
	{
		$nword = '';

		$len = strlen($word);

		for ($i = 0; $i < $len; $i++)
		{
			$o = ord( $word[$i] );

			if ($o < 97)
			{
				// convert to lowercase
				$o += 32;
			}

			if ($o > 122 || $o < 97)
			{
				// skip anything not a lowercase letter
				continue;
			}

			$nword .= chr($o);
		}

		return $nword;
	}

	/**
	 * Check if a word is based on a name
	 *
	 * @param   string  $word
	 * @param   string  $name
	 * @return  bool
	 */
	public static function isBasedOnName($word, $name)
	{
		$word = self::normalize($word);

		if (empty($word))
		{
			return false;
		}

		$names = explode(" ", $name);

		$count = count($names);
		$words = array();

		$fullname = self::normalize($name);

		$words[] = $fullname;
		$words[] = strrev($fullname);

		foreach ($names as $e)
		{
			$e = self::normalize($e);

			if (strlen($e) > 3)
			{
				$words[] = $e;
				$words[] = strrev($e);
			}
		}

		if ($count > 1)
		{
			$e = self::normalize($names[0] . $names[$count-1]);

			$words[] = $e;
			$words[] = strrev($e);
		}

		foreach ($words as $w)
		{
			if (empty($w))
			{
				continue;
			}

			if (strpos($w, $word) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if a word is based on a username
	 *
	 * @param   string  $word
	 * @param   string  $username
	 * @return  bool
	 */
	public static function isBasedOnUsername($word, $username)
	{
		return preg_match("/$username/i", $word);
	}
}
