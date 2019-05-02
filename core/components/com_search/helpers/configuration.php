<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Hubzero\Utility\Date;
use Hubzero\User\Group;

/**
 * Search config helper
 */
class ConfigHelper
{
	/**
	 * loadConfig 
	 * 
	 * @param string $name 
	 * @access public
	 * @return void
	 */
	public function loadConfig($name = '')
	{

		$baseDir = PATH_APP . DS . 'config' . DS . 'search' . DS . 'types';

		if ($name == '')
		{
			$configFiles = Filesystem::listContents($baseDir);
			$config = array();

			foreach ($configFiles as $file)
			{
				if (Filesystem::extension($baseDir . $file['path']) == 'php')
				{
					$typeConfig = require_once($baseDir . $file['path']);
					array_push($config, $typeConfig);
				}
			}
			return $config;
		}
		elseif ($name != '')
		{
			$filename = $baseDir . DS . $name . '.php';
			if (Filesystem::exists($filename))
			{
				$config = include($filename);
			}
			else
			{
				$config = false;
			}
			return $config;
		}
		return false;
	}

	/**
	 * getFilePath 
	 * 
	 * @param   string  $name 
	 * @access  public
	 * @return  mixed   String on success, FALSE on failure
	 */
	public function getFilePath($name = '')
	{
		if ($name != '')
		{
			$baseDir = PATH_APP . DS . 'config' . DS . 'search' . DS . 'types';
			$filename = $baseDir . DS . $name . '.php';
			if (Filesystem::exists($filename))
			{
				$config = include_once $filename;
				$classpath = $config['filepath'];
				return $classpath;
			}
			return false;
		}

		return false;
	}

	/**
	 * instantiate 
	 * 
	 * @param   string  $name 
	 * @access  public
	 * @return  mixed   Object on success, FALSE on failure
	 */
	public function instantiate($name = '')
	{
		if ($name != '')
		{
			require_once PATH_ROOT . DS . $this->getFilePath($name);
			$class = $this->getClassPath($name);

			return new $class;
		}

		return false;
	}

	/**
	 * getClassPath 
	 * 
	 * @param   string  $name 
	 * @access  public
	 * @return  mixed   Object on success, FALSE on failure
	 */
	public function getClassPath($name = '')
	{
		if ($name != '')
		{
			$baseDir = PATH_APP . DS . 'config' . DS . 'search' . DS . 'types';
			$filename = $baseDir . DS . $name . '.php';
			if (Filesystem::exists($filename))
			{
				$config = include_once $filename;
				$classpath = $config['classpath'];
				return $classpath;
			}
			return false;
		}

		return false;
	}

	/**
	 * parse 
	 * Borrowed from Plugin\Content\Formathtml\Parser
	 * Used to do some processing on config file 'macros'
	 * 
	 * @param   mixed   $text 
	 * @param   mixed   $row 
	 * @access  public
	 * @return  string
	 */
	public function parse($text, $row)
	{
		// Remove any trailing whitespace
		$text = rtrim($text);

		// Prepend a line break
		// Makes block parsing a little easier
		$text = "\n" . $text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r", '', $text);
		$text = preg_replace('/<p>\s*?(\[\[[^\]]+\]\])\s*?<\/p>/i', "\n$1\n", $text);
		$text = preg_replace('/<p>(\[\[[^\]]+\]\])\n/i', "$1\n<p>", $text);
		$matches = array();
		preg_match_all('/\[\[(?P<macroname>[\w.]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U', $text, $matches);

		// Copy the original string
		$path = $text;

		// Build out the path
		foreach ($matches['macroname'] as $k => $match)
		{
			$macroname = $match;
			$argument = $matches['macroargs'][$k];
			$replacement = $this->$macroname($argument, $row);
			$path = (preg_replace("(\\[\[".$macroname."\(".$argument."\)\\]\])", $replacement, $path));
		}
		return $path;
	}

	/**
	 * processPaths 
	 * 
	 * @param   string  $type 
	 * @param   mixed   $row 
	 * @access  public
	 * @return  string
	 */
	public function processPaths($type = '', $row)
	{
		$config = $this->loadConfig($type);
		foreach ($config['paths'] as $scope => $path)
		{
			if ($scope == $row->scope)
			{
				$parsed = $this->parse($path, $row);
				return $parsed;
			}
		}
		return '';
	}

	/**
	 * Year
	 * 
	 * @param   string   $date 
	 * @param   mixed    $row 
	 * @access  private
	 * @return  string
	 */
	private function Year($date = '', $row)
	{
		$date = $row->$date;
		return Date::of(strtotime($date))->toLocal('Y');
	}

	/**
	 * Month
	 * 
	 * @param   string   $date 
	 * @param   mixed    $row 
	 * @access  private
	 * @return  string
	 */
	private function Month($date = '', $row)
	{
		$date = $row->$date;
		return Date::of(strtotime($date))->toLocal('m');
	}

	/**
	 * Field
	 * 
	 * @param   string   $argument 
	 * @param   mixed    $row 
	 * @access  private
	 * @return  string
	 */
	private function Field($argument = '', $row)
	{
		$argument = $row->$argument;
		return $argument;
	}

	/**
	 * Group_cn
	 * 
	 * @param   mixed    $id 
	 * @param   mixed    $row 
	 * @access  private
	 * @return  string
	 */
	private function Group_cn($id, $row)
	{
		$group = Group::getInstance($id);
		return $group->get('cn');
	}

	/**
	 * Member_id
	 * 
	 * @param   mixed    $id 
	 * @param   mixed    $row 
	 * @access  private
	 * @return  int
	 */
	private function Member_id($id, $row)
	{
		$id = $row->scope_id;
		return $id;
	}
}
