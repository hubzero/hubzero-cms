<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author	Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since	 Class available since release 1.3.2
 */

namespace Components\Citations\Models;

use Components\Citations\Tables\Citation;
use Components\Citations\Tables\Type;
use Components\Citations\Tables\Tags;
use Hubzero\Base\Object;
use Filesystem;
use Date;

/**
 * Citation importer
 */
class Import extends Object
{
	/**
	 * The tmp file path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * The session ID
	 *
	 * @var  string
	 */
	protected $session = null;

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @param   string  $path
	 * @param   string  $session
	 * @return  void
	 */
	public function __construct($path, $session)
	{
		$this->setTmpPath($path);

		$this->session = $session;
	}

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function cleanup($force = false)
	{
		$p = $this->getTmpPath();

		if (!is_dir($p))
		{
			return;
		}

		$tmp = Filesystem::files($p);

		if ($tmp)
		{
			foreach ($tmp as $t)
			{
				$ft = filemtime($p . DS . $t);

				if ($ft < strtotime("-1 DAY") || $force)
				{
					Filesystem::delete($p . DS . $t);
				}
			}
		}
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function setTmpPath($path)
	{
		if (!is_dir($path))
		{
			Filesystem::makeDirectory($path, 0755);
		}

		$this->path = $path;
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function getTmpPath()
	{
		return $this->path;
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  string
	 */
	protected function getRequiresAttentionPath()
	{
		return $this->getTmpPath() . DS . 'citations_require_attention_' . $this->session . '.txt';
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function writeRequiresAttention($data)
	{
		$p = $this->getRequiresAttentionPath();

		return Filesystem::write($p, serialize($data));
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function readRequiresAttention()
	{
		$p = $this->getRequiresAttentionPath();

		$citations = null;

		if (file_exists($p))
		{
			$citations = unserialize(Filesystem::read($p));
		}

		return $citations;
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  string
	 */
	protected function getRequiresNoAttentionPath()
	{
		return $this->getTmpPath() . DS . 'citations_require_no_attention_' . $this->session . '.txt';
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function writeRequiresNoAttention($data)
	{
		$p = $this->getRequiresNoAttentionPath();

		return Filesystem::write($p, serialize($data));
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function readRequiresNoAttention()
	{
		$p = $this->getRequiresNoAttentionPath();

		$citations = null;

		if (file_exists($p))
		{
			$citations = unserialize(Filesystem::read($p));
		}

		return $citations;
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function process($action_attention = array(), $action_no_attention = array())
	{
		$results = array(
			'saved'     => array(),
			'not_saved' => array(),
			'error'     => array()
		);

		$now    = Date::toSql();
		$user   = $this->get('user');
		$tags   = $this->get('tags');
		$badges = $this->get('badges');

		// loop through each citation that required attention from user
		if ($cites_require_attention = $this->readRequiresAttention())
		{
			foreach ($cites_require_attention as $k => $cra)
			{
				$cc = new Citation($this->database);

				// add a couple of needed keys
				$cra['uid']     = $user;
				$cra['created'] = $now;

				// reset tags and badges
				$tags = '';
				$badges = '';

				// remove errors
				unset($cra['errors']);

				// if tags were sent over
				if (array_key_exists('tags', $cra))
				{
					$tags = $cra['tags'];
					unset($cra['tags']);
				}

				// if badges were sent over
				if (array_key_exists('badges', $cra))
				{
					$badges = $cra['badges'];
					unset($cra['badges']);
				}

				//take care fo type
				$ct = new Type($this->database);
				$types = $ct->getType();

				$type = '';
				foreach ($types as $t)
				{
					if (strtolower($t['type_title']) == strtolower($cra['type']))
					{
						$type = $t['id'];
					}
				}
				$cra['type'] = ($type) ? $type : '1';

				switch ($action_attention[$k])
				{
					case 'overwrite':
						$cra['id'] = $cra['duplicate'];
					break;

					case 'both':
					break;

					case 'discard':
						$results['not_saved'][] = $cra;
						continue 2;
					break;
				}

				// remove duplicate flag
				unset($cra['duplicate']);

				//sets group if set
				if ($scope = $this->get('scope'))
				{
					$cra['scope'] = $scope;
				}

				if ($scope_id = $this->get('scope_id'))
				{
					$cra['scope_id'] = $scope_id;
				}

				// save the citation
				if (!$cc->save($cra))
				{
					$results['error'][] = $cra;
				}
				else
				{
					// tags
					if ($allow_tags == 'yes' && isset($tags))
					{
						$this->tag($user, $cc->id, $tags, '');
					}

					// badges
					if ($allow_badges == 'yes' && isset($badges))
					{
						$this->tag($user, $cc->id, $badges, 'badge');
					}

					// add the citattion to the saved
					$results['saved'][] = $cc->id;
				}
			}
		}

		if ($cites_require_no_attention = $this->readRequiresNoAttention())
		{
			foreach ($cites_require_no_attention as $k => $crna)
			{
				// new citation object
				$cc = new Citation($this->database);

				// add a couple of needed keys
				$crna['uid'] = $user;
				$crna['created'] = $now;

				// reset tags and badges
				$tags = '';
				$badges = '';

				// remove errors
				unset($crna['errors']);

				// if tags were sent over
				if (array_key_exists('tags', $crna))
				{
					$tags = $crna['tags'];
					unset($crna['tags']);
				}

				// if badges were sent over
				if (array_key_exists('badges', $crna))
				{
					$badges = $crna['badges'];
					unset($crna['badges']);
				}

				// verify we haad this one checked to be submitted
				if ($action_no_attention[$k] != 1)
				{
					$results['not_saved'][] = $crna;
					continue;
				}

				// take care fo type
				$ct = new Type($this->database);
				$types = $ct->getType();

				$type = '';
				foreach ($types as $t)
				{
					// TODO: undefined index type? I just suppressed the error b/c I'm not sure what the logic is supposed to be /SS
					if (strtolower($t['type_title']) == strtolower($crna['type']))
					{
						$type = $t['id'];
					}
				}
				$crna['type'] = ($type) ? $type : '1';

				// remove duplicate flag
				unset($crna['duplicate']);

				//sets group if set
				$group = Request::getVar('group');
				if (isset($group) && $group != '')
				{
					$crna['gid'] = $group;
					$crna['scope'] = 'group';
				}

				// save the citation
				if (!$cc->save($crna))
				{
					$results['error'][] = $crna;
				}
				else
				{
					// tags
					if ($allow_tags == 'yes' && isset($tags))
					{
						$this->tag($user, $cc->id, $tags, '');
					}

					// badges
					if ($allow_badges == 'yes' && isset($badges))
					{
						$this->tag($user, $cc->id, $badges, 'badge');
					}

					// add the citattion to the saved
					$results['saved'][] = $cc->id;
				}
			}
		}

		$this->cleanup();

		return $results;
	}

	/**
	 * Add tags to a citation
	 *
	 * @param   integer  $userid      User ID
	 * @param   integer  $objectid    Citation ID
	 * @param   string   $tag_string  Comma separated list of tags
	 * @param   string   $label       Label
	 * @return  void
	 */
	protected function tag($userid, $objectid, $tag_string, $label)
	{
		if ($tag_string)
		{
			$ct = new Tags($objectid);
			$ct->setTags($tag_string, $userid, 0, 1, $label);
		}
	}
}
