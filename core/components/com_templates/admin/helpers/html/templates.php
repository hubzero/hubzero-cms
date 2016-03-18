<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 */
class JHtmlTemplates
{
	/**
	 * Display the thumb for the template.
	 *
	 * @param   string   $template   The name of the active view.
	 * @param   integer  $protected
	 * @return  string
	 */
	public static function thumb($template, $protected = 0)
	{
		$basePath = ($protected == 0 ? PATH_APP : PATH_CORE) . '/templates/' . $template;
		$baseUrl  = Request::root(true) . ($protected == 0 ? '/app' : '/core');
		$thumb    = $basePath . '/template_thumbnail.png';
		$preview  = $basePath . '/template_preview.png';
		$html     = '';

		if (file_exists($thumb))
		{
			$html = Html::asset('image', ltrim(substr($thumb, strlen(PATH_ROOT)), DS) , Lang::txt('COM_TEMPLATES_PREVIEW'));

			if (file_exists($preview))
			{
				$preview = $baseUrl . '/templates/' . $template . '/template_preview.png';
				$html    = '<a href="' . $preview . '" class="modal" title="' . Lang::txt('COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';
			}
		}

		return $html;
	}
}
