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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Document\Asset;

use Hubzero\Base\Object;

/**
 * Base asset class
 */
class File extends Object
{
	/**
	 * Asset type
	 *
	 * @var  string
	 */
	protected $type = null;

	/**
	 * Asset name (without file extension)
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Asset directory
	 *
	 * @var  string
	 */
	protected $directory = null;

	/**
	 * CMS Extension name (e.g., com_blog)
	 *
	 * @var  string
	 */
	protected $extension = null;

	/**
	 * CMS Extension type
	 *
	 * @var  string
	 */
	protected $kind = null;

	/**
	 * Is a declaration?
	 *
	 * Declarations do not have files or paths.
	 * These will be raw scripts or styles.
	 *
	 * @var  string
	 */
	protected $declaration = false;

	/**
	 * Asset paths
	 *
	 * @var  array
	 */
	protected $paths = array(
		'source'   => null,
		'override' => null,
		'target'   => null
	);

	/**
	 * Constructor
	 *
	 * @param   string  $extension  CMS Extension to load asset from
	 * @param   string  $name       Asset name (optional)
	 * @return  void
	 */
	public function __construct($extension, $name=null)
	{
		$this->extension = strtolower(trim((string) $extension));

		switch (substr($this->extension, 0, 4))
		{
			case 'com_':
				$this->kind = 'components';
				$name = $name ?: substr($this->extension, 4);
			break;
			case 'mod_':
				$this->kind = 'modules';
				$name = $name ?: $this->extension;
			break;
			case 'plg_':
				$this->kind = 'plugins';
				if (!$name)
				{
					$parts = explode('_', $this->extension);
					$name = $parts[2];
				}
			break;
			case 'tpl_':
				$this->kind = 'templates';
			break;
			default:
				if ($this->extension == 'system')
				{
					$this->kind = $this->extension;
				}
			break;
		}

		$this->name = $name;

		if (strstr($this->name, '.'))
		{
			if (strtolower(\App::get('filesystem')->extension($name)) == $this->type)
			{
				$this->name = \App::get('filesystem')->name($this->name);
			}
		}

		$this->directory = $this->dir($this->name, $this->type());
	}

	/**
	 * Determine the asset directory
	 *
	 * @param   string  $name     Asset path
	 * @param   string  $default  Default directory
	 * @return  string
	 */
	protected function dir(&$name, $default='')
	{
		if (substr($name, 0, 2) == './')
		{
			$name = substr($name, 2);

			return '';
		}

		if (substr($name, 0, 1) == '/')
		{
			$name = substr($name, 1);

			return '/';
		}

		return $default;
	}

	/**
	 * Get the asset type
	 *
	 * @return  string
	 */
	public function type()
	{
		return $this->type;
	}

	/**
	 * Get the CMS extension name
	 * [mod_*, com_*, plg_folder_type, tpl_*, system]
	 *
	 * @return  string
	 */
	public function extensionName()
	{
		return $this->extension;
	}

	/**
	 * Get the CMS extension type
	 * [modules, plugins, components, templates, system]
	 *
	 * @return  string
	 */
	public function extensionType()
	{
		return $this->kind;
	}

	/**
	 * Get the file name
	 *
	 * @return  string
	 */
	public function file()
	{
		return $this->name . '.' . $this->type;
	}

	/**
	 * Is the asset a declaration?
	 *
	 * @return  boolean
	 */
	public function isDeclaration()
	{
		return $this->declaration;
	}

	/**
	 * Adds to the list of paths
	 *
	 * @param   string  $type  The type of path to add.
	 * @param   mixed   $path  The directory or stream, or an array of either, to search.
	 * @return  object
	 */
	public function setPath($type, $path)
	{
		if (!array_key_exists($type, $this->paths))
		{
			throw new \Exception(\App::get('language')->txt('Unknown asset path type of %s given.', $type));
		}

		$path = trim((string) $path);

		// Add separators as needed
		$path = DS . trim($path, DS);

		// Add to list of paths
		$this->paths[$type] = $path;

		return $this;
	}

	/**
	 * Get the source path
	 *
	 * @return  string
	 */
	public function sourcePath()
	{
		if (!isset($this->paths['source']))
		{
			// If loading from an absolute path
			if ($this->directory == '/')
			{
				$this->paths['source'] = PATH_APP . DS . $this->directory . $this->file();
			}
			else
			{
				$paths = array();
				$base  = JPATH_SITE;
				$client = 'site';

				if ($this->extensionType() == 'plugins')
				{
					$parts = explode('_', $this->extensionName());
					$base .= DS . $this->extensionType() . DS . $parts[1] . DS . $parts[2];
				}
				else if ($this->extensionType() == 'system')
				{
					$base .= DS . 'media' . DS . $this->extensionType();
					$paths[] = $base . DS . $this->directory . DS . $this->file();
				}
				else
				{
					if (\App::isAdmin())
					{
						$base = JPATH_ADMINISTRATOR;
						$client = 'admin';
					}
					$base .= DS . $this->extensionType() . DS . $this->extensionName();
				}

				if ($this->extensionType() == 'components')
				{
					$paths[] = PATH_ROOT . DS . $this->extensionType() . DS . $this->extensionName() . DS . $client . DS . 'assets' . ($this->directory ? DS . $this->directory : '') . DS . $this->file();
				}
				if ($this->extensionType() == 'modules')
				{
					$paths[] = PATH_ROOT . DS . $this->extensionType() . DS . $this->extensionName() . DS . 'assets' . ($this->directory ? DS . $this->directory : '') . DS . $this->file();
				}
				$paths[] = $base . DS . 'assets' . ($this->directory ? DS . $this->directory : '') . DS . $this->file();
				$paths[] = $base . DS . $this->file();

				// Run through each path until we find one that works
				foreach ($paths as $path)
				{
					if (file_exists($path))
					{
						$this->paths['source'] = $path;
						break;
					}
				}
			}
		}
		return $this->paths['source'];
	}

	/**
	 * Get the override path
	 *
	 * @return  string
	 */
	public function overridePath()
	{
		if (!isset($this->paths['override']))
		{
			$this->paths['override']  = JPATH_BASE . DS . 'templates' . DS . \App::get('template')->template . DS . 'html';
			$this->paths['override'] .= DS . $this->extensionName() . DS . ($this->extensionType() == 'system' ? $this->type() . DS : '') . $this->file();
		}
		return $this->paths['override'];
	}

	/**
	 * Get the target path
	 *
	 * @return  string
	 */
	public function targetPath()
	{
		if (!isset($this->paths['target']))
		{
			if ($this->declaration)
			{
				$this->paths['target'] = '';
			}
			else
			{
				$this->paths['target'] = $this->sourcePath();

				if ($this->overridePath() && file_exists($this->overridePath()))
				{
					$this->paths['target'] = $this->overridePath();
				}
			}
		}

		return $this->paths['target'];
	}

	/**
	 * Get the last modified time for a file
	 *
	 * @return  integer
	 */
	public function lastModified()
	{
		$source = $this->targetPath();

		if ($this->declaration || !is_file($source))
		{
			return 0;
		}

		return filemtime($source);
	}

	/**
	 * Does asset exist?
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		if ($this->declaration && $this->name)
		{
			return true;
		}

		if ($this->targetPath() && file_exists($this->targetPath()))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get public asset path
	 *
	 * @param   boolean  $timestamp  Append timestamp?
	 * @return  string
	 */
	public function link($timestamp=true)
	{
		$output = $this->targetPath();

		if (!$output)
		{
			return $output;
		}

		if ($this->extensionType() == 'system')
		{
			$relative = rtrim(str_replace('/administrator', '', \Request::base(true)), '/') . substr($output, strlen(JPATH_ROOT));
		}
		else
		{
			if (substr($output, 0, strlen(JPATH_BASE)) == JPATH_BASE)
			{
				$relative = rtrim(\Request::base(true), '/') . substr($output, strlen(JPATH_BASE));
			}
			else
			{
				$relative = rtrim(str_replace('/administrator', '', \Request::base(true)), '/') . substr($output, strlen(JPATH_SITE));
			}
		}

		return $relative . ($timestamp ? '?v=' . $this->lastModified() : '');
	}

	/**
	 * Get target asset's content
	 *
	 * @return  string
	 */
	public function contents()
	{
		if ($this->declaration)
		{
			return $this->name;
		}

		return file_exists($this->targetPath()) ? file_get_contents($this->targetPath()) : '';
	}

	/**
	 * Convert to string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) ($this->declaration ? $this->name : $this->link());
	}
}
