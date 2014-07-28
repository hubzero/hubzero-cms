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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Config;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Scaffolding class for generating template extensions
 **/
class Scaffolding extends Base implements CommandInterface
{
	/**
	 * Array of vars to replace in template
	 *
	 * @var array
	 **/
	private $replacements = array();

	/**
	 * Array of template files to parse
	 *
	 * @var array
	 **/
	private $templateFiles = array();

	/**
	 * The type of scaffolding item that we're making
	 *
	 * @var string
	 **/
	private $type = false;

	/**
	 * Whether or not to look for template vars or just do a blind replacement
	 *
	 * @var bool
	 **/
	private $doBlindReplacements = false;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param  object - output renderer
	 * @param  object - command arguments
	 * @return void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);

		$this->type      = $this->arguments->getOpt(3);
	}

	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for scaffolding command
	 *
	 * @return void
	 **/
	public function help()
	{
		if ($this->type)
		{
			$class = __NAMESPACE__ . '\\Scaffolding\\' . ucfirst($this->type);
			$obj   = new $class($this->output, $this->arguments);

			// Call the help method
			$obj->help();
		}
		else
		{
			$this->output = $this->output->getHelpOutput();
			$this
				->output
				->addOverview(
					'Create a new item scaffold. There are currently no arguments available.
					Type "muse scaffolding help [scaffolding type]" for more details.');
			$this->output->render();
		}
	}

	/**
	 * Create a new item from scaffolding templates
	 *
	 * @return void
	 **/
	public function create()
	{
		$class = __NAMESPACE__ . '\\Scaffolding\\' . ucfirst($this->type);

		if (class_exists($class))
		{
			$obj = new $class($this->output, $this->arguments);
		}
		else
		{
			if (empty($this->type))
			{
				$this->output->error('Error: Sorry, scaffolding can\'t create nothing/everything. Try telling it what you want to create.');
			}
			else
			{
				$this->output->error('Error: Sorry, scaffolding doesn\'t know how to create a ' . $this->type);
			}
		}

		// Get author name and email - we'll go ahaed and retrieve for all create calls
		$user_name  = Config::get('user_name');
		$user_email = Config::get('user_email');

		if (!$user_name || !$user_email)
		{
			$this->output
			     ->addSpacer()
			     ->addLine('You can specify your name and email via:')
			     ->addLine(
					'muse configure --name="John Doe"',
					array(
						'indentation' => '2',
						'color'       => 'blue'
					)
				)
				->addLine(
					'muse configure --email=john.doe@gmail.com',
					array(
						'indentation' => '2',
						'color'       => 'blue'
					)
				)
				->addSpacer()
				->error("Error: failed to retrieve author name and/or email.");
		}

		$obj->addReplacement('author_name', $user_name)
			->addReplacement('author_email', $user_email);

		// Call the construct method
		$obj->construct();
	}

	/**
	 * Copy item and attempt to rename appropriatly
	 *
	 * @return void
	 **/
	public function copy()
	{
		$class = __NAMESPACE__ . '\\Scaffolding\\' . ucfirst($this->type);

		if (class_exists($class))
		{
			$obj = new $class($this->output, $this->arguments);
		}
		else
		{
			if (empty($this->type))
			{
				$this->output->error('Error: Sorry, scaffolding can\'t copy nothing. Try telling it what you want to copy.');
			}
			else
			{
				$this->output->error('Error: Sorry, scaffolding doesn\'t know how to copy a ' . $this->type);
			}
		}

		if (!method_exists($obj, 'doCopy'))
		{
			$this->output->error('Error: scaffolding doesn\'t know how to copy a ' . $this->type);
		}

		// Do the actual copy
		$obj->doCopy();
	}

	/**
	 * Get the type of template we're making
	 *
	 * @return (string) $type
	 **/
	protected function getType()
	{
		return $this->type;
	}

	/**
	 * Set blind replacement var
	 *
	 * @return (object) $this - for method chaining
	 **/
	protected function doBlindReplacements()
	{
		$this->doBlindReplacements = true;

		return $this;
	}

	/**
	 * Make template
	 *
	 * @return void
	 **/
	protected function make()
	{
		if (count($this->templateFiles) > 0)
		{
			foreach ($this->templateFiles as $template)
			{
				if (is_dir($template['path']))
				{
					if (!\JFolder::copy($template['path'], $template['destination']))
					{
						$this->output->error("Error: an problem occured copying {$template['path']} to {$template['destination']}.");
					}

					// Get folder contents
					$this->scanFolder($template['destination']);
				}
				elseif (is_file($template['path']))
				{
					if (!copy($template['path'], $template['destination']))
					{
						$this->output->error("Error: an problem occured copying {$template['path']} to {$template['destination']}.");
					}

					// Get template contents
					$contents = file_get_contents($template['destination']);
					$contents = $this->doReplacements($contents);

					// Write file
					$this->putContents($template['destination'], $contents);
				}
			}
		}
	}

	/**
	 * Add a new replacement for the template
	 *
	 * @param  (string) $key
	 * @param  (string) $value
	 * @return (object) $this - for method chaining
	 **/
	protected function addReplacement($key, $value)
	{
		$this->replacements[$key] = $value;

		return $this;
	}

	/**
	 * Add a new template file
	 *
	 * @param  (string) $filename    - template filename
	 * @param  (string) $destination - final location of template file after making
	 * @param  (bool)   $fullPath    - true if full path is given
	 * @return (object) $this        - for method chaining
	 **/
	protected function addTemplateFile($filename, $destination, $fullPath=false)
	{
		$this->templateFiles[] = array(
			'path'        => ((!$fullPath) ? __DIR__ . DS . 'Scaffolding' . DS . 'Templates' . DS . $filename : $filename),
			'destination' => $destination
		);

		return $this;
	}

	/**
	 * Make replacements in a given content string
	 *
	 * @param  (string) $contents - incoming content
	 * @return (string) $contents - outgoing content
	 **/
	private function doReplacements($contents)
	{
		// Replace variables
		if (isset($this->replacements) && count($this->replacements) > 0)
		{
			foreach ($this->replacements as $k => $v)
			{
				if (is_array($v))
				{
					foreach ($v as $replacement)
					{
						foreach ($replacement as $key => $value)
						{
							$contents = preg_replace("/%={$key}=%/", $value, $contents, 1);
						}
					}
				}
				else
				{
					// See if there are any instances of our key with special qualifiers
					if (preg_match_all("/%={$k}\+([[:alpha:]]+)=%/", $contents, $matches) && isset($matches[1]))
					{
						// Remove complete match
						unset($matches[0]);
						foreach ($matches[1] as $match)
						{
							switch ($match)
							{
								// Upper case first character
								case 'ucf':
									$value = ucfirst($v);
									break;
								// Upper case first character and plural
								case 'ucfp':
									$value = ucfirst($this->makePlural($v));
									break;
								// Plural form
								case 'p':
									$value = $this->makePlural($v);
									break;
							}

							$contents = str_replace("%={$k}+{$match}=%", $value, $contents);
						}
					}

					// Now do all basic replacements
					$contents = str_replace("%={$k}=%", $v, $contents);

					if ($this->doBlindReplacements)
					{
						$contents = str_replace($k, $v, $contents);
					}
				}
			}
		}

		// Now parse for nested templates
		preg_match_all('/\$\^[[:alpha:]\.]*\^\$/', $contents, $matches, PREG_SET_ORDER);

		if (isset($matches) && count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				foreach ($match as $key)
				{
					// Get template contents
					$keyReal = preg_replace('/\$\^([[:alpha:]\.]*)\^\$/', '$1', $key);
					$subPath = __DIR__ . DS . 'Scaffolding' . DS . 'Templates' . DS . $this->getType() . '.' . $keyReal . '.tmpl';

					if (!is_file($subPath))
					{
						continue;
					}

					$subTmpl = file_get_contents($subPath);

					if (isset($this->replacements[$keyReal]) && is_array($this->replacements[$keyReal]))
					{
						$count    = count($this->replacements[$keyReal]);
						$repeat   = str_repeat($key, $count);
						$contents = str_replace($key, $repeat, $contents);
					}

					$contents = str_replace($key, $subTmpl, $contents);
					$contents = $this->doReplacements($contents);
				}
			}
		}

		return $contents;
	}

	/**
	 * Write contents out to file
	 *
	 * @param  (string) $path - location of file to put contents
	 * @param  (string) $contents - contents to write to file
	 * @return void
	 **/
	private function putContents($path, $contents)
	{
		file_put_contents($path, $contents);

		// See if we have a .tmpl at the end that we need to remove.
		if (substr($path, -5) == '.tmpl')
		{
			rename($path, substr($path, 0, -5));
			$path = substr($path, 0, -5);
		}

		$info = pathinfo($path);

		// See if we need to do var replacement in actual filename
		if (preg_match("/%=([[:alpha:]_]*)(\+[[:alpha:]]+)?=%/", $info['filename'], $matches) && isset($this->replacements[$matches[1]]))
		{
			$newfile = str_replace($matches[0], $this->replacements[$matches[1]], $info['filename']);

			if (isset($matches[2]))
			{
				$modifier = substr($matches[2], 1);
				switch ($modifier)
				{
					// Upper case first character
					case 'ucf':
						$newfile = ucfirst($newfile);
						break;
					// Upper case first character and plural
					case 'ucfp':
						$newfile = ucfirst($this->makePlural($newfile));
						break;
					// Plural form
					case 'p':
						$newfile = $this->makePlural($newfile);
						break;
				}
			}

			rename($path, $info['dirname'] . DS . $newfile . '.' . $info['extension']);

			$path = $info['dirname'] . DS . $newfile . '.' . $info['extension'];
		}

		$this->output->addLine("Creating {$path}", 'success');
	}

	/**
	 * Scan template folder for files to iterate through
	 *
	 * @param  (string) $path - path of folder to scan
	 * @return void
	 **/
	private function scanFolder($path)
	{
		$files = array_diff(scandir($path), array('.', '..'));

		if ($files && count($files) > 0)
		{
			foreach ($files as $file)
			{
				if (is_file($path . DS . $file))
				{
					$contents = file_get_contents($path . DS . $file);
					$contents = $this->doReplacements($contents);

					$this->putContents($path . DS . $file, $contents);
				}
				else
				{
					$this->scanFolder($path . DS . $file);
				}
			}
		}
	}

	/**
	 * Make a given string plural...trying to account for as many english language constructs as possible
	 *
	 * @param  (string) $string - incoming string
	 * @return (string) $string - plural form of given string
	 **/
	private static function makePlural($string)
	{
		$plural = array(
			array( '/(quiz)$/i',               "$1zes"   ),
			array( '/^(ox)$/i',                "$1en"    ),
			array( '/([m|l])ouse$/i',          "$1ice"   ),
			array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
			array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
			array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
			array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
			array( '/(hive)$/i',               "$1s"     ),
			array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
			array( '/sis$/i',                  "ses"     ),
			array( '/([ti])um$/i',             "$1a"     ),
			array( '/(buffal|tomat)o$/i',      "$1oes"   ),
			array( '/(bu)s$/i',                "$1ses"   ),
			array( '/(alias|status)$/i',       "$1es"    ),
			array( '/(octop|vir)us$/i',        "$1i"     ),
			array( '/(ax|test)is$/i',          "$1es"    ),
			array( '/s$/i',                    "s"       ),
			array( '/$/',                      "s"       )
		);

		$irregular = array(
			array( 'move',   'moves'    ),
			array( 'sex',    'sexes'    ),
			array( 'child',  'children' ),
			array( 'man',    'men'      ),
			array( 'person', 'people'   )
		);

		$uncountable = array(
			'sheep',
			'fish',
			'series',
			'species',
			'money',
			'rice',
			'information',
			'equipment'
		);

		// First, check if singular and plural are the same
		if (in_array(strtolower($string), $uncountable))
		{
			return $string;
		}

		// Now, check for irregular singular forms
		foreach ($irregular as $noun)
		{
			if (strtolower($string) == $noun[0])
			{
				return $noun[1];
			}
		}

		// Finally, check for matches using regular expressions
		foreach ($plural as $pattern)
		{
			if (preg_match($pattern[0], $string))
			{
				return preg_replace($pattern[0], $pattern[1], $string);
			}
		}

		return $string;
	}
}