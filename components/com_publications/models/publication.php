<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;
use Components\Publications\Helpers;
use Components\Publications\Tables;

// Include table classes
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'publication.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'version.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'access.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'audience.level.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'audience.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'author.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'license.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'category.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'master.type.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'screenshot.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'attachment.php');

// Projects
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_projects' . DS . 'tables' . DS . 'project.php');
require_once( PATH_CORE . DS . 'administrator' . DS . 'components'.DS
	.'com_projects' . DS . 'tables' . DS . 'project.owner.php');
require_once( PATH_CORE . DS . 'components' . DS . 'com_projects'. DS
	. 'helpers' . DS . 'html.php');

// Common models
require_once(__DIR__ . DS . 'curation.php');
require_once(__DIR__ . DS . 'types.php');
require_once(__DIR__ . DS . 'doi.php');

// Helpers
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php');
require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'tags.php');

/**
 * Information retrieval for items/info linked to a publication
 */
class Publication extends Object
{
	/**
	 * Authorized
	 *
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Container for properties
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * Constructor
	 *
	 * @param      integer $oid  Publication ID or alias
	 * @param      string  $version  Publication version number or alias (dev/default)
	 * @param      integer $vid  Publication version ID
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid, $version = 'default', $vid = NULL)
	{
		$this->_db = \JFactory::getDBO();

		if (is_object($oid))
		{
			// Temp as we are converting to models
			$this->version = $oid;
			$this->publication = NULL;
		}
		else
		{
			// Load master entry
			$this->publication = new Tables\Publication($this->_db);
			$this->publication->loadPublication($oid);

			// Load version
			$this->version = new Tables\Version($this->_db);
			if (intval($vid))
			{
				$this->version->load($vid);
			}
			else
			{
				$this->version->loadVersion($this->publication->id, $version);
			}

			// Version alternative label
			$versionAlias = $this->version->main == 1
				&& $this->version->state != 0 ? 'default' : $version;
			$versionAlias = $this->version->state == 3 ? 'dev' : $version;
			$this->versionAlias   = $versionAlias;

			// Get what we need
			$this->masterType();
			$this->category();

			// Map to former publication object (TEMP measure while converting)
			foreach ($this->version as $field => $value)
			{
				$this->$field = $value;
			}
			$this->version_id        = $this->version->id;
			$this->id                = $this->publication->id;
			$this->base 	         = $this->_type->alias;
			$this->curatorgroup      = $this->_type->curatorgroup;
			$this->dev_version_label = $this->version->getAttribute(
				$this->publication->id,
				'dev',
				'version_label'
			);

			// Map master values
			foreach ($this->publication as $field => $value)
			{
				if (isset($this->$field))
				{
					$masterField = 'master_' . $field;
					$this->$masterField = $value;
				}
				else
				{
					$this->$field = $value;
				}
			}

			// Collect params
			$this->params = Component::params('com_publications');
			$this->params->merge(new \JRegistry($this->version->params));
			$this->params->merge($this->_type->_params);
		}
	}

	/**
	 * Returns a reference to an article model
	 *
	 * @param      mixed $oid Article ID or alias
	 * @return     object KbModelArticle
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}

	/**
	 * Check if a property is set
	 *
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Check if the publication exists
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function masterExists()
	{
		if (empty($this->publication) || !$this->publication->id)
		{
			return false;
		}
		return true;
	}

	/**
	 * Check if the version exists
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->version->id && $this->version->id > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return \JHTML::_('date', $this->get('created'), Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return \JHTML::_('date', $this->get('created'), Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get the home project of this entry
	 *
	 * @return     mixed
	 */
	public function project()
	{
		if (empty($this->_project))
		{
			$this->_project = new \Components\Projects\Tables\Project($this->_db);
			$this->_project->load($this->publication->project_id);
			$this->_project->_params = new \JParameter( $this->_project->params );
		}

		return $this->_project;
	}

	/**
	 * Get last public release
	 *
	 * @return     mixed
	 */
	public function lastPublicRelease()
	{
		if (!isset($this->_lastPublicRelease))
		{
			$pid = empty($this->publication->id) ? $this->version->publication_id : $this->publication->id;
			$this->_lastPublicRelease = $this->version->getLastPubRelease($pid);
		}

		return $this->_lastPublicRelease;
	}

	/**
	 * Get the master type of this entry
	 *
	 * @return     mixed
	 */
	public function masterType()
	{
		if (empty($this->_type))
		{
			$this->_type = new Tables\MasterType($this->_db);
			$this->_type->load($this->publication->master_type);
			$this->_type->_params = new \JParameter( $this->_type->params );
			$this->pubTypeHelper = new Types($this->_db, $this->project());
		}

		return $this->_type;
	}

	/**
	 * Set curation
	 *
	 * @return     mixed
	 */
	public function setCuration()
	{
		if (!$this->exists())
		{
			return NULL;
		}

		$this->masterType();
		$this->project();

		if (!isset($this->_curationModel))
		{
			// Get manifest from either version record (published) or master type
			$manifest = $this->version->curation
						? $this->version->curation
						: $this->_type->curation;

			// Get curation model
			$this->_curationModel = new Curation($manifest);

			// Set pub assoc and load curation
			$this->_curationModel->setPubAssoc($this);
		}

		return $this->_curationModel;
	}

	/**
	 * Get the category of this entry
	 *
	 * @return     mixed
	 */
	public function category()
	{
		if (empty($this->_category))
		{
			$this->_category = new Tables\Category( $this->_db );
			$this->_category->load($this->publication->category);
			$this->_category->_params = new \JParameter( $this->_category->params );
		}

		return $this->_category;
	}

	/**
	 * Get the authors of this entry
	 *
	 * @return     mixed
	 */
	public function authors()
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_authors))
		{
			$objA = new Tables\Author( $this->_db );
			$this->_authors = $objA->getAuthors($this->version->id);
			$this->_submitter = $objA->getSubmitter($this->version->id, $this->version->created_by);
		}

		return $this->_authors;
	}

	/**
	 * Get unlinked contributors
	 * @param      array 	$contributors
	 * @param      boolean 	$incSubmitter
	 *
	 * @return     string
	 */
	public function getUnlinkedContributors($incSubmitter = false )
	{
		if (!$this->exists())
		{
			return array();
		}
		$contributors = $this->authors();

		$html = '';
		if (!empty($contributors))
		{
			$names = array();
			foreach ($contributors as $contributor)
			{
				if ($incSubmitter == false && $contributor->role == 'submitter')
				{
					continue;
				}
				if ($contributor->lastName || $contributor->firstName)
				{
					$name  = stripslashes($contributor->lastName);
					$name .= ', ' . substr(stripslashes($contributor->firstName), 0, 1) . '.';
				}
				else
				{
					$name = $contributor->name;
				}
				$name = str_replace( '"', '&quot;', $name );
				$names[] = $name;
			}
			if (count($names) > 0)
			{
				$html = implode( '; ', $names );
			}
		}
		return $html;
	}

	/**
	 * Get submitter of this entry
	 *
	 * @return     mixed
	 */
	public function submitter()
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_submitter))
		{
			$this->authors();
		}

		return $this->_submitter;
	}

	/**
	 * Get project owner
	 *
	 * @return     mixed
	 */
	public function owner()
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_owner))
		{
			$juser = \JFactory::getUser();
			$objO = new \Components\Projects\Tables\Owner($this->_db);
			$this->_owner = $objO->isOwner($juser->get('id'), $this->publication->project_id);
		}

		return $this->_owner;
	}

	/**
	 * Get curator group names
	 *
	 * @return     mixed
	 */
	public function curatorGroups()
	{
		if (!$this->exists() || !$this->masterType())
		{
			return array();
		}
		if (!isset($this->_curatorGroups))
		{
			$groups = array();
			if ($this->_type->curatorgroup)
			{
				$groups[] = $this->_type->curatorgroup;
			}
			if ($this->params->get('curatorgroup'))
			{
				$groups[] = $this->params->get('curatorgroup');
			}
			$this->_curatorGroups = $groups;
		}

		return $this->_curatorGroups;
	}

	/**
	 * Get access group names
	 *
	 * @return     mixed
	 */
	public function getAccessGroups()
	{
		if (!$this->exists() || !$this->masterType())
		{
			return array();
		}
		if (!isset($this->_accessGroups))
		{
			$paccess = new Tables\Access( $this->_db );
			$aGroups = $paccess->getGroups( $this->version->id, $this->publication->id );
			$this->_accessGroups = $this->getGroupProperty($aGroups);
		}

		return $this->_accessGroups;
	}

	/**
	 * Get group property
	 *
	 * @param      object 	$groups
	 * @param      string 	$get
	 *
	 * @return     array
	 */
	public function getGroupProperty($groups, $get = 'cn')
	{
		$arr = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $get == 'cn' ? $group->cn : $group->gidNumber;
				}
			}
		}
		return $arr;
	}

	/**
	 * Get publication content
	 *
	 * @return     mixed
	 */
	public function attachments()
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_attachments))
		{
			$pContent = new Tables\Attachment( $this->_db );
			$this->_attachments = $pContent->sortAttachments ( $this->version->id );
		}

		return $this->_attachments;
	}

	/**
	 * Get publication license
	 *
	 * @return     mixed
	 */
	public function license()
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_license))
		{
			$this->_license = new Tables\License($this->_db);
			$this->_license->load($this->version->license_type);
		}

		return $this->_license;
	}

	/**
	 * Check if the resource was deleted
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function deleted()
	{
		if ($this->version->state == 2)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the publication is published
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function published()
	{
		if (!$this->exists())
		{
			return false;
		}

		if (in_array($this->version->state, array(0, 2, 3, 4, 5, 6, 7)))
		{
			return false;
		}

		$now = \JFactory::getDate();

		if ($this->version->published_up
		 && $this->version->published_up != $this->_db->getNullDate()
		 && $this->version->published_up >= $now)
		{
			return false;
		}
		if ($this->version->published_down
		 && $this->version->published_down != $this->_db->getNullDate()
		 && $this->version->published_down <= $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Authorize current user
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	private function _authorize()
	{
		$juser = \JFactory::getUser();

		// NOT logged in
		if ($juser->get('guest'))
		{
			// If the resource is published and public
			if ($this->published() && $this->publication->access == 0)
			{
				// Allow view access
				$this->params->set('access-view-publication', true);
				if ($this->publication->access == 0)
				{
					$this->params->set('access-view-all-publication', true);
				}
			}
			$this->_authorized = true;
			return;
		}

		// Check if they're a site admin (from Joomla)
		$this->params->set('access-admin-publication', $juser->authorise('core.admin', null));
		$this->params->set('access-manage-publication', $juser->authorise('core.manage', null));

		if ($this->params->get('access-admin-publication')
		 || $this->params->get('access-manage-publication'))
		{
			$this->params->set('access-view-publication', true);
			$this->params->set('access-view-all-publication', true);

			$this->params->set('access-create-publication', true);
			$this->params->set('access-delete-publication', true);
			$this->params->set('access-edit-publication', true);
			$this->params->set('access-edit-state-publication', true);

			// May curate
			$this->params->set('access-curator-publication', true);
		}

		// Get user groups
		$ugs = \Hubzero\User\Helper::getGroups($juser->get('id'));
		$usersgroups = $this->getGroupProperty($ugs);

		// If they're not an admin
		if (!$this->params->get('access-admin-publication')
		 && !$this->params->get('access-manage-publication'))
		{
			// If logged in and resource is published and public or registered
			if ($this->published() && $this->publication->access <= 1)
			{
				// Allow view access
				$this->params->set('access-view-publication', true);
				$this->params->set('access-view-all-publication', true);
			}
			// Allowed groups (private access)
			if ($this->publication->access >= 2)
			{
				$groups      = $this->getAccessGroups();
				if (array_intersect($usersgroups, $groups) > 1)
				{
					$this->params->set('access-view-publication', true);
					$this->params->set('access-view-all-publication', true);
				}
			}
		}

		// Project owners
		if ($this->owner())
		{
			$this->params->set('access-owner-publication', true);
			$this->params->set('access-manage-publication', true);

			$this->params->set('access-view-publication', true);
			$this->params->set('access-view-all-publication', true);
			$this->params->set('access-create-publication', true);
			$this->params->set('access-delete-publication', true);
			$this->params->set('access-edit-publication', true);
			$this->params->set('access-edit-state-publication', true);
		}

		// Curator
		if ($this->version->curator && $juser->get('id') == $this->version->curator)
		{
			$this->params->set('access-curator-publication', true);
			$this->params->set('access-curator-assigned-publication', true);
		}

		// Curator from groups
		$curatorGroups = $this->curatorGroups();
		if (!empty($curatorGroups))
		{
			if (array_intersect($usersgroups, $curatorGroups) > 1)
			{
				$this->params->set('access-curator-publication', true);
			}
		}

		// Curators have full view access and approval controls
		if ($this->params->get('access-curator-publication'))
		{
			$this->params->set('access-view-publication', true);
			$this->params->set('access-view-all-publication', true);
			$this->params->set('access-edit-state-publication', true);
			$this->params->set('access-manage-publication', true);
		}

		$this->_authorized = true;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action = 'view')
	{
		if (!$this->_authorized)
		{
			$this->_authorize();
		}
		return $this->params->get('access-' . strtolower($action) . '-publication');
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\Profile))
		{
			$this->_creator = \Hubzero\User\Profile::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'uidNumber' : $property);
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function describe($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		if ($this->get('description', null) == null)
		{
			$content = stripslashes($this->version->description);
			$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);

			$this->set('description', trim($content));
		}

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('description.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('description', ''));
					\JPluginHelper::importPlugin('content');
					\JDispatcher::getInstance()->trigger('onContentPrepare', array(
						'com_publications.publication.description',
						&$this,
						&$config
					));

					$this->set('description.parsed', (string) $this->get('description', ''));
					$this->set('description', $content);

					return $this->describe($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->describe('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('description'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function notes($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		if ($this->get('release_notes', null) == null)
		{
			$content = stripslashes($this->version->release_notes);

			$this->set('release_notes', trim($content));
		}

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('release_notes.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get('release_notes', ''));
					\JPluginHelper::importPlugin('content');
					\JDispatcher::getInstance()->trigger('onContentPrepare', array(
						'com_publications.publication.release_notes',
						&$this,
						&$config
					));


					$this->set('release_notes.parsed', (string) $this->get('release_notes', ''));
					$this->set('release_notes', $content);

					return $this->notes($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->notes('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('release_notes'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Get the content of nbtag in metadata field
	 *
	 * @return     mixed String or Integer
	 */
	public function getNbtag ($aliasmap = '')
	{
		$data = array();

		// Parse data
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->get('metadata', ''), $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = Helpers\Html::_txtUnpee($match[2]);
			}
		}

		$value = isset($data[$aliasmap]) ? $data[$aliasmap] : NULL;

		return $value;
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param      string  $as      Format to return content in [parsed, clean, raw]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function parse($aliasmap = '', $field = '', $as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		if ($this->get($field, null) == null)
		{
			$content = stripslashes($this->version->$field);

			$this->set($field, trim($content));
		}

		if (!$this->get($field, ''))
		{
			return false;
		}

		switch ($as)
		{
			case 'parsed':
				$content = $this->get($field . '.parsed', null);

				if ($content === null)
				{
					$config = array(
						'option'   => 'com_publications',
						'scope'    => '',
						'pagename' => 'publications',
						'pageid'   => '',
						'filepath' => '',
						'domain'   => ''
					);

					$content = (string) stripslashes($this->get($field, ''));
					if ($field == 'metadata')
					{
						$content = (string) stripslashes($this->getNbtag($aliasmap));
					}

					\JPluginHelper::importPlugin('content');
					\JDispatcher::getInstance()->trigger('onContentPrepare', array(
						'com_publications.publication.' . $field,
						&$this,
						&$config
					));

					$parsed = (string) stripslashes($this->get($field, ''));

					if ($field == 'metadata')
					{
						$parsed = (string) stripslashes($this->getNbtag($aliasmap));
					}

					$this->set($field . '.parsed', $parsed);
					$this->set($field, $content);

					return $this->parse($aliasmap, $field, $as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->parse($aliasmap, $field, 'parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get($field));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Do nothing here yet.
	}

	/**
	 * Get citations
	 *
	 * @return     void
	 */
	public function getCitations()
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->_citations))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

			$cc = new \Components\Citations\Tables\Citation( $this->_db );

			$this->_citations = $cc->getCitations( 'publication', $this->publication->id );
		}

		return $this->_citations;
	}

	/**
	 * Get citations count
	 *
	 * @return     void
	 */
	public function getCitationsCount()
	{
		$this->getCitations();

		return count($this->_citations);
	}

	/**
	 * Get last citation date
	 *
	 * @return     void
	 */
	public function getLastCitationDate()
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->_lastCitationDate))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

			$cc = new \Components\Citations\Tables\Citation( $this->_db );

			$this->_lastCitationDate = $cc->getLastCitationDate( 'publication', $this->publication->id );
		}

		return $this->_lastCitationDate;
	}

	/**
	 * Get tags
	 *
	 * @param      int $tagger_id
	 * @param      int $strength
	 * @param      boolean $admin
	 *
	 * @return     string HTML
	 */
	public function getTags($tagger_id = 0, $strength = 0, $admin = 0)
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->_tags))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php');

			$rt = new Helpers\Tags( $this->_db );
			$this->_tags = $rt->get_tags_on_object($this->id, 0, 0, $tagger_id, $strength, $admin);
		}

		return $this->_tags;
	}

	/**
	 * Get tags for editing
	 *
	 * @param      int $tagger_id
	 * @param      int $strength
	 *
	 * @return     string HTML
	 */
	public function getTagsForEditing( $tagger_id = 0, $strength = 0 )
	{
		if (!$this->exists())
		{
			return false;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php');

		$rt = new Helpers\Tags( $this->_db );
		$this->_tagsForEditing = $rt->get_tag_string( $this->id, 0, 0, $tagger_id, $strength, 0 );
	}

	/**
	 * Get tag cloud
	 *
	 * @param      boolean $admin
	 *
	 * @return     string HTML
	 */
	public function getTagCloud( $admin = 0 )
	{
		if (!$this->exists())
		{
			return false;
		}

		if (!isset($this->_tagCloud))
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php');

			$rt = new Helpers\Tags( $this->_db );
			$this->_tagCloud = $rt->get_tag_cloud(0, $admin, $this->id);
		}

		return $this->_tagCloud;
	}

	/**
	 * Get path to archival bundle
	 *
	 * @return     mixed
	 */
	public function bundlePath()
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->_bundlePath))
		{
			// Archival package
			$tarname  = Lang::txt('Publication') . '_' . $this->publication->id . '.zip';
			$this->_bundlePath = Helpers\Html::buildPubPath(
				$this->publication->id,
				$this->version->id,
				'', '', 1) . DS . $tarname;
		}

		return $this->_bundlePath;
	}

	/**
	 * Get wiki page
	 *
	 * @param      object $attachment
	 * @param      object $publication
	 * @param      string $masterscope
	 * @param      string $versionid
	 * @return     object
	 */
	public function getWikiPage( $pageid = NULL, $masterscope = NULL, $versionid = NULL )
	{
		if (!$pageid || !$this->exists())
		{
			return false;
		}

		$query = "SELECT p.* ";
		if ($this->version->state == 3)
		{
			// Draft - load latest version
			$query .= ", (SELECT v.pagetext FROM #__wiki_version as v WHERE v.pageid=p.id
			  ORDER by p.state ASC, v.version DESC LIMIT 1) as pagetext ";
		}
		else
		{
			$date = $this->version->accepted && $this->version->accepted != '0000-00-00 00:00:00'
				? $this->version->accepted : $this->version->submitted;
			$date = (!$date || $date == '0000-00-00 00:00:00') ? $this->version->published_up : $date;

			$query .= ", (SELECT v.pagetext FROM #__wiki_version as v WHERE v.pageid=p.id AND ";
			$query .= $versionid ? " v.id=" . $versionid : " v.created <= '" . $date . "'";
			$query .= " ORDER BY v.created DESC LIMIT 1) as pagetext ";
		}

		$query .= " FROM #__wiki_page as p WHERE p.scope LIKE '" . $masterscope . "%' ";
		$query .=  is_numeric($pageid) ? " AND p.id='$pageid' " : " AND p.pagename='$pageid' ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result ? $result[0] : NULL;
	}
}

