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

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;
use Hubzero\Utility\Arr;

/**
 * Head renderer
 *
 * Inspired by Joomla's JDocumentRendererHead class
 */
class Head extends Renderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @param   string  $head     (unused)
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  The script
	 * @return  string  The output of the script
	 */
	public function render($head, $params = array(), $content = null)
	{
		ob_start();
		echo $this->fetchHead($this->doc);
		$buffer = ob_get_contents();
		ob_end_clean();

		return $buffer;
	}

	/**
	 * Generates the head HTML and return the results as a string
	 *
	 * @param   object  &$document  The document for which the head will be created
	 * @return  string  The head hTML
	 */
	public function fetchHead(&$document)
	{
		// Trigger the onBeforeCompileHead event (skip for installation, since it causes an error)
		\Event::trigger('onBeforeCompileHead');

		// Get line endings
		$lnEnd  = $document->_getLineEnd();
		$tab    = $document->_getTab();
		$tagEnd = ' />';
		$buffer = array();

		// Generate base tag (need to happen first)
		$base = $document->getBase();
		if (!empty($base))
		{
			$buffer[] = $tab . '<base href="' . $document->getBase() . '" />';
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($document->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv')
				{
					$content .= '; charset=' . $document->getCharset();
					$buffer[] = $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />';
				}
				elseif ($type == 'standard' && !empty($content) && isset($content['content']))
				{
					$buffer[] = $tab . '<meta name="' . $content['name'] . '" content="' . htmlspecialchars($content['content']) . '" />';
				}
			}
		}

		// Don't add empty descriptions
		if ($description = $document->getDescription())
		{
			$buffer[] = $tab . '<meta name="description" content="' . htmlspecialchars($description) . '" />';
		}

		// Don't add empty generators
		if ($generator = $document->getGenerator())
		{
			$buffer[] = $tab . '<meta name="generator" content="' . htmlspecialchars($generator) . '" />';
		}

		$buffer[] = $tab . '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>';

		// Generate link declarations
		foreach ($document->_links as $link => $linkAtrr)
		{
			$line = $tab . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
			if ($temp = Arr::toString($linkAtrr['attribs']))
			{
				$line .= ' ' . $temp;
			}
			$line .= ' />';

			$buffer[] = $line;
		}

		// Generate stylesheet links
		foreach ($document->_styleSheets as $strSrc => $strAttr)
		{
			$line = $tab . '<link rel="stylesheet" href="' . $strSrc . '" type="' . $strAttr['mime'] . '"';
			if (!is_null($strAttr['media']))
			{
				$line .= ' media="' . $strAttr['media'] . '" ';
			}
			if ($temp = Arr::toString($strAttr['attribs']))
			{
				$line .= ' ' . $temp;
			}

			$buffer[] = $line . $tagEnd;
		}

		// Generate stylesheet declarations
		foreach ($document->_style as $type => $content)
		{
			$buffer[] = $tab . '<style type="' . $type . '">';

			// This is for full XHTML support.
			if ($document->_mime != 'text/html')
			{
				$buffer[] = $tab . $tab . '<![CDATA[';
			}

			$buffer[] = $content;

			// See above note
			if ($document->_mime != 'text/html')
			{
				$buffer[] = $tab . $tab . ']]>';
			}
			$buffer[] = $tab . '</style>';
		}

		// Generate script file links
		foreach ($document->_scripts as $strSrc => $strAttr)
		{
			$line = $tab . '<script src="' . $strSrc . '"';
			if (!is_null($strAttr['mime']))
			{
				$line .= ' type="' . $strAttr['mime'] . '"';
			}
			if ($strAttr['defer'])
			{
				$line .= ' defer="defer"';
			}
			if ($strAttr['async'])
			{
				$line .= ' async="async"';
			}
			$line .= '></script>';

			$buffer[] = $line;
		}

		// Generate script declarations
		foreach ($document->_script as $type => $content)
		{
			$buffer[] = $tab . '<script type="' . $type . '">';

			// This is for full XHTML support.
			if ($document->_mime != 'text/html')
			{
				$buffer[] = $tab . $tab . '<![CDATA[';
			}

			if (is_array($content))
			{
				foreach ($content as $c)
				{
					$buffer[] = $c;
				}
			}
			else
			{
				$buffer[] = $content;
			}

			// See above note
			if ($document->_mime != 'text/html')
			{
				$buffer[] = $tab . $tab . ']]>';
			}
			$buffer[] = $tab . '</script>';
		}

		// Generate script language declarations.
		if (count(\JText::script()))
		{
			$buffer[] = $tab . '<script type="text/javascript">';
			$buffer[] = $tab . $tab . '(function() {';
			$buffer[] = $tab . $tab . $tab . 'var strings = ' . json_encode(\JText::script()) . ';';
			$buffer[] = $tab . $tab . $tab . 'if (typeof Joomla == \'undefined\') {';
			$buffer[] = $tab . $tab . $tab . $tab . 'Joomla = {};';
			$buffer[] = $tab . $tab . $tab . $tab . 'Joomla.JText = strings;';
			$buffer[] = $tab . $tab . $tab . '} else {';
			$buffer[] = $tab . $tab . $tab . $tab . 'Joomla.JText.load(strings);';
			$buffer[] = $tab . $tab . $tab . '}';
			$buffer[] = $tab . $tab . '})();';
			$buffer[] = $tab . '</script>';
		}

		foreach ($document->_custom as $custom)
		{
			$buffer[] = $tab . $custom;
		}

		return implode($lnEnd, $buffer);
	}
}
