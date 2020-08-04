<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Config;
use Hubzero\Utility\Inflector;

/**
 * Scaffolding class for generating template extensions
 **/
class Scaffolding extends Base implements CommandInterface
{
	/**
	 * Array of vars to replace in template
	 *
	 * @var  array
	 **/
	private $replacements = array();

	/**
	 * Array of template files to parse
	 *
	 * @var  array
	 **/
	private $templateFiles = array();

	/**
	 * The type of scaffolding item that we're making
	 *
	 * @var  string
	 **/
	private $type = false;

	/**
	 * Whether or not to look for template vars or just do a blind replacement
	 *
	 * @var  bool
	 **/
	private $doBlindReplacements = false;

	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);

		$this->type = $this->arguments->getOpt(3);
	}

	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for scaffolding command
	 *
	 * @return  void
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
	 * @return  void
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
					'muse configuration set --user_name="John Doe"',
					array(
						'indentation' => '2',
						'color'       => 'blue'
					)
				)
				->addLine(
					'muse configuration set --user_email=john.doe@gmail.com',
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
	 * @return  void
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
	 * @return  string
	 **/
	protected function getType()
	{
		return $this->type;
	}

	/**
	 * Set blind replacement var
	 *
	 * @return  $this
	 **/
	protected function doBlindReplacements()
	{
		$this->doBlindReplacements = true;

		return $this;
	}

	/**
	 * Make template
	 *
	 * @return  void
	 **/
	protected function make()
	{
		if (count($this->templateFiles) > 0)
		{
			foreach ($this->templateFiles as $template)
			{
				if (is_dir($template['path']))
				{
					if (!Filesystem::copyDirectory($template['path'], $template['destination']))
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
	 * @param   string  $key    The replacement key
	 * @param   string  $value  The replacement value
	 * @return  $this
	 **/
	protected function addReplacement($key, $value)
	{
		$this->replacements[$key] = $value;

		return $this;
	}

	/**
	 * Add a new template file
	 *
	 * @param   string  $filename     The template filename
	 * @param   string  $destination  Final location of template file after making
	 * @param   bool    $fullPath     True if full path is given
	 * @return  $this
	 **/
	protected function addTemplateFile($filename, $destination, $fullPath = false)
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
	 * @param   string  $contents  Incoming content
	 * @return  string
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
								// Upper case word
								case 'uc':
									$value = strtoupper($v);
									break;
								// Upper case first character
								case 'ucf':
									$value = ucfirst($v);
									break;
								// Upper case first character and plural
								case 'ucfp':
									$value = ucfirst(Inflector::pluralize($v));
									break;
								// Plural form
								case 'p':
									$value = Inflector::pluralize($v);
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
	 * @param   string  $path      Location of file to put contents
	 * @param   string  $contents  Contents to write to file
	 * @return  void
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
					// Upper case word
					case 'uc':
						$value = strtoupper($v);
						break;
					// Upper case first character
					case 'ucf':
						$newfile = ucfirst($newfile);
						break;
					// Upper case first character and plural
					case 'ucfp':
						$newfile = ucfirst(Inflector::pluralize($newfile));
						break;
					// Plural form
					case 'p':
						$newfile = Inflector::pluralize($newfile);
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
	 * @param   string  $path  Path of folder to scan
	 * @return  void
	 **/
	private function scanFolder($path)
	{
		$files = array_diff(scandir($path), array('.', '..', '.DS_Store'));

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
					// See if we need to do var replacement in directory name
					if (preg_match("/%=([[:alpha:]_]*)(\+[[:alpha:]]+)?=%/", $path . DS . $file, $matches) && isset($this->replacements[$matches[1]]))
					{
						$newfile = str_replace($matches[0], $this->replacements[$matches[1]], $file);

						if (isset($matches[2]))
						{
							$modifier = substr($matches[2], 1);
							switch ($modifier)
							{
								// Upper case word
								case 'uc':
									$value = strtoupper($v);
									break;
								// Upper case first character
								case 'ucf':
									$newfile = ucfirst($newfile);
									break;
								// Upper case first character and plural
								case 'ucfp':
									$newfile = ucfirst(Inflector::pluralize($newfile));
									break;
								// Plural form
								case 'p':
									$newfile = Inflector::pluralize($newfile);
									break;
							}
						}

						rename($path. DS . $file, $path . DS . $newfile);
						$file = $newfile;
					}

					$this->scanFolder($path . DS . $file);
				}
			}
		}
	}
}
