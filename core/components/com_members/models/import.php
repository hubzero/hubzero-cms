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

namespace Components\Members\Models;

use Components\Members\Models\Import\Record;
use Components\Members\Models\Profile\Field;
use Hubzero\Content\Import\Model\Import as Base;
use Hubzero\Content\Importer;
use Hubzero\Utility\Sanitize;
use stdClass;

include_once(__DIR__ . DS . 'import' . DS . 'record.php');

/**
 * Member importer
 */
class Import extends Base
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
		'surname' => array(
			'lname',
			'lastname',
			'surname',
		),
		'givenName' => array(
			'fname',
			'firstname',
			'givenname',
		),
		'middleName' => array(
			'mname',
			'middlename',
			'midname',
		),
		'name' => array(
			'name',
			'fullname',
		),
		'id' => array(
			'id',
			'uid',
			'userid',
			'uidnumber',
			'mid',
			'midnumber',
			'memberid',
			'profileid',
		),
		'email' => array(
			'mail',
			'email',
			'electronicmail',
			'emailaddress',
		),
		'username' => array(
			'username',
			'uname',
			'login',
			'userlogin',
		),
		'access' => array(
			'public',
			'publicprofile',
			'access',
			'visibility',
		),
		'block' => array(
			'block',
			'blocked',
		),
		'approved' => array(
			'approve',
			'approved',
		),
		'interests' => array(
			'tags',
			'interests',
			'tag',
			'interest',
			'likes',
			'keyword',
			'keywords',
		),
		'note' => array(
			'note',
			'notes',
			'adminnote',
			'adminnotes',
			'accountnote',
			'accountnotes',
			'administratornote',
			'administratornotes',
			'comment',
			'comments',
			'admincomment',
			'admincomments',
			'accountcomment',
			'accountcomments',
			'administratorcomment',
			'administratorcomment',
		),
		'jobsAllowed' => array(
			'jobsallowed',
			'jobquota',
			'joballowance',
			'jobs',
			'quota',
		),
		'homeDirectory' => array(
			'home',
			'homedirectory',
			'homepath',
			'homefolder',
			'homedir',
		),
		'loginShell' => array(
			'loginshell'
		),
		'ftpShell' => array(
			'ftpshell'
		),
		'usageAgreement' => array(
			'usageagreement',
			'termsofservice',
			'tos',
			'agreement',
		),
		'password' => array(
			'userpassword',
			'password',
			'pass',
			'passcode',
			'passwd',
			'passwrd',
			'memberpassword',
		),
		'sendEmail' => array(
			'mailpreferenceoption',
			'mailpreference',
			'recievemail',
			'recieveemail',
			'getmail',
			'getemail',
			'mailupdates',
			'emailupdates',
			'sendemail',
		),
		'activation' => array(
			'emailconfirmed',
			'confirmedemail',
			'validemail',
			'emailconfirm',
			'activation',
		),
		'access' => array(
			'access',
			'permissions',
			'public',
		),
		/* @deprecated
		'countryresident' => array(
			'countryresident',
			'resident',
			'residence',
			'residency',
			'country',
		),
		'countryorigin' => array(
			'countryorigin',
			'origin',
			'birthplace',
			'birthcountry',
			'citizenship',
			'citizen',
		),
		'bio' => array(
			'bio',
			'biography',
			'about',
		),
		'race' => array(
			'race',
			'racial',
			'ethnicity',
			'ethnic',
		),
		'hispanic' => array(
			'hispanic',
			'latin',
			'latino',
		),
		'nativeTribe' => array(
			'nativetribe',
			'tribe',
			'nativeamericantribe',
			'indiantribe',
		),
		'disability' => array(
			'disability',
			'disabled',
			'handicap',
		),
		'organization' => array(
			'org',
			'organization',
			'organisation',
			'company',
		),
		'orgtype' => array(
			'orgtype',
			'organizationtype',
			'organisationtype',
			'companytype',
			'employertype',
			'employmenttype',
		),
		'phone' => array(
			'phone',
			'cell',
			'cellphone',
			'telephone',
			'workphone',
		),
		'gender' => array(
			'gender',
			'sex',
		),
		'orcid' => array(
			'orcid',
		),
		'url' => array(
			'url',
			'website',
			'webpage',
			'site',
			'homepage',
		),
		'reason' => array(
			'reason',
			'reasonforaccount',
			'reasonforjoining',
			'reasonformembership',
			'whyjoin',
			'whybecomeamember',
		),
		'picture' => array(
			'picture',
			'pic',
			'image',
			'img',
			'photo',
		),
		'vip' => array(
			'vip',
			'veryimportantperson',
			'veryimportant',
		),*/
		'locked' => array(
			'locked',
			'lock',
			'padlock',
			'restrict',
			'restricted',
		),
		'registerIP' => array(
			'regip',
			'registrationip',
			'registeredip',
			'registerip',
		),
		'registerHost' => array(
			'reghost',
			'registrationhost',
			'registeredhost',
			'registerhost',
		),
		'registerDate' => array(
			'registerdate',
			'registrationdate',
			'registereddate',
			'registered',
			'signedup',
			'created',
			'createddate',
			'createdate',
		),
		'modifiedDate' => array(
			'modifieddate',
			'modified',
			'changed',
			'changedate',
			'changeddate',
			'updated',
			'updatedate',
			'updateddate',
			'lastmodified',
			'lastchanged',
			'lastupdate',
			'lastupdated',
		),
		'groups' => array(
			'group',
			'groups',
			'gid',
			'gids',
			'gidNumber',
			'gidNumbers',
			'cn',
			'group_alias',
			'group_aliases',
			'group_cn',
			'group_membership',
		),
		'projects' => array(
			'project',
			'projects',
			'pid',
			'pids',
			'pidNumber',
			'pidNumbers',
			'project_alias',
			'project_aliases',
			'project_membership',
		),
	);

	/**
	 * Store changes to this database entry
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$this->set('type', 'members');

		return parent::save();
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
		$record->_unused = array();

		if (is_array($raw))
		{
			foreach ($raw as $key => $val)
			{
				$val = trim($val);
				$val = Sanitize::cleanMsChar($val);

				if (!$field = $this->fields($key))
				{
					$record->_unused[$key] = $val;
					continue;
				}

				$record->$field = $val;
			}
		}
		else if (is_object($raw))
		{
			foreach (get_object_vars($raw) as $key => $val)
			{
				$val = trim($val);
				$val = Sanitize::cleanMsChar($val);

				if (!$field = $this->fields($key))
				{
					$record->_unused[$key] = $val;
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

			if ($fields = $this->get('fields'))
			{
				$this->_fields = json_decode($fields, true);
			}

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

			foreach ($this->_fieldMap as $column => $aliases)
			{
				if (in_array($name, $aliases))
				{
					$field = $column;
					break;
				}
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

			foreach ($this->fieldMap() as $column => $aliases)
			{
				if (in_array($norm, $aliases))
				{
					$map['field'] = $column;
					break;
				}
			}

			$mapping[$norm] = $map;
		}

		return $mapping;
	}

	/**
	 * Map custom fields
	 *
	 * @return  array
	 */
	public function fieldMap()
	{
		if (!$this->mapped)
		{
			include_once __DIR__ . DS . 'profile' . DS . 'field.php';

			$fields = Field::all()
				->ordered()
				->rows();

			foreach ($fields as $field)
			{
				if (isset($this->_fieldMap[$field->get('name')]))
				{
					continue;
				}

				$this->_fieldMap[$field->get('name')] = array(
					$field->get('name'),
					strtolower($field->get('name')),
					preg_replace('/[^a-zA-Z0-9]/', '', $field->get('name'))
				);
			}
		}

		return $this->_fieldMap;
	}
}