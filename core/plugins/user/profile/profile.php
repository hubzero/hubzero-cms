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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

/**
 * An example custom profile plugin.
 */
class plgUserProfile extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * @param   string   $context  The context for the data
	 * @param   integer  $data     The user id
	 * @param   object
	 * @return  boolean
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			return true;
		}

		if (is_object($data))
		{
			$userId = isset($data->id) ? $data->id : 0;

			if (!isset($data->profile) and $userId > 0)
			{
				// Load the profile data from the database.
				$db = App::get('db');
				$db->setQuery(
					'SELECT profile_key, profile_value FROM `#__user_profiles`' .
					' WHERE user_id = '.(int) $userId." AND profile_key LIKE 'profile.%'" .
					' ORDER BY ordering'
				);
				$results = $db->loadRowList();

				// Check for a database error.
				if ($db->getErrorNum())
				{
					$this->_subject->setError($db->getErrorMsg());
					return false;
				}

				// Merge the profile data.
				$data->profile = array();

				foreach ($results as $v)
				{
					$k = str_replace('profile.', '', $v[0]);
					$data->profile[$k] = json_decode($v[1], true);
					if ($data->profile[$k] === null)
					{
						$data->profile[$k] = $v[1];
					}
				}
			}

			if (!Html::has('users.url'))
			{
				Html::register('users.url', array(__CLASS__, 'url'));
			}
			if (!Html::has('users.calendar'))
			{
				Html::register('users.calendar', array(__CLASS__, 'calendar'));
			}
			if (!Html::has('users.tos'))
			{
				Html::register('users.tos', array(__CLASS__, 'tos'));
			}
		}

		return true;
	}

	/**
	 * Check URL
	 *
	 * @param   mixed   $value
	 * @return  string
	 */
	public static function url($value)
	{
		if (empty($value))
		{
			return Html::users('value', $value);
		}
		else
		{
			$value = htmlspecialchars($value);

			if (substr($value, 0, 4) == "http")
			{
				return '<a href="' . $value . '">' . $value . '</a>';
			}
			else
			{
				return '<a href="http://' . $value . '">' . $value . '</a>';
			}
		}
	}

	/**
	 * Check calendar
	 *
	 * @param   mixed   $value
	 * @return  string
	 */
	public static function calendar($value)
	{
		if (empty($value))
		{
			return Html::users('value', $value);
		}
		else
		{
			return Date::of($value)->format(Lang::txt('DATE_FORMAT_LC1'));
		}
	}

	/**
	 * Check TOS
	 *
	 * @param   mixed   $value
	 * @return  string
	 */
	public static function tos($value)
	{
		if ($value)
		{
			return Lang::txt('JYES');
		}
		else
		{
			return Lang::txt('JNO');
		}
	}

	/**
	 * Prepare form
	 *
	 * @param   object   $form  The form to be altered.
	 * @param   array    $data  The associated data for the form.
	 * @return  boolean
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof Hubzero\Form\Form))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		// Add the registration fields to the form.
		Hubzero\Form\Form::addFieldPath(__DIR__ . '/fields');
		Hubzero\Form\Form::addFormPath(__DIR__ . '/profiles');

		$form->loadFile('profile', false);

		$fields = array(
			'address1',
			'address2',
			'city',
			'region',
			'country',
			'postal_code',
			'phone',
			'website',
			'favoritebook',
			'aboutme',
			'dob',
			'tos',
		);

		$tosarticle = $this->params->get('register_tos_article');
		$tosenabled = $this->params->get('register-require_tos', 0);

		// We need to be in the registration form, field needs to be enabled and we need an article ID
		if ($name != 'com_users.registration' || !$tosenabled || !$tosarticle)
		{
			// We only want the TOS in the registration form
			$form->removeField('tos', 'profile');
		}
		else
		{
			// Push the TOS article ID into the TOS field.
			$form->setFieldAttribute('tos', 'article', $tosarticle, 'profile');
		}

		foreach ($fields as $field)
		{
			// Case using the users manager in admin
			if ($name == 'com_users.user')
			{
				// Remove the field if it is disabled in registration and profile
				if ($this->params->get('register-require_' . $field, 1) == 0
					&& $this->params->get('profile-require_' . $field, 1) == 0)
				{
					$form->removeField($field, 'profile');
				}
			}
			// Case registration
			elseif ($name == 'com_users.registration')
			{
				// Toggle whether the field is required.
				if ($this->params->get('register-require_' . $field, 1) > 0)
				{
					$form->setFieldAttribute($field, 'required', ($this->params->get('register-require_' . $field) == 2) ? 'required' : '', 'profile');
				}
				else
				{
					$form->removeField($field, 'profile');
				}
			}
			// Case profile in site or admin
			elseif ($name == 'com_users.profile' || $name == 'com_admin.profile')
			{
				// Toggle whether the field is required.
				if ($this->params->get('profile-require_' . $field, 1) > 0)
				{
					$form->setFieldAttribute($field, 'required', ($this->params->get('profile-require_' . $field) == 2) ? 'required' : '', 'profile');
				}
				else
				{
					$form->removeField($field, 'profile');
				}
			}
		}

		return true;
	}

	/**
	 * Remove all user profile information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $data
	 * @param   boolean  $isNew
	 * @param   boolean  $result
	 * @param   string   $error
	 * @return  boolean
	 */
	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		$userId = \Hubzero\Utility\Arr::getValue($data, 'id', 0, 'int');

		if ($userId && $result && isset($data['profile']) && (count($data['profile'])))
		{
			try
			{
				//Sanitize the date
				if (!empty($data['profile']['dob']))
				{
					$date = new \Hubzero\Utility\Date($data['profile']['dob']);
					$data['profile']['dob'] = $date->format('Y-m-d');
				}

				$db = App::get('db');
				$db->setQuery(
					"DELETE FROM `#__user_profiles` WHERE user_id = " . $userId . " AND profile_key LIKE 'profile.%'"
				);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}

				$tuples = array();
				$order  = 1;

				foreach ($data['profile'] as $k => $v)
				{
					$tuples[] = '(' . $userId . ', ' . $db->quote('profile.' . $k) . ', ' . $db->quote(json_encode($v)) . ', ' . $order++ . ')';
				}

				$db->setQuery('INSERT INTO `#__user_profiles` VALUES ' . implode(', ', $tuples));

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove all user profile information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 * @return  boolean
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$userId = \Hubzero\Utility\Arr::getValue($user, 'id', 0, 'int');

		if ($userId)
		{
			try
			{
				$db = App::get('db');
				$db->setQuery(
					"DELETE FROM `#__user_profiles` WHERE user_id = " . $userId . " AND profile_key LIKE 'profile.%'"
				);

				if (!$db->query())
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}

		return true;
	}
}
