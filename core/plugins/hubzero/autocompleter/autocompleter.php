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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for autocompletion
 */
class plgHubzeroAutocompleter extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Flag for if scripts need to be pushed to the document or not
	 *
	 * @var  boolean
	 */
	private $_pushscripts = true;

	/**
	 * Display the autocompleter. Defaults to multi-entry for tags
	 *
	 * @param   array   $atts  Attributes for setting up the autocomplete
	 * @return  string  HTML
	 */
	public function onGetAutocompleter($atts)
	{
		// Ensure we have an array
		if (!is_array($atts))
		{
			$atts = array();
		}

		//var to hold scripts
		$scripts = '';

		// Set some parameters
		$opt   = (isset($atts[0])) ? $atts[0] : 'tags';  // The component to call
		$name  = (isset($atts[1])) ? $atts[1] : 'tags';  // Name of the input field
		$id    = (isset($atts[2])) ? $atts[2] : 'act';   // ID of the input field
		$class = (isset($atts[3])) ? 'autocomplete ' . $atts[3] : 'autocomplete';  // CSS class(es) for the input field
		$value = (isset($atts[4])) ? $atts[4] : '';      // The value of the input field
		$size  = (isset($atts[5])) ? $atts[5] : '';      // The size of the input field
		$wsel  = (isset($atts[6])) ? $atts[6] : '';      // AC autopopulates a select list based on choice?
		$type  = (isset($atts[7])) ? $atts[7] : 'multi'; // Allow single or multiple entries
		$dsabl = (isset($atts[8])) ? $atts[8] : '';      // Readonly input

		$base = rtrim(Request::root(true), '/');
		$datascript = $base . (App::isAdmin() ? '/adminsitrator' : '') . '/index.php';

		if ($opt != 'members')
		{
			$datascript = str_replace('/administrator', '', $datascript);
		}

		// Push some needed scripts and stylings to the template but ensure we do it only once
		if ($this->_pushscripts)
		{
			$scripts .= '<script type="text/javascript">var plgAutocompleterCss = "';

			$templatecss = DS . 'templates' . DS . App::get('template')->template . DS . 'html' . DS . 'plg_hubzero_autocompleter' . DS . 'autocompleter.css';
			$plugincss = DS . 'plugins' . DS . 'hubzero' . DS . 'autocompleter' . DS . 'autocompleter.css';
			if (file_exists(PATH_APP . $templatecss))
			{
				$scripts .= $base . substr(PATH_APP, strlen(PATH_ROOT)) . $templatecss . '?v=' . filemtime(PATH_APP . $templatecss);
			}
			else if (file_exists(PATH_CORE . $templatecss))
			{
				$scripts .= $base . '/core' . $templatecss . '?v=' . filemtime(PATH_CORE . $templatecss);
			}
			else
			{
				$scripts .= $base . '/core' . $plugincss . '?v=' . filemtime(PATH_CORE . $plugincss);
			}

			$scripts .= '";</script>' . "\n";
			$scripts .= '<script type="text/javascript" src="' . $base . '/core/plugins' . DS . $this->_type . DS . $this->_name . DS . $this->_name . '.js"></script>' . "\n";

			$this->_pushscripts = false;
		}

		// Build the input tag
		$html  = '<input type="text" name="' . $name . '" data-options="' . $opt . ',' . $type . ',' . $wsel . '"';
		$html .= ($id)    ? ' id="' . $id . '"'             : '';
		$html .= ($class) ? ' class="' . trim($class) . '"' : '';
		$html .= ($size)  ? ' size="' . $size . '"'         : '';
		$html .= ' placeholder="' . Lang::txt('PLG_HUBZERO_AUTOCOMPLETER_' . strtoupper($opt) . '_PLACEHOLDER') . '"';
		$html .= ' value="' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '" autocomplete="off" data-css="" data-script="' . $datascript . '" />' . "\n";
		$html .= $scripts;

		/*$json = '';
		if ($value)
		{
			$items = array();
			$data = explode(',', $value);
			$data = array_map('trim', $data);
			foreach ($data as $item)
			{
				if ($type != 'tags')
				{
					if (preg_match('/(.*)\((.*)\)/U', $item, $matched)) {
						$itemId = htmlentities(stripslashes($matched[2]), ENT_COMPAT, 'UTF-8');
						$itemName = htmlentities(stripslashes($matched[1]), ENT_COMPAT, 'UTF-8');
					} else {
						$itemId = htmlentities(stripslashes($item), ENT_COMPAT, 'UTF-8');
						$itemName = $itemId;
					}
				}
				$items[] = "{'id':'".$itemId."','name':'".$itemName."'}";
			}
			$json = '['.implode(',',$items).']';
		}

		$html .= '<input type="hidden" name="pre-'.$name.'" rel="'.$id.'"';
		$html .= ($id)    ? ' id="pre-'.$id.'"'       : '';
		$html .= ($class) ? ' class="pre-'.trim($class).'"' : '';
		$html .= ' value="'. $json .'" />';*/

		// Return the Input tag
		return $html;
	}

	/**
	 * Display the autocompleter for a multi-entry field
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetMultiEntry($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'multi';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter($params);
	}

	/**
	 * Display the autocompleter for a single entry field
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetSingleEntry($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'single';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter($params);
	}

	/**
	 * Display the autocompleter for a single entry field with accompanying select
	 *
	 * @param      array $atts Attributes for setting up the autocomplete
	 * @return     string HTML
	 */
	public function onGetSingleEntryWithSelect($atts)
	{
		if (!is_array($atts))
		{
			$atts = array();
		}
		$params   = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'groups';
		$params[] = (isset($atts[1])) ? $atts[1] : 'groups';
		$params[] = (isset($atts[2])) ? $atts[2] : 'acg';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = (isset($atts[6])) ? $atts[6] : 'ticketowner';
		$params[] = 'single';
		$params[] = (isset($atts[7])) ? $atts[7] : '';

		return $this->onGetAutocompleter($params);
	}
}
