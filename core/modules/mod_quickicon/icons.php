<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\QuickIcon;

/**
 * Utility class for icons.
 */
class Icons
{
	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array   $buttons  Array of buttons
	 * @return  string
	 */
	public static function buttons($buttons)
	{
		$html = array();
		foreach ($buttons as $button)
		{
			$html[] = self::button($button);
		}
		return implode($html);
	}

	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array|object   $button  Button properties
	 * @return  string
	 */
	public static function button($button)
	{
		if (!empty($button['access']))
		{
			if (is_bool($button['access']))
			{
				if ($button['access'] == false)
				{
					return '';
				}
			}
			else
			{

				// Take each pair of permission, context values.
				for ($i = 0, $n = count($button['access']); $i < $n; $i += 2)
				{
					if (!\User::authorise($button['access'][$i], $button['access'][$i+1]))
					{
						return '';
					}
				}
			}
		}

		$html[] = '<div class="icon-wrapper"' . (empty($button['id']) ? '' : (' id="' . $button['id'] . '"')) . '>';
		$html[] = '<div class="icon">';
		$html[] = '<a href="' . $button['link'] . '"';
		$html[] = (empty($button['target']) ? '' : (' rel="noopener" target="' . $button['target'] . '"'));
		$html[] = (empty($button['onclick']) ? '' : (' onclick="' . $button['onclick'] . '"'));
		$html[] = (empty($button['title']) ? '' : (' title="' . htmlspecialchars($button['title']) . '"'));
		$html[] = '>';
		if (isset($button['image']) && $button['image'])
		{
			$html[] = \Html::asset('image', empty($button['image']) ? '' : $button['image'], empty($button['alt']) ? null : htmlspecialchars($button['alt']), null, true);
		}
		$html[] = (empty($button['text'])) ? '' : ('<span>' . $button['text'] . '</span>');
		$html[] = '</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode($html);
	}
}
