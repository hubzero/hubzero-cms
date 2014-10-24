<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Members\Models;

use Members\Models\Import\Record;
use Hubzero\Content\Importer;
use stdClass;

include_once(__DIR__ . DS . 'import' . DS . 'record.php');

/**
 * Member importer
 */
class Import extends \Hubzero\Content\Import\Model\Import
{
	/**
	 * Import column to field mapping
	 *
	 * @var  object
	 */
	private $_fields = null;

	/**
	 * Mapping key
	 *
	 * @var  array
	 */
	private $_fieldMap = array(
		// Last Name
		'lname'      => 'surname',
		'lastname'   => 'surname',
		'surname'    => 'surname',
		// First name
		'fname'      => 'givenName',
		'firstname'  => 'givenName',
		'givenname'  => 'givenName',
		// First name
		'mname'      => 'middleName',
		'middlename' => 'middleName',
		'midname'    => 'middleName',
		// Name
		'name'       => 'name',
		'fullname'   => 'name',
		// User ID
		'id'         => 'uidNumber',
		'uid'        => 'uidNumber',
		'userid'     => 'uidNumber',
		'uidnumber'  => 'uidNumber',
		'mid'        => 'uidNumber',
		'midnumber'  => 'uidNumber',
		'memberid'   => 'uidNumber',
		'profileid'  => 'uidNumber',
		// Email
		'mail'       => 'email',
		'email'      => 'email',
		'electronicmail' => 'email',
		'emailaddress' => 'email',
		// Username
		'username'   => 'username',
		'uname'      => 'username',
		'login'      => 'username',
		'userlogin'  => 'username',
		// Phone
		'phone'      => 'phone',
		'cell'       => 'phone',
		'cellphone'  => 'phone',
		'telephone'  => 'phone',
		'workphone'  => 'phone',
		// Gender
		'gender'     => 'gender',
		'sex'        => 'gender',
		// ORCID
		'orcid'      => 'orcid',
		// ORCID
		'public'        => 'public',
		'publicprofile' => 'public',
		'access'        => 'public',
		'visibility'    => 'public',
		// Organization
		'org'          => 'organization',
		'organization' => 'organization',
		'organisation' => 'organization',
		'company'      => 'organization',
		// Org type
		'orgtype'          => 'orgtype',
		'organizationtype' => 'orgtype',
		'organisationtype' => 'orgtype',
		'companytype'      => 'orgtype',
		'employertype'     => 'orgtype',
		'employmenttype'   => 'orgtype',
		// URL
		'url'        => 'url',
		'website'    => 'url',
		'webpage'    => 'url',
		'site'       => 'url',
		'homepage'   => 'url',
		// Tags
		'tags'      => 'interests',
		'interests' => 'interests',
		'tag'       => 'interests',
		'interest'  => 'interests',
		'likes'     => 'interests',
		'keyword'   => 'interests',
		'keywords'  => 'interests',
		// Bio
		'bio'        => 'bio',
		'biography'  => 'bio',
		'about'      => 'bio',
		// Race
		'race'       => 'race',
		'racial'     => 'race',
		'ethnicity'  => 'race',
		'ethnic'     => 'race',
		// Disability
		'disability' => 'disability',
		'disabled'   => 'disability',
		'handicap'   => 'disability',
	);

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Accepts integer, object, or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		if ($fields = $this->get('fields'))
		{
			$this->_fields = json_decode($fields, true);
		}
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param   boolean  $check  Perform data validation check?
	 * @return  boolean  False if error, True on success
	 */
	public function store($check=true)
	{
		$this->set('type', 'members');

		/*if ($this->_fields)
		{
			$this->set('field_map', json_encode($this->_fields));
		}*/

		return parent::store($check);
	}

	/**
	 * Get the generated record from the raw data
	 *
	 * @param   mixes   $raw      Raw data
	 * @param   array   $options  Import options
	 * @param   string  $mode     Operation mode (update|patch)
	 * @return  object
	 */
	public function getRecord($raw, $options = array(), $mode = 'UPDATE')
	{
		$record = new stdClass;

		if (is_array($raw))
		{
			foreach ($raw as $key => $val)
			{
				$field = $this->fields($key);

				$record->$field = $val;
			}
		}
		else if (is_object($raw))
		{
			foreach (get_object_vars($raw) as $key => $val)
			{
				if (!$field = $this->fields($key))
				{
					continue;
				}

				$record->$field = $val;
			}
		}

		return new Record($record, $options, $mode);
	}

	/**
	 * Accepts a column name (from import file) and tries
	 * to find an associated record field
	 *
	 * @param   string  $name
	 * @return  string
	 */
	public function fields($name=null)
	{
		if (!$this->_fields)
		{
			$this->_fields = array();
			if ($this->get('file'))
			{
				$headers = with(new Importer())->headers($this);
				$this->_fields = $this->autoDetectFields($headers);
			}
		}

		if ($name)
		{
			$name  = $this->normalize($name);
			$field = $name;

			if (isset($this->_fields[$name]))
			{
				$field = $this->_fields[$name]['field'];
			}

			return $field;
		}

		return $this->_fields;
	}

	/**
	 * Strip spaces, punctuation, and make lower case
	 *
	 * @param   string  $txt
	 * @return  string
	 */
	private function normalize($txt)
	{
		return strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $txt));
	}

	/**
	 * Map headers to fields
	 *
	 * @param   array   $headers  List of columns from import file
	 * @return  object
	 */
	public function autoDetectFields(array $headers)
	{
		$mapping = array();

		foreach ($headers as $header)
		{
			$norm = $this->normalize($header);

			$map = array(
				'label' => $header,
				'name'  => $norm,
				'field' => ''
			);

			if (isset($this->_fieldMap[$norm]))
			{
				$map['field'] = $this->_fieldMap[$norm];
			}

			$mapping[$norm] = $map;
		}

		return $mapping;
	}
}