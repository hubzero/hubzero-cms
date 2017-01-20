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
 * @package    hubzero-cms
 * @author     Shawn Rice <zooley@purdue.edu>
 * @copyright  Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Components\Citations\Tables;
use Hubzero\Utility\Date;
use Hubzero\Base\Object;
use App;

include_once (dirname(__DIR__) . DS . 'tables' . DS . 'citation.php');
include_once (dirname(__DIR__) . DS . 'tables' . DS . 'type.php');
include_once (dirname(__DIR__) . DS . 'tables' . DS . 'tags.php');

/**
 * Citation importer
 */
class Importer extends Object
{
	/**
	 * Database connection
	 *
	 * @var  object
	 */
	protected $database = null;

	/**
	 * Filesystem
	 *
	 * @var  object
	 */
	protected $filesystem = null;

	/**
	 * The tmp file path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * Unique string
	 *
	 * @var  string
	 */
	protected $hash = null;

	/**
	 * Allow tags?
	 *
	 * @var  boolean
	 */
	protected $tags = false;

	/**
	 * Allow badges?
	 *
	 * @var  boolean
	 */
	protected $badges = false;

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @param   object   $database
	 * @param   object   $filesystem
	 * @param   string   $path
	 * @param   string   $hash
	 * @param   boolean  $tags
	 * @param   boolean  $badges
	 * @return  void
	 */
	public function __construct($database, $filesystem, $path, $hash = null, $tags = false, $badges = false)
	{
		if (!$database)
		{
			$database = App::get('db');
		}

		if (!$filesystem)
		{
			$filesystem = App::get('filesystem');
		}

		if (!$path)
		{
			$path = App::get('config')->get('tmp_path') . DS . 'citations';
		}

		if (!$hash)
		{
			$hash = App::get('session')->getId();
		}

		$this->database   = $database;
		$this->filesystem = $filesystem;

		$this->setTmpPath($path);
		$this->setHash($hash);

		$this->setTags($tags);
		$this->setBadges($badges);
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

		$tmp = $this->filesystem->files($p);

		if ($tmp)
		{
			foreach ($tmp as $t)
			{
				$ft = filemtime($p . DS . $t);

				if ($ft < strtotime("-1 DAY") || $force)
				{
					$this->filesystem->delete($p . DS . $t);
				}
			}
		}
	}

	/**
	 * Set if tags are allowed
	 *
	 * @param   boolean  $val
	 * @return  object
	 */
	public function setTags($val)
	{
		$this->tags = $val;

		return $this;
	}

	/**
	 * Set if badges are allowed
	 *
	 * @param   boolean  $val
	 * @return  object
	 */
	public function setBadges($val)
	{
		$this->badges = $val;

		return $this;
	}

	/**
	 * Set the path to temp files
	 *
	 * @param   string  $path
	 * @return  object
	 */
	public function setTmpPath($path)
	{
		if (!is_dir($path))
		{
			$this->filesystem->makeDirectory($path, 0755);
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Set a unique hash (for tmp file names)
	 *
	 * @param   string  $hash
	 * @return  object
	 */
	public function setHash($hash)
	{
		$this->hash = (string) $hash;

		return $this;
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
		return $this->getTmpPath() . DS . 'citations_require_attention_' . $this->hash . '.txt';
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function writeRequiresAttention($data)
	{
		$p = $this->getRequiresAttentionPath();

		return $this->filesystem->write($p, serialize($data));
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
			$citations = unserialize($this->filesystem->read($p));
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
		return $this->getTmpPath() . DS . 'citations_require_no_attention_' . $this->hash . '.txt';
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @return  array
	 */
	public function writeRequiresNoAttention($data)
	{
		$p = $this->getRequiresNoAttentionPath();

		return $this->filesystem->write($p, serialize($data));
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
			$citations = unserialize($this->filesystem->read($p));
		}

		return $citations;
	}

	/**
	 * Get a list of citations requiring attention
	 *
	 * @param   array  $action_attention
	 * @param   array  $action_no_attention
	 * @return  array
	 */
	public function process($action_attention = array(), $action_no_attention = array())
	{
		$results = array(
			'saved'     => array(),
			'not_saved' => array(),
			'error'     => array()
		);

		$now       = with(new Date('now'))->toSql();
		$user      = $this->get('user');
		$scope     = $this->get('scope');
		$scope_id  = $this->get('scope_id');
		$published = $this->get('published', null);

		// loop through each citation that required attention from user
		if ($cites_require_attention = $this->readRequiresAttention())
		{
			foreach ($cites_require_attention as $k => $cra)
			{
				$cc = new Tables\Citation($this->database);

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
				$ct = new Tables\Type($this->database);
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
				if ($scope)
				{
					$cra['scope'] = $scope;
				}

				if ($scope_id)
				{
					$cra['scope_id'] = $scope_id;
				}

				if (!is_null($published))
				{
					$cra['published'] = $published;
				}

				// save the citation
				if (!$cc->save($cra))
				{
					$results['error'][] = $cra;
				}
				else
				{
					// tags
					if ($this->tags && isset($tags))
					{
						$this->tag($user, $cc->id, $tags, '');
					}

					// badges
					if ($this->badges && isset($badges))
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
				$cc = new Tables\Citation($this->database);

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
				$ct = new Tables\Type($this->database);
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

				// sets group if set
				if ($scope)
				{
					$crna['scope'] = $scope;
				}

				if ($scope_id)
				{
					$crna['scope_id'] = $scope_id;
				}

				if (!is_null($published))
				{
					$crna['published'] = $published;
				}

				// save the citation
				if (!$cc->save($crna))
				{
					$results['error'][] = $crna;
				}
				else
				{
					// tags
					if ($this->tags && isset($tags))
					{
						$this->tag($user, $cc->id, $tags, '');
					}

					// badges
					if ($this->badges && isset($badges))
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
			$ct = new Tables\Tags($objectid);
			$ct->setTags($tag_string, $userid, 0, 1, $label);
		}
	}
}
