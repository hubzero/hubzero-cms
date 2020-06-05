<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Macro class for creating columns
 */
class Column extends Macro
{
	/**
	 * Number of columns to dipslay
	 *
	 * @var integer
	 */
	protected static $_columns = 0;

	/**
	 * Indicator of where we are in the column count
	 *
	 * @var integer
	 */
	protected static $_cursor = 0;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Allows content to be split into columns. This macro must first start with a declaration of the number of columns you want to use: `Column(3)`. Then you must use the macro twice to indicate where a column starts and ends: `Column(start)` and `Column(end)`. Attributes may be applied by separating name/value pairs with a comma. Example: Column(start, class=myclass)';
		$txt['html'] = '<p>Allows content to be split into columns. This macro must first start with a declaration of the number of columns you want to use: <code>[[Column(3)]]</code> Then you must use the macro twice to indicate where a column starts and ends:</p><p><code>[[Column(start)]]<br />content<br />[[Column(end)]]</code></p><p>Attributes may be applied by separating name/value pairs with a comma. Example: <code>[[Column(start, class=myclass)]]</code>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$attribs = explode(',', $et);
		$text = strtolower(array_shift($attribs));

		if (is_numeric($text))
		{
			$this->_columns = intval($text);
			return '<div class="grid">' . "\n";
		}

		$div  = '';

		if (trim($text) == 'start')
		{
			$this->_cursor++;

			$cls = array('col');

			switch ($this->_columns)
			{
				case 6:
					$cls[] = 'span2';
					break;
				//case 5:
					//$cls[] = 'five';
					//break;
				case 4:
					$cls[] = 'span3';
					break;
				case 3:
					$cls[] = 'span4';
					break;
				case 2:
					$cls[] = 'span6';
					break;
				default:
					break;
			}

			if ($this->_cursor == $this->_columns)
			{
				$cls[] = 'omega';
			}

			$atts = array();
			if (!empty($attribs) && count($attribs) > 0)
			{
				foreach ($attribs as $a)
				{
					$a = preg_split('/=/', $a);
					$key = strtolower(trim($a[0]));
					$val = trim(end($a));
					$val = trim($val, '"');
					$val = trim($val, "'");

					$key = htmlentities($key, ENT_COMPAT, 'UTF-8');
					$val = htmlentities($val, ENT_COMPAT, 'UTF-8');

					if ($key == 'class')
					{
						$cls[] = $val;
						continue;
					}

					$atts[] = $key.'="'.$val.'"';
				}
			}

			$div  = '<div class="' . implode(' ', $cls) . '"';
			$div .= (!empty($atts)) ? ' ' . implode(' ', $atts) . '>' : '>';
		}
		elseif (trim($text) == 'end')
		{
			$div  = '</div><!-- / .col -->';
			if ($this->_cursor == $this->_columns)
			{
				$div .= "\n" . '</div><!-- / .grid -->';
				$this->_cursor = 0;
			}
		}

		return $div;
	}
}
