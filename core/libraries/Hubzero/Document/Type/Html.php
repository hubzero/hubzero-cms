<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type;

use Hubzero\Document\Base;
use Hubzero\Config\Registry;

/**
 * HTML Document class for parsing and displaying a HTML document
 *
 * Inspired by Joomla's JDocumentHtml class
 */
class Html extends Base
{
	/**
	 * Array of Header <link> tags
	 *
	 * @var  array
	 */
	public $_links = array();

	/**
	 * Array of custom tags
	 *
	 * @var  array
	 */
	public $_custom = array();

	/**
	 * Name of the template
	 *
	 * @var  string
	 */
	public $template = null;

	/**
	 * Base url
	 *
	 * @var  string
	 */
	public $baseurl = null;

	/**
	 * Array of template parameters
	 *
	 * @var  array
	 */
	public $params = null;

	/**
	 * File name
	 *
	 * @var  array
	 */
	public $_file = null;

	/**
	 * String holding parsed template
	 *
	 * @var  string
	 */
	protected $_template = '';

	/**
	 * Array of parsed template tags
	 *
	 * @var  array
	 */
	protected $_template_tags = array();

	/**
	 * Integer with caching setting
	 *
	 * @var  integer
	 */
	protected $_caching = null;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 * @return  void
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set document type
		$this->_type = 'html';

		// Set default mime type and document metadata (meta data syncs with mime type by default)
		$this->setMimeEncoding('text/html');
	}

	/**
	 * Get the HTML document head data
	 *
	 * @return  array  The document head data in array form
	 */
	public function getHeadData()
	{
		$data = array();
		$data['title']       = $this->title;
		$data['description'] = $this->description;
		$data['link']        = $this->link;
		$data['metaTags']    = $this->_metaTags;
		$data['links']       = $this->_links;
		$data['styleSheets'] = $this->_styleSheets;
		$data['style']       = $this->_style;
		$data['scripts']     = $this->_scripts;
		$data['script']      = $this->_script;
		$data['custom']      = $this->_custom;
		return $data;
	}

	/**
	 * Set the HTML document head data
	 *
	 * @param   array   $data  The document head data in array form
	 * @return  object  instance of $this to allow chaining
	 */
	public function setHeadData($data)
	{
		if (empty($data) || !is_array($data))
		{
			return;
		}

		$this->title        = (isset($data['title']) && !empty($data['title'])) ? $data['title'] : $this->title;
		$this->description  = (isset($data['description']) && !empty($data['description'])) ? $data['description'] : $this->description;
		$this->link         = (isset($data['link']) && !empty($data['link'])) ? $data['link'] : $this->link;

		$this->_metaTags    = (isset($data['metaTags']) && !empty($data['metaTags'])) ? $data['metaTags'] : $this->_metaTags;
		$this->_links       = (isset($data['links']) && !empty($data['links'])) ? $data['links'] : $this->_links;
		$this->_styleSheets = (isset($data['styleSheets']) && !empty($data['styleSheets'])) ? $data['styleSheets'] : $this->_styleSheets;
		$this->_style       = (isset($data['style']) && !empty($data['style'])) ? $data['style'] : $this->_style;
		$this->_scripts     = (isset($data['scripts']) && !empty($data['scripts'])) ? $data['scripts'] : $this->_scripts;
		$this->_script      = (isset($data['script']) && !empty($data['script'])) ? $data['script'] : $this->_script;
		$this->_custom      = (isset($data['custom']) && !empty($data['custom'])) ? $data['custom'] : $this->_custom;

		return $this;
	}

	/**
	 * Merge the HTML document head data
	 *
	 * @param   array   $data  The document head data in array form
	 * @return  object  instance of $this to allow chaining
	 */
	public function mergeHeadData($data)
	{
		if (empty($data) || !is_array($data))
		{
			return;
		}

		$this->title = (isset($data['title']) && !empty($data['title']) && !stristr($this->title, $data['title']))
			? $this->title . $data['title']
			: $this->title;
		$this->description = (isset($data['description']) && !empty($data['description']) && !stristr($this->description, $data['description']))
			? $this->description . $data['description']
			: $this->description;
		$this->link = (isset($data['link'])) ? $data['link'] : $this->link;

		if (isset($data['metaTags']))
		{
			foreach ($data['metaTags'] as $type1 => $data1)
			{
				$booldog = $type1 == 'http-equiv' ? true : false;
				foreach ($data1 as $name2 => $data2)
				{
					$this->setMetaData($name2, $data2, $booldog);
				}
			}
		}

		$this->_links = (isset($data['links']) && !empty($data['links']) && is_array($data['links']))
			? array_unique(array_merge($this->_links, $data['links']))
			: $this->_links;

		$this->_styleSheets = (isset($data['styleSheets']) && !empty($data['styleSheets']) && is_array($data['styleSheets']))
			? array_merge($this->_styleSheets, $data['styleSheets'])
			: $this->_styleSheets;

		if (isset($data['style']))
		{
			foreach ($data['style'] as $type => $stdata)
			{
				if (!isset($this->_style[strtolower($type)]) || !stristr($this->_style[strtolower($type)], $stdata))
				{
					$this->addStyleDeclaration($stdata, $type);
				}
			}
		}

		$this->_scripts = (isset($data['scripts']) && !empty($data['scripts']) && is_array($data['scripts']))
			? array_merge($this->_scripts, $data['scripts'])
			: $this->_scripts;

		if (isset($data['script']))
		{
			foreach ($data['script'] as $type => $sdata)
			{
				if (!isset($this->_script[strtolower($type)]) || !stristr($this->_script[strtolower($type)], $sdata))
				{
					$this->addScriptDeclaration($sdata, $type);
				}
			}
		}

		$this->_custom = (isset($data['custom']) && !empty($data['custom']) && is_array($data['custom']))
			? array_unique(array_merge($this->_custom, $data['custom']))
			: $this->_custom;

		return $this;
	}

	/**
	 * Adds <link> tags to the head of the document
	 *
	 * $relType defaults to 'rel' as it is the most common relation type used.
	 * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
	 * Typical tag: <link href="index.php" rel="Start">
	 *
	 * @param   string  $href      The link that is being related.
	 * @param   string  $relation  Relation of link.
	 * @param   string  $relType   Relation type attribute.  Either rel or rev (default: 'rel').
	 * @param   array   $attribs   Associative array of remaining attributes.
	 * @return  object  instance of $this to allow chaining
	 */
	public function addHeadLink($href, $relation, $relType = 'rel', $attribs = array())
	{
		$this->_links[$href]['relation'] = $relation;
		$this->_links[$href]['relType'] = $relType;
		$this->_links[$href]['attribs'] = $attribs;

		return $this;
	}

	/**
	 * Adds a shortcut icon (favicon)
	 *
	 * This adds a link to the icon shown in the favorites list or on
	 * the left of the url in the address bar. Some browsers display
	 * it on the tab, as well.
	 *
	 * @param   string  $href      The link that is being related.
	 * @param   string  $type      File type
	 * @param   string  $relation  Relation of link
	 * @return  object  instance of $this to allow chaining
	 */
	public function addFavicon($href, $type = 'image/vnd.microsoft.icon', $relation = 'shortcut icon')
	{
		$href = str_replace('\\', '/', $href);
		$this->addHeadLink($href, $relation, 'rel', array('type' => $type));

		return $this;
	}

	/**
	 * Adds a custom HTML string to the head block
	 *
	 * @param   string  $html  The HTML to add to the head
	 * @return  object  instance of $this to allow chaining
	 */
	public function addCustomTag($html)
	{
		$this->_custom[] = trim($html);

		return $this;
	}

	/**
	 * Get the contents of a document include
	 *
	 * @param   string  $type     The type of renderer
	 * @param   string  $name     The name of the element to render
	 * @param   array   $attribs  Associative array of remaining attributes.
	 * @return  The output of the renderer
	 */
	public function getBuffer($type = null, $name = null, $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null)
		{
			return parent::$_buffer;
		}

		$result = null;
		if (isset(parent::$_buffer[$type][$name]))
		{
			return parent::$_buffer[$type][$name];
		}

		// If the buffer has been explicitly turned off don't display or attempt to render
		if ($result === false)
		{
			return null;
		}

		$renderer = $this->loadRenderer($type);

		$this->setBuffer($renderer->render($name, $attribs, $result), $type, $name);

		return parent::$_buffer[$type][$name];
	}

	/**
	 * Set the contents a document includes
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 * @return  object  instance of $this to allow chaining
	 */
	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options))
		{
			$args = func_get_args();
			$options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
		}

		parent::$_buffer[$options['type']][$options['name']] = $content;

		return $this;
	}

	/**
	 * Parses the template and populates the buffer
	 *
	 * @param   array   $params  Parameters for fetching the template
	 * @return  object  instance of $this to allow chaining
	 */
	public function parse($params = array())
	{
		return $this->_fetchTemplate($params)->_parseTemplate();
	}

	/**
	 * Outputs the template to the browser.
	 *
	 * @param   boolean  $caching  If true, cache the output
	 * @param   array    $params   Associative array of attributes
	 * @return  object   The rendered data
	 */
	public function render($caching = false, $params = array())
	{
		$this->_caching = $caching;

		if (!empty($this->_template))
		{
			$data = $this->_renderTemplate();
		}
		else
		{
			$this->parse($params);
			$data = $this->_renderTemplate();
		}

		parent::render();
		return $data;
	}

	/**
	 * Count the modules based on the given condition
	 *
	 * @param   string   $condition  The condition to use
	 * @return  integer  Number of modules found
	 */
	public function countModules($condition)
	{
		$operators = '(\+|\-|\*|\/|==|\!=|\<\>|\<|\>|\<=|\>=|and|or|xor)';
		$words = preg_split('# ' . $operators . ' #', $condition, null, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $n = count($words); $i < $n; $i += 2)
		{
			// odd parts (modules)
			$name = strtolower($words[$i]);
			$words[$i] = ((isset(parent::$_buffer['modules'][$name])) && (parent::$_buffer['modules'][$name] === false))
				? 0
				: count(\Module::byPosition($name));
		}

		$str = 'return ' . implode(' ', $words) . ';';

		return eval($str);
	}

	/**
	 * Count the number of child menu items
	 *
	 * @return  integer  Number of child menu items
	 */
	public function countMenuChildren()
	{
		static $children;

		if (!isset($children))
		{
			$menu = \App::get('menu');
			$active = $menu->getActive();
			if ($active)
			{
				$dbo = \App::get('db');

				$query = $dbo->getQuery(true);
				$query->select('COUNT(*)');
				$query->from('#__menu');
				$query->where('parent_id = ' . $active->id);
				$query->where('published = 1');

				$children = $dbo->loadResult();
			}
			else
			{
				$children = 0;
			}
		}

		return $children;
	}

	/**
	 * Load a template file
	 *
	 * @param   string  $directory  The name of the template
	 * @param   string  $filename   The actual filename
	 * @return  string  The contents of the template
	 */
	protected function _loadTemplate($directory, $filename)
	{
		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . DS . $filename))
		{
			// Store the file path
			$this->_file = $directory . DS . $filename;

			//get the file content
			ob_start();
			require $directory . DS . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		// Try to find a favicon by checking the template and root folder
		$dirs = array(
			$directory . DS,
			PATH_ROOT . DS
		);

		foreach ($dirs as $dir)
		{
			$icon = $dir . 'favicon.ico';

			if (file_exists($icon))
			{
				$path = str_replace(PATH_ROOT . '/', '', $dir);
				$path = str_replace('\\', '/', $path);

				$this->addFavicon(rtrim(\Request::root(true), '/') . '/' . $path . 'favicon.ico');
				break;
			}
		}

		return $contents;
	}

	/**
	 * Fetch the template, and initialise the params
	 *
	 * @param   array   $params  Parameters to determine the template
	 * @return  object  instance of $this to allow chaining
	 */
	protected function _fetchTemplate($params = array())
	{
		// Check
		$directory = isset($params['directory']) ? $params['directory'] : PATH_CORE . '/templates';

		$template = isset($params['template']) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $params['template']) : 'system';
		$file     = isset($params['file'])     ? preg_replace('/[^A-Z0-9_\.-]/i', '', $params['file'])     : 'index.php';

		if (!file_exists($directory . DS . $template . DS . $file))
		{
			$directory = PATH_CORE . '/templates';
			$template  = 'system';
			$params['baseurl'] = str_replace('/app', '/core', $params['baseurl']);
		}

		// Load the language file for the template
		$lang = \App::get('language');
		$lang->load('tpl_' . $template, PATH_APP . DS . 'bootstrap' . DS . \App::get('client')->name, null, false, true) ||
		$lang->load('tpl_' . $template, $directory . DS . $template, null, false, true);

		// Assign the variables
		$this->template = $template;
		//$this->path     = (isset($params['path']) ? $params['path'] : rtrim(\Request::root(true), '/')) . '/templates/' . $template;
		//$this->baseurl  = rtrim(\Request::root(true), '/');
		$this->baseurl  = isset($params['baseurl']) ? $params['baseurl'] : rtrim(\Request::root(true), '/');
		$this->params   = isset($params['params'])  ? $params['params']  : new Registry;

		// Load
		$this->_template = $this->_loadTemplate($directory . DS . $template, $file);

		return $this;
	}

	/**
	 * Parse a document template
	 *
	 * @return  object  instance of $this to allow chaining
	 */
	protected function _parseTemplate()
	{
		$matches = array();

		if (preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $this->_template, $matches))
		{
			$template_tags_first = array();
			$template_tags_last  = array();

			// Step through the jdocs in reverse order.
			for ($i = count($matches[0]) - 1; $i >= 0; $i--)
			{
				$type = $matches[1][$i];
				$attribs = empty($matches[2][$i]) ? array() : $this->parseAttributes($matches[2][$i]);
				$name = isset($attribs['name']) ? $attribs['name'] : null;

				// Separate buffers to be executed first and last
				if ($type == 'module' || $type == 'modules')
				{
					$template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
				else
				{
					$template_tags_last[$matches[0][$i]]  = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
			}
			// Reverse the last array so the jdocs are in forward order.
			$template_tags_last = array_reverse($template_tags_last);

			$this->_template_tags = $template_tags_first + $template_tags_last;
		}

		return $this;
	}

	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 * @return  array   Key/Value pairs for the attributes
	 */
	private function parseAttributes($string)
	{
		// Initialise variables.
		$attr = array();
		$ret  = array();

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);
			for ($i = 0; $i < $numPairs; $i++)
			{
				$ret[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $ret;
	}

	/**
	 * Render pre-parsed template
	 *
	 * @return  string  rendered template
	 */
	protected function _renderTemplate()
	{
		$replace = array();
		$with    = array();

		foreach ($this->_template_tags as $jdoc => $args)
		{
			$replace[] = $jdoc;
			$with[]    = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
		}

		return str_replace($replace, $with, $this->_template);
	}
}
