<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml;

/**
 * Convert macros to HTML
 */
class Parser
{
	/**
	 * A unique token
	 *
	 * @var  string
	 */
	private $_tokenPrefix = null;

	/**
	 * Configuration options
	 *
	 * @var  array
	 */
	private $_config = array(
		'option'    => null,
		'scope'     => null,
		'pagename'  => null,
		'pageid'    => null,
		'filepath'  => null,
		'domain'    => null,
		'fullparse' => true,
	);

	/**
	 * Data store
	 *
	 * @var  array
	 */
	private $_data = array(
		'input'  => null,
		'output' => null,
		'links'  => array()
	);

	/**
	 * Parsed content temp storage
	 *
	 * @var  array
	 */
	private $_tokens = array(
		'pre'    => array(),
		'code'   => array(),
		'macro'  => array()
	);

	/**
	 * Configuration options
	 *
	 * @var  array
	 */
	private $_macroPaths = array();

	/**
	 * List of used macros
	 *
	 * @var  array
	 */
	private static $macros = array();

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration options
	 * @return  void
	 */
	public function __construct($config=array())
	{
		if (is_array($config))
		{
			// We need this info for links that may get generated
			foreach ($config as $k => $s)
			{
				if ($k == 'alt_macro_path')
				{
					$this->addMacroPath($s);
					continue;
				}

				$this->set($k, $s);
			}
		}

		// Set the unique token prefix
		$this->_tokenPrefix = "\x07UNIQ";
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  object  Chainable
	 */
	public function set($property, $value = null)
	{
		$this->_config[$property] = $value;
		return $this;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed   The value of the property.
	 */
	public function get($property, $default = null)
	{
		if (isset($this->_config[$property]))
		{
			return $this->_config[$property];
		}
		return $default;
	}

	/**
	 * Add a macro path
	 *
	 * @param   string  $path
	 * @return  object
	 */
	public function addMacroPath($path)
	{
		$path = trim((string)$path);

		if ($path)
		{
			// Make sure paths start with a slash and do NOT end with one
			$path = DS . trim($path, DS);

			// If the path isn't absolute, make it so
			if (substr($path, 0, strlen(PATH_ROOT)) != PATH_ROOT)
			{
				$path = PATH_ROOT . $path;
			}

			$this->_macroPaths[] = $path;
		}

		return $this;
	}

	/**
	 * Get macro paths
	 *
	 * @return  array
	 */
	public function getMacroPaths()
	{
		return $this->_macroPaths;
	}

	/**
	 * Clear set macro paths
	 *
	 * @return  object
	 */
	public function clearMacroPaths()
	{
		$this->_macroPaths = array();

		return $this;
	}

	/**
	 * Get the unique string
	 *
	 * @return  string
	 */
	public function token()
	{
		return $this->_tokenPrefix . $this->_randomString();
	}

	/**
	 * Get the raw input
	 *
	 * @return  string
	 */
	public function input()
	{
		return $this->_data['input'];
	}

	/**
	 * Get the parsed output
	 *
	 * @return  string
	 */
	public function output()
	{
		return $this->_data['output'];
	}

	/**
	 * Generate a unique prefix
	 *
	 * @return  integer
	 */
	private function _randomString()
	{
		return dechex(mt_rand(0, 0x7fffffff)) . dechex(mt_rand(0, 0x7fffffff));
	}

	/**
	 * Parse macros
	 *
	 * @param   string   $text       Raw markup
	 * @param   boolean  $fullparse  Full or limited parse? Limited does not parse macros
	 * @param   integer  $linestart
	 * @param   integer  $camelcase
	 * @return  string
	 */
	public function parse($text, $fullparse=true, $linestart=0, $camelcase=1)
	{
		// Store the raw input
		$this->_data['input'] = $text;

		// Remove any trailing whitespace
		$text = rtrim($text);

		// Prepend a line break
		// Makes block parsing a little easier
		$text = "\n" . $text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r", '', $text);

		// Strip out <pre> code
		// We'll put this back after other processes
		$text = $this->strip($text);

		$text = preg_replace('/<p>\s*?(\[\[[^\]]+\]\])\s*?<\/p>/i', "\n$1\n", $text);

		$text = preg_replace('/<p>(\[\[[^\]]+\]\])\n/i', "$1\n<p>", $text);

		// Process macros
		// Individual macros determine if they're allowed in fullparse mode or not
		$text = $this->macros($text);

		// Unstrip macro blocks BEFORE doing block levels or <p> tags will get messy
		//$text = preg_replace_callback('/MACRO' . $this->_token . '/i', array(&$this, 'restore_macros'), $text);
		$text = preg_replace_callback('/<(macro) (.+?)>(.*)<\/(\1) \2>/si', array(&$this, '_dataPull'), $text);
		$this->_tokens['macro'] = array();

		// Put back removed blocks <pre>, <code>, <a>, <math>
		$text = $this->unstrip($text);

		$text = $this->post($text);

		$this->_data['output'] = $text;

		if (trim($this->_data['input']) && !trim($this->_data['output']))
		{
			$this->_data['output']  = '<p class="warning">Parsing error resulted in empty content. Displaying raw markup below.</p>';
			$this->_data['output'] .= '<pre>' . htmlentities($this->_data['input'], ENT_COMPAT, 'UTF-8') . '</pre>';
		}

		return $this->_data['output'];
	}

	/**
	 * Strip <pre> and <code> blocks from text
	 *
	 * @param   string  $text  markup
	 * @return  string
	 */
	private function strip($text)
	{
		$text = preg_replace_callback('/<(pre)(.*?)>(.+?)<\/(pre)>/is', array(&$this, '_dataPush'), $text);

		$text = preg_replace_callback('/<(code)(.*?)>(.+)<\/(code)>/iU', array(&$this, '_dataPush'), $text);

		return $text;
	}

	/**
	 * Store an item in the shelf
	 * Returns a unique ID as a placeholder for content retrieval later on
	 *
	 * @param   array    $matches
	 * @return  integer  Unique ID
	 */
	private function _dataPush($matches)
	{
		$tag = $matches[1];
		$key = $this->token();
		$val = $matches[0];

		if ($tag == 'macro')
		{
			$key = $matches[2];
			$val = $matches[3];
		}

		$this->_tokens[$tag][$key] = $val;

		return '<' . $tag . ' ' . $key . '></' . $tag . ' ' . $key . '>';
	}

	/**
	 * Store an item in the shelf
	 * Returns a unique ID as a placeholder for content retrieval later on
	 *
	 * @param   string   $matches  Content to store
	 * @return  integer  Unique ID
	 */
	private function _dataPull($matches)
	{
		$tag = $matches[1];
		$key = $matches[2];

		if (isset($this->_tokens[$tag]) && isset($this->_tokens[$tag][$key]))
		{
			return $this->{'_restore' . ucfirst($tag)}($this->_tokens[$tag][$key]);
		}
		if ($tag == 'macro')
		{
			return '';
		}
		return $matches[0];
	}

	/**
	 * Put <pre> blocks back into the main content flow
	 *
	 * @param   string   $text  Markup
	 * @param   boolean  $html  Escape HTML?
	 * @return  string
	 */
	private function unstrip($text, $html=true)
	{
		foreach ($this->_tokens as $tag => $vals)
		{
			$text = preg_replace_callback('/<(' . $tag . ') (.+?)><\/(\1) \2>/si', array(&$this, '_dataPull'), $text);
			$this->_tokens[$tag] = array();
		}

		return $text;
	}

	/**
	 * Restores <pre></pre> blocks to their actual content
	 *
	 * @param   string  $txt
	 * @return  string
	 */
	private function _restorePre($txt)
	{
		return $txt;
	}

	/**
	 * Restores <pre></pre> blocks to their actual content
	 *
	 * @param   string  $txt
	 * @return  string
	 */
	private function _restoreCode($txt)
	{
		return $txt;
	}

	/**
	 * Parse macro tags
	 * [[MacroName(args)]]
	 *
	 * @param   string  $text  Raw markup
	 * @return  string
	 */
	private function macros($text)
	{
		$path = __DIR__;
		if (is_file($path . DS . 'macro.php'))
		{
			// Include abstract macro class
			include_once $path . DS . 'macro.php';
		}
		else
		{
			// Abstract macro class not found
			// Proceed no further
			return $text;
		}

		$this->addMacroPath(__DIR__ . DS . 'macros');

		// Get macros [[name(args)]]
		return preg_replace_callback('/\[\[(?P<macroname>[\w.]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U', array(&$this, '_getMacro'), $text);
	}

	/**
	 * Attempt to load a specific macro class and return its contents
	 *
	 * @param   array  $matches  Result form [[Macro()]] pattern matching
	 * @return  string
	 */
	private function _getMacro($matches)
	{
		if (isset($matches[1]) && $matches[1] != '')
		{
			// Split macro by . (dot) char
			$macroPieces = explode('.', strtolower($matches[1]));

			// Build namespaced macro name
			$macroname = __NAMESPACE__ . '\\Macros\\' . implode('\\', array_map('ucfirst', $macroPieces));

			if (!isset(self::$macros[$matches[1]]))
			{
				// Build macro path
				$file = DS . implode(DS, array_map('strtolower', $macroPieces)) . '.php';

				$found = false;

				foreach ($this->getMacroPaths() as $path)
				{
					if (is_file($path . $file))
					{
						$found = true;

						include_once $path . $file;
						break;
					}
				}

				if (!$found)
				{
					return '';
				}

				if (class_exists($macroname))
				{
					$macro = new $macroname();

					if (!$this->get('fullparse') && !$macro->allowPartial)
					{
						return '<strong>Macro "' . $matches[1] . '" not allowed.</strong>';
					}

					self::$macros[$matches[1]] =& $macro;
				}
				else
				{
					self::$macros[$matches[1]] = false;
				}
			}
			else
			{
				$macro =& self::$macros[$matches[1]];
			}

			if (!isset($macro) || !is_object($macro))
			{
				return '';
			}

			$macro->args = null;
			if (isset($matches[3]) && $matches[3])
			{
				$macro->args = $matches[3];
			}
			$macro->option     = $this->get('option');
			$macro->scope      = $this->get('scope');
			$macro->pagename   = $this->get('pagename');
			$macro->domain     = $this->get('domain');
			$macro->uniqPrefix = $this->token();
			if ($this->get('pageid') > 0)
			{
				$macro->pageid = $this->get('pageid');
			}
			else
			{
				$macro->pageid = \Request::getInt('lid', 0, 'post');
			}
			$macro->filepath   = $this->get('filepath');

			// Push contents to a container -- we'll retrieve this later
			// This is done to prevent any further wiki parsing of contents macro may return
			if (count($macro->linkLog) > 0)
			{
				foreach ($macro->linkLog as $linkLog)
				{
					array_push($this->_data['links'], $linkLog);
				}
			}

			return $this->_dataPush(array(
				$matches[1],
				'macro',
				$this->_randomString(),
				$macro->render()
			));
		}
	}

	/**
	 * Put macro output back into the text
	 *
	 * @param   string  $txt
	 * @return  string
	 */
	private function _restoreMacro($txt)
	{
		return $txt;
	}

	/**
	 * Post process the text
	 *
	 * @param   string  $text  Raw markup
	 * @return  string
	 */
	private function post($text)
	{
		foreach (self::$macros as $macro)
		{
			if ($macro)
			{
				$text = $macro->postProcess($text);
			}
		}

		return $text;
	}
}
