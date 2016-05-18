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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for profile
 */
class plgMembersProfile extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'profile' => Lang::txt('PLG_MEMBERS_PROFILE'),
			'icon'    => 'f007'
		);
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'models' . DS . 'profile' . DS . 'field.php';

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Build the final HTML
		if ($returnhtml)
		{
			$this->user   = $user;
			$this->member = $member;
			$this->option = $option;
			$this->areas  = $areas;

			//get task
			$this->task = Request::getVar('action', 'view');
			switch ($this->task)
			{
				case 'addaddress':    $arr['html'] = $this->addAddress();    break;
				case 'editaddress':   $arr['html'] = $this->editAddress();   break;
				case 'saveaddress':   $arr['html'] = $this->saveAddress();   break;
				case 'deleteaddress': $arr['html'] = $this->deleteAddress(); break;
				case 'view':
				default:              $arr['html'] = $this->display();
			}
		}

		return $arr;
	}

	/**
	 * View the profile page
	 *
	 * @return  string
	 */
	private function display()
	{
		//get member params
		$rparams = new \Hubzero\Config\Registry($this->member->get('params'));

		//get profile plugin's params
		$params = $this->params;
		$params->merge($rparams);

		$xreg = null;

		if (App::get('session')->get('registration.incomplete'))
		{
			$xreg = new \Components\Members\Models\Registration();
			$xreg->loadProfile($this->member);

			$check = $xreg->check('update');

			// Validate profile data
			// @TODO  Move this to central validation model (e.g., registraiton)?
			$fields = Components\Members\Models\Profile\Field::all()
				->including(['options', function ($option){
					$option
						->select('*');
				}])
				->where('action_create', '!=', Components\Members\Models\Profile\Field::STATE_HIDDEN)
				->ordered()
				->rows();

			// Compile profile data
			$profile = array();
			foreach ($fields as $field)
			{
				$profile[$field->get('name')] = $this->member->get($field->get('name'));
			}

			// Validate profile fields
			$form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
			$form->load(Components\Members\Models\Profile\Field::toXml($fields, 'create'));
			$form->bind(new Hubzero\Config\Registry($profile));

			if (!$form->validate($profile))
			{
				$check = false;

				foreach ($form->getErrors() as $key => $error)
				{
					if ($error instanceof Hubzero\Form\Exception\MissingData)
					{
						$xreg->_missing[$key] = (string)$error;
					}

					$xreg->_invalid[$key] = (string)$error;
				}
			}

			// If no errors, redirect to where they were going
			if ($check)
			{
				App::get('session')->set('registration.incomplete', 0);
				App::redirect($_SERVER['REQUEST_URI']);
			}
		}

		$fields = Components\Members\Models\Profile\Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->where('action_edit', '!=', Components\Members\Models\Profile\Field::STATE_HIDDEN)
			->ordered()
			->rows();

		$view = $this->view('default', 'index')
			->set('params', $params)
			->set('option', 'com_members')
			->set('profile', $this->member)
			->set('fields', $fields)
			->set('completeness', $this->getProfileCompleteness($fields, $this->member))
			->set('registration_update', $xreg);

		return $view
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   array    $fields   Fields filled in
	 * @param   object   $profile  Profile
	 * @return  integer
	 */
	public function getProfileCompleteness($fields, $profile)
	{
		//default vars
		$num_fields = 0;
		$num_filled_fields = 0;

		//loop through each field to see if we want to count it
		foreach ($fields as $field)
		{
			// if the field is anything button hidden we want to count it
			if ($field->get('type') != 'hidden')
			{
				//add to the number of fields count
				$num_fields++;

				//check to see if we have it filled in
				$value = $profile->get($field->get('name'));
				$type = gettype($value);

				if (($type == 'array' && !empty($value)) || ($type == 'string' && $value != ''))
				{
					$num_filled_fields++;
				}
			}
		}

		// return percentage
		return number_format(($num_filled_fields/$num_fields) * 100, 0);
	}

	/**
	 * Method to add a user address
	 *
	 * @return  void
	 */
	public function addAddress()
	{
		return $this->editAddress();
	}

	/**
	 * Method to edit a user address
	 *
	 * @return  void
	 */
	public function editAddress()
	{
		$this->view = $this->view('edit', 'address');

		//get request vars
		$this->view->addressId = Request::getInt('addressid', 0);

		//get member addresses
		$database = App::get('db');
		$this->view->address = new \Components\Members\Tables\Address($database);
		$this->view->address->load($this->view->addressId);

		//are we passing back the vars from save
		if (isset($this->address))
		{
			$this->view->address = $this->address;
		}

		//set vars for view
		$this->view->member = $this->member;

		//set errors and display
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		return $this->view->loadTemplate();
	}

	/**
	 * Method to save a user address
	 *
	 * @return  void
	 */
	public function saveAddress()
	{
		//get request vars
		$address = Request::getVar('address', array());

		//set up objects
		$database = App::get('db');
		$membersAddress = new \Components\Members\Tables\Address($database);

		//create object from vars
		$addressObj = new stdClass;
		$addressObj->id               = $address['id'];
		$addressObj->uidNumber        = User::get('id');
		$addressObj->addressTo        = $address['addressTo'];
		$addressObj->address1         = $address['address1'];
		$addressObj->address2         = $address['address2'];
		$addressObj->addressCity      = $address['addressCity'];
		$addressObj->addressRegion    = $address['addressRegion'];
		$addressObj->addressPostal    = $address['addressPostal'];
		$addressObj->addressCountry   = $address['addressCountry'];
		$addressObj->addressLatitude  = $address['addressLatitude'];
		$addressObj->addressLongitude = $address['addressLongitude'];

		//attempt to save
		if (!$membersAddress->save($addressObj))
		{
			$this->address = $addressObj;
			$this->setError($membersAddress->getError());
			return $this->editAddress();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'),
			Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_SAVED'),
			'passed'
		);
	}

	/**
	 * Method to delete a user address
	 *
	 * @return  void
	 */
	public function deleteAddress()
	{
		//get request vars
		$addressId = Request::getInt('addressid', 0);

		//set up objects
		$database = App::get('db');
		$membersAddress = new \Components\Members\Tables\Address($database);

		//load address object
		$membersAddress->load($addressId);

		//make sure we have a valid member address object
		if (!is_object($membersAddress) || !$membersAddress->id)
		{
			return $this->view();
		}

		//make sure user can delete this address
		if ($membersAddress->uidNumber != User::get('id'))
		{
			$this->setError(Lang::txt('PLG_MEMBERS_PROFILE_ERROR_PERMISSION_DENIED'));
			return $this->view();
		}

		//make sure we dont have another stimulation
		if (!$membersAddress->canDelete())
		{
			$this->setError($membersAddress->getError());
			return $this->view();
		}

		//attempt to delete address
		if (!$membersAddress->delete($addressId))
		{
			$this->setErrror($membersAddress->getError());
			return $this->view();
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=profile'),
			Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_REMOVED'),
			'passed'
		);
	}
}
