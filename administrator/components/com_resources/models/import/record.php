<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Resources\Model\Import;

use Exception;
use stdClass;
use JText;
use JFactory;
use JRoute;
use JURI;
use JParameter;

// include elements model
include_once JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php';

/**
 * Resource Import Record Model
 */
class Record extends \Hubzero\Base\Object
{
	CONST TITLE_MATCH = 10;

	public $raw;
	public $record;
	private $_mode;
	private $_options;
	private $_database;
	private $_user;

	/**
	 * Resource Import Record Constructor
	 *
	 * @param mixes  $raw     Raw Resource data
	 * @param array  $options Import options
	 */
	public function __construct($raw, $options = array(), $mode = 'UPDATE')
	{
		// store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = $mode;

		// create core objects
		$this->_database = JFactory::getDBO();
		$this->_user     = JFactory::getUser();

		// create resource objects
		$this->record               = new stdClass;
		$this->record->resource     = new \ResourcesResource($this->_database);
		$this->record->type         = new \ResourcesType($this->_database);
		$this->record->children     = array();
		$this->record->tags         = array();
		$this->record->contributors = array();
		$this->record->custom       = new stdClass;

		// message bags for user
		$this->record->errors       = array();
		$this->record->notices      = array();

		// bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return $this Current object
	 */
	public function bind()
	{
		// wrap type mapping in separate try catch to allow resource
		// data to still be mapped even if there is no id.
		try
		{
			$this->_mapTypeData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		// wrap in try catch to avoid breaking in middle of import
		try
		{
			// map resource data
			$this->_mapResourceData();

			// map child resource data
			$this->_mapChildData();

			// map contributors
			$this->_mapContributorData();

			// map tags
			$this->_mapTagsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		// chainability
		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return $this Current object
	 */
	public function check()
	{
		// run save check method
		if (!$this->record->resource->check())
		{
			array_push($this->record->errors, $this->record->resource->getError());
		}

		// check custom field if we have that on
		if ($this->_options['requiredfields'] && isset($this->record->type->id))
		{
			$resourcesElements = new \ResourcesElements((array) $this->record->custom, $this->record->type->customFields);
			foreach ($resourcesElements->getSchema()->fields as $field)
			{
				$value = $resourcesElements->get($field->label);
				if ($field->required && (!isset($value) || $value == ''))
				{
					array_push($this->record->errors, \JText::sprintf('COM_RESOURCES_IMPORT_RECORD_MODEL_MISSING_REQUIREDCUSTOMFIELDS', $field->label));
				}
			}
		}

		// chainability
		return $this;
	}

	/**
	 * Store Resource Data
	 *
	 * @param  integer $dryRun Dry Run mode
	 * @return $this Current object
	 */
	public function store($dryRun = 1)
	{
		// are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			return $this;
		}

		// attempt to save all data
		// wrap in try catch to avoid break mid import
		try
		{
			// save resource
			$this->_saveResourceData();

			// save child resource data
			$this->_saveChildData();

			// save contributors
			$this->_saveContributorData();

			// save tags
			$this->_saveTagsData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		// chainability
		return $this;
	}

	/**
	 * Map Resource Type
	 *
	 * @return void
	 */
	private function _mapTypeData()
	{
		// make sure we have a type
		if (!isset($this->raw->type))
		{
			throw new Exception(JText::_('COM_RESOURCES_IMPORT_RECORD_MODEL_MUSTHAVETYPE'));
		}

		// load type
		$this->record->type->load($this->raw->type);

		// make sure we have a valid type
		if (!$this->record->type->id)
		{
			throw new Exception(JText::sprintf('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_LOADTYPE', $this->raw->type));
		}
	}

	/**
	 * Map Resource Data
	 *
	 * @return void
	 */
	private function _mapResourceData()
	{
		// do we want to do a title match?
		if ($this->_options['titlematch'] == 1 && isset($this->record->type->id))
		{
			$sql = 'SELECT id, title, LEVENSHTEIN( title, '.$this->_database->quote($this->raw->title).' ) as titleDiff
			        FROM `#__resources`
			        WHERE `type`=' . $this->record->type->id . ' HAVING titleDiff < ' . self::TITLE_MATCH;
			$this->_database->setQuery($sql);
			$results = $this->_database->loadObjectList('id');

			// did we get more then one result?
			if (count($results) > 1)
			{
				$ids = implode(", ", array_keys($results));
				throw new Exception(JText::sprintf('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_DETECTDUPLICATE', $ids));
			}

			// if we only have one were all good
			if (count($results) == 1)
			{
				// set our id to the matched resource
				$resource = reset($results);
				$this->raw->id = $resource->id;

				// add a notice with link to resource matched
				$resourceLink = rtrim(str_replace('administrator', '', JURI::base()), DS) . DS . 'resources' . DS . $resource->id;
				$link = '<a target="_blank" href="' . $resourceLink . '">' . $resourceLink . '</a>';
				array_push($this->record->notices, JText::sprintf('COM_RESOURCES_IMPORT_RECORD_MODEL_MATCHEDBYTITLE', $link));
			}
		}

		// do we have a resource id
		// either passed in the raw data or gotten from the title match
		if (isset($this->raw->id) && $this->raw->id > 1)
		{
			$this->record->resource->load($this->raw->id);
		}
		else
		{
			$this->raw->standalone = 1;
			$this->raw->created    = JFactory::getDate()->toSql();
			$this->raw->created_by = $this->_user->get('id');

			// publish up/down
			if (!isset($this->raw->publish_up))
			{
				$this->raw->publish_up = JFactory::getDate()->toSql();
			}
			if (!isset($this->raw->publish_down))
			{
				$this->raw->publish_down = '0000-00-00 00:00:00';
			}
		}

		// set modified date/user
		$this->raw->modified    = JFactory::getDate()->toSql();
		$this->raw->modified_by = $this->_user->get('id');

		// set status
		if (isset($this->_options['status']))
		{
			$this->raw->published = (int) $this->_options['status'];
		}

		// set group
		if (isset($this->_options['group']))
		{
			$this->raw->group_owner = $this->_options['group'];
		}

		// set access
		if (isset($this->_options['access']))
		{
			$this->raw->access = (int) $this->_options['access'];
		}

		// bind resource data
		$this->record->resource->bind($this->raw);

		// resource params
		$params = new JParameter();
		$params->bind($this->record->resource->params);
		$this->record->resource->params = $params->toString();

		// resource attributes
		$attribs = new JParameter();
		$attribs->bind($this->record->resource->attribs);
		$this->record->resource->attribs = $attribs->toString();

		// full text pieces - to add paragraph tags
		$fullTextPieces = array_map("trim", explode("\n", $this->record->resource->introtext));
		$fullTextPieces = array_values(array_filter($fullTextPieces));

		// set the full text
		$this->record->resource->fulltxt = "<p>" . implode("</p>\n<p>", $fullTextPieces) . "</p>";

		if (!isset($this->raw->custom_fields))
		{
			$this->raw->custom_fields = array();
		}

		// bind custom fields to types custom fields
		if (isset($this->record->type->id))
		{
			$resourcesElements = new \ResourcesElements((array) $this->raw->custom_fields, $this->record->type->customFields);
			$customFieldsHtml  = $resourcesElements->toDatabaseHtml();

			// add all custom fields to custom object
			foreach ($resourcesElements->getSchema()->fields as $field)
			{
				$fieldLabel = $field->label;
				$fieldName  = $field->name;
				$value      = (isset($this->raw->custom_fields->$fieldName)) ? $this->raw->custom_fields->$fieldName : null;

				if ($field->type == 'hidden')
				{
					$value = (isset($field->options[0])) ? $field->options[0]->value : $value;
				}

				$this->record->custom->$fieldLabel = $value;
			}
		}
		else
		{
			$customFieldsHtml = '';
		}

		// add custom fields to fulltxt
		$this->record->resource->fulltxt .= "\n\n" . $customFieldsHtml;
	}

	/**
	 * Save Parent Resource Data
	 * @return void
	 */
	private function _saveResourceData()
	{
		// save main resource
		if (!$this->record->resource->save($this->record->resource))
		{
			throw new Exception(JText::_('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVERESOURCE'));
		}
	}

	/**
	 * Map Child Resources
	 *
	 * @return void
	 */
	private function _mapChildData()
	{
		// do we have an array of child resources?
		if (isset($this->raw->children) && is_array($this->raw->children))
		{
			// loop through each child resource and bind as a resource object
			foreach ($this->raw->children as $child)
			{
				$childResource = new \ResourcesResource($this->_database);
				$childResource->bind($child);

				// add this child to
				array_push($this->record->children, $childResource);
			}
		}
	}

	/**
	 * Save Child Resources
	 *
	 * @return void
	 */
	private function _saveChildData()
	{
		// if we updating we want to completely replace
		if ($this->_mode == 'UPDATE' && isset($this->record->resource->id))
		{
			// remove any existing files
			$children = $this->record->resource->getItemChildren(array('parent_id' => $this->record->resource->id));
			foreach ($children as $child)
			{
				$rconfig = \JComponentHelper::getParams('com_resources');
				$base = JPATH_ROOT . DS . trim($rconfig->get('uploadpath', '/site/resources'), DS);
				$file = $base . DS . $child->path;

				//get file info
				$info = pathinfo($file);
				$directory = $info['dirname'];

				if ($child->type == 13 && file_exists($file))
				{
					shell_exec('rm ' . $file);
				}

				if (is_dir($directory))
				{
					// get iterator on direcotry
					$iterator = new \FilesystemIterator($directory);
					$isDirEmpty = !$iterator->valid();

					// remove directory if empty
					if ($isDirEmpty)
					{
						shell_exec('rmdir ' . $directory);
					}
				}
			}

			// delete all child resources
			$sql = "DELETE FROM `#__resources` WHERE `id` IN (
						SELECT child_id FROM `#__resource_assoc` WHERE `parent_id`=" . $this->_database->quote($this->record->resource->id) .
					")";
			$this->_database->setQuery($sql);
			$this->_database->query();

			// delete all child resource associations
			$sql = "DELETE FROM `#__resource_assoc` WHERE `parent_id`=" . $this->_database->quote($this->record->resource->id);
			$this->_database->setQuery($sql);
			$this->_database->query();
		}

		// loop through each child
		foreach ($this->record->children as $child)
		{
			// save child
			if (!$child->store())
			{
				throw new Exception(JText::_('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVECHILD'));
			}

			// create parent - child association
			$assoc = new \ResourcesAssoc($this->_database);
			$assoc->ordering = $assoc->getLastOrder($this->record->resource->id);
			$assoc->ordering = ($assoc->ordering) ? $assoc->ordering : 0;
			$assoc->ordering++;
			$assoc->parent_id = $this->record->resource->id;
			$assoc->child_id  = $child->id;
			$assoc->grouping  = 0;
			if (!$assoc->store(true))
			{
				throw new Exception(JText::_('COM_RESOURCES_IMPORT_RECORD_MODEL_UNABLE_SAVECHILDASSOC'));
			}
		}
	}

	/**
	 * Map Resource Contributors
	 *
	 * @return void
	 */
	private function _mapContributorData()
	{
		// get any contributors
		$contributors = (isset($this->raw->contributors)) ? $this->raw->contributors : new stdClass;

		// get roles for resource type
		$contributorRoles = new \ResourcesContributorRoleType($this->_database);
		$rolesForType     = $contributorRoles->getRolesForType($this->record->resource->type);
		$rolesForType     = (is_array($rolesForType)) ? $rolesForType : array();

		// get valid role aliases
		$existingRoles = array_map(function($role) {
			return $role->alias;
		}, $rolesForType);

		// handle contributors as string
		if (is_string($contributors))
		{
			$contributors = array_map("trim", explode(';', $contributors));
			$contributors = array_values(array_filter($contributors));

			$contributors = array_map(function($c) {
				$cc = new stdClass;
				$cc->name = $c;
				return $cc;
			}, $contributors);
		}

		// loop through each contributor
		foreach ($contributors as $contributor)
		{
			// create resource contributor object
			$resourceContributor = new \ResourcesContributor($this->_database);

			// check to see if we have an author id
			$authorid = (isset($contributor->authorid)) ? $contributor->authorid : null;

			// load name
			if ($authorid != null)
			{
				if ($profile = \Hubzero\User\Profile::getInstance($authorid))
				{
					$resourceContributor->authorid = $profile->get('uidNumber');
				}
			}

			$resourceContributor->name         = (isset($contributor->name)) ? $contributor->name : '';
			$resourceContributor->organization = (isset($contributor->organization)) ? $contributor->organization : '';
			$resourceContributor->role         = (isset($contributor->role) && in_array($contributor->role, $existingRoles)) ? $contributor->role : '';
			$resourceContributor->subtable     = 'resources';

			array_push($this->record->contributors, $resourceContributor);
		}
	}

	/**
	 * Save Resource Contributors
	 * @return [type] [description]
	 */
	private function _saveContributorData()
	{
		// if we updating we want to completely replace
		if ($this->_mode == 'UPDATE' && isset($this->record->resource->id))
		{
			// delete all child resource associations
			$sql = "DELETE FROM `#__author_assoc` WHERE `subtable`='resources' AND `subid`=" . $this->_database->quote($this->record->resource->id);
			$this->_database->setQuery($sql);
			$this->_database->query();
		}

		// create new author assoc for resource
		foreach ($this->record->contributors as $contributor)
		{
			$contributor->subid = $this->record->resource->id;
			$order = $contributor->getLastOrder($this->record->resource->id, 'resources');
			$contributor->ordering = $order + 1;
			$contributor->authorid = ($contributor->authorid == '') ? $contributor->getUserId($contributor->name) : $contributor->authorid;
			$contributor->createAssociation();
		}
	}

	/**
	 * Map Resource Tags
	 *
	 * @return void
	 */
	private function _mapTagsData()
	{
		if (isset($this->raw->tags))
		{
			$tags = $this->raw->tags;

			// handle tags as string (comma separated)
			if (is_string($tags))
			{
				$tags = array_map("trim", explode(',', $tags));
				$tags = array_values(array_filter($tags));
			}

			$this->record->tags = $tags;
		}
	}

	/**
	 * Save Resource Tags
	 *
	 * @return void
	 */
	private function _saveTagsData()
	{
		// save tags
		$resourcesTags = new \ResourcesTags($this->record->resource->id);
		$resourcesTags->setTags($this->record->tags, $this->_user->get('id'), 1, 1);
	}

	/**
	 * Output object of string
	 *
	 * @return toString() method
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * To String object
	 *
	 * Removes private properties before returning
	 *
	 * @return string
	 */
	public function toString()
	{
		// reflect on class to get private or protected props
		$reflectionClass   = new \ReflectionClass($this);
		$privateProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE);

		// remove each private or protected prop
		foreach ($privateProperties as $prop)
		{
			$name = (string) $prop->name;
			unset($this->$name);
		}

		// output as json
		return json_encode($this);
	}
}