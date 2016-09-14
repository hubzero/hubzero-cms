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
 * @author    Alissa Nedossekina <aliasa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;
use Components\Publications\Helpers;
use Components\Publications\Tables;
use Hubzero\Base\ItemList;

// Include table classes
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'publication.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'version.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'access.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'audience.level.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'audience.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'author.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'license.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'category.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'master.type.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'screenshot.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');
require_once( dirname(__DIR__) . DS . 'tables' . DS . 'logs.php');

// Projects
require_once( PATH_CORE . DS . 'components'.DS
	.'com_projects' . DS . 'models' . DS . 'project.php');
require_once( PATH_CORE . DS . 'components'.DS
	.'com_projects' . DS . 'models' . DS . 'repo.php');

// Common models
require_once(__DIR__ . DS . 'curation.php');
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
	public function __construct($oid = NULL, $version = 'default', $vid = NULL)
	{
		$this->_db = \App::get('db');

		if (is_object($oid))
		{
			// This mapping is used in item listings
			$this->version = new Tables\Version($this->_db);
			$this->version->bind($oid);

			$this->publication = new Tables\Publication($this->_db);
			$this->publication->bind($oid);

			// Map values
			foreach ($oid as $field => $value)
			{
				$this->$field = $value;
			}
			// Some adjustments
			$this->version->id = $this->get('version_id');

			$this->params = Component::params('com_publications');
			$this->params->merge(new \Hubzero\Config\Registry($this->version->params));
			if (isset($this->version->type_params))
			{
				$this->params->merge($this->version->type_params);
			}
		}
		else
		{
			$this->version = new Tables\Version($this->_db);
			$this->publication = new Tables\Publication($this->_db);

			// Load version & master record
			if (intval($vid))
			{
				$this->version->load($vid);
				$oid = $this->version->publication_id;
				$this->publication->loadPublication($oid);
			}
			elseif ($oid)
			{
				$this->publication->loadPublication($oid);
				$this->version->loadVersion($this->publication->id, $version);
			}

			// Get what we need
			$this->masterType();
			$this->category();

			// Map values for easy access
			foreach ($this->version as $field => $value)
			{
				$this->$field = $value;
			}
			$this->version_id        = $this->version->id;
			$this->id                = $this->publication->id;
			$this->base 	         = $this->_type->alias;
			$this->curatorgroup      = $this->_type->curatorgroup;

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
			$this->params->merge(new \Hubzero\Config\Registry($this->version->params));
			$this->params->merge($this->_type->_params);
		}

		// Set version alias
		$this->versionAlias($version);
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
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param      string $key Config property to retrieve
	 * @return     mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_publications');
		}
		if ($key)
		{
			return $this->_config->get($key, $default);
		}
		return $this->_config;
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
		if (empty($this->version) || !$this->get('version_id'))
		{
			return false;
		}
		return true;
	}

	/**
	 * Set/get version alternative label if applicable (dev/default)
	 *
	 * @return     array
	 */
	public function versionAlias($name = 'default')
	{
		if (!$this->exists())
		{
			return false;
		}
		if (!isset($this->versionAlias))
		{
			$this->versionAlias = $this->isMain() && !$this->isUnpublished() ? 'default' : $name;
			$this->versionAlias = $this->isDev() ? 'dev' : $name;
		}

		return $this->versionAlias;
	}

	/**
	 * Check if the publication belongs to project
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function belongsToProject( $projectId = NULL)
	{
		if (!$projectId)
		{
			return false;
		}
		if (!$this->masterExists())
		{
			return false;
		}
		if ($this->get('project_id') != $projectId)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get version property
	 *
	 * @param      string $property
	 * @param      string $versionAlias   dev/default
	 * @return     string
	 */
	public function versionProperty($property = '', $versionAlias = 'default')
	{
		if (!$property || ($versionAlias != 'dev' && $versionAlias != 'default'))
		{
			return false;
		}

		$val = $versionAlias . '_' . $property;
		if (!isset($this->$val))
		{
			$this->$val = $this->version->getAttribute(
				$this->get('id'),
				$versionAlias,
				$property
			);
		}
		return $this->$val;
	}

	/**
	 * Return a formatted created timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function created($as='')
	{
		return $this->_date('created', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function modified($as='')
	{
		return $this->_date('modified', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function published($as='')
	{
		if ($this->get('accepted')
		 && $this->get('accepted') != '0000-00-00 00:00:00'
		 && $this->get('accepted') > $this->get('published_up'))
		{
			return $this->_date('accepted', $as);
		}
		return $this->_date('published_up', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function unpublished($as='')
	{
		return $this->_date('published_down', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function submitted($as='')
	{
		return $this->_date('submitted', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function accepted($as='')
	{
		return $this->_date('accepted', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function archived($as='')
	{
		return $this->_date('archived', $as);
	}

	/**
	 * Return a formatted modified timestamp
	 *
	 * @param      string $as What data to return
	 * @return     string
	 */
	public function released($as='')
	{
		return $this->_date('released', $as);
	}

	/**
	 * Does publication have future release date?
	 *
	 * @return     boolean
	 */
	public function isEmbargoed()
	{
		if (!$this->get('published_up') || $this->get('published_up') == $this->_db->getNullDate())
		{
			return false;
		}
		if (Date::of($this->get('published_up'))->toUnix() > Date::toUnix())
		{
			return true;
		}
		return false;
	}

	/**
	 * Determine future archive date
	 *
	 * @return     string
	 */
	public function futureArchivalDate()
	{
		if ($this->accepted())
		{
			$archDate = Date::of($this->get('accepted') . '+1 month')->toSql();
			if (Date::of($archDate)->toUnix() > Date::toUnix())
			{
				return $archDate;
			}
		}
		return NULL;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param      string $key Field to return
	 * @param      string $as  What data to return
	 * @return     string
	 */
	protected function _date($key, $as='')
	{
		if ($this->get($key) == $this->_db->getNullDate())
		{
			return NULL;
		}
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'datetime':
				return $this->_date($key, 'date') . ' &#64; ' . $this->_date($key, 'time');
			break;

			case 'timeago':
				return \Components\Projects\Helpers\Html::timeAgo($this->get($key));
			break;

			default:
				return $this->get($key);
			break;
		}
	}

	/**
	 * Get the home project of this entry
	 *
	 * @return  object Models\Project
	 */
	public function project()
	{
		if (empty($this->_project))
		{
			$this->_project = new \Components\Projects\Models\Project($this->publication->project_id);
			$this->_project->_params = new \Hubzero\Config\Registry( $this->_project->params );
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
			$this->_type->_params = new \Hubzero\Config\Registry( $this->_type->params );
		}

		return $this->_type;
	}

	/**
	 * Set curation
	 * Get & apply manifest from saved record or master type)
	 *
	 * @param      boolean 	$setProgress 	Get status of each section
	 * @return     mixed
	 */
	public function setCuration($setProgress = true)
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
			$this->_curationModel = new Curation($manifest, $this->_type->curation);

			// Set pub assoc and load curation
			$this->_curationModel->setPubAssoc($this, $setProgress);
		}

		return $this->_curationModel;
	}

	/**
	 * Get curation model or its properties
	 *
	 * @param      string 	$property
	 * @return     mixed
	 */
	public function curation($property = NULL, $blockId = NULL, $blockProperty = NULL, $elementId = NULL)
	{
		if (!isset($this->_curationModel))
		{
			$this->setCuration();
		}
		switch ($property)
		{
			case 'progress':
				return $this->_curationModel->_progress;
			break;

			case 'blocks':
				if ($blockId && isset($this->_curationModel->_progress->blocks->$blockId))
				{
					if ($blockProperty)
					{
						switch ($blockProperty)
						{
							case 'complete':
								return $this->_curationModel->_progress->blocks->$blockId->status->status;
								break;

							case 'elementStatus':
								if ($elementId && isset($this->_curationModel->_progress->blocks->$blockId->status->elements->$elementId))
								{
									return $this->_curationModel->_progress->blocks->$blockId->status->elements->$elementId->status;
								}
								break;

							case 'elements':
								if ($elementId)
								{
									if (isset($this->_curationModel->_progress->blocks->$blockId->manifest->elements->$elementId))
									{
										return $this->_curationModel->_progress->blocks->$blockId->manifest->elements->$elementId;
									}
									else
									{
										$first = $this->curation('blocks', $blockId, 'firstElement');
										return $this->_curationModel->_progress->blocks->$blockId->manifest->elements->$first;
									}
								}
								return $this->_curationModel->_progress->blocks->$blockId->manifest->elements;
								break;

							case 'props':
								return $this->_curationModel->_progress->blocks->$blockId->name . '-' . $blockId;
								break;

							case 'required':
								return isset($this->_curationModel->_progress->blocks->$blockId->manifest->params->required) ?  $this->_curationModel->_progress->blocks->$blockId->manifest->params->required : 0;
								break;

							case 'hasElements':
								return empty($this->_curationModel->_progress->blocks->$blockId->manifest->elements) ?  false : true;
								break;

							default:
								if (isset($this->_curationModel->_progress->blocks->$blockId->$blockProperty))
								{
									return $this->_curationModel->_progress->blocks->$blockId->$blockProperty;
								}
								else
								{
									return NULL;
								}
								break;
						}
					}
					return $this->_curationModel->_progress->blocks->$blockId;
				}
				elseif ($blockId)
				{
					// Block does not exist or is inactive
					return false;
				}
				return $this->_curationModel->_progress->blocks;
			break;

			case 'complete':
				return $this->_curationModel->_progress->complete;
			break;

			case 'lastBlock':
				return $this->_curationModel->_progress->lastBlock;
			break;

			case 'firstBlock':
				return $this->_curationModel->_progress->firstBlock;
			break;

			case 'params':
				return $this->_curationModel->_manifest->params;
			break;

			default:
				return $this->_curationModel;
			break;
		}
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
			$this->_category->_params = new \Hubzero\Config\Registry( $this->_category->params );
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
		if (!isset($this->_tblAuthors))
		{
			$this->_tblAuthors = new Tables\Author( $this->_db );
		}
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_authors))
		{
			$this->_authors   = $this->_tblAuthors->getAuthors($this->version->id);
			$this->_submitter = $this->_tblAuthors->getSubmitter($this->version->id, $this->version->created_by);
		}

		return $this->_authors;
	}

	/**
	 * Get publication table
	 *
	 * @return  object
	 */
	public function table($name = NULL)
	{
		if ($name == 'Author')
		{
			if (!isset($this->_tblAuthor))
			{
				$this->_tblAuthor = new Tables\Author( $this->_db );
			}
			return $this->_tblAuthor;
		}
		if ($name == 'Content')
		{
			if (!isset($this->_tblContent))
			{
				$this->_tblContent = new Tables\Attachment( $this->_db );
			}
			return $this->_tblContent;
		}
		if ($name == 'License')
		{
			if (!isset($this->_tblLicense))
			{
				$this->_tblLicense = new Tables\License( $this->_db );
			}
			return $this->_tblLicense;
		}

		return $this->publication;
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
					if (strstr($contributor->firstName, ' '))
					{
						$parts = explode(' ', $contributor->firstName);
						$contributor->firstName = array_shift($parts);
						$contributor->middleName = implode(' ', $parts);
					}
					$name .= ', ' . substr(stripslashes($contributor->firstName), 0, 1) . '.';
					if ($contributor->middleName)
					{
						$name .= ' ' . substr(stripslashes($contributor->middleName), 0, 1) . '.';
					}
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
			$objO = new \Components\Projects\Tables\Owner($this->_db);
			$this->_owner = $objO->isOwner(User::get('id'), $this->publication->project_id);
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
	public function attachments($reload = false)
	{
		if (!$this->exists())
		{
			return array();
		}
		if (!isset($this->_tblContent))
		{
			$this->_tblContent = new Tables\Attachment( $this->_db );
		}
		if (!isset($this->_attachments) || $reload == true)
		{
			$this->_attachments = $this->_tblContent->sortAttachments ( $this->version->id );
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
		if (!isset($this->_tblLicense))
		{
			$this->_tblLicense = new Tables\License( $this->_db );
		}
		if (!isset($this->_license))
		{
			$this->_license = $this->_tblLicense->getLicense($this->version->license_type);
		}

		return $this->_license;
	}

	/**
	 * Check if the resource was deleted
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function isDeleted()
	{
		if ($this->get('state') == 2)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the draft is ready
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function isReady()
	{
		if ($this->get('state') == 4)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the resource is pending approval
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function isPending()
	{
		if ($this->get('state') == 5)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if the resource is pending author changes
	 *
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function isWorked()
	{
		if ($this->get('state') == 7)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is publication unpublished?
	 *
	 * @return     boolean
	 */
	public function isUnpublished()
	{
		if ($this->get('state') == 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is this main version
	 *
	 * @return     boolean
	 */
	public function isMain()
	{
		if ($this->get('main') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is this main published version?
	 *
	 * @return     boolean
	 */
	public function isCurrent()
	{
		if ($this->get('main') == 1 && $this->get('state') == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Is this dev version
	 *
	 * @return     boolean
	 */
	public function isDev()
	{
		if ($this->get('state') == 3 || $this->versionAlias == 'dev')
		{
			return true;
		}
		return false;
	}

	/**
	 * Does this version have expired unpublished date
	 *
	 * @return     boolean
	 */
	public function isDown()
	{
		if ($this->unpublished() && Date::of($this->get('published_down'))->toUnix() < Date::toUnix())
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
	public function isPublished()
	{
		if (!$this->exists())
		{
			return false;
		}

		if (in_array($this->get('state'), array(0, 2, 3, 4, 5, 6, 7)))
		{
			return false;
		}

		if ($this->published() && $this->isEmbargoed())
		{
			return false;
		}
		if ($this->isDown())
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
		// NOT logged in
		if (User::isGuest())
		{
			// If the resource is published and public
			if ($this->isPublished() && $this->get('master_access') == 0)
			{
				// Allow view access
				$this->params->set('access-view-publication', true);
				if ($this->get('master_access') == 0)
				{
					$this->params->set('access-view-all-publication', true);
				}
			}
			$this->_authorized = true;
			return;
		}

		// Check if they're a site admin (from Joomla)
		$this->params->set('access-admin-publication', User::authorise('core.admin', null));
		$this->params->set('access-manage-publication', User::authorise('core.manage', null));

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
		$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
		$usersgroups = $this->getGroupProperty($ugs);

		// If they're not an admin
		if (!$this->params->get('access-admin-publication')
		 && !$this->params->get('access-manage-publication'))
		{
			// If logged in and resource is published and public or registered
			if ($this->isPublished() && $this->get('master_access') <= 1)
			{
				// Allow view access
				$this->params->set('access-view-publication', true);
				$this->params->set('access-view-all-publication', true);
			}
			// Allowed groups (private access)
			if ($this->get('master_access') >= 2)
			{
				$groups = $this->getAccessGroups();
				$common = array_intersect($usersgroups, $groups);
				if (count($common) > 1)
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
		if ($this->get('curator') && User::get('id') == $this->get('curator'))
		{
			$this->params->set('access-curator-publication', true);
			$this->params->set('access-curator-assigned-publication', true);
		}

		// Curator from groups
		$curatorGroups = $this->curatorGroups();
		if (!empty($curatorGroups))
		{
			$common = array_intersect($usersgroups, $curatorGroups);
			if (count($common) > 1)
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
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!($this->_creator instanceof \Hubzero\User\User))
		{
			$this->_creator = \User::getInstance($this->get('created_by'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber' ? 'id' : $property);
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the author of last change to this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function modifier($property=null)
	{
		if (!($this->_modifier instanceof \Hubzero\User\User))
		{
			$this->_modifier = \User::getInstance($this->get('modified_by'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber' ? 'id' : $property);
			return $this->_modifier ? $this->_modifier->get($property) : NULL;
		}
		return $this->_modifier;
	}

	/**
	 * Get the assigned curator
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @return     mixed
	 */
	public function curator($property=null)
	{
		if (!$this->get('curator'))
		{
			return false;
		}
		if (!($this->_curator instanceof \Hubzero\User\User))
		{
			$this->_curator = \User::getInstance($this->get('curator'));
		}
		if ($property)
		{
			$property = ($property == 'uidNumber' ? 'id' : $property);
			return $this->_curator ? $this->_curator->get($property) : NULL;
		}
		return $this->_curator;
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
			$content = stripslashes($this->get('description'));
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
					Event::trigger('content.onContentPrepare', array(
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
			$content = stripslashes($this->get('release_notes'));

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
					Event::trigger('content.onContentPrepare', array(
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
			$content = stripslashes($this->get($field));

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
					Event::trigger('content.onContentPrepare', array(
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
		if ($this->masterExists())
		{
			$this->publication->store();
			if (!$this->publication->getError())
			{
				return true;
			}
			$this->setError($this->publication->getError());
			return false;
		}
	}

	/**
	 * Get version count
	 *
	 * @return     void
	 */
	public function versionCount()
	{
		return $this->version->getCount($this->get('id'));
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

			$this->_citations = $cc->getCitations( 'publication', $this->get('id') );
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

			$this->_lastCitationDate = $cc->getLastCitationDate( 'publication', $this->get('id') );
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
			$this->_tags = $rt->get_tags_on_object($this->get('id'), 0, 0, $tagger_id, $strength, $admin);
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
	public function getTagsForEditing( $tagger_id = 0, $strength = 0, $admin = 0 )
	{
		if (!$this->exists())
		{
			return false;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php');

		$rt = new Helpers\Tags( $this->_db );
		$this->_tagsForEditing = $rt->get_tag_string( $this->get('id'), 0, 0, $tagger_id, $strength, $admin );
		return $this->_tagsForEditing;
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
			$this->_tagCloud = $rt->get_tag_cloud(0, $admin, $this->get('id'));
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
			$tarname  = Lang::txt('Publication') . '_' . $this->get('id') . '.zip';
			$this->_bundlePath = Helpers\Html::buildPubPath(
				$this->get('id'),
				$this->get('version_id'),
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
		if ($this->get('state') == 3)
		{
			// Draft - load latest version
			$query .= ", (SELECT v.pagetext FROM #__wiki_version as v WHERE v.pageid=p.id
			  ORDER by p.state ASC, v.version DESC LIMIT 1) as pagetext ";
		}
		else
		{
			$date = $this->accepted() ? $this->accepted() : $this->submitted();
			$date = $date ? $date : $this->published();

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

	/**
	 * Get path to specific publication directory
	 *
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function path($type = '', $root = false)
	{
		if (!isset($this->_basePath))
		{
			$this->_basePath = DS . trim($this->config('webpath'), DS) . DS . \Hubzero\Utility\String::pad($this->get('id')) . DS . \Hubzero\Utility\String::pad($this->get('version_id'));
		}
		switch (strtolower($type))
		{
			case 'base':
				$path = $this->_basePath;
			break;

			case 'data':
				$path = $this->_basePath . DS . 'data';
			break;

			case 'logs':
				$path = $this->_basePath . DS . 'logs';
			break;

			case 'content':
			default:
				$path = $this->_basePath . DS . $this->get('secret');
			break;
		}

		return $root ? PATH_APP . $path : $path;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type = '')
	{
		if (!isset($this->_base))
		{
			$this->_base  = 'index.php?option=com_publications';
			$this->_base .= $this->get('alias')
				? '&alias=' . $this->get('alias') : '&id=' . $this->get('id');
		}
		if (!isset($this->_editBase) && strpos($type, 'edit') !== false)
		{
			$this->_editBase  = $this->project()->isProvisioned()
				? 'index.php?option=com_publications&task=submit'
				: 'index.php?option=com_projects&alias=' . $this->project()->get('alias') . '&active=publications';
		}

		switch (strtolower($type))
		{
			case 'category':
				$link = 'index.php?option=com_publications&category=' . $this->category()->url_alias;
			break;

			case 'thumb':
				$link = 'index.php?option=com_publications&id=' . $this->get('id') . '&v=' . $this->get('version_id') . '&media=Image:thumb';
			break;

			case 'masterimage':
				$link = 'index.php?option=com_publications&id=' . $this->get('id') . '&v=' . $this->get('version_id') . '&media=Image:master';
			break;

			case 'serve':
				$link = $this->_base . '&task=serve' . '&v=' . $this->get('version_number');
			break;

			case 'data':
				$link = $this->_base . '&task=serve' . '&vid=' . $this->get('version_id');
			break;

			case 'citation':
				$link = $this->_base . '&task=citation' . '&v=' . $this->get('version_number');
			break;

			case 'curate':
				$link = $this->_base . '&task=curate' . '&version=' . $this->get('version_number');
			break;

			case 'version':
				$link = $this->_base . '&v=' . $this->get('version_number');
			break;

			case 'versionid':
				$link = $this->_base . '&v=' . $this->get('version_id');
			break;

			case 'questions':
			case 'versions':
			case 'supportingdocs':
			case 'reviews':
			case 'wishlist':
			case 'citations':
				$link = $this->_base . '&v=' . $this->get('version_number') . '&active=' . strtolower($type);
			break;

			case 'edit':
				$link = $this->get('id') ? $this->_editBase . '&pid=' . $this->get('id') : $this->_editBase;
			break;

			case 'editversion':
				$link = $this->_editBase . '&pid=' . $this->get('id') . '&version=' . $this->get('version_number');
			break;

			case 'editdev':
				$link = $this->_editBase . '&pid=' . $this->get('id') . '&version=dev';
			break;

			case 'editdefault':
				$link = $this->_editBase . '&pid=' . $this->get('id') . '&version=default';
			break;

			case 'editversionid':
				$link = $this->_editBase . '&pid=' . $this->get('id') . '&vid=' . $this->get('version_id');
			break;

			case 'editbase':
				$link = $this->_editBase;
			break;

			case 'project':
				$link = $this->project()->isProvisioned()
					? 'index.php?option=com_publications&task=submit'
					: 'index.php?option=com_projects&alias=' . $this->project()->get('alias');
			break;

			case 'permalink':
			default:
				$link = $this->_base;
			break;
		}

		return $link;
	}

	/**
	 * Save param
	 *
	 * @param      string 	$param
	 * @param      string 	$value
	 *
	 * @return     void
	 */
	public function saveParam($param = '', $value = '')
	{
		// Clean up incoming
		$param  = \Hubzero\Utility\Sanitize::paranoid($param, array('-', '_'));
		$value  = \Hubzero\Utility\Sanitize::clean($value);

		if (!$this->exists())
		{
			return false;
		}
		if (!$param || !$value)
		{
			return false;
		}

		$this->version->saveParam(
			$this->get('version_id'),
			trim($param),
			htmlentities($value)
		);
		return $value;
	}

	/**
	 * Log access
	 *
	 * @param      string 	$type
	 *
	 * @return     void
	 */
	public function logAccess($type = 'view')
	{
		// Only logging access for published
		if (!$this->isPublished())
		{
			return false;
		}

		if (!isset($this->_tblLog))
		{
			$this->_tblLog = new Tables\Log( $this->_db );
		}

		// Build log path (access logs)
		$logPath = $this->path('logs');

		// Create log directory
		if (!is_dir(PATH_APP . $logPath))
		{
			Filesystem::makeDirectory( PATH_APP . $logPath, 0755, true, true);
		}

		$this->_tblLog->logAccess($this->get('id'), $this->get('version_id'), $type, $logPath);
	}

	/**
	 * Get the group owner of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire Group object
	 *
	 * @return     mixed
	 */
	public function groupOwner($property=null)
	{
		if ($this->project()->groupOwner())
		{
			$this->_groupOwner = $this->project()->groupOwner();
		}
		elseif (!$this->get('group_owner'))
		{
			return false;
		}
		if (!isset($this->_groupOwner) || !($this->_groupOwner instanceof \Hubzero\User\Group))
		{
			$this->_groupOwner = \Hubzero\User\Group::getInstance($this->get('group_owner'));
		}
		if ($property)
		{
			$property = ($property == 'id' ? 'gidNumber' : $property);
			return $this->_groupOwner ? $this->_groupOwner->get($property) : NULL;
		}
		return $this->_groupOwner;
	}

	/**
	 * Get a count of, model for, or list of entries
	 *
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $admin    Admin?
	 * @return  mixed
	 */
	public function entries($rtrn = 'list', $filters = array(), $admin = false)
	{
		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\Publication($this->_db);
		}

		switch (strtolower($rtrn))
		{
			case 'count':
				return (int) $this->_tbl->getCount($filters, $admin);
			break;
		}

		if ($results = $this->_tbl->getRecords($filters, $admin))
		{
			foreach ($results as $key => $result)
			{
				$results[$key] = new self($result);
			}
		}

		return new ItemList($results);
	}

	/**
	 * Check if this is a tool publication
	 *
	 * @return     array
	 */
	public function isTool()
	{
		static $isTool;

		if (!isset($isTool))
		{
			$isTool = false;

			if ($this->category()->alias == 'tool')
			{
				$isTool = true;
			}
		}

		return $isTool;
	}

	/**
	 * Get alias or id
	 *
	 * @return     array
	 */
	public function identifier()
	{
		if ($this->get('alias'))
		{
			return $this->get('alias');
		}

		return $this->get('id');
	}

	/**
	 * Get status name
	 *
	 * @param      int $status
	 * @return     string HTML
	 */
	public function getStatusName($status = NULL)
	{
		if ($status === NULL)
		{
			$status = $this->get('state');
		}

		switch ($status)
		{
			case 0:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED');
				break;

			case 1:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED');
				break;

			case 3:
			default:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT');
				break;

			case 4:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_READY');
				break;

			case 5:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_PENDING');
				break;

			case 7:
				$name = Lang::txt('COM_PUBLICATIONS_VERSION_WIP');
				break;
		}

		return $name;
	}

	/**
	 * Get status name
	 *
	 * @param      int $status
	 * @return     string HTML
	 */
	public function getStatusCss($status = NULL)
	{
		if ($status === NULL)
		{
			$status = $this->get('state');
		}

		switch ($status)
		{
			case 0:
				$name = 'unpublished';
				break;

			case 1:
				$name = 'published';
				break;

			case 3:
			default:
				$name = 'draft';
				break;

			case 4:
				$name = 'ready';
				break;

			case 5:
				$name = 'pending';
				break;

			case 7:
				$name = 'wip';
				break;
		}

		return $name;
	}

	/**
	 * Get status date
	 *
	 * @param      object $row
	 * @return     string HTML
	 */
	public function getStatusDate($row = NULL)
	{
		if ($row === NULL)
		{
			$row    = $this->version;
		}
		$status = $row && isset($row->state) ? $row->state : $this->get('state');

		switch ($status)
		{
			case 0:
				$date = strtolower(Lang::txt('COM_PUBLICATIONS_UNPUBLISHED'))
						. ' ' . Date::of($row->published_down)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;

			case 1:
				$date = (Date::of($row->published_up)->toUnix() > Date::toUnix()) ? Lang::txt('to be') . ' ' : '';
				$date .= strtolower(Lang::txt('COM_PUBLICATIONS_RELEASED'))
					. ' ' . Date::of($row->published_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

				break;

			case 3:
			case 4:
			default:
				$date = strtolower(Lang::txt('COM_PUBLICATIONS_STARTED'))
					. ' ' . Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;

			case 5:
			case 7:
				$date = strtolower(Lang::txt('COM_PUBLICATIONS_SUBMITTED'))
						. ' ' . Date::of($row->submitted)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				break;
		}

		return $date;
	}
}
